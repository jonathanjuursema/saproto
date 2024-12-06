<?php

namespace App\Console\Commands;

use App\Http\Controllers\LdapController;
use App\Mail\UtwenteCleanup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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
    public function handle(): void
    {
        $users = User::query()->whereNotNull('utwente_username')->select(['id', 'utwente_username', 'name'])->get();

        $userSns = implode('', $users->pluck('utwente_username')->map(fn ($username): string => '(cn='.strtolower($username).')')->toArray());
        $this->info('Checking '.$users->count().' UTwente accounts.');

        $unlinked = [];

        $remoteusers = LdapController::searchUtwente("(&(extensionattribute6=actief)(|{$userSns}))");
        if (property_exists($remoteusers, 'error')) {
            $this->error($remoteusers->error);

            return;
        }

        $remoteusers = collect($remoteusers->result)->pluck('cn');

        foreach ($users as $user) {

            if ($remoteusers->filter(fn ($item): bool => strtolower($item) === strtolower($user->utwente_username))->count() <= 0) {
                $msg = "Not found: {$user->utwente_username} (".$user->name.')';
                $this->info($msg);
                $unlinked[] = $msg;
                $user->utwente_username = null;
                $user->utwente_department = null;
                $user->save();
            }
        }

        Mail::queue((new UtwenteCleanup($unlinked))->onQueue('high'));

        $this->info('Done');
    }
}
