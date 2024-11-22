<?php

namespace App\Handlers\Events;

use App\Models\Committee;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Config;

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
     */
    public function handle($event): void
    {
        /** @var User $user */
        $user = $event->user;
        $user->generateNewToken();

        // We will grant the user all roles to which they are entitled to!
        $committees = [
            ['committee' => Committee::query()->where('slug', Config::string('proto.rootcommittee'))->first(), 'role' => 'protube', 'nda' => true],
            ['committee' => Committee::query()->find(Config::integer('proto.committee.board')), 'role' => 'board', 'nda' => true],
            ['committee' => Committee::query()->find(Config::integer('proto.committee.omnomcom')), 'role' => 'omnomcom', 'nda' => true],
            ['committee' => Committee::query()->find(Config::integer('proto.committee.tipcie')), 'role' => 'tipcie', 'nda' => true],
            ['committee' => Committee::query()->find(Config::integer('proto.committee.drafters')), 'role' => 'drafters', 'nda' => false],
            ['committee' => Committee::query()->find(Config::integer('proto.committee.protography')), 'role' => 'protography', 'nda' => false],
        ];

        foreach ($committees as $committee) {
            if ($user->isInCommittee($committee['committee']) && (! $committee['nda'] || $user->signed_nda)) {
                if (! $user->hasRole($committee['role'])) {
                    $user->assignRole($committee['role']);
                }
            } elseif ($user->hasRole($committee['role'])) {
                $user->removeRole($committee['role']);
            }
        }
    }
}
