<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddSysadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:addsysadmin {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give a user the sysadmin role.';

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
    public function handle(): int
    {
        $user_id = (int) $this->argument('user_id');
        $user = User::query()->find($user_id);
        if (! $user) {
            $this->error('User could not be found.');

            return 1;
        }

        if ($this->confirm('Give '.$user->name.' admin right?')) {
            $user->assignRole('sysadmin');
            $this->info('Sysadmin role granted!');
        }

        return 0;
    }
}
