<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;
use Override;
use Spatie\Csp\Nonce\NonceGenerator;

class LaravelViteNonceGenerator implements NonceGenerator
{
    #[Override]
    public function generate(): string
    {
        return Vite::useCspNonce();
    }
}
