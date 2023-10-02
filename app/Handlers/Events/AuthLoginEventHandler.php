<?php

namespace App\Handlers\Events;

use Illuminate\Auth\Events\Login;
use App\Models\Committee;
use App\Models\User;

class AuthLoginEventHandler
{
    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle($event)
    {
        /** @var User $user */
        $user = $event->user;
        $user->generateNewToken();

        // We will grant the user all roles to which they are entitled to!
        $committees = [
            ['committee' => Committee::where('slug', config('proto.rootcommittee'))->first(), 'role' => 'protube', 'nda' => true],
            ['committee' => Committee::find(config('proto.committee')['board']), 'role' => 'board', 'nda' => true],
            ['committee' => Committee::find(config('proto.committee')['omnomcom']), 'role' => 'omnomcom', 'nda' => true],
            ['committee' => Committee::find(config('proto.committee')['tipcie']), 'role' => 'tipcie', 'nda' => true],
            ['committee' => Committee::find(config('proto.committee')['drafters']), 'role' => 'drafters', 'nda' => false],
            ['committee' => Committee::find(config('proto.committee')['protography']), 'role' => 'protography', 'nda' => false],
        ];

        foreach ($committees as $committee) {
            if ($user->isInCommittee($committee['committee']) && (! $committee['nda'] or $user->signed_nda)) {
                if (! $user->hasRole($committee['role'])) {
                    $user->assignRole($committee['role']);
                }
            } else {
                if ($user->hasRole($committee['role'])) {
                    $user->removeRole($committee['role']);
                }
            }
        }
    }
}
