<?php

namespace Proto\Handlers\Events;

use Proto\Models\Committee;
use Proto\Models\Role;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  Events $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        $user->generateNewToken();

        // We will grant the user all roles to which he is entitled!
        $rootcommittee = Committee::where('slug', config('proto.rootcommittee'))->first();
        $boardcommittee = Committee::find(config('proto.committee')['board']);
        $omnomcom = Committee::find(config('proto.committee')['omnomcom']);
        $tipcie = Committee::find(config('proto.committee')['tipcie']);
        $drafters = Committee::find(config('proto.committee')['drafters']);

        if ($user->isInCommittee($rootcommittee) && $user->signed_nda) {
            if (!$user->hasRole('protube')) {
                $user->attachRole(Role::where('name', '=', 'protube')->first());
            }
        } else {
            if ($user->hasRole('protube')) {
                $user->detachRole(Role::where('name', '=', 'protube')->first());
            }
        }

        if ($user->isInCommittee($boardcommittee) && $user->signed_nda) {
            if (!$user->hasRole('board')) {
                $user->attachRole(Role::where('name', '=', 'board')->first());
            }
        } else {
            if ($user->hasRole('board')) {
                $user->detachRole(Role::where('name', '=', 'board')->first());
            }
        }

        if ($user->isInCommittee($omnomcom) && $user->signed_nda) {
            if (!$user->hasRole('omnomcom')) {
                $user->attachRole(Role::where('name', '=', 'omnomcom')->first());
            }
        } else {
            if ($user->hasRole('omnomcom')) {
                $user->detachRole(Role::where('name', '=', 'omnomcom')->first());
            }
        }

        if ($user->isInCommittee($tipcie) && $user->signed_nda) {
            if (!$user->hasRole('tipcie')) {
                $user->attachRole(Role::where('name', '=', 'tipcie')->first());
            }
        } else {
            if ($user->hasRole('tipcie')) {
                $user->detachRole(Role::where('name', '=', 'tipcie')->first());
            }
        }

        if ($user->isInCommittee($drafters)) {
            if (!$user->hasRole('drafters')) {
                $user->attachRole(Role::where('name', '=', 'drafters')->first());
            }
        } else {
            if ($user->hasRole('drafters')) {
                $user->detachRole(Role::where('name', '=', 'drafters')->first());
            }
        }
    }
}
