<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class LdapController extends Controller
{
    public static function searchUtwente(string $query): object
    {
        $cacheKey = md5($query);

        if (Cache::has($cacheKey)) {
            return (object) [
                'result' => Cache::get($cacheKey),
            ];
        }

        $client = new Client;
        try {
            // Make a POST request to the LDAP Proxy
            $response = $client->post(Config::string('ldap.proxy.utwente.post_url'), [
                'form_params' => [
                    'key' => Config::string('ldap.proxy.utwente.key'),
                    'query' => $query,
                ],
            ]);

            // Get the response body
            $body = $response->getBody();
            $content = json_decode($body->getContents(), true);
            Cache::put($cacheKey, $content, 3600 * 60);

            // Return the response content
            return (object) [
                'result' => $content,
            ];
        } catch (Exception|GuzzleException $e) {
            // Handle exceptions
            return (object) [
                'error' => $e->getMessage(),
            ];
        }
    }
}
