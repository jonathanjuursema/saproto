<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Override;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        HttpExceptionInterface::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        HttpExceptionInterface::class,
        ValidationException::class,
        AuthorizationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     *
     * @throws Throwable
     */
    #[Override]
    public function report(Throwable $e): void
    {
        if ($this->shouldReport($e) && app()->bound('sentry') && App::environment('production')) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @return SymfonyResponse
     *
     * @throws Throwable
     */
    #[Override]
    public function render($request, Throwable $e)
    {
        if ($e instanceof UnauthorizedException) {
            return response()->view('errors.403');
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  Request  $request
     * @return JsonResponse|RedirectResponse
     */
    #[Override]
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return Redirect::guest('login');
    }

    /**
     * Render the given HttpException.
     *
     * @return SymfonyResponse
     *
     * @throws Throwable
     */
    #[Override]
    protected function renderHttpException(HttpExceptionInterface $e)
    {
        if (! view()->exists("errors.{$e->getStatusCode()}")) {

            $maySeeError = App::environment('local') || (Auth::check() && Auth::user()->can('finadmin'));

            return response()->view('errors.default', ['exception' => $e, 'hide_message' => ! $maySeeError], 500, $e->getHeaders());
        }

        return parent::renderHttpException($e);
    }
}
