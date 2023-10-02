<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:makeadmin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give a user sys-admin rights. This command only works on non-production databases.';

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
        if (getenv('APP_ENV') == 'production') {
            $this->error('Cannot do this on production.');
        }

        $user = User::where('email', $this->argument('email'))->firstOrFail();

        if (! $user->hasRole('sysadmin')) {
            $user->assignRole('sysadmin');
            $this->info('User '.$user->name.' now has sysadmin role.');
        } else {
            $this->info('User '.$user->name.' already had sysadmin role.');
        }
    }
}
