<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Http\Controllers\LdapController;
use App\Mail\UtwenteCleanup;
use App\Models\User;

class CheckUtwenteAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:checkutaccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies all currently linked UT accounts for being valid, and check studies.';

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
        $users = User::whereNotNull('utwente_username')->get();
        $this->info('Checking '.$users->count().' UTwente accounts.');

        $unlinked = [];

        foreach ($users as $user) {

            // Find remote user
            $utwente_username = $user->utwente_username;
            $remoteusers = LdapController::searchUtwente("cn=$utwente_username");

            // See if user is active
            $active = true;
            if (count($remoteusers) < 1) {
                $msg = "Not found: $utwente_username (".$user->name.')';
                $this->info($msg);
                $unlinked[] = $msg;
                $active = false;
            } elseif (! $remoteusers[0]->active) {
                $msg = "Inactive: $utwente_username (".$user->name.')';
                $this->info($msg);
                $unlinked[] = $msg;
                $active = false;
            }

            if ($active && property_exists($remoteusers[0], 'department')) {
                // See if user studies CreaTe
                if (strpos($remoteusers[0]->department, 'CREA') > 0) {
                    $user->did_study_create = true;
                }
                // See if user studies ITech
                if (strpos($remoteusers[0]->department, 'ITECH') > 0) {
                    $user->did_study_itech = true;
                }
                $user->utwente_department = $remoteusers[0]->department;
            } else {
                $user->utwente_department = null;
            }
            $user->save();

            // Act
            if (! $active) {
                $user->utwente_username = null;
                $user->utwente_department = null;
                $user->save();
            }
        }

        Mail::queue((new UtwenteCleanup($unlinked))->onQueue('high'));

        $this->info('Done');
    }
}
