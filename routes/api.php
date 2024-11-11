<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['forcedomain'], 'as' => 'api::'], static function () {
    /* Routes related to the General APIs */
    Route::group(['middleware' => ['web']], static function () {
        Route::get('dmx_values', ['as' => 'dmx_values', 'uses' => 'DmxFixtureController@valueApi']);
        Route::get('token', ['as' => 'token', 'uses' => 'ApiController@getToken']);
        Route::get('scan/{event}', ['as' => 'scan', 'middleware' => ['auth'], 'uses' => 'TicketController@scanApi']);
        Route::get('news', ['as' => 'news', 'uses' => 'NewsController@apiIndex']);
        Route::get('verify_iban', ['as' => 'verify_iban', 'uses' => 'BankController@verifyIban']);
    });
    /* Routes related to the User API */
    Route::group(['prefix' => 'user', 'as' => 'user::'], static function () {
        Route::group(['middleware' => ['auth:api'], 'as' => 'qr::'], static function () {
            Route::get('qr_auth_approve/{code}', ['as' => 'approve', 'uses' => 'QrAuthController@apiApprove']);
            Route::get('qr_auth_info/{code}', ['as' => 'info', 'uses' => 'QrAuthController@apiInfo']);
            Route::get('token', ['as' => 'token', 'uses' => 'ApiController@getToken']);
        });
        Route::group(['middleware' => ['web']], static function () {
            Route::get('gdpr_export', ['as' => 'gdpr_export', 'middleware' => ['auth'], 'uses' => 'ApiController@gdprExport']);
            Route::get('dev_export/{table}/{personal_key}', ['as' => 'dev_export', 'uses' => 'ExportController@export']);
        });
    });

    Route::group(['prefix' => 'discord', 'as' => 'discord::'], function () {
        Route::get('redirect', ['as' => 'redirect', 'middleware' => ['auth'], 'uses' => 'DiscordController@discordLinkRedirect']);
        Route::get('linked', ['as' => 'linked', 'middleware' => ['auth'], 'uses' => 'DiscordController@discordLinkCallback']);
        Route::get('unlink', ['as' => 'unlink', 'middleware' => ['auth'], 'uses' => 'DiscordController@discordUnlink']);
        Route::get('verify/{userId}', ['as' => 'verify', 'middleware' => ['proboto'], 'uses' => 'ApiController@discordVerifyMember']);
    });
    /* Routes related to the Events API */
    Route::group(['prefix' => 'events', 'as' => 'events::', 'middleware' => ['web']], static function () {
        Route::get('upcoming/{limit?}', ['as' => 'upcoming', 'uses' => 'EventController@apiUpcomingEvents']);
    });
    /* Routes related to the Photos API */
    Route::group(['prefix' => 'photos', 'as' => 'photos::'], static function () {
        Route::get('random_photo', ['as' => 'randomPhoto', 'uses' => 'ApiController@randomPhoto']);
    });
    Route::group(['prefix' => 'screen', 'as' => 'screen::', 'middleware' => 'api'], static function () {
        Route::get('bus', ['as' => 'bus', 'uses' => 'SmartXpScreenController@bus']);
        Route::get('timetable', ['as' => 'timetable', 'uses' => 'SmartXpScreenController@timetable']);
        Route::get('timetable/protopeners', ['as' => 'timetable::protopeners', 'uses' => 'SmartXpScreenController@protopenersTimetable']);
        Route::get('narrowcasting', ['as' => 'narrowcasting', 'uses' => 'NarrowcastingController@indexApi']);
    });
    /* Routes related to the Protube API */
    Route::group(['prefix' => 'protube', 'as' => 'protube::', 'middleware' => ['web']], static function () {
        Route::get('played', ['as' => 'played', 'uses' => 'ApiController@protubePlayed']);
        Route::get('userdetails', ['middleware' => ['auth:api'], 'uses' => 'ApiController@protubeUserDetails']);
    });
    /* Routes related to the Search API */
    Route::group(['prefix' => 'search', 'as' => 'search::', 'middleware' => ['web', 'auth', 'permission:board|omnomcom']], static function () {
        Route::get('user', ['as' => 'user', 'uses' => 'SearchController@getUserSearch']);
        Route::get('committee', ['as' => 'committee', 'uses' => 'SearchController@getCommitteeSearch']);
        Route::get('event', ['as' => 'event', 'uses' => 'SearchController@getEventSearch']);
        Route::get('product', ['as' => 'product', 'uses' => 'SearchController@getProductSearch']);
        Route::get('achievement', ['as' => 'achievement', 'uses' => 'SearchController@getAchievementSearch']);
    });
    /* Routes related to OmNomCom */
    Route::group(['prefix' => 'omnomcom', 'as' => 'omnomcom::', 'middleware' => ['web']], static function () {
        Route::get('stock', ['as' => 'stock', 'uses' => 'OmNomController@stock']);
    });
    Route::group(['prefix' => 'wallstreet', 'as' => 'wallstreet::', 'middleware' => ['web']], static function () {
        Route::get('active', ['as' => 'active', 'uses' => 'WallstreetController@active']);
        Route::get('updated_prices/{id}', ['as' => 'updated_prices', 'uses' => 'WallstreetController@getUpdatedPricesJSON']);
        Route::get('all_prices/{id}', ['as' => 'all_prices', 'uses' => 'WallstreetController@getAllPrices']);
        Route::get('latest_events/{id}', ['as' => 'latest_events', 'uses' => 'WallstreetController@getLatestEvents']);
        Route::get('toggle_event', ['as' => 'toggle_event', 'uses' => 'WallstreetController@toggleEvent', 'middleware' => ['permission:tipcie']]);
    });
    /* Route related to the IsAlfredThere API */
    Route::get('isalfredthere', ['as' => 'isalfredthere', 'uses' => 'IsAlfredThereController@getApi']);
    /* Routes related to the OmNomCom Wrapped API */
    Route::get('wrapped')->middleware('auth:api')->uses('WrappedController@index')->name('wrapped');
});
