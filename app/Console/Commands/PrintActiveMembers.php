<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;

class PrintActiveMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:printactivemembers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print all active members of the association and the committees they are currently in.';

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
     * @return mixed
     */
    public function handle()
    {
        foreach (Member::all() as $member) {
            if (! $member->user->isActiveMember()) {
                continue;
            }
            $this->info(sprintf('%s: %s', $member->user->name, implode(', ', $member->user->committees->pluck('name')->toArray())));
        }
    }
}
