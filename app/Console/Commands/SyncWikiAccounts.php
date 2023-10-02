<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Committee;
use App\Models\User;

class SyncWikiAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:generatewikiauthfile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes all elegible accounts for the Proto wiki.';

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
    public function handle()
    {
        $users = User::get();

        $configlines = [];

        foreach ($users as $user) {
            if (! $user->is_member) {
                continue;
            }

            $configlines[] = sprintf(
                '%s:%s:%s:%s:%s',
                $user->member->proto_username,
                $user->password,
                $user->name,
                $user->email,
                $this->constructWikiGroups($user)
            );
        }

        echo implode("\n", $configlines);
    }

    private function convertCommitteeNameToGroup($name)
    {
        return strtolower(str_replace(' ', '_', $name));
    }

    private function convertCommitteesToGroups($committees)
    {
        $groups = [];
        foreach ($committees as $committee) {
            $groups[] = $this->convertCommitteeNameToGroup($committee->name);
        }

        return $groups;
    }

    private function constructWikiGroups($user)
    {
        $rootCommittee = $this->convertCommitteeNameToGroup(
            Committee::whereSlug(config('proto.rootcommittee'))->firstOrFail()->name
        );
        $groups = ['user'];
        $groups = array_merge($groups, $this->convertCommitteesToGroups($user->committees));
        if (in_array($rootCommittee, $groups)) {
            $groups[] = 'admin';
        }

        return implode(',', $groups);
    }
}
