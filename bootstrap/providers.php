<?php

use Aacotroneo\Saml2\Saml2ServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\SoloServiceProvider;
use Biscolab\ReCaptcha\ReCaptchaServiceProvider;
use GrahamCampbell\Markdown\MarkdownServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Milon\Barcode\BarcodeServiceProvider;
use nickurt\PwnedPasswords\ServiceProvider;
use Vinkla\Hashids\HashidsServiceProvider;

return [
    AppServiceProvider::class,
    MailServiceProvider::class,
    ReCaptchaServiceProvider::class,
    BarcodeServiceProvider::class,
    MarkdownServiceProvider::class,
    HashidsServiceProvider::class,
    ServiceProvider::class,
    Saml2ServiceProvider::class,
    Sentry\Laravel\ServiceProvider::class,
    SoloServiceProvider::class,
];
