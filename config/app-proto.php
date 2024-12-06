<?php

return [

    /*
     * Primary app location
     */

    'primary-domain' => env('PRIMARY_DOMAIN'),
    'app-url' => config('app.url'),
    'assets-domain' => env('REDIRECT_ASSETS_DOMAIN'),

    'fishcam-url' => env('FISHCAM_URL'),
    /*
     * Whitelist for debug environment
     */
    'debug-whitelist' => env('DEV_ALLOWED'),

    /*
     * Personal Proto key
     */
    'personal-proto-key' => env('PERSONAL_PROTO_KEY'),

    /*
     * Printer server settings
     */
    'printer-host' => env('PRINTER_HOST'),
    'printer-port' => env('PRINTER_PORT'),
    'printer-secret' => env('PRINTER_SECRET'),

    /*
     * Google API keys
     */
    'google-key-public' => env('GOOGLE_KEY_PUBLIC'),
    'google-key-private' => env('GOOGLE_KEY_PRIVATE'),

    /*
     * Spotify config
     */
    'spotify-clientkey' => env('SPOTIFY_CLIENT'),
    'spotify-secretkey' => env('SPOTIFY_SECRET'),
    'spotify-user' => env('SPOTIFY_USER'),
    'spotify-alltime-playlist' => env('SPOTIFY_ALLTIME_PLAYLIST'),
    'spotify-pastyears-playlist' => env('SPOTIFY_PASTYEARS_PLAYLIST'),
    'spotify-recent-playlist' => env('SPOTIFY_RECENT_PLAYLIST'),

    /*
     * Sentry config
     */
    'sentry-dsn' => env('SENTRY_PUBLIC_DSN'),

    /*
     * ProBoto config
     */
    'proboto-secret' => env('PROBOTO_SECRET'),

];
