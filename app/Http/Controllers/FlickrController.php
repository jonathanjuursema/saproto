<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;

use Redirect;

class FlickrController extends Controller
{
    /**
     * A dummy OAuth flow to generate a token for the Flickr API.
     */
    public function oauthTool(Request $request)
    {
        $flickr = \OAuth::consumer('Flickr');

        if (!$request->has('oauth_token') || !$request->has('oauth_verifier') || !$request->session()->has('flickr_token')) {

            $token = $flickr->requestRequestToken();

            $request->session()->put('flickr_token', $token);

            return Redirect::to($flickr->getAuthorizationUri(array(
                'oauth_token' => $token->getAccessToken(),
                'perms' => 'read'
            )));

        } else {

            $token = $flickr->requestAccessToken(
                $request->get('oauth_token'),
                $request->get('oauth_verifier'),
                $request->session()->get('flickr_token')->getAccessTokenSecret()
            );

            $xml = simplexml_load_string($flickr->request('flickr.test.login'));

            $flickr_user = (string)$xml->user->attributes()->id;
            $right_user = getenv('FLICKR_USER');
            if ($flickr_user != $right_user) {
                abort(404, "You authenticated as the wrong user. (Authenticated as $flickr_user but should authenticate as $right_user.)");
            }

            $request->session()->forget('flickr_token');

            abort(200, "Successfully authenticated. AccessToken = " . $token->getAccessToken() . ", AccessTokenSecret = " . $token->getAccessTokenSecret());

        }
    }
}
