<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

use function Sentry\captureException;

class ProTubeApiService
{
    /**
     * @var string API route prefix
     */
    private const API_PREFIX = '/api/laravel';

    private static function client(): PendingRequest
    {
        return Http::withToken(Config::string('protube.laravel_to_protube_secret'))
            ->withOptions(['verify' => (App::environment('production'))])
            ->baseUrl(Config::string('protube.server').self::API_PREFIX);
    }

    /**
     * Process the response and log if any errors occur.
     *
     * @return bool successfull response
     */
    private static function assertResponse(Response $response): bool
    {
        try {
            $response->throw();
        } catch (Exception $exception) {
            captureException($exception);

            return false;
        }

        return true;
    }

    /**
     * Skip the current song.
     *
     * @return bool skipped successfully
     */
    public static function skipSong(): bool
    {
        //when in production don't update the protube admin status
        if (! App::environment('production')) {
            return true;
        }

        $response = self::client()->post('/skipsong');
        if (! self::assertResponse($response)) {
            return false;
        }

        $json = $response->json();

        return $json['success'];
    }

    /**
     * Trigger ProTube to update the user admin status for the given user id.
     *
     * @return bool successfull response (if the user was found)
     */
    public static function updateAdmin(int $userID, bool $admin): bool
    {
        //when in production don't update the protube admin status
        if (! App::environment('production')) {
            return true;
        }

        $response = self::client()->post('/updateadmin', [
            'user_id' => $userID,
            'admin' => $admin,
        ]);

        if (! self::assertResponse($response)) {
            return false;
        }

        $json = $response->json();

        return $json['success'];
    }
}
