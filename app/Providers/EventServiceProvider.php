<?php

namespace App\Providers;

use App\Handlers\Events\AuthLoginEventHandler;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Override;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string,array<int,string>>
     */
    protected $listen = [
        Login::class => [
            AuthLoginEventHandler::class,
        ],
        SocialiteWasCalled::class => [
            'SocialiteProviders\\Saml2\\Saml2ExtendSocialite@handle',
        ],
    ];

    /**
     * Register any other events for your application.
     */
    #[Override]
    public function boot(): void
    {
        parent::boot();
    }
}
