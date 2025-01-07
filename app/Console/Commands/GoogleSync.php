<?php

namespace App\Console\Commands;

use App\Console\ConsoleOutput;
use App\Enums\MembershipTypeEnum;
use App\Mail\NewWorkspaceAccount;
use App\Models\Alias as ProtoAlias;
use App\Models\Committee;
use App\Models\User as ProtoUser;
use Google\Service\Directory;
use Google\Service\Directory\Group as GoogleGroup;
use Google\Service\Directory\Member as GoogleGroupMembership;
use Google\Service\Directory\User as GoogleUser;
use Google\Service\Directory\UserExternalId;
use Google\Service\Directory\UserName;
use Google\Service\Exception;
use Google\Service\Gmail;
use Google\Service\Gmail\AutoForwarding;
use Google\Service\Gmail\ForwardingAddress;
use Google_Client;
use Google_Service_Exception;
use Google_Task_Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class GoogleSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:gsync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages e-mail accounts and lists via Google Workspace API.';

    /**
     * The Google Directory service.
     */
    private Directory $directory;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(): int
    {
        $this->output = new ConsoleOutput;
        if (! app()->environment('production')) {
            $this->output->info("Don't sync your local users to Google!");

            return 1;
        }

        $client = $this->createClient(
            'superadmin@proto.utwente.nl',
            [
                'https://www.googleapis.com/auth/admin.directory.user',
                'https://www.googleapis.com/auth/admin.directory.group',
                'https://www.googleapis.com/auth/admin.directory.group.member',
            ]
        );
        $this->directory = new Directory($client);
        $this->syncGoogleGroupsWithCommittees();
        $this->syncGoogleGroupsWithAliases();
        $this->syncGoogleGroupMembersWithExternalAliasesMembers();
        $this->syncGoogleUsersWithProtoUsers();

        $this->output->success('Finished!');

        return 0;
    }

    /**
     * Create Google Client for subject with given scopes.
     *
     * @throws \Google\Exception
     */
    public function createClient(string $subject, array $scopes): Google_Client
    {
        $client = new Google_Client;
        $client->setAuthConfig(config('proto.google_application_credentials'));
        $client->useApplicationDefaultCredentials();
        $client->setSubject($subject);
        $client->setApplicationName('Proto Website');
        $client->setScopes($scopes);

        return $client;
    }

    /**
     * Synchronise the current Proto groups with the Google Workspace groups.
     *
     *
     * @throws Throwable
     */
    public function syncGoogleGroupsWithCommittees(): void
    {
        $this->output->info('Groups:');

        $committees = Committee::query()->wherePublic(true)->get();
        $googleGroups = $this->listGoogleGroups();

        foreach ($committees as $committee) {

            // Try to find the corresponding Google Group by committee email and set the description if necessary.
            if (($googleGroup = $googleGroups->firstWhere('email', strtolower($committee->email))) != null) {
                $this->pp(
                    '<fg=green>✓</> '.str_pad("#$committee->id", 6).str_pad("$committee->name <fg=gray>", 65, '.')."</> $committee->email",
                );

                $this->setGoogleGroupToCommitteeDescription($googleGroup, $committee);

                continue;
            }

            // Try to find the corresponding Google Group by committee name and set the email if necessary.
            if (($googleGroup = $googleGroups->firstWhere('name', $committee->name)) != null) {
                $googleGroup->setEmail(strtolower($committee->email));
                $this->pp(
                    '<fg=blue>⟳</> '.str_pad("#$committee->id", 6).str_pad("$committee->name <fg=gray>", 65, '.')."</> ✎ $committee->email",
                    fn () => $this->directory->groups->patch($googleGroup->id, $googleGroup)
                );

                continue;
            }

            // Create a new Google Group for this committee if it can not be found by email or by name.
            $this->pp(
                '<fg=yellow>+</> '.str_pad("#$committee->id", 6).$committee->name,
                fn () => $this->directory->groups->insert(
                    new GoogleGroup([
                        'name' => $committee->name,
                        'email' => $committee->email,
                        'description' => $this->formatCommitteeDescription($committee),
                    ])
                )
            );
        }
    }

    /**
     * Synchronise the current Proto aliases with the Google Workspace groups.
     *
     *
     * @throws Throwable
     */
    public function syncGoogleGroupsWithAliases(): void
    {
        $this->output->info('Aliases:');

        $aliases = ProtoAlias::query()->groupBy('alias')->get();
        $googleGroups = $this->listGoogleGroups();
        $googleAliasGroups = $googleGroups->filter(fn ($group) => Str::startsWith($group->name, 'Alias'));

        $groupsCorrect = $aliases->filter(fn ($alias): bool => $googleAliasGroups->contains('email', $alias->email));
        $groupsToAdd = $aliases->reject(fn ($alias): bool => $googleAliasGroups->contains('email', $alias->email));
        $groupsToRemove = $googleAliasGroups->reject(fn ($group): bool => $aliases->contains('alias', strtok($group->email, '@')));

        foreach ($groupsCorrect as $alias) {
            $this->pp(
                '<fg=green>✓</> '.str_pad("#$alias->id", 6).$alias->alias,
            );
        }

        foreach ($groupsToRemove as $group) {
            $this->pp(
                '<fg=red>-</> '.str_pad("#$group->id", 16).str_pad("$group->name <fg=gray>", 40, '.')."</> $group->email",
                fn () => $this->directory->groups->delete($group->id)
            );
        }

        foreach ($groupsToAdd as $alias) {
            try {
                $this->pp(
                    '<fg=yellow>+</> '.str_pad("#$alias->id", 6).$alias->alias,
                    fn () => $this->directory->groups->insert(
                        new GoogleGroup([
                            'name' => "Alias $alias->alias",
                            'email' => $alias->email,
                            'description' => 'Email alias '.$alias->alias,
                        ])
                    )
                );
            } catch (Throwable $throwable) {
                $this->pp(
                    '<fg=red>x</> '.str_pad("#$alias->id", 6).$alias->alias,
                    fn (): mixed => dump($throwable->getMessage())
                );
            }
        }
    }

    /**
     * Synchronise the current Proto alias members with the Google Workspace group members.
     *
     *
     * @throws Throwable
     */
    public function syncGoogleGroupMembersWithExternalAliasesMembers(): void
    {
        $this->output->info('Alias Members:');

        $externalAliases = ProtoAlias::query()->whereNotNull('destination')->get();
        $inactiveMemberAliases = ProtoAlias::query()->select(['alias', 'users.email AS destination'])->join('users', 'user_id', '=', 'users.id')->whereHas('user', function (Builder $query) {
            $query->whereDoesntHave('groups');
        })->get();

        $aliases = $externalAliases->concat($inactiveMemberAliases)->groupBy('alias');
        $googleGroups = $this->listGoogleGroups();
        $googleAliasGroups = $googleGroups->filter(fn ($group) => Str::startsWith($group->name, 'Alias'));

        foreach ($aliases as $aliasGroup) {
            $googleGroup = $googleAliasGroups->firstWhere('email', $aliasGroup->first()->email);
            if ($googleGroup == null) {
                $this->pp(
                    '<fg=red>x</> '.str_pad($aliasGroup->first()->alias.' <fg=gray>', 65, '.').'</> '.$aliasGroup->count().' members'
                );

                continue;
            }

            $this->pp(
                '<fg=green>✓</> '.str_pad($aliasGroup->first()->alias.' <fg=gray>', 65, '.').'</> '.$aliasGroup->count().' members',
            );
            $googleGroupMembers = $this->listGoogleGroupMembers($googleGroup);

            $membersCorrect = $aliasGroup->filter(fn ($alias): bool => $googleGroupMembers->contains('email', $alias->destination));
            $membersToAdd = $aliasGroup->reject(fn ($alias): bool => $googleGroupMembers->contains('email', $alias->destination));
            $membersToRemove = $googleGroupMembers->reject(fn ($member): bool => $aliasGroup->contains('destination', $member->email));

            foreach ($membersCorrect as $alias) {
                $this->pp(
                    '        <fg=green>✓</> '.str_pad("#$alias->id", 6).$alias->destination,
                );
            }

            foreach ($membersToRemove as $member) {
                $this->pp(
                    '        <fg=red>-</> '.str_pad("#$member->id", 6).str_pad("$member->email <fg=gray>", 65, '.')."</> $member->email",
                    fn () => $this->directory->members->delete($googleGroup->id, $member->email)
                );
            }

            foreach ($membersToAdd as $alias) {
                $this->pp(
                    '        <fg=yellow>+</> '.str_pad("#$alias->id", 6).$alias->destination,
                    fn () => $this->directory->members->insert(
                        $googleGroup->id,
                        new GoogleGroupMembership([
                            'email' => $alias->destination,
                            'keyType' => 'USER',
                        ])
                    )
                );
            }
        }

    }

    /**
     * Synchronise the current Proto users with the Google Workspace users.
     *
     *
     * @throws Throwable
     */
    public function syncGoogleUsersWithProtoUsers(): void
    {
        $this->output->info('Users:');
        /* @var Collection<ProtoUser> $protoUsers */
        $protoUsers = ProtoUser::query()
            ->whereHas('member', function ($query) {
                $query->whereNotIn('membership_type', [MembershipTypeEnum::PENDING, MembershipTypeEnum::PET]);
            })
            ->whereHas('groups')
            ->get();

        $protoUsersWithGoogleAccount = $this->listProtoUserWithGoogleAccount();

        // Remove Google Workspace users that are no longer in active member list.
        /* @var Collection<ProtoUser> $usersToRemove */
        $usersToRemove = $protoUsersWithGoogleAccount->whereNotIn('id', $protoUsers->pluck('id'));
        foreach ($usersToRemove as $protoUser) {
            $optParams = ['domain' => 'proto.utwente.nl', 'query' => "externalId:$protoUser->id"];
            $googleUser = $this->directory->users->listUsers($optParams)->getUsers()[0];
            $this->pp(
                '<fg=red>-</> '.str_pad("#$protoUser->id", 6).$protoUser->name,
                fn () => $this->directory->users->delete($googleUser->id)
            );
        }

        // Loop over all active members.
        foreach ($protoUsers as $protoUser) {
            // Check if ProtoUser exists in Google Workspace, otherwise create the Google Workspace user.
            if ($protoUsersWithGoogleAccount->where('id', $protoUser->id)->first() != null) {
                $this->pp('<fg=green>✓</> '.str_pad("#$protoUser->id", 6).$protoUser->name.' | '.$protoUser->proto_email);
            } else {
                $this->createGoogleUser($protoUser);
            }

            // Synchronise Google Workspace groups for user.
            $this->syncGoogleGroupsForUser($protoUser);

            // Remove aliases created using old method.
            $this->removeAliasesForUser($protoUser);

            // Patch user's Gmail settings.
            $this->patchGmailSettings($protoUser);
        }
    }

    public function createGoogleUser($protoUser): void
    {
        try {
            $name = explode(' ', $protoUser->name);
            $password = Str::password();
            $this->pp(
                '<fg=yellow>+</> '.str_pad("#$protoUser->id", 6).
                str_pad("$protoUser->name <fg=gray>", 65, '.')."</> pass: {$password}",
                fn () => $this->directory->users->insert(
                    new GoogleUser([
                        'name' => new UserName([
                            'givenName' => $name[0],
                            'familyName' => implode(' ', array_slice($name, 1)),
                            'displayName' => $protoUser->calling_name,
                        ]),
                        'primaryEmail' => $protoUser->proto_email,
                        'hashFunction' => 'MD5',
                        'password' => hash('md5', $password),
                        'externalIds' => [new UserExternalId([
                            'type' => 'account',
                            'value' => $protoUser->id,
                        ])],
                    ])
                )
            );

            Mail::to($protoUser)->send(new NewWorkspaceAccount($protoUser));
        } catch (Throwable $throwable) {
            $this->pp(
                '<fg=red>x</> '.str_pad("#$protoUser->id", 6).$protoUser->name,
                fn (): mixed => dump($throwable->getMessage())
            );
        }
    }

    /**
     * List Google Workspace groups. Pass in user to get only their groups.
     *
     * @return Collection<GoogleGroup>
     *
     * @throws Exception
     */
    public function listGoogleGroups(?ProtoUser $protoUser = null): Collection
    {
        $googleGroups = collect();
        $optParams = ['domain' => 'proto.utwente.nl', 'maxResults' => 25];
        if ($protoUser != null) {
            $optParams['userKey'] = $protoUser->proto_email;
        }

        $pageToken = null;
        do {
            $optParams['pageToken'] = $pageToken;
            $results = $this->directory->groups->listGroups($optParams);
            $pageToken = $results->getNextPageToken();
            foreach ($results->getGroups() as $googleGroup) {
                $googleGroups->push($googleGroup);
            }
        } while ($pageToken);

        return $googleGroups;
    }

    /**
     * List Google Workspace group members of a specific group
     *
     * @return Collection<GoogleGroup>
     *
     * @throws Exception
     */
    public function listGoogleGroupMembers(GoogleGroup $googleGroup): Collection
    {
        $googleGroupMembers = collect();
        $optParams = [];
        $pageToken = null;
        do {
            $optParams['pageToken'] = $pageToken;
            $results = $this->directory->members->listMembers($googleGroup->id, $optParams);
            $pageToken = $results->getNextPageToken();
            foreach ($results->getMembers() as $members) {
                $googleGroupMembers->push($members);
            }
        } while ($pageToken);

        return $googleGroupMembers;
    }

    /**
     * List current Google Workspace users.
     *
     * @throws Exception
     */
    public function listProtoUserWithGoogleAccount(): Collection
    {
        $protoUsers = collect();
        $optParams = ['domain' => 'proto.utwente.nl', 'maxResults' => 50];
        $pageToken = null;
        do {
            $optParams['pageToken'] = $pageToken;
            $results = $this->directory->users->listUsers($optParams);
            $pageToken = $results->getNextPageToken();
            foreach ($results->getUsers() as $googleUser) {
                $id = $googleUser->getExternalIds()[0]['value'] ?? null;
                $protoUser = ProtoUser::find($id);
                if ($protoUser != null) {
                    $protoUsers->push($protoUser);
                }
            }
        } while ($pageToken);

        return $protoUsers;
    }

    /**
     * Synchronise Google Workspace groups for user.
     *
     *
     * @throws Exception
     */
    public function syncGoogleGroupsForUser(ProtoUser $protoUser): void
    {
        $indent = '        ';
        $protoGroups = $protoUser->groups()->where('public', true)->get()->pluck('email');
        $protoAliases = ProtoAlias::where('user_id', $protoUser->id)->get()->pluck('email');

        $targetGroups = $protoGroups->merge($protoAliases);
        $googleGroups = $this->listGoogleGroups($protoUser);

        $groupsCorrect = $targetGroups->intersect($googleGroups->pluck('email'));
        $groupsToAdd = $targetGroups->diff($googleGroups->pluck('email'));
        $groupsToRemove = $googleGroups->pluck('email')->diff($targetGroups);

        foreach ($groupsCorrect as $group) {
            $this->pp(
                $indent.'<fg=green>✓</> '.$group,
            );
        }

        foreach ($groupsToRemove as $group) {
            $googleGroup = $googleGroups->firstWhere('email', $group);
            $this->pp(
                $indent.'<fg=red>-</> '.str_pad("#$googleGroup->id", 6).$googleGroup->name,
                fn () => $this->directory->members->delete($googleGroup->email, $protoUser->proto_email)
            );
        }

        foreach ($groupsToAdd as $group) {
            try {
                $this->pp(
                    $indent.'<fg=yellow>+</> '.str_pad('', 6).$group,
                    fn () => $this->directory->members->insert(
                        $group,
                        new GoogleGroupMembership([
                            'email' => $protoUser->proto_email,
                            'keyType' => 'USER',
                        ])
                    )
                );
            } catch (Throwable $throwable) {
                $this->pp(
                    $indent.'<fg=red>x</> '.str_pad('', 6).$group,
                    fn (): mixed => throw $throwable
                );
            }
        }
    }

    /**
     * @throws Exception
     */
    public function removeAliasesForUser(ProtoUser $protoUser): void
    {
        $indent = '        ';
        $googleUserAliases = $this->directory->users_aliases->listUsersAliases($protoUser->proto_email)->getAliases();
        $googleUserAliases = collect(array_column($googleUserAliases ?? [], 'alias'));

        foreach ($googleUserAliases as $emailAlias) {
            $this->pp(
                $indent."<fg=red>-</> ✉ {$emailAlias}",
                fn () => $this->directory->users_aliases->delete($protoUser->proto_email, $emailAlias)
            );
        }
    }

    /**
     * Patch Gmail settings by impersonating a Google Workspace user.
     *
     * @throws \Google\Exception
     */
    public function patchGmailSettings(ProtoUser $protoUser): void
    {
        $client = $this->createClient(
            $protoUser->proto_email,
            [
                'https://www.googleapis.com/auth/gmail.settings.basic',
                'https://www.googleapis.com/auth/gmail.settings.sharing',
            ]
        );
        $gmail = new Gmail($client);
        $indent = '        ';
        try {
            $gmail->users_settings_forwardingAddresses->get('me', $protoUser->email);
        } catch (Throwable) {
            $this->pp(
                $indent."<fg=yellow>+</> ✉ $protoUser->email",
                fn () => $gmail->users_settings_forwardingAddresses->create(
                    'me',
                    new ForwardingAddress(['forwardingEmail' => $protoUser->email])
                )
            );

            return;
        } finally {
            $this->pp(
                $indent."<fg=green>✓</> ✉ $protoUser->email"
            );

            try {
                $gmail->users_settings->updateAutoForwarding('me', new AutoForwarding([
                    'enabled' => true,
                    'emailAddress' => $protoUser->email,
                    'disposition' => 'leaveInInbox',
                ]));
            } catch (Throwable) {
                $this->pp(
                    $indent."<fg=yellow>x</> ✉ $protoUser->email: Forwarder is not verified",
                    fn () => throw new Exception('Invalid forwarding address')
                );
            }
        }
    }

    private function setGoogleGroupToCommitteeDescription(GoogleGroup $googleGroup, Committee $committee): void
    {
        $description = $this->formatCommitteeDescription($committee);

        if ($googleGroup->description != $description) {
            $googleGroup->setDescription($description);
            try {
                $this->pp(
                    '<fg=blue>⟳</> '.str_pad("#$committee->id", 6).str_pad("$committee->name <fg=gray>", 65, '.').'</> ✎ Description',
                    fn () => $this->directory->groups->patch($googleGroup->id, $googleGroup)
                );
            } catch (Throwable) {/* Ignored */
            }
        }
    }

    /**
     * Pretty print a task with a message and a function.
     *
     * @param  $message  string
     * @param  $func  callable|null
     */
    private function pp(string $message, ?callable $func = null): void
    {
        try {
            $this->output->task($message, $func);
        } catch (Google_Service_Exception|Google_Task_Exception $e) {
            // Ignore 409 errors, as these are expected when trying to create an existing resource.
            if ($e->getCode() == 409) {
                return;
            }

            // Ignore 403 errors, as these are expected when trying to access a resource not created by this client.
            if ($e->getCode() == 403) {
                return;
            }

            // Ignore invalid forwarding address error as it just means the user still has to accept the forwarder.
            if ($e->getMessage() === 'Invalid forwarding address') {
                return;
            }

            if ($e->getErrors() != null) {
                dump($e->getErrors());
            } elseif ($e->getMessage() != null) {
                dump($e->getMessage());
            } else {
                dump($e);
            }
        } catch (Throwable $e) {
            dump($e);
        }
    }

    private function formatCommitteeDescription(Committee $committee): string
    {
        $description = str_replace("\r", ' ', $committee->description);
        $description = str_replace('"', '', $description);

        return strip_tags($description);
    }
}
