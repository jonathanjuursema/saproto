<?php

Route::group(['middleware' => ['forcedomain'], 'as' => 'api::'], function () {
    /* Routes related to the General APIs */
    Route::group(['middleware' => ['web']], function () {
        Route::get('dmx_values', ['as' => 'dmx_values', 'uses' => 'DmxController@valueApi']);
        Route::get('token', ['as' => 'token', 'uses' => 'ApiController@getToken']);
        Route::get('scan/{event}', ['as' => 'scan', 'middleware' => ['auth'], 'uses' => 'TicketController@scanApi']);
        Route::get('news', ['as' => 'news', 'uses' => 'NewsController@apiIndex']);
        Route::get('verify_iban', ['as' => 'verify_iban', 'uses' => 'BankController@verifyIban']);
    });

    /* Routes related to the User API */
    Route::group(['prefix' => 'user', 'as' => 'user::'], function () {
        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('info', ['uses' => 'UserApiController@getUser']);
            Route::get('profile_picture', ['uses' => 'UserApiController@getUserProfilePicture']);
            Route::get('address', ['uses' => 'UserApiController@getAddress']);
            Route::get('committees', ['uses' => 'UserApiController@getCommittees']);
            Route::get('societies', ['uses' => 'UserApiController@getSocieties']);
            Route::get('achievements', ['uses' => 'UserApiController@getAchievements']);
            Route::get('qr_auth_approve/{code}', ['uses' => 'QrAuthController@apiApprove']);
            Route::get('qr_auth_info/{code}', ['uses' => 'QrAuthController@apiInfo']);
            Route::get('token', ['as' => 'token', 'uses' => 'ApiController@getToken']);
            Route::group(['prefix' => 'orders'], function () {
                Route::get('', ['uses' => 'UserApiController@getPurchases']);
                Route::get('total_month', ['uses' => 'UserApiController@getPurchasesMonth']);
                Route::get('next_withdrawal', ['uses' => 'UserApiController@getNextWithdrawal']);
            });
        });
        Route::group(['middleware' => ['web']], function () {
            Route::get('gdpr_export', ['as' => 'gdpr_export', 'middleware' => ['auth'], 'uses' => 'ApiController@gdprExport']);
            Route::get('dev_export/{table}/{personal_key}', ['as' => 'dev_export', 'uses' => 'ExportController@export']);
        });
    });

    /* Routes related to the Events API */
    Route::group(['prefix' => 'events', 'as' => 'events::'], function () {
        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('upcoming/for_user/{limit?}', ['as' => 'list_for_user', 'uses' => 'EventController@apiUpcomingEvents']);
            Route::get('signup/{id}', ['middleware' => ['member'], 'uses' => 'ParticipationController@create']);
            Route::get('signout/{participation_id}', ['middleware' => ['member'], 'uses' => 'ParticipationController@destroy']);
        });
        Route::group(['middleware' => ['web']], function () {
            Route::get('upcoming/{limit?}', ['as' => 'upcoming', 'uses' => 'EventController@apiUpcomingEvents']);
        });
    });

    /* Routes related to the Photos API */
    Route::group(['prefix' => 'photos', 'as' => 'photos::'], function () {
        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('photos_api', ['as' => 'albums', 'uses' => 'PhotoController@apiIndex']);
            Route::get('photos_api/{id?}', ['as' => 'albumList', 'uses' => 'PhotoController@apiShow']);
        });
        Route::get('random_photo', ['as' => 'randomPhoto', 'uses' => 'ApiController@randomPhoto']);
        Route::get('random_old_photo', ['as' => 'randomOldPhoto', 'uses' => 'ApiController@randomOldPhoto']);
        Route::group(['middleware' => ['web']], function () {
            Route::get('photos', ['as' => 'albums', 'uses' => 'PhotoController@apiIndex']);
            Route::get('photos/{id?}/', ['as' => 'albumList', 'uses' => 'PhotoController@apiShow']);
            Route::get('photos/get_photo/{id}', ['as' => 'getPhoto', 'uses' => 'PhotoController@getPhoto']);
            Route::get('photos/get_next_photo/{id}', ['as' => 'getNextPhoto', 'uses' => 'PhotoController@getNextPhoto']);
            Route::get('photos/get_previous_photo/{id}', ['as' => 'getPreviousPhoto', 'uses' => 'PhotoController@getPreviousPhoto']);

            Route::get('photos/get_next_liked_photo/{id}', ['as' => 'getNextLikedPhoto', 'uses' => 'LikedPicturesController@getNextPhoto']);
            Route::get('photos/get_previous_liked_photo/{id}', ['as' => 'getPreviousLikedPhoto', 'uses' => 'LikedPicturesController@getPreviousPhoto']);
        });
    });

    /* Routes related to the Committees API */
    Route::group(['prefix' => 'committees', 'as' => 'committees::'], function () {
        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('', ['as' => 'index', 'uses' => 'CommitteeController@indexApi']);
        });
        Route::group(['middleware' => ['web']], function () {
            Route::get('unauthenticated', ['as' => 'index', 'uses' => 'CommitteeController@indexApi']);
        });
    });

    Route::group(['prefix' => 'screen', 'as' => 'screen::'], function () {
        Route::get('bus', ['as' => 'bus', 'uses' => 'SmartXpScreenController@bus']);
        Route::get('timetable', ['as' => 'timetable', 'uses' => 'SmartXpScreenController@timetable']);
        Route::get('timetable/protopeners', ['as' => 'timetable::protopeners', 'uses' => 'SmartXpScreenController@protopenersTimetable']);
        Route::get('narrowcasting', ['as' => 'narrowcasting', 'uses' => 'NarrowcastingController@indexApi']);
    });

    /* Routes related to the Protube API */
    Route::group(['prefix' => 'protube', 'as' => 'protube::', 'middleware' => ['web']], function () {
        Route::get('played', ['as' => 'played', 'uses' => 'ApiController@protubePlayed']);
        Route::get('userdetails', ['middleware' => ['auth:api'], 'uses' => 'ApiController@protubeUserDetails']);
    });

    /* Routes related to the Search API */
    Route::group(['prefix' => 'search', 'as' => 'search::', 'middleware' => ['web', 'auth', 'permission:board|omnomcom']], function () {
        Route::get('user', ['as' => 'user', 'uses' => 'SearchController@getUserSearch']);
        Route::get('committee', ['as' => 'committee', 'uses' => 'SearchController@getCommitteeSearch']);
        Route::get('event', ['as' => 'event', 'uses' => 'SearchController@getEventSearch']);
        Route::get('product', ['as' => 'product', 'uses' => 'SearchController@getProductSearch']);
        Route::get('achievement', ['as' => 'achievement', 'uses' => 'SearchController@getAchievementSearch']);
    });

    /* Routes related to OmNomCom */
    Route::group(['prefix' => 'omnomcom', 'as' => 'omnomcom::', 'middleware' => ['web']], function () {
        Route::get('stock', ['as' => 'stock', 'uses' => 'OmNomController@stock']);
    });

    Route::group(['prefix' => 'wallstreet', 'as' => 'wallstreet::', 'middleware' => ['web']], function () {
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
