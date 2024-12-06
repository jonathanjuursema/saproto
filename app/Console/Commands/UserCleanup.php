<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:usercleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears all users registrations that have not been completed (users without password and no data associated).';

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
        $this->info('Starting clean-up.');

        $count = 0;

        foreach (User::withTrashed()->get() as $user) {
            if (! $user->isStale()) {
                continue;
            }

            $count++;
            $user->forceDelete();
        }

        $this->info("Found and deleted {$count} stale users.");
    }
}
