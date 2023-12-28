<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
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
        return Http::withToken(config('protube.secret'))
            ->withOptions(['verify' => (config('app.env') === 'production')])
            ->baseUrl(config('protube.server').self::API_PREFIX);
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
        } catch (\Exception $e) {
            captureException($e);

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
