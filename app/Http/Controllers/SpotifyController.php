<?php

namespace App\Http\Controllers;

use App\Models\HashMapItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController extends Controller
{
    /**
     * A dummy OAuth flow to generate a token for the Spotify API.
     *
     * @return RedirectResponse
     */
    public function oauthTool(Request $request)
    {
        $session = new SpotifySession(
            Config::string('app-proto.spotify-clientkey'),
            Config::string('app-proto.spotify-secretkey'),
            route('spotify::oauth')
        );

        $api = new SpotifyWebAPI;

        if (! $request->has('code')) {
            $options = [
                'scope' => [
                    'playlist-modify-public',
                    'playlist-modify-private',
                ],
            ];

            return Redirect::to($session->getAuthorizeUrl($options));
        }

        $session->requestAccessToken($request->get('code'));
        $api->setAccessToken($session->getAccessToken());
        /** @phpstan-ignore-next-line */
        $spotify_user = $api->me()->id;
        $right_user = Config::string('app-proto.spotify-user');
        if ($spotify_user != $right_user) {
            abort(404, "You authenticated as the wrong user. (Authenticated as {$spotify_user} but should authenticate as {$right_user}.)");
        }

        self::setSession($session);
        self::setApi($api);
        \Illuminate\Support\Facades\Session::flash('flash_message', 'Successfully saved Spotify credentials.');

        return Redirect::route('homepage');
    }

    public static function setSession(SpotifySession $session): void
    {
        $dbSession = HashMapItem::query()->where('key', 'spotify')->where('subkey', 'session')->first();
        if ($dbSession == null) {
            $dbSession = HashMapItem::query()->create([
                'key' => 'spotify',
                'subkey' => 'session',
            ]);
        }

        $dbSession->value = serialize($session);
        $dbSession->save();
    }

    public static function setApi(SpotifyWebAPI $api): void
    {
        $dbApi = HashMapItem::query()->where('key', 'spotify')->where('subkey', 'api')->first();
        if ($dbApi == null) {
            $dbApi = HashMapItem::query()->create([
                'key' => 'spotify',
                'subkey' => 'api',
            ]);
        }

        $dbApi->value = serialize($api);
        $dbApi->save();
    }

    public static function getSession(): SpotifySession
    {
        return unserialize(
            HashMapItem::query()->where('key', 'spotify')
                ->where('subkey', 'session')
                ->first()->value
        );
    }

    public static function getApi(): SpotifyWebAPI
    {
        return unserialize(
            HashMapItem::query()->where('key', 'spotify')
                ->where('subkey', 'api')
                ->first()->value
        );
    }
}
