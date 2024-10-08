<?php

namespace App\Console\Commands;

use App\Console\ConsoleOutput;
use App\Mail\NewWorkspaceAccount;
use App\Models\Alias as ProtoAlias;
use App\Models\Committee;
use App\Models\User as ProtoUser;
use Google\Service\Directory;
use Google\Service\Directory\Alias as GoogleAlias;
use Google\Service\Directory\Group as GoogleGroup;
use Google\Service\Directory\Member as GoogleGroupMembership;
use Google\Service\Directory\User as GoogleUser;
use Google\Service\Directory\UserExternalId;
use Google\Service\Directory\UserName;
use Google\Service\Exception;
use Google\Service\Gmail;
use Google\Service\Gmail\ForwardingAddress;
use Google_Client;
use Illuminate\Console\Command;
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
        $this->output = new ConsoleOutput();
        if (!app()->environment('production')) {
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
        $this->syncGoogleUsersWithProtoUsers();

        return 0;
    }

    /**
     * Create Google Client for subject with given scopes.
     */
    public function createClient(string $subject, array $scopes): Google_Client
    {
        $client = new Google_Client();
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

        $committees = Committee::whereSlug(config('proto.rootcommittee'))->get()->where('public');
        $googleGroups = $this->listGoogleGroups();

        foreach ($committees as $committee) {

            // Try to find the corresponding Google Group by committee email and set the description if necessary.
            if (($googleGroup = $googleGroups->firstWhere('email', $committee->email)) != null) {
                $this->setGoogleGroupToCommitteeDescription($googleGroup, $committee);

                continue;
            }

            //Try to find the corresponding Google Group by committee name and set the email if necessary.
            if (($googleGroup = $googleGroups->firstWhere('name', $committee->name)) != null) {
                $googleGroup->setEmail($committee->email);
                $this->pp(
                    '<fg=blue>⟳</> ' . str_pad("#$committee->id", 6) . str_pad("$committee->name <fg=gray>", 65, '.') . "</> ✎ $committee->email",
                    fn() => $this->directory->groups->patch($googleGroup->id, $googleGroup)
                );

                continue;
            }
            // Create a new Google Group for this committee if it can not be found by email or by name.
            $this->pp(
                '<fg=yellow>+</> ' . str_pad("#$committee->id", 6) . $committee->name,
                fn() => $this->directory->groups->insert(
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
            ->whereHas('member')
            ->whereHas('committees', function ($query) {
                $query->whereSlug(config('proto.rootcommittee'));
            })
            ->get();

        $googleUsers = $this->listGoogleUsers();

        // Remove Google Workspace users that are no longer in active member list.
        /* @var Collection<ProtoUser> $usersToRemove */

        $usersToRemove = $googleUsers->diff($protoUsers);
        foreach ($usersToRemove as $protoUser) {
            $optParams = ['domain' => 'proto.utwente.nl', 'query' => "externalId:$protoUser->id"];
            $googleUser = $this->directory->users->listUsers($optParams)->getUsers()[0];
            $this->pp(
                '<fg=red>-</> ' . str_pad("#$protoUser->id", 6) . $protoUser->name,
                fn() => $this->directory->users->delete($googleUser->id)
            );
        }
        // Loop over all active members.
        foreach ($protoUsers as $protoUser) {
            // Check if ProtoUser exists in Google Workspace.
            if ($googleUsers->contains($protoUser)) {
                $this->pp('<fg=green>✓</> ' . str_pad("#$protoUser->id", 6) . $protoUser->name);
                // Otherwise, create the Google Workspace user.
            } else {
                $this->createGoogleUser($protoUser);
            }

            // Synchronise Google Workspace groups for user.
            $this->syncGoogleGroupsForUser($protoUser);

            // Synchronise Google Workspace email aliases for user.
            $this->syncAliasesForUser($protoUser);

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
                '<fg=yellow>+</> ' . str_pad("#$protoUser->id", 6) .
                str_pad("$protoUser->name <fg=gray>", 65, '.') . "</> pass: $password",
                fn() => $this->directory->users->insert(
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
        } catch (Throwable $e) {
            $this->pp(
                '<fg=red>x</> ' . str_pad("#$protoUser->id", 6) . $protoUser->name,
                fn() => throw $e
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
    public
    function listGoogleGroups(?ProtoUser $protoUser = null): Collection
    {
        $optParams = ['domain' => 'proto.utwente.nl'];
        if ($protoUser != null) {
            $optParams['userKey'] = $protoUser->proto_email;
        }

        $results = $this->directory->groups->listGroups($optParams);
        $googleGroups = collect($results->getGroups());
        $pageToken = $results->getNextPageToken();
        do {
            $optParams['pageToken'] = $pageToken;
            $results = $this->directory->groups->listGroups($optParams);
            $pageToken = $results->getNextPageToken();
            $googleGroups->merge(collect($results->getGroups()));
        } while ($pageToken);

        return $googleGroups;
    }

    /**
     * List current Google Workspace users.
     *
     *
     * @throws Exception
     */
    public
    function listGoogleUsers(): Collection
    {
        $users = collect();
        $optParams = ['domain' => 'proto.utwente.nl'];
        $results = $this->directory->users->listUsers($optParams);
        $pageToken = $results->getNextPageToken();
        do {
            $optParams['pageToken'] = $pageToken;
            $results = $this->directory->users->listUsers($optParams);
            $pageToken = $results->getNextPageToken();
            foreach ($results->getUsers() as $user) {
                $id = $user->getExternalIds()[0]['value'] ?? null;
                if ($id == null) {
                    continue;
                }
                $user = ProtoUser::find($id);
                if ($user == null) {
                    continue;
                }
                $users->push($user);

            }
        } while ($pageToken);
        return $users;
    }

    /**
     * Synchronise Google Workspace groups for user.
     *
     *
     * @throws Exception
     */
    public
    function syncGoogleGroupsForUser(ProtoUser $protoUser): void
    {
        $indent = '        ';
        /* @var Committee $committee */
        foreach ($protoUser->groups()->get() as $committee) {
            try {
                if ($this->directory->members->hasMember($committee->email, $protoUser->proto_email)->isMember) {
                    $this->pp($indent . '<fg=green>✓</> ' . str_pad("#$committee->id", 6) . $committee->name);
                } else {
                    $this->pp(
                        $indent . '<fg=yellow>+</> ' . str_pad("#$committee->id", 6) . $committee->name,
                        fn() => $this->directory->members->insert(
                            $committee->email,
                            new GoogleGroupMembership([
                                'email' => $protoUser->proto_email,
                                'keyType' => 'USER',
                            ])
                        )
                    );
                }
            } catch (\Google_Service_Exception $e) {
                $this->pp(
                    $indent . '<fg=red>?</> ' . str_pad("#$committee->id", 6) . $committee->name,
                    fn() => dump($e->getErrors())
                );
            }
        }

        foreach ($this->listGoogleGroups($protoUser) as $googleGroup) {
            $committee = Committee::firstWhere('slug', explode('@', $googleGroup->email)[0]);

            if ($committee && !$committee->isMember($protoUser)) {
                $this->pp(
                    $indent . '<fg=red>-</> ' . str_pad("#$committee->id", 6) . "$committee->name",
                    fn() => $this->directory->members->delete($committee->email, $protoUser->proto_email)
                );
            }

        }
    }

    /**
     * @throws Exception
     */
    public
    function syncAliasesForUser(ProtoUser $protoUser): void
    {
        $indent = '        ';
        $googleUserAliases = $this->directory->users_aliases->listUsersAliases($protoUser->proto_email)->getAliases();
        $googleUserAliases = collect(array_column($googleUserAliases ?? [], 'alias'));
        $protoUserAliases = ProtoAlias::query()
            ->where('user_id', $protoUser->id)
            ->get()
            ->map(function ($alias) {
                /** @var ProtoAlias $alias */
                return $alias->alias . '@' . config('proto.emaildomain');
            })->unique();
        foreach ($protoUserAliases->diff($googleUserAliases) as $emailAlias) {
            $this->pp(
                $indent . "<fg=yellow>+</> ✉ $emailAlias",
                fn() => $this->directory->users_aliases->insert($protoUser->proto_email, new GoogleAlias([
                    'primaryEmail' => $protoUser->proto_email,
                    'alias' => $emailAlias,
                ]))
            );
        }


        foreach ($googleUserAliases->diff($protoUserAliases) as $emailAlias) {
            $this->pp(
                $indent . "<fg=red>-</> ✉ $emailAlias",
                fn() => $this->directory->users_aliases->delete($protoUser->proto_email, $emailAlias)
            );
        }
    }

    /**
     * Patch Gmail settings by impersonating a Google Workspace user.
     */
    public
    function patchGmailSettings(ProtoUser $protoUser): void
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
                $indent . "<fg=yellow>+</> ✉ $protoUser->email",
                fn() => $gmail->users_settings_forwardingAddresses->create(
                    'me',
                    new ForwardingAddress(['forwardingEmail' => $protoUser->email])
                )
            );

            return;
        }
        $this->pp(
            $indent . "<fg=green>✓</> ✉ $protoUser->email"
        );
    }

    private
    function setGoogleGroupToCommitteeDescription(GoogleGroup $googleGroup, Committee $committee): void
    {
        $description = $this->formatCommitteeDescription($committee);

        if ($googleGroup->description != $description) {
            $googleGroup->setDescription($description);
            try {
                $this->pp(
                    '<fg=blue>⟳</> ' . str_pad("#$committee->id", 6) . str_pad("$committee->name <fg=gray>", 65, '.') . '</> ✎ Description',
                    fn() => $this->directory->groups->patch($googleGroup->id, $googleGroup)
                );
            } catch (Throwable) {/* Ignored */
            }
        } else {
            $this->pp('<fg=green>✓</> ' . str_pad("#$committee->id", 6) . $committee->name);
        }
    }

    /**
     * Pretty print a task with a message and a function.
     *
     * @param  $message  string
     * @param  $func  callable|null
     */
    private
    function pp(string $message, ?callable $func = null): void
    {
        try {
            $this->output->task($message, $func);
        } catch (\Google_Service_Exception|\Google_Task_Exception $e) {
            // Ignore 409 errors, as they are expected when trying to create an existing resource.
            if ($e->getCode() == 409) {
                return;
            }

            if ($e->getErrors() != null) {
                dump($e->getErrors());
            } else if ($e->getMessage() != null) {
                dump($e->getMessage());
            } else {
                dump($e);
            }
        } catch (Throwable $e) {
            dump($e);
        }
    }

    private
    function formatCommitteeDescription(Committee $committee): string
    {
        $description = str_replace("\r", ' ', $committee->description);
        $description = str_replace('"', '', $description);
        return strip_tags($description);
    }
}
