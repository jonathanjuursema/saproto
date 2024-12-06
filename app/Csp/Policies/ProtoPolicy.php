<?php

namespace App\Csp\Policies;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Spatie\Csp\Directive;
use Spatie\Csp\Exceptions\InvalidDirective;
use Spatie\Csp\Exceptions\InvalidValueSet;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Policy;
use Symfony\Component\HttpFoundation\Response;

use function Sentry\captureException;

class ProtoPolicy extends Policy
{
    public function shouldBeApplied(Request $request, Response $response): bool
    {
        // Don't apply csp in debug mode to enable the whoops (standard laravel) error page to be displayed correctly.
        // see https://github.com/spatie/laravel-csp?tab=readme-ov-file#using-whoops
        if (Config::boolean('app.debug') && ($response->isClientError() || $response->isServerError())) {
            return false;
        }

        return parent::shouldBeApplied($request, $response);
    }

    public function configure(): void
    {
        try {
            $this
                ->addDirective(Directive::BASE, Keyword::SELF)
                ->addDirective(Directive::DEFAULT, Keyword::SELF)
                ->addDirective(Directive::FORM_ACTION, [
                    Keyword::SELF,
                    'https://www.mollie.com/checkout/',
                    'https://wrapped.omnomcom.nl',
                    ...Config::array('proto.domains.protube'),
                    ...(App::environment('production') ? [] : ['http://localhost:*']),
                ])
                ->addDirective(Directive::OBJECT, Keyword::NONE)
                ->addDirective(Directive::SCRIPT, [
                    Keyword::SELF,
                    'https://discordapp.com/api/guilds/600338792766767289/widget.json',
                    'https://cdn.jsdelivr.net/codemirror.spell-checker/latest/en_US.aff',
                    'https://cdn.jsdelivr.net/codemirror.spell-checker/latest/en_US.dic',
                    'https://www.youtube.com/iframe_api',
                    'https://s.ytimg.com',
                    'https://www.google.com/recaptcha/api.js',
                    'blob:',
                    ...(App::environment('production') ? [] : ['http://localhost:*']),
                ])
                ->addNonceForDirective(Directive::SCRIPT)
                ->addDirective(Directive::STYLE, [
                    Keyword::SELF,
                    Keyword::UNSAFE_INLINE,
                    'https://fonts.googleapis.com/css2',
                    ...(App::environment('production') ? [] : ['http://localhost:*']),
                ])
                ->addDirective(Directive::IMG, [
                    Keyword::SELF,
                    '*',
                    'data:',
                ])
                ->addDirective(Directive::MEDIA, [
                    Keyword::SELF,
                    '*',
                    'https://static.saproto.com',
                ])
                ->addDirective(Directive::FRAME, [
                    Keyword::SELF,
                    'https://www.youtube.com/embed/',
                    'https://www.google.com/recaptcha/',
                    'https://recaptcha.google.com/recaptcha/',
                ])
                ->addDirective(Directive::FONT, [
                    Keyword::SELF,
                    'data:',
                    'https://fonts.gstatic.com',
                    ...(App::environment('production') ? [] : ['http://localhost:*']),
                ])
                ->addDirective(Directive::CONNECT, [
                    Keyword::SELF,
                    'https://proto.utwente.nl',
                    'https://www.proto.utwente.nl',
                    'https://www.staging.proto.utwente.nl',
                    'https://static.saproto.com',
                    'https://metis.proto.utwente.nl:3001',
                    'wss://metis.proto.utwente.nl:3001',
                    'ws://localhost:3000',
                    'https://discordapp.com/api/guilds/600338792766767289/widget.json',
                    'https://cdn.jsdelivr.net/codemirror.spell-checker/latest/en_US.aff',
                    'https://cdn.jsdelivr.net/codemirror.spell-checker/latest/en_US.dic',
                    'https://cdn.jsdelivr.net/npm/chart.js',
                    'https://api.fontawesome.com/',
                    ...(App::environment('production') ? [] : ['ws://localhost:*']),
                ]);
        } catch (InvalidValueSet|InvalidDirective $e) {
            captureException($e);
        }
    }
}
