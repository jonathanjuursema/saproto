<?php

namespace App\Console\Commands;

use App\Models\Alias;
use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Solitweb\DirectAdmin\DirectAdmin;

class DirectAdminSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:dasync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages e-mail accounts and lists via DirectAdmin.';

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
     */
    public function handle(): void
    {
        $da = new DirectAdmin;
        $da->connect(Config::string('directadmin.da-hostname'), Config::string('directadmin.da-port'));
        $da->set_login(Config::string('directadmin.da-username'), Config::string('directadmin.da-password'));

        // Mail forwarders
        $da->query('/CMD_API_EMAIL_FORWARDERS', [
            'domain' => Config::string('directadmin.da-domain'),
        ]);
        $current = $da->fetch_parsed_body();
        $target = $this->constructForwarderList();
        $patch = $this->constructForwarderPatchList($current, $target);
        $forwarder_queries = $this->applyForwarderPatchList($patch);

        // E-mail accounts
        $da->query('/CMD_API_POP', [
            'domain' => Config::string('directadmin.da-domain'),
            'action' => 'list',
        ]);
        $current = $da->fetch_parsed_body();
        $target = $this->constructAccountList();
        $patch = $this->constructAccountPatchList($current['list'], $target);
        $account_queries = $this->applyAccountPatchList($patch);

        // Execute queries
        $this->executeQueries($da, array_merge($forwarder_queries, $account_queries));
        $this->info('Done!');
    }

    /**
     * Generate the user, committee and manually defined mail forwarders.
     */
    private function constructForwarderList(): array
    {
        $data = [];

        // Constructing user forwarders.
        $members = Member::all();
        foreach ($members as $member) {
            if ($member->proto_username) {
                $data[$member->proto_username] = [$member->user->email];
            }
        }

        // Constructing committee forwarders.
        $committees = Committee::all();
        foreach ($committees as $committee) {
            $destinations = [];

            $users = CommitteeMembership::withTrashed()
                ->where('committee_id', $committee->id)
                ->where('created_at', '<', date('Y-m-d H:i:s'))
                ->where(static function ($q) {
                    $q->whereNull('deleted_at')
                        ->orWhere('deleted_at', '>', date('Y-m-d H:i:s'));
                })->get();

            foreach ($users as $user) {
                $destinations[] = $user->user->email;
            }

            if ($destinations !== []) {
                $data[strtolower($committee->slug)] = $destinations;
                $data['committees'][] = strtolower($committee->slug.'@'.Config::string('proto.emaildomain'));
            }
        }

        // Constructing manual aliases.
        $aliases = Alias::all();
        foreach ($aliases as $alias) {
            $data[$alias->alias][] = $alias->destination ?? $alias->user->email;
        }

        return $data;
    }

    /**
     * Generate the list of accounts for all members.
     */
    private function constructAccountList(): array
    {
        $data = [];

        $members = Member::all();
        foreach ($members as $member) {
            if ($member->proto_username) {
                $data[] = $member->proto_username;
            }
        }

        foreach (Config::array('proto.additional_mailboxes') as $additional) {
            $data[] = $additional;
        }

        return $data;
    }

    /**
     * Construct a patch list of forwarders from the target list.
     *
     * @param  array  $current  The current list of forwarders
     * @param  array  $target  The target list of forwarders
     * @return array A forwarders patch list containing an 'add', 'mod' and 'del' array
     */
    private function constructForwarderPatchList(array $current, array $target): array
    {
        $data = [
            'add' => [],
            'mod' => [],
            'del' => [],
        ];

        // For each current forwarder, we check if it should exist against the target list.
        foreach ($current as $alias => $destination) {
            $alias = strtolower(str_replace('_', '.', $alias));
            $destination = explode(',', $destination);

            // It should exist, now we check if the forwarder needs to be rewritten.
            if (array_key_exists($alias, $target)) {

                // If one target destination is not currently present, rewrite whole forwarder.
                foreach ($target[$alias] as $d) {
                    if (! in_array($d, $destination)) {
                        $data['mod'][$alias] = $target[$alias];
                        break;
                    }
                }

                // If one current destination should not be present, rewrite whole forwarder.
                foreach ($destination as $d) {
                    if (! in_array($d, $target[$alias])) {
                        $data['mod'][$alias] = $target[$alias];
                        break;
                    }
                }

                // Otherwise, we do not modify this alias.
            } // Remove the forwarder because it does not exist according to the target list.
            else {
                $data['del'][] = $alias;
            }
        }

        // Now we check if we need to create any new forwarders.
        foreach ($target as $alias => $destination) {
            $alias = strtolower(str_replace('.', '_', $alias));
            if (! array_key_exists($alias, $current)) {
                // The forwarder does not yet exist...
                $data['add'][$alias] = $destination;
            }
        }

        return $data;
    }

    /**
     * Generate queries to apply the forwarders patch lists.
     *
     * @param  array  $patch  The forwarders patch list containing a 'add' and 'del' array.
     * @return array A list of queries to apply the forwarders patch
     */
    private function applyForwarderPatchList(array $patch): array
    {
        $queries = [];

        foreach ($patch['add'] as $alias => $destination) {
            $queries[] = [
                'cmd' => '/CMD_API_EMAIL_FORWARDERS',
                'options' => [
                    'domain' => Config::string('directadmin.da-domain'),
                    'action' => 'create',
                    'user' => $alias,
                    'email' => implode(',', $destination),
                ],
            ];
        }

        foreach ($patch['mod'] as $alias => $destination) {
            $queries[] = [
                'cmd' => '/CMD_API_EMAIL_FORWARDERS',
                'options' => [
                    'domain' => Config::string('directadmin.da-domain'),
                    'action' => 'modify',
                    'user' => $alias,
                    'email' => implode(',', $destination),
                ],
            ];
        }

        foreach ($patch['del'] as $del) {
            $queries[] = [
                'cmd' => '/CMD_API_EMAIL_FORWARDERS',
                'options' => [
                    'domain' => Config::string('directadmin.da-domain'),
                    'action' => 'delete',
                    'select0' => $del,
                ],
            ];
        }

        return $queries;
    }

    /**
     * Construct a patch list of accounts from the target list.
     *
     * @param  array  $current  The current list of accounts
     * @param  array  $target  The target list of accounts
     * @return array An accounts patch list containing an 'add' and 'del' array
     */
    private function constructAccountPatchList($current, array $target): array
    {
        $data = [
            'add' => [],
            'del' => [],
        ];

        // For each current account, we check if it should exist against the target list.
        foreach ($current as $account) {

            // The account should not exist!
            if (! in_array($account, $target)) {
                $data['del'][] = $account;
            }
        }

        // Now we check if we need to create any new accounts.
        foreach ($target as $account) {

            // The account should be created!
            if (! in_array($account, $current)) {
                $data['add'][] = $account;
            }
        }

        return $data;
    }

    /**
     * Generate queries to apply the accounts patch lists.
     *
     * @param  array  $patch  The accounts patch list containing a 'add' and 'del' array.
     * @return array A list of queries to apply the accounts patch
     */
    private function applyAccountPatchList(array $patch): array
    {
        $queries = [];

        foreach ($patch['add'] as $account) {
            $password = Str::random(32);
            $queries[] = [
                'cmd' => '/CMD_API_POP',
                'options' => [
                    'domain' => Config::string('directadmin.da-domain'),
                    'action' => 'create',
                    'user' => $account,
                    'passwd' => $password,
                    'passwd2' => $password,
                    'quota' => 0, // Unlimited
                    'limit' => 0, // Unlimited
                ],
            ];
        }

        foreach ($patch['del'] as $account) {
            $queries[] = [
                'cmd' => '/CMD_API_POP',
                'options' => [
                    'domain' => Config::string('directadmin.da-domain'),
                    'action' => 'delete',
                    'user' => $account,
                ],
            ];
        }

        return $queries;
    }

    /**
     * Execute a list of DirectAdmin queries.
     *
     * @param  DirectAdmin  $da  The DirectAdmin instance
     * @param  array  $queries  An array containing a 'cmd' and 'options' array
     */
    private function executeQueries(DirectAdmin $da, array $queries): void
    {
        foreach ($queries as $query) {
            // $this->info('Query '.$i.'/'.count($queries).': '.$query['cmd'].implode($query['options'])); //Temporarily disabled to reduce Sentry spam
            $da->query($query['cmd'], $query['options']);

            $response = $da->fetch_parsed_body();
            if (! array_key_exists('error', $response)) {
                continue;
            }

            if ($response['error'] != 1) {
                continue;
            }

            $this->info('Error: '.$response['text'].', '.$response['details'].'!'.PHP_EOL);
        }
    }
}
