<?php

namespace App\Handlers\Events;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Session;

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
     * @return void
     */
    public function handle($event)
    {
        $remoteUser = $event->getSaml2User()->getAttributes();
        Session::flash('surfconext_sso_user', $remoteUser);
        Session::reflash();
    }
}
