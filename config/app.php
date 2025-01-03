<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => 'S.A. Proto',

    /*
    |--------------------------------------------------------------------------
    | Misc
    |--------------------------------------------------------------------------
    |
    */

    'env' => env('APP_ENV', 'local'),
    'ssl' => env('SSL', true),
    'forcedomain' => env('FORCE_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    // This is handled in the Handler class. On production, a nice page is shown. On other environments (which should always be well protected!) the stacktrace is shown.
    'debug' => env('DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Europe/Amsterdam',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'SomeRandomString'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    //    /*
    //    |--------------------------------------------------------------------------
    //    | Autoloaded Service Providers
    //    |--------------------------------------------------------------------------
    //    |
    //    | The service providers listed here will be automatically loaded on the
    //    | request to your application. Feel free to add your own services to
    //    | this array to grant expanded functionality to your applications.
    //    |
    //    */
    //
    //    'providers' => [
    //
    //        /*
    //         * Laravel Framework Service Providers...
    //         */
    //        AuthServiceProvider::class,
    //        BroadcastServiceProvider::class,
    //        BusServiceProvider::class,
    //        CacheServiceProvider::class,
    //        ConsoleSupportServiceProvider::class,
    //        CookieServiceProvider::class,
    //        DatabaseServiceProvider::class,
    //        EncryptionServiceProvider::class,
    //        FilesystemServiceProvider::class,
    //        FoundationServiceProvider::class,
    //        HashServiceProvider::class,
    //        PaginationServiceProvider::class,
    //        PipelineServiceProvider::class,
    //        QueueServiceProvider::class,
    //        RedisServiceProvider::class,
    //        PasswordResetServiceProvider::class,
    //        SessionServiceProvider::class,
    //        TranslationServiceProvider::class,
    //        ValidationServiceProvider::class,
    //        ViewServiceProvider::class,
    //        NotificationServiceProvider::class,
    //        ConcurrencyServiceProvider::class,
    //
    //        /*
    //         * Application Service Providers...
    //         */
    //        AppServiceProvider::class,
    //        EventServiceProvider::class,
    //        RouteServiceProvider::class,
    //        \App\Providers\BroadcastServiceProvider::class,
    //
    //        /*
    //         * External Service Providers
    //         */
    //        MailServiceProvider::class,
    //        ReCaptchaServiceProvider::class,
    //        BarcodeServiceProvider::class,
    //        MarkdownServiceProvider::class,
    //        HashidsServiceProvider::class,
    //        ServiceProvider::class,
    //        Saml2ServiceProvider::class,
    //        Sentry\Laravel\ServiceProvider::class,
    //        SoloServiceProvider::class,
    //    ],
    //
    //    /*
    //    |--------------------------------------------------------------------------
    //    | Class Aliases
    //    |--------------------------------------------------------------------------
    //    |
    //    | This array of class aliases will be registered when this application
    //    | is started. However, feel free to register as many as you wish as
    //    | the aliases are "lazy" loaded so they don't hinder performance.
    //    |
    //    */
    //
    //    'aliases' => [
    //
    //        'App' => App::class,
    //        'Artisan' => Artisan::class,
    //        'Auth' => Auth::class,
    //        'Blade' => Blade::class,
    //        'Bus' => Bus::class,
    //        'Cache' => Cache::class,
    //        'Config' => Config::class,
    //        'Cookie' => Cookie::class,
    //        'Crypt' => Crypt::class,
    //        'DB' => DB::class,
    //        'Eloquent' => Model::class,
    //        'File' => File::class,
    //        'Gate' => Gate::class,
    //        'Hash' => Hash::class,
    //        'Input' => Request::class,
    //        'Lang' => Lang::class,
    //        'Log' => Log::class,
    //        'Mail' => Mail::class,
    //        'Notification' => Notification::class,
    //        'Password' => Password::class,
    //        'Queue' => Queue::class,
    //        'Redirect' => Redirect::class,
    //        'Redis' => Redis::class,
    //        'Request' => Request::class,
    //        'Response' => Response::class,
    //        'Route' => Route::class,
    //        'Schema' => Schema::class,
    //        'Session' => Session::class,
    //        'Storage' => Storage::class,
    //        'URL' => URL::class,
    //        'Validator' => Validator::class,
    //        'View' => View::class,
    //
    //        'Role' => Role::class,
    //        'Permission' => Permission::class,
    //
    //        'Image' => Image::class,
    //
    //        'PDF' => Html2Pdf::class,
    //
    //        'DNS1D' => DNS1DFacade::class,
    //        'DNS2D' => DNS2DFacade::class,
    //
    //        'Carbon' => Carbon::class,
    //
    //        'Markdown' => Markdown::class,
    //
    //        'ReCaptcha' => ReCaptcha::class,
    //
    //        'Mollie' => Mollie::class,
    //
    //        'Hashids' => Hashids::class,
    //
    //        'PwnedPasswords' => Facade::class,
    //
    //    ],

];
