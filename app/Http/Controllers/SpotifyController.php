<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\HashMapItem;
use Redirect;
use Session;
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
            config('app-proto.spotify-clientkey'),
            config('app-proto.spotify-secretkey'),
            route('spotify::oauth')
        );

        $api = new SpotifyWebAPI();

        if (! $request->has('code')) {
            $options = [
                'scope' => [
                    'playlist-modify-public',
                    'playlist-modify-private',
                ],
            ];

            return Redirect::to($session->getAuthorizeUrl($options));
        } else {
            $session->requestAccessToken($request->get('code'));
            $api->setAccessToken($session->getAccessToken());

            /** @phpstan-ignore-next-line  */
            $spotify_user = $api->me()->id;
            $right_user = config('app-proto.spotify-user');
            if ($spotify_user != $right_user) {
                abort(404, "You authenticated as the wrong user. (Authenticated as $spotify_user but should authenticate as $right_user.)");
            }

            self::setSession($session);
            self::setApi($api);

            Session::flash('flash_message', 'Successfully saved Spotify credentials.');

            return Redirect::route('homepage');
        }
    }

    /** @param  SpotifySession  $session */
    public static function setSession($session)
    {
        $dbSession = HashMapItem::where('key', 'spotify')->where('subkey', 'session')->first();
        if ($dbSession == null) {
            $dbSession = HashMapItem::create([
                'key' => 'spotify',
                'subkey' => 'session',
            ]);
        }
        $dbSession->value = serialize($session);
        $dbSession->save();
    }

    /** @param  SpotifyWebAPI  $api */
    public static function setApi($api)
    {
        $dbApi = HashMapItem::where('key', 'spotify')->where('subkey', 'api')->first();
        if ($dbApi == null) {
            $dbApi = HashMapItem::create([
                'key' => 'spotify',
                'subkey' => 'api',
            ]);
        }
        $dbApi->value = serialize($api);
        $dbApi->save();
    }

    /** @return SpotifySession */
    public static function getSession()
    {
        return unserialize(
            HashMapItem::where('key', 'spotify')
                ->where('subkey', 'session')
                ->first()->value
        );
    }

    /** @return SpotifyWebAPI */
    public static function getApi()
    {
        return unserialize(
            HashMapItem::where('key', 'spotify')
                ->where('subkey', 'api')
                ->first()->value
        );
    }
}
