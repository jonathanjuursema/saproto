<?php

namespace App\Console\Commands;

use App\Models\Committee;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

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
    public function handle(): void
    {
        $users = User::query()->get();

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

    private function convertCommitteeNameToGroup($name): string
    {
        return strtolower(str_replace(' ', '_', $name));
    }

    /**
     * @return mixed[]
     */
    private function convertCommitteesToGroups($committees): array
    {
        $groups = [];
        foreach ($committees as $committee) {
            $groups[] = $this->convertCommitteeNameToGroup($committee->name);
        }

        return $groups;
    }

    private function constructWikiGroups($user): string
    {
        $rootCommittee = $this->convertCommitteeNameToGroup(
            Committee::whereSlug(Config::string('proto.rootcommittee'))->firstOrFail()->name
        );
        $groups = ['user'];
        $groups = array_merge($groups, $this->convertCommitteesToGroups($user->committees));
        if (in_array($rootCommittee, $groups)) {
            $groups[] = 'admin';
        }

        return implode(',', $groups);
    }
}
