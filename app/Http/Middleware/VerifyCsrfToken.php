<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /** @var array The URIs that should be excluded from CSRF verification. */
    protected $except = [
        'webhook/*',
        'saml2/*',
        'api/*',
        'image/*',
        'file/*',
    ];
}
