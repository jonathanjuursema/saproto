<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\DmxFixtureController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\IsAlfredThereController;
use App\Http\Controllers\NarrowcastingController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OmNomController;
use App\Http\Controllers\QrAuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SmartXpScreenController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WallstreetController;
use App\Http\Controllers\WrappedController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['forcedomain'], 'as' => 'api::'], static function () {
    /* Routes related to the General APIs */
    Route::group(['middleware' => ['web']], static function () {
        Route::get('dmx_values', [DmxFixtureController::class, 'valueApi'])->name('dmx_values');
        Route::get('token', [ApiController::class, 'getToken'])->name('token');
        Route::get('scan/{event}', [TicketController::class, 'scanApi'])->name('scan');
        Route::get('news', [NewsController::class, 'apiIndex'])->name('news');
        Route::get('verify_iban', [BankController::class, 'verifyIban'])->name('verify_iban');
    });

    /* Routes related to the User API */
    Route::group(['prefix' => 'user', 'as' => 'user::'], static function () {
        Route::get('info', fn () => Auth::user())->name('info')->middleware('auth:api');
        Route::group(['middleware' => ['auth:api'], 'as' => 'qr::'], static function () {
            Route::get('qr_auth_approve/{code}', [QrAuthController::class, 'apiApprove'])->name('approve');
            Route::get('qr_auth_info/{code}', [QrAuthController::class, 'apiInfo'])->name('info');
            Route::get('token', [ApiController::class, 'getToken'])->name('token');
        });
        Route::group(['middleware' => ['web']], static function () {
            Route::get('gdpr_export', [ApiController::class, 'gdprExport'])->name('gdpr_export');
            Route::get('dev_export/{table}/{personal_key}', [ApiController::class, 'devExport'])->name('dev_export');
        });
    });

    Route::group(['prefix' => 'discord', 'as' => 'discord::'], function () {
        Route::get('redirect', [DiscordController::class, 'discordLinkRedirect'])->name('redirect')->middleware('auth');
        Route::get('linked', [DiscordController::class, 'discordLinkCallback'])->name('linked')->middleware('auth');
        Route::get('unlink', [DiscordController::class, 'discordUnlink'])->name('unlink')->middleware('auth');
        Route::get('verify/{userId}', [ApiController::class, 'discordVerifyMember'])->name('verify')->middleware('proboto');
    });
    /* Routes related to the Events API */
    Route::group(['prefix' => 'events', 'as' => 'events::', 'middleware' => ['web']], static function () {
        Route::get('upcoming/{limit?}', [EventController::class, 'apiUpcomingEvents'])->name('upcoming');
    });
    /* Routes related to the Photos API */
    Route::group(['prefix' => 'photos', 'as' => 'photos::'], static function () {
        Route::get('random_photo', [ApiController::class, 'randomPhoto'])->name('random_photo');
    });
    Route::group(['prefix' => 'screen', 'as' => 'screen::', 'middleware' => 'api'], static function () {
        Route::get('bus', [SmartXpScreenController::class, 'bus'])->name('bus');
        Route::get('timetable', [SmartXpScreenController::class, 'timetable'])->name('timetable');
        Route::get('timetable/protopeners', [SmartXpScreenController::class, 'protopenersTimetable'])->name('timetable::protopeners');
        Route::get('narrowcasting', [NarrowcastingController::class, 'indexApi'])->name('narrowcasting');
    });
    /* Routes related to the Protube API */
    Route::group(['prefix' => 'protube', 'as' => 'protube::', 'middleware' => ['web']], static function () {
        Route::get('played', [ApiController::class, 'protubePlayed'])->name('played');
        Route::get('userdetails', [ApiController::class, 'protubeUserDetails'])->name('userdetails');
    });
    /* Routes related to the Search API */
    Route::group(['prefix' => 'search', 'as' => 'search::', 'middleware' => ['web', 'auth', 'permission:board|omnomcom']], static function () {
        Route::get('user', [SearchController::class, 'getUserSearch'])->name('user');
        Route::get('committee', [SearchController::class, 'getCommitteeSearch'])->name('committee');
        Route::get('event', [SearchController::class, 'getEventSearch'])->name('event');
        Route::get('product', [SearchController::class, 'getProductSearch'])->name('product');
        Route::get('achievement', [SearchController::class, 'getAchievementSearch'])->name('achievement');
    });
    /* Routes related to OmNomCom */
    Route::group(['prefix' => 'omnomcom', 'as' => 'omnomcom::', 'middleware' => ['web']], static function () {
        Route::get('stock', [OmNomController::class, 'stock'])->name('stock');
    });
    Route::group(['prefix' => 'wallstreet', 'as' => 'wallstreet::', 'middleware' => ['web']], static function () {
        Route::get('active', WallstreetController::active(...))->name('active');
        Route::get('updated_prices/{id}', [WallstreetController::class, 'getUpdatedPricesJSON'])->name('updated_prices');
        Route::get('all_prices/{id}', [WallstreetController::class, 'getAllPrices'])->name('all_prices');
        Route::get('toggle_event', [WallstreetController::class, 'toggleEvent'])->name('toggle_event')->middleware('permission:tipcie');
    });
    /* Route related to the IsAlfredThere API */
    Route::get('isalfredthere', [IsAlfredThereController::class, 'getApi'])->name('isalfredthere');
    /* Routes related to the OmNomCom Wrapped API */
    Route::get('wrapped', [WrappedController::class, 'index'])->name('wrapped')->middleware('auth:api');
});
