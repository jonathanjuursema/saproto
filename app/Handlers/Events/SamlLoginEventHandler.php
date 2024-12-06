<?php

namespace App\Handlers\Events;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Illuminate\Support\Facades\Session;

class SamlLoginEventHandler
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
     * @param  Saml2LoginEvent  $event
     */
    public function handle($event): void
    {
        $remoteUser = $event->getSaml2User()->getAttributes();
        Session::flash('surfconext_sso_user', $remoteUser);
        Session::reflash();
    }
}
