<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

require 'minisites.php';

/* Pass view name to body class */
View::composer('*', function ($view) {
    View::share('viewName', $view->getName());
});

// TODO: change string based controller route definitions to standard PHP callable syntax.

Route::group(['middleware' => ['forcedomain']], function () {

    /* The main route for the frontpage. */
    Route::get('', ['as' => 'homepage', 'uses' => 'HomeController@show']);
    Route::get('developers', ['uses' => 'HomeController@developers']);
    Route::get('becomeamember', ['as' => 'becomeamember', 'uses' => 'UserDashboardController@becomeAMemberOf']);

    Route::get('fishcam', ['as' => 'fishcam', 'middleware' => ['member'], 'uses' => 'HomeController@fishcam']);

    /* Routes related to the header images. */
    Route::group(['prefix' => 'headerimage', 'middleware' => ['auth', 'permission:header-image'], 'as' => 'headerimage::'], function () {
        Route::get('', ['as' => 'index', 'uses' => 'HeaderImageController@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'HeaderImageController@create']);
        Route::post('add', ['as' => 'add', 'uses' => 'HeaderImageController@store']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'HeaderImageController@destroy']);
    });

    /* Routes for the search function. */
    Route::get('search', ['as' => 'search', 'uses' => 'SearchController@search']);
    Route::post('search', ['as' => 'search', 'uses' => 'SearchController@search']);
    Route::get('opensearch', ['as' => 'search::opensearch', 'uses' => 'SearchController@openSearch']);

    /* Routes for the UTwente address book. */
    Route::group(['prefix' => 'ldap', 'as' => 'ldap::', 'middleware' => ['utwente']], function () {
        Route::get('search', ['as' => 'search', 'uses' => 'SearchController@ldapSearch']);
        Route::post('search', ['as' => 'search', 'uses' => 'SearchController@ldapSearch']);
    });

    /* Routes related to authentication. */
    Route::group(['as' => 'login::'], function () {
        Route::get('login', ['as' => 'show', 'uses' => 'AuthController@getLogin']);
        Route::post('login', ['as' => 'post', 'middleware' => ['throttle:5,1'], 'uses' => 'AuthController@postLogin']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
        Route::get('logout/redirect', ['as' => 'logout::redirect', 'uses' => 'AuthController@getLogoutRedirect']);

        Route::get('password/reset/{token}', ['as' => 'resetpass::token', 'uses' => 'AuthController@getPasswordReset']);
        Route::post('password/reset', ['as' => 'resetpass::submit', 'middleware' => ['throttle:5,1'], 'uses' => 'AuthController@postPasswordReset']);

        Route::get('password/email', ['as' => 'resetpass', 'uses' => 'AuthController@getPasswordResetEmail']);
        Route::post('password/email', ['as' => 'resetpass::send', 'middleware' => ['throttle:5,1'], 'uses' => 'AuthController@postPasswordResetEmail']);

        Route::get('password/sync', ['as' => 'password::sync', 'middleware' => ['auth'], 'uses' => 'AuthController@getPasswordSync']);
        Route::post('password/sync', ['as' => 'password::sync', 'middleware' => ['throttle:5,1', 'auth'], 'uses' => 'AuthController@postPasswordSync']);

        Route::get('password/change', ['as' => 'password::change', 'middleware' => ['auth'], 'uses' => 'AuthController@getPasswordChange']);
        Route::post('password/change', ['as' => 'password::change', 'middleware' => ['throttle:5,1', 'auth'], 'uses' => 'AuthController@postPasswordChange']);

        Route::get('register', ['as' => 'register', 'uses' => 'AuthController@getRegister']);
        Route::post('register', ['as' => 'register', 'middleware' => ['throttle:5,1'], 'uses' => 'AuthController@postRegister']);
        Route::post('register/surfconext', ['as' => 'register::surfconext', 'middleware' => ['throttle:5,1'], 'uses' => 'AuthController@postRegisterSurfConext']);

        Route::get('surfconext', ['as' => 'edu', 'uses' => 'AuthController@startSurfConextAuth']);
        Route::get('surfconext/post', ['as' => 'edupost', 'uses' => 'AuthController@postSurfConextAuth']);

        Route::get('username', ['as' => 'requestusername', 'uses' => 'AuthController@requestUsername']);
        Route::post('username', ['as' => 'requestusername', 'middleware' => ['throttle:5,1'], 'uses' => 'AuthController@requestUsername']);
    });

    /* Routes related to user profiles. */
    Route::group(['prefix' => 'user', 'as' => 'user::', 'middleware' => ['auth']], function () {
        Route::post('delete', ['as' => 'delete', 'uses' => 'AuthController@deleteUser']);
        Route::post('password', ['as' => 'changepassword', 'uses' => 'AuthController@updatePassword']);

        Route::get('personal_key', ['as' => 'personal_key::generate', 'uses' => 'UserDashboardController@generateKey']);

        /* Routes related to members. */
        Route::group(['prefix' => '{id}/member', 'as' => 'member::', 'middleware' => ['auth', 'permission:registermembers']], function () {
            Route::get('impersonate', ['as' => 'impersonate', 'middleware' => ['auth', 'permission:board'], 'uses' => 'UserAdminController@impersonate']);

            Route::post('add', ['as' => 'add', 'uses' => 'UserAdminController@addMembership']);
            Route::post('remove', ['as' => 'remove', 'uses' => 'UserAdminController@endMembership']);
            Route::post('end_in_september', ['as' => 'endinseptember', 'uses' => 'UserAdminController@EndMembershipInSeptember']);
            Route::post('remove_end', ['as' => 'removeend', 'uses' => 'UserAdminController@removeMembershipEnd']);
            Route::post('settype', ['as' => 'settype', 'uses' => 'UserAdminController@setMembershipType']);

            /* Routes related to the custom omnomcom sound. */
            Route::group(['prefix' => 'omnomcomsound', 'as' => 'omnomcomsound::', 'middleware' => ['auth', 'permission:board']], function () {
                Route::post('update', ['as' => 'update', 'uses' => 'UserAdminController@uploadOmnomcomSound']);
                Route::get('delete', ['as' => 'delete', 'uses' => 'UserAdminController@deleteOmnomcomSound']);
            });
        });

        Route::group(['prefix' => 'memberprofile', 'as' => 'memberprofile::', 'middleware' => ['auth']], function () {
            Route::get('complete', ['as' => 'complete', 'uses' => 'UserDashboardController@getCompleteProfile']);
            Route::post('complete', ['as' => 'complete', 'uses' => 'UserDashboardController@postCompleteProfile']);
            Route::get('clear', ['as' => 'clear', 'uses' => 'UserDashboardController@getClearProfile']);
            Route::post('clear', ['as' => 'clear', 'uses' => 'UserDashboardController@postClearProfile']);
        });

        Route::group(['prefix' => 'admin', 'as' => 'admin::', 'middleware' => ['auth', 'permission:board']], function () {
            Route::get('', ['as' => 'list', 'uses' => 'UserAdminController@index']);
            Route::get('{id}', ['as' => 'details', 'uses' => 'UserAdminController@details']);
            Route::post('{id}', ['as' => 'update', 'uses' => 'UserAdminController@update']);

            Route::get('studied_create/{id}', ['as' => 'toggle_studied_create', 'uses' => 'UserAdminController@toggleStudiedCreate']);
            Route::get('studied_itech/{id}', ['as' => 'toggle_studied_itech', 'uses' => 'UserAdminController@toggleStudiedITech']);
            Route::get('nda/{id}', ['as' => 'toggle_nda', 'middleware' => ['permission:board'], 'uses' => 'UserAdminController@toggleNda']);
            Route::get('unblock_omnomcom/{id}', ['as' => 'unblock_omnomcom', 'uses' => 'UserAdminController@unblockOmnomcom']);
        });

        Route::group(['prefix' => 'registrationhelper', 'as' => 'registrationhelper::', 'middleware' => ['auth' => 'permission:registermembers']], function () {
            Route::get('', ['as' => 'list', 'uses' => 'RegistrationHelperController@index']);
            Route::get('{id}', ['as' => 'details', 'uses' => 'RegistrationHelperController@details']);
        });

        Route::get('quit_impersonating', ['as' => 'quitimpersonating', 'uses' => 'UserAdminController@quitImpersonating']);

        Route::post('change_email', ['as' => 'changemail', 'middleware' => ['throttle:3,1'], 'uses' => 'UserDashboardController@updateMail']);

        Route::get('dashboard/{page?}', ['as' => 'dashboard', 'uses' => 'UserDashboardController@show']);
        Route::post('dashboard', ['as' => 'dashboard', 'uses' => 'UserDashboardController@update']);

        Route::get('{id?}', ['as' => 'profile', 'middleware' => ['member'], 'uses' => 'UserProfileController@show']);

        /* Routes related to addresses. */
        Route::group(['prefix' => 'address', 'as' => 'address::'], function () {
            Route::get('add', ['as' => 'add', 'uses' => 'AddressController@add']);
            Route::post('add', ['as' => 'add', 'uses' => 'AddressController@store']);
            Route::get('delete', ['as' => 'delete', 'uses' => 'AddressController@destroy']);
            Route::get('edit', ['as' => 'edit', 'uses' => 'AddressController@edit']);
            Route::post('edit', ['as' => 'edit', 'uses' => 'AddressController@update']);
            Route::get('togglehidden', ['as' => 'togglehidden', 'uses' => 'AddressController@toggleHidden']);
        });

        /* Routes related to diet. */
        Route::group(['prefix' => 'diet', 'as' => 'diet::'], function () {
            Route::post('edit', ['as' => 'edit', 'uses' => 'UserDashboardController@editDiet']);
        });

        /* Routes related to bank accounts. */
        Route::group(['prefix' => 'bank', 'as' => 'bank::'], function () {
            Route::get('add', ['as' => 'add', 'uses' => 'BankController@add']);
            Route::post('add', ['as' => 'add', 'uses' => 'BankController@store']);
            Route::post('delete', ['as' => 'delete', 'uses' => 'BankController@destroy']);
            Route::get('edit', ['as' => 'edit', 'uses' => 'BankController@edit']);
            Route::post('edit', ['as' => 'edit', 'uses' => 'BankController@update']);
        });

        /* Routes related to RFID cards. */
        Route::group(['prefix' => 'rfidcard/{id}', 'as' => 'rfid::'], function () {
            Route::get('delete', ['as' => 'delete', 'uses' => 'RfidCardController@destroy']);
            Route::get('edit', ['as' => 'edit', 'uses' => 'RfidCardController@edit']);
            Route::post('edit', ['as' => 'edit', 'uses' => 'RfidCardController@update']);
        });

        /* Routes related to profile pictures. */
        Route::group(['prefix' => 'profilepic', 'as' => 'pic::'], function () {
            Route::post('update', ['as' => 'update', 'uses' => 'ProfilePictureController@update']);
            Route::get('delete', ['as' => 'delete', 'uses' => 'ProfilePictureController@destroy']);
        });

        /* Routes related to UT accounts. */
        Route::group(['prefix' => 'edu', 'as' => 'edu::'], function () {
            Route::get('delete', ['as' => 'delete', 'uses' => 'SurfConextController@destroy']);
            Route::get('add', ['as' => 'add', 'uses' => 'SurfConextController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'SurfConextController@store']);
        });

        /* Routes related to 2FA. */
        Route::group(['prefix' => '2fa', 'as' => '2fa::'], function () {
            Route::post('add', ['as' => 'add', 'uses' => 'TFAController@create']);
            Route::post('delete', ['as' => 'delete', 'uses' => 'TFAController@destroy']);
            Route::post('delete/{id}', ['as' => 'admindelete', 'middleware' => ['permission:board'], 'uses' => 'TFAController@adminDestroy']);
        });
    });

    /* Routes related to the Membership Forms */
    Route::group(['prefix' => 'memberform', 'as' => 'memberform::', 'middleware' => ['auth']], function () {
        Route::get('sign', ['as' => 'sign', 'uses' => 'UserDashboardController@getMemberForm']);
        Route::post('sign', ['as' => 'sign', 'uses' => 'UserDashboardController@postMemberForm']);
        Route::group(['prefix' => 'download', 'as' => 'download::'], function () {
            Route::get('new/{id}', ['as' => 'new', 'uses' => 'UserAdminController@getNewMemberForm']);
            Route::get('signed/{id}', ['as' => 'signed', 'uses' => 'UserAdminController@getSignedMemberForm']);
        });
        Route::post('print/{id}', ['as' => 'print', 'middleware' => ['permission:board'], 'uses' => 'UserAdminController@printMemberForm']);
        Route::post('delete/{id}', ['as' => 'delete', 'middleware' => ['permission:board'], 'uses' => 'UserAdminController@destroyMemberForm']);
    });

    /* Routes related to committees. */
    Route::group(['prefix' => 'committee', 'as' => 'committee::'], function () {
        Route::group(['prefix' => 'membership', 'as' => 'membership::', 'middleware' => ['auth', 'permission:board']], function () {
            Route::post('add', ['as' => 'add', 'uses' => 'CommitteeController@addMembership']);
            Route::get('{id}/delete', ['as' => 'delete', 'uses' => 'CommitteeController@deleteMembership']);
            Route::get('{id}', ['as' => 'edit', 'uses' => 'CommitteeController@editMembershipForm']);
            Route::post('{id}', ['as' => 'edit', 'uses' => 'CommitteeController@editMembership']);
            Route::get('end/{committee}/{edition}', ['as' => 'endedition', 'uses' => 'CommitteeController@endEdition']);
        });

        Route::get('list', ['as' => 'list', 'uses' => 'CommitteeController@overview']);

        Route::get('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CommitteeController@add']);
        Route::post('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CommitteeController@store']);

        Route::get('{id}', ['as' => 'show', 'uses' => 'CommitteeController@show']);

        Route::get('{id}/send_anonymous_email', ['as' => 'anonymousmail', 'middleware' => ['auth', 'member'], 'uses' => 'CommitteeController@showAnonMailForm']);
        Route::post('{id}/send_anonymous_email', ['as' => 'anonymousmail', 'middleware' => ['auth', 'member'], 'uses' => 'CommitteeController@postAnonMailForm']);

        Route::get('{id}/edit', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CommitteeController@edit']);
        Route::post('{id}/edit', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CommitteeController@update']);

        Route::post('{id}/image', ['as' => 'image', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CommitteeController@image']);

        Route::get('{slug}/toggle_helper_reminder', ['as' => 'toggle_helper_reminder', 'middleware' => ['auth'], 'uses' => 'CommitteeController@toggleHelperReminder']);
    });

    /* Routes related to societies. */
    Route::group(['prefix' => 'society', 'as' => 'society::'], function () {
        Route::get('list', ['as' => 'list', 'uses' => 'CommitteeController@overview'])->defaults('showSociety', true);
        Route::get('{id}', ['as' => 'show', 'uses' => 'CommitteeController@show']);
    });

    /* Routes related to narrowcasting. */
    Route::group(['prefix' => 'narrowcasting', 'as' => 'narrowcasting::'], function () {
        Route::get('', ['as' => 'display', 'uses' => 'NarrowcastingController@show']);
        Route::get('list', ['as' => 'list', 'middleware' => ['auth', 'permission:board'], 'uses' => 'NarrowcastingController@index']);
        Route::get('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'NarrowcastingController@create']);
        Route::post('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'NarrowcastingController@store']);
        Route::get('edit/{id}', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'NarrowcastingController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'NarrowcastingController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'middleware' => ['auth', 'permission:board'], 'uses' => 'NarrowcastingController@destroy']);
        Route::get('clear', ['as' => 'clear', 'middleware' => ['auth', 'permission:board'], 'uses' => 'NarrowcastingController@clear']);
    });

    /* Routes related to companies. */
    Route::group(['prefix' => 'companies', 'as' => 'companies::'], function () {
        Route::get('list', ['as' => 'admin', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@adminIndex']);
        Route::get('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@create']);
        Route::post('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@store']);
        Route::get('edit/{id}', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@destroy']);

        Route::get('up/{id}', ['as' => 'orderUp', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@orderUp']);
        Route::get('down/{id}', ['as' => 'orderDown', 'middleware' => ['auth', 'permission:board'], 'uses' => 'CompanyController@orderDown']);

        Route::get('', ['as' => 'index', 'uses' => 'CompanyController@index']);
        Route::get('{id}', ['as' => 'show', 'uses' => 'CompanyController@show']);
    });

    /* Routes related to membercard. */
    Route::group(['prefix' => 'membercard', 'as' => 'membercard::'], function () {
        Route::get('', ['as' => 'index', 'uses' => 'CompanyController@indexmembercard']);

        Route::post('print', ['as' => 'print', 'middleware' => ['auth', 'permission:board'], 'uses' => 'MemberCardController@startPrint']);
        Route::get('download/{id}', ['as' => 'download', 'uses' => 'MemberCardController@download']);

        Route::get('{id}', ['as' => 'show', 'uses' => 'CompanyController@showmembercard']);
    });

    /* Routes related to joboffers. */
    Route::group(['prefix' => 'joboffers', 'as' => 'joboffers::'], function () {
        Route::get('', ['as' => 'index', 'uses' => 'JobofferController@index']);
        Route::get('list', ['as' => 'admin', 'middleware' => ['auth', 'permission:board'], 'uses' => 'JobofferController@adminIndex']);

        Route::get('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'JobofferController@create']);
        Route::post('add', ['as' => 'add', 'middleware' => ['auth', 'permission:board'], 'uses' => 'JobofferController@store']);

        Route::get('edit/{id}', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'JobofferController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'middleware' => ['auth', 'permission:board'], 'uses' => 'JobofferController@update']);

        Route::get('delete/{id}', ['as' => 'delete', 'middleware' => ['auth', 'permission:board'], 'uses' => 'JobofferController@destroy']);

        Route::get('{id}', ['as' => 'show', 'uses' => 'JobofferController@show']);
    });

    /* Routes related to leaderboards. */
    Route::group(['prefix' => 'leaderboards', 'as' => 'leaderboards::', 'middleware' => ['auth', 'member']], function () {
        Route::get('', ['as' => 'index', 'uses' => 'LeaderboardController@index']);

        Route::get('list', ['as' => 'admin', 'uses' => 'LeaderboardController@adminIndex']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'LeaderboardController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'LeaderboardController@update']);

        Route::group(['middleware' => ['permission:board']], function () {
            Route::get('add', ['as' => 'add', 'uses' => 'LeaderboardController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'LeaderboardController@store']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'LeaderboardController@destroy']);
        });

        Route::group(['prefix' => 'entries', 'as' => 'entries::'], function () {
            Route::post('add', ['as' => 'add', 'uses' => 'LeaderboardEntryController@store']);
            Route::post('update', ['as' => 'update', 'uses' => 'LeaderboardEntryController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'LeaderboardEntryController@destroy']);
        });
    });

    /* Routes related to dinnerforms. */
    Route::group(['prefix' => 'dinnerform', 'as' => 'dinnerform::',  'middleware' => ['auth']], function () {
        Route::group(['middleware' => ['permission:tipcie']], function () {
            Route::get('add', ['as' => 'add', 'uses' => 'DinnerformController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'DinnerformController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'DinnerformController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'DinnerformController@update']);
            Route::get('close/{id}', ['as' => 'close', 'uses' => 'DinnerformController@close']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'DinnerformController@destroy']);
            Route::get('admin/{id}', ['as' => 'admin', 'uses' => 'DinnerformController@admin']);
            Route::get('process/{id}', ['as' => 'process', 'uses' => 'DinnerformController@process']);
        });
        Route::group(['prefix' => 'orderline', 'as' => 'orderline::'], function () {
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'DinnerformOrderlineController@delete']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'DinnerformOrderlineController@edit']);
            Route::post('add/{id}', ['as' => 'add', 'uses' => 'DinnerformOrderlineController@store']);
            Route::post('update/{id}', ['as' => 'update', 'middleware' => ['permission:tipcie'], 'uses' => 'DinnerformOrderlineController@update']);
        });
        Route::get('{id}', ['as' => 'show', 'uses' => 'DinnerformController@show']);
    });

    /* routes related to the wallstreet drink system */
    Route::group(['prefix' => 'wallstreet', 'as' => 'wallstreet::', 'middleware' => ['permission:tipcie']], function () {
        Route::get('', ['as' => 'admin', 'uses' => 'WallstreetController@admin']);
        Route::get('marquee', ['as' => 'marquee', 'uses' => 'WallstreetController@marquee']);
        Route::post('add', ['as' => 'add', 'uses' => 'WallstreetController@store']);
        Route::get('close/{id}', ['as' => 'close', 'uses' => 'WallstreetController@close']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'WallstreetController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'WallstreetController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'WallstreetController@destroy']);
        Route::get('statistics/{id}', ['as' => 'statistics', 'uses' => 'WallstreetController@statistics']);
        route::group(['prefix' => 'products', 'as' => 'products::'], function () {
            Route::post('add/{id}', ['as' => 'add', 'uses' => 'WallstreetController@addProducts']);
            Route::get('remove/{id}/{productId}', ['as' => 'remove', 'uses' => 'WallstreetController@removeProduct']);
        });
    });

    /*
     * Routes related to events.
     * Important: routes in this block always use event_id or a relevant other ID. activity_id is in principle never used.
     */
    Route::group(['prefix' => 'events', 'as' => 'event::'], function () {
        Route::group(['prefix' => 'financial', 'as' => 'financial::', 'middleware' => ['permission:finadmin']], function () {
            Route::get('', ['as' => 'list', 'uses' => 'EventController@finindex']);
            Route::post('close/{id}', ['as' => 'close', 'uses' => 'EventController@finclose']);
        });

        Route::group(['prefix' => 'categories', 'middleware' => ['permission:board'], 'as' => 'category::'], function () {
            Route::get('', ['as' => 'admin', 'uses' => 'EventController@categoryAdmin']);
            Route::post('add', ['as' => 'add', 'uses' => 'EventController@categoryStore']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'EventController@categoryEdit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'EventController@categoryUpdate']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'EventController@categoryDestroy']);
        });

        Route::get('', ['as' => 'list', 'uses' => 'EventController@index']);
        Route::get('add', ['as' => 'add', 'middleware' => ['permission:board'], 'uses' => 'EventController@create']);
        Route::post('add', ['as' => 'add', 'middleware' => ['permission:board'], 'uses' => 'EventController@store']);
        Route::get('edit/{id}', ['as' => 'edit', 'middleware' => ['permission:board'], 'uses' => 'EventController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'middleware' => ['permission:board'], 'uses' => 'EventController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'middleware' => ['permission:board'], 'uses' => 'EventController@destroy']);

        Route::post('set_reminder', ['as' => 'set_reminder', 'middleware' => ['auth'], 'uses' => 'EventController@setReminder']);
        Route::get('toggle_relevant_only', ['as' => 'toggle_relevant_only', 'middleware' => ['auth'], 'uses' => 'EventController@toggleRelevantOnly']);

        Route::post('album/{event}/link', ['as' => 'linkalbum', 'middleware' => ['permission:board'], 'uses' => 'EventController@linkAlbum']);
        Route::get('album/unlink/{album}', ['as' => 'unlinkalbum', 'middleware' => ['permission:board'], 'uses' => 'EventController@unlinkAlbum']);

        Route::get('admin/{id}', ['as' => 'admin', 'middleware' => ['auth'], 'uses' => 'EventController@admin']);
        Route::get('scan/{id}', ['as' => 'scan', 'middleware' => ['auth'], 'uses' => 'EventController@scan']);

        Route::get('checklist/{id}', ['as' => 'checklist', 'uses' => 'ActivityController@checklist']);

        Route::get('archive/{year}', ['as' => 'archive', 'uses' => 'EventController@archive']);

        // Related to presence
        Route::get('togglepresence/{id}', ['as' => 'togglepresence', 'middleware' => ['auth'], 'uses' => 'ParticipationController@togglePresence']);

        // Related to participation
        Route::get('participate/{id}', ['as' => 'addparticipation', 'middleware' => ['member'], 'uses' => 'ParticipationController@create']);
        Route::get('unparticipate/{participation_id}', ['as' => 'deleteparticipation', 'uses' => 'ParticipationController@destroy']);

        Route::post('participatefor/{id}', ['as' => 'addparticipationfor', 'middleware' => ['permission:board'], 'uses' => 'ParticipationController@createFor']);

        // Related to activities
        Route::post('signup/{id}', ['as' => 'addsignup', 'middleware' => ['permission:board'], 'uses' => 'ActivityController@store']);
        Route::get('signup/{id}/delete', ['as' => 'deletesignup', 'middleware' => ['permission:board'], 'uses' => 'ActivityController@destroy']);

        // Related to helping committees
        Route::post('addhelp/{id}', ['as' => 'addhelp', 'middleware' => ['permission:board'], 'uses' => 'ActivityController@addHelp']);
        Route::post('updatehelp/{id}', ['as' => 'updatehelp', 'middleware' => ['permission:board'], 'uses' => 'ActivityController@updateHelp']);
        Route::get('deletehelp/{id}', ['as' => 'deletehelp', 'middleware' => ['permission:board'], 'uses' => 'ActivityController@deleteHelp']);

        // Buy tickets for an event
        Route::post('buytickets/{id}', ['as' => 'buytickets', 'middleware' => ['auth'], 'uses' => 'TicketController@buyForEvent']);

        // Show event
        Route::get('{id}', ['as' => 'show', 'uses' => 'EventController@show']);

        Route::post('copy', ['as' => 'copy', 'uses' => 'EventController@copyEvent']);

        // Force login for event
        Route::get('{id}/login', ['as' => 'login', 'middleware' => ['auth'], 'uses' => 'EventController@forceLogin']);
    });

    /* Routes related to pages. */
    Route::group(['prefix' => 'page', 'as' => 'page::'], function () {
        Route::group(['middleware' => ['auth', 'permission:board']], function () {
            Route::get('', ['as' => 'list', 'uses' => 'PageController@index']);
            Route::get('add', ['as' => 'add', 'uses' => 'PageController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'PageController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'PageController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'PageController@update']);
            Route::post('edit/{id}/image', ['as' => 'image', 'uses' => 'PageController@featuredImage']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'PageController@destroy']);

            Route::group(['prefix' => '/edit/{id}/file', 'as' => 'file::'], function () {
                Route::post('add', ['as' => 'add', 'uses' => 'PageController@addFile']);
                Route::get('{file_id}/delete', ['as' => 'delete', 'uses' => 'PageController@deleteFile']);
            });
        });

        Route::get('{slug}', ['as' => 'show', 'uses' => 'PageController@show']);
    });

    /* Routes related to news. */
    Route::group(['prefix' => 'news', 'as' => 'news::'], function () {
        Route::group(['middleware' => ['auth', 'permission:board']], function () {
            Route::get('admin', ['as' => 'admin', 'uses' => 'NewsController@admin']);
            Route::get('add', ['as' => 'add', 'uses' => 'NewsController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'NewsController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'NewsController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'NewsController@update']);
            Route::post('edit/{id}/image', ['as' => 'image', 'uses' => 'NewsController@featuredImage']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'NewsController@destroy']);
            Route::get('sendWeekly/{id}', ['as' => 'sendWeekly', 'uses' => 'NewsController@sendWeeklyEmail']);
        });
        Route::get('showWeeklyPreview/{id}', ['as' => 'showWeeklyPreview', 'uses' => 'NewsController@showWeeklyPreview']);
        Route::get('', ['as' => 'list', 'uses' => 'NewsController@index']);
        Route::get('{id}', ['as' => 'show', 'uses' => 'NewsController@show']);
    });

    /* Routes related to menu. */
    Route::group(['prefix' => 'menu', 'as' => 'menu::', 'middleware' => ['auth', 'permission:board']], function () {
        Route::get('', ['as' => 'list', 'uses' => 'MenuController@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'MenuController@create']);
        Route::post('add', ['as' => 'add', 'uses' => 'MenuController@store']);

        Route::get('up/{id}', ['as' => 'orderUp', 'uses' => 'MenuController@orderUp']);
        Route::get('down/{id}', ['as' => 'orderDown', 'uses' => 'MenuController@orderDown']);

        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'MenuController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'MenuController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'MenuController@destroy']);
    });

    /* Routes related to tickets. */
    Route::group(['prefix' => 'tickets', 'middleware' => ['auth'], 'as' => 'tickets::'], function () {
        Route::group(['middleware' => ['auth', 'permission:board']], function () {
            Route::get('', ['as' => 'list', 'uses' => 'TicketController@index']);
            Route::get('add', ['as' => 'add', 'uses' => 'TicketController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'TicketController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'TicketController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'TicketController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'TicketController@destroy']);
        });

        Route::get('scan/{barcode}', ['as' => 'scan', 'uses' => 'TicketController@scan']);
        Route::get('unscan/{barcode?}', ['as' => 'unscan', 'uses' => 'TicketController@unscan']);
        Route::get('download/{id}', ['as' => 'download', 'uses' => 'TicketController@download']);
    });

    /* Routes related to e-mail. */
    Route::get('togglelist/{id}', ['as' => 'togglelist', 'middleware' => ['auth'], 'uses' => 'EmailListController@toggleSubscription']);

    Route::get('unsubscribe/{hash}', ['as' => 'unsubscribefromlist', 'uses' => 'EmailController@unsubscribeLink']);

    Route::group(['prefix' => 'email', 'middleware' => ['auth', 'permission:board'], 'as' => 'email::'], function () {
        Route::get('', ['as' => 'admin', 'uses' => 'EmailController@index']);
        Route::get('filter', ['as' => 'filter', 'uses' => 'EmailController@filter']);

        Route::group(['prefix' => 'list', 'as' => 'list::'], function () {
            Route::get('add', ['as' => 'add', 'uses' => 'EmailListController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'EmailListController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'EmailListController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'EmailListController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'EmailListController@destroy']);
        });

        Route::get('add', ['as' => 'add', 'uses' => 'EmailController@create']);
        Route::post('add', ['as' => 'add', 'uses' => 'EmailController@store']);
        Route::get('preview/{id}', ['as' => 'show', 'uses' => 'EmailController@show']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'EmailController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'EmailController@update']);
        Route::get('toggleready/{id}', ['as' => 'toggleready', 'uses' => 'EmailController@toggleReady']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'EmailController@destroy']);

        Route::group(['prefix' => '{id}/attachment', 'as' => 'attachment::'], function () {
            Route::post('add', ['as' => 'add', 'uses' => 'EmailController@addAttachment']);
            Route::get('delete/{file_id}', ['as' => 'delete', 'uses' => 'EmailController@deleteAttachment']);
        });
    });

    Route::get('quotes', ['middleware' => ['member'], 'as' => 'quotes::list', function (Illuminate\Http\Request $request) {
        return (new FeedbackController())->index($request, 'quotes');
    }]);

    Route::get('goodideas', ['middleware' => ['member'], 'as' => 'goodideas::index', function (Illuminate\Http\Request $request) {
        return (new FeedbackController())->index($request, 'goodideas');
    }]);

    /* Routes related to the Feedback Boards. */
    Route::group(['prefix' => 'feedback', 'middleware' => ['member'], 'as' => 'feedback::'], function () {
        Route::group(['prefix' => '/{category}'], function () {
            Route::get('', ['as' => 'index', 'uses' => 'FeedbackController@index']);
            Route::get('search/{searchTerm?}', ['as' => 'search', 'uses' => 'FeedbackController@search']);
            Route::get('archived', ['as' => 'archived', 'uses' => 'FeedbackController@archived']);
            Route::post('add', ['as' => 'add', 'uses' => 'FeedbackController@add']);
            Route::get('archiveall', ['as' => 'archiveall', 'middleware' => ['permission:board'], 'uses' => 'FeedbackController@archiveAll']);
        });

        Route::group(['prefix' => 'categories', 'middleware' => ['permission:board'], 'as' => 'category::'], function () {
            Route::get('admin', ['as' => 'admin', 'uses' => 'FeedbackController@categoryAdmin']);
            Route::post('addone', ['as' => 'add', 'uses' => 'FeedbackController@categoryStore']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'FeedbackController@categoryEdit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'FeedbackController@categoryUpdate']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'FeedbackController@categoryDestroy']);
        });

        Route::get('approve/{id}', ['as' => 'approve', 'uses' => 'FeedbackController@approve']);
        Route::post('reply/{id}', ['as' => 'reply', 'uses' => 'FeedbackController@reply']);
        Route::get('archive/{id}', ['as' => 'archive', 'uses' => 'FeedbackController@archive']);
        Route::get('restore/{id}', ['as' => 'restore', 'uses' => 'FeedbackController@restore']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'FeedbackController@delete']);
        Route::post('vote', ['as' => 'vote', 'uses' => 'FeedbackController@vote']);
    });

    /* Routes related to the OmNomCom. */
    Route::group(['prefix' => 'omnomcom', 'as' => 'omnomcom::'], function () {
        Route::get('minisite', ['uses' => 'OmNomController@miniSite']);

        /* Routes related to OmNomCom stores. */
        Route::group(['prefix' => 'store', 'as' => 'store::'], function () {
            Route::get('', ['as' => 'show', 'middleware' => ['auth'], 'uses' => 'OmNomController@choose']);
            Route::get('{store?}', ['as' => 'show', 'uses' => 'OmNomController@display']);
            Route::post('rfid/add', ['as' => 'rfidadd', 'uses' => 'RfidCardController@store']);
            Route::post('{store}/buy', ['as' => 'buy', 'uses' => 'OmNomController@buy']);
        });

        /* Routes related to OmNomCom orders. */
        Route::group(['prefix' => 'orders', 'middleware' => ['auth'], 'as' => 'orders::'], function () {
            Route::post('add/bulk', ['as' => 'addbulk', 'middleware' => ['permission:omnomcom'], 'uses' => 'OrderLineController@bulkStore']);
            Route::post('add/single', ['as' => 'add', 'middleware' => ['permission:omnomcom'], 'uses' => 'OrderLineController@store']);
            Route::get('delete/{id}', ['as' => 'delete', 'middleware' => ['permission:omnomcom'], 'uses' => 'OrderLineController@destroy']);

            Route::get('history/{date?}', ['as' => 'list', 'uses' => 'OrderLineController@index']);
            Route::get('', ['as' => 'adminlist', 'middleware' => ['permission:omnomcom'], 'uses' => 'OrderLineController@adminindex']);
            Route::get('orderline-wizard', ['as' => 'orderline-wizard', 'uses' => 'OrderLineController@orderlineWizard']);

            Route::group(['prefix' => 'filter', 'middleware' => ['permission:omnomcom'], 'as' => 'filter::'], function () {
                Route::get('name/{name?}', ['as' => 'name', 'middleware' => ['permission:omnomcom'], 'uses' => 'OrderLineController@filterByUser']);
                Route::get('date/{date?}', ['as' => 'date', 'middleware' => ['permission:omnomcom'], 'uses' => 'OrderLineController@filterByDate']);
            });
        });

        /* Routes related to the TIPCie OmNomCom store. */
        Route::group(['prefix' => 'tipcie', 'middleware' => ['auth', 'permission:tipcie'], 'as' => 'tipcie::'], function () {
            Route::get('', ['as' => 'orderhistory', 'uses' => 'TIPCieController@orderIndex']);
        });

        /* Routes related to Financial Accounts. */
        Route::group(['prefix' => 'accounts', 'middleware' => ['permission:finadmin'], 'as' => 'accounts::'], function () {
            Route::get('', ['as' => 'list', 'uses' => 'AccountController@index']);
            Route::get('add', ['as' => 'add', 'uses' => 'AccountController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'AccountController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'AccountController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'AccountController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'AccountController@destroy']);
            Route::get('{id}', ['as' => 'show', 'uses' => 'AccountController@show']);
            Route::post('aggregate/{account}', ['as' => 'aggregate', 'uses' => 'AccountController@showAggregation']);
        });

        /* Routes related to Payment Statistics. */
        Route::group(['prefix' => 'payments', 'middleware' => ['permission:finadmin'], 'as' => 'payments::'], function () {
            Route::get('statistics', ['as' => 'statistics', 'uses' => 'OrderLineController@showPaymentStatistics']);
            Route::post('statistics', ['as' => 'statistics', 'uses' => 'OrderLineController@showPaymentStatistics']);
        });

        /* Routes related to Products. */
        Route::group(['prefix' => 'products', 'middleware' => ['permission:omnomcom'], 'as' => 'products::'], function () {
            Route::get('', ['as' => 'list', 'uses' => 'ProductController@index']);
            Route::get('add', ['as' => 'add', 'uses' => 'ProductController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'ProductController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'ProductController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'ProductController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'ProductController@destroy']);

            Route::get('export/csv', ['as' => 'export_csv', 'uses' => 'ProductController@generateCsv']);
            Route::get('statistics', ['as' => 'statistics', 'uses' => 'AccountController@showOmnomcomStatistics']);
            Route::post('statistics', ['as' => 'statistics', 'uses' => 'AccountController@showOmnomcomStatistics']);

            Route::post('update/bulk', ['as' => 'bulkupdate', 'middleware' => ['permission:omnomcom'], 'uses' => 'ProductController@bulkUpdate']);

            Route::get('mut', ['as' => 'mutations', 'uses' => 'StockMutationController@index']);
            Route::get('mut/csv', ['as' => 'mutations_export', 'uses' => 'StockMutationController@generateCsv']);
        });

        /* Routes related to OmNomCom Categories. */
        Route::group(['prefix' => 'categories', 'middleware' => ['permission:omnomcom'], 'as' => 'categories::'], function () {
            Route::get('', ['as' => 'list', 'uses' => 'ProductCategoryController@index']);
            Route::get('add', ['as' => 'add', 'uses' => 'ProductCategoryController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'ProductCategoryController@store']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'ProductCategoryController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'ProductCategoryController@destroy']);
            Route::get('{id}', ['as' => 'show', 'uses' => 'ProductCategoryController@show']);
        });

        /* Routes related to Withdrawals. */
        Route::group(['prefix' => 'withdrawals', 'middleware' => ['permission:finadmin'], 'as' => 'withdrawal::'], function () {
            Route::get('', ['as' => 'list', 'uses' => 'WithdrawalController@index']);
            Route::get('add', ['as' => 'add', 'uses' => 'WithdrawalController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'WithdrawalController@store']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'WithdrawalController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'WithdrawalController@destroy']);
            Route::get('{id}', ['as' => 'show', 'uses' => 'WithdrawalController@show']);
            Route::get('accounts/{id}', ['as' => 'showAccounts', 'uses' => 'WithdrawalController@showAccounts']);

            Route::get('export/{id}', ['as' => 'export', 'uses' => 'WithdrawalController@export']);
            Route::get('close/{id}', ['as' => 'close', 'uses' => 'WithdrawalController@close']);
            Route::get('email/{id}', ['as' => 'email', 'uses' => 'WithdrawalController@email']);

            Route::get('deletefrom/{id}/{user_id}', ['as' => 'deleteuser', 'uses' => 'WithdrawalController@deleteFrom']);
            Route::get('markfailed/{id}/{user_id}', ['as' => 'markfailed', 'uses' => 'WithdrawalController@markFailed']);
            Route::get('markloss/{id}/{user_id}', ['as' => 'markloss', 'uses' => 'WithdrawalController@markLoss']);
        });

        Route::get('unwithdrawable', ['middleware' => ['permission:finadmin'], 'as' => 'unwithdrawable', 'uses' => 'WithdrawalController@unwithdrawable']);

        /* Routes related to Mollie. */
        Route::group(['prefix' => 'mollie', 'middleware' => ['auth'], 'as' => 'mollie::'], function () {
            Route::post('pay', ['as' => 'pay', 'uses' => 'MollieController@pay']);
            Route::get('status/{id}', ['as' => 'status', 'uses' => 'MollieController@status']);
            Route::get('receive/{id}', ['as' => 'receive', 'uses' => 'MollieController@receive']);
            Route::get('list', ['as' => 'list', 'middleware' => ['permission:finadmin'], 'uses' => 'MollieController@index']);
            Route::get('monthly/{month}', ['as' => 'monthly', 'middleware' => ['permission:finadmin'], 'uses' => 'MollieController@monthly']);
        });

        Route::get('mywithdrawal/{id}', ['as' => 'mywithdrawal', 'middleware' => ['auth'], 'uses' => 'WithdrawalController@showForUser']);

        Route::get('supplier', ['as' => 'generateorder', 'middleware' => ['permission:omnomcom'], 'uses' => 'OmNomController@generateOrder']);
    });

    /* Routes related to webhooks. */
    Route::group(['prefix' => 'webhook', 'as' => 'webhook::'], function () {
        Route::any('mollie/{id}', ['as' => 'mollie', 'uses' => 'MollieController@webhook']);
    });

    /* Routes related to YouTube videos. */
    Route::group(['prefix' => 'video', 'as' => 'video::'], function () {
        Route::group(['prefix' => 'admin', 'middleware' => ['permission:board'], 'as' => 'admin::'], function () {
            Route::get('', ['as' => 'index', 'uses' => 'VideoController@index']);
            Route::post('add', ['as' => 'add', 'uses' => 'VideoController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'VideoController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'VideoController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'VideoController@destroy']);
        });
        Route::get('{id}', ['as' => 'view', 'uses' => 'VideoController@view']);
        Route::get('', ['as' => 'index', 'uses' => 'VideoController@publicIndex']);
    });

    /* Routes related to announcements. */
    Route::group(['prefix' => 'announcement', 'as' => 'announcement::'], function () {
        Route::group(['prefix' => 'admin', 'middleware' => ['permission:sysadmin'], 'as' => ''], function () {
            Route::get('', ['as' => 'index', 'uses' => 'AnnouncementController@index']);
            Route::get('add', ['as' => 'add', 'uses' => 'AnnouncementController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'AnnouncementController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'AnnouncementController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'AnnouncementController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'AnnouncementController@destroy']);
            Route::get('clear', ['as' => 'clear', 'uses' => 'AnnouncementController@clear']);
        });
        Route::get('dismiss/{id}', ['as' => 'dismiss', 'uses' => 'AnnouncementController@dismiss']);
    });

    /* Routes related to photos. */
    Route::group(['prefix' => 'photos', 'as' => 'photo::'], function () {
        Route::get('list', ['as' => 'albums', 'uses' => 'PhotoController@index']);
        Route::get('slideshow', ['as' => 'slideshow', 'uses' => 'PhotoController@slideshow']);

        Route::group(['prefix' => 'liked', 'middleware' => ['member'], 'as' => 'liked::'], function () {
            Route::get('', ['as' => 'list', 'uses' => 'LikedPicturesController@show']);
            Route::get('/{id}', ['as' => 'likedPhoto', 'uses' => 'LikedPicturesController@photo']);
        });

        Route::group(['prefix' => '{id}', 'as' => 'album::'], function () {
            Route::get('', ['as' => 'list', 'uses' => 'PhotoController@show']);
        });

        Route::get('/likes/{id}', ['as' => 'like', 'uses' => 'PhotoController@toggleLike']);
        Route::get('/photo/{id}', ['as' => 'view', 'uses' => 'PhotoController@photo']);

        /* Routes related to the photo admin. */
        Route::group(['prefix' => 'admin', 'middleware' => ['permission:protography'], 'as' => 'admin::'], function () {
            Route::get('index', ['as' => 'index', 'uses' => 'PhotoAdminController@index']);
            Route::post('index', ['as' => 'index', 'uses' => 'PhotoAdminController@search']);
            Route::post('add', ['as' => 'add', 'uses' => 'PhotoAdminController@create']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'PhotoAdminController@edit']);
            Route::post('edit/{id}', ['as' => 'edit', 'middleware' => ['permission:publishalbums'], 'uses' => 'PhotoAdminController@update']);
            Route::post('edit/{id}/action', ['as' => 'action', 'uses' => 'PhotoAdminController@action']);
            Route::post('edit/{id}/upload', ['as' => 'upload', 'uses' => 'PhotoAdminController@upload']);
            Route::get('edit/{id}/delete', ['as' => 'delete', 'middleware' => ['permission:publishalbums'], 'uses' => 'PhotoAdminController@delete']);
            Route::get('publish/{id}', ['as' => 'publish', 'middleware' => ['permission:publishalbums'], 'uses' => 'PhotoAdminController@publish']);
            Route::get('unpublish/{id}', ['as' => 'unpublish', 'middleware' => ['permission:publishalbums'], 'uses' => 'PhotoAdminController@unpublish']);
        });
    });

    Route::group(['prefix' => 'image', 'as' => 'image::'], function () {
        Route::get('{id}/{hash}', ['as' => 'get', 'uses' => 'FileController@get']);
        Route::get('{id}/{hash}/{name}', ['uses' => 'FileController@get']);
    });

    /* Routes related to Spotify. */
    Route::group(['prefix' => 'spotify', 'middleware' => ['auth', 'permission:board'], 'as' => 'spotify::'], function () {
        Route::get('oauth', ['as' => 'oauth', 'uses' => 'SpotifyController@oauthTool']);
    });

    /* Routes related to roles and permissions. */
    Route::group(['prefix' => 'authorization', 'middleware' => ['auth', 'permission:sysadmin'], 'as' => 'authorization::'], function () {
        Route::get('', ['as' => 'overview', 'uses' => 'AuthorizationController@index']);
        Route::post('{id}/grant', ['as' => 'grant', 'uses' => 'AuthorizationController@grant']);
        Route::get('{id}/revoke/{user}', ['as' => 'revoke', 'uses' => 'AuthorizationController@revoke']);
    });

    /* Routes related to the password manager. */
    Route::group(['prefix' => 'passwordstore', 'middleware' => ['auth'], 'as' => 'passwordstore::'], function () {
        Route::get('', ['as' => 'index', 'uses' => 'PasswordController@index']);
        Route::get('auth', ['as' => 'auth', 'uses' => 'PasswordController@getAuth']);
        Route::post('auth', ['as' => 'auth', 'uses' => 'PasswordController@postAuth']);
        Route::get('add', ['as' => 'add', 'uses' => 'PasswordController@create']);
        Route::post('add', ['as' => 'add', 'uses' => 'PasswordController@store']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'PasswordController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'PasswordController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'PasswordController@destroy']);
    });

    /* Routes related to e-mail aliases. */
    Route::group(['prefix' => 'alias', 'middleware' => ['auth', 'permission:sysadmin'], 'as' => 'alias::'], function () {
        Route::get('', ['as' => 'index', 'uses' => 'AliasController@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'AliasController@create']);
        Route::post('add', ['as' => 'add', 'uses' => 'AliasController@store']);
        Route::get('delete/{id_or_alias}', ['as' => 'delete', 'uses' => 'AliasController@destroy']);
        Route::post('update', ['as' => 'update', 'uses' => 'AliasController@update']);
    });

    /* The route for the SmartXp Screen. */
    Route::get('smartxp', ['as' => 'smartxp', 'uses' => 'SmartXpScreenController@show']);
    Route::get('protopolis', ['as' => 'protopolis', 'uses' => 'SmartXpScreenController@showProtopolis']);
    Route::get('caniworkinthesmartxp', ['uses' => 'SmartXpScreenController@canWork']);

    /* The routes for Protube. */
    Route::group(['prefix' => 'protube', 'as' => 'protube::'], function () {
        Route::get('screen', ['as' => 'screen', 'uses' => 'ProtubeController@screen']);
        Route::get('admin', ['as' => 'admin', 'middleware' => ['auth'], 'uses' => 'ProtubeController@admin']);
        Route::get('offline', ['as' => 'offline', 'uses' => 'ProtubeController@offline']);
        Route::get('dashboard', ['as' => 'dashboard', 'middleware' => ['auth'], 'uses' => 'ProtubeController@dashboard']);
        Route::get('togglehistory', ['as' => 'togglehistory', 'middleware' => ['auth'], 'uses' => 'ProtubeController@toggleHistory']);
        Route::get('clearhistory', ['as' => 'clearhistory', 'middleware' => ['auth'], 'uses' => 'ProtubeController@clearHistory']);
        Route::get('top', ['as' => 'top', 'uses' => 'ProtubeController@topVideos']);
        Route::get('login', ['as' => 'login', 'middleware' => ['auth'], 'uses' => 'ProtubeController@loginRedirect']);
        Route::get('{id?}', ['as' => 'remote', 'uses' => 'ProtubeController@remote']);

        /* Routes related to the Protube Radio */
        Route::group(['prefix' => 'radio', 'middleware' => ['permission:sysadmin'], 'as' => 'radio::'], function () {
            Route::get('index', ['as' => 'index', 'uses' => 'RadioController@index']);
            Route::post('store', ['as' => 'store', 'uses' => 'RadioController@store']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'RadioController@destroy']);
        });

        /* Routes related to the Protube displays */
        Route::group(['prefix' => 'display', 'middleware' => ['permission:sysadmin'], 'as' => 'display::'], function () {
            Route::get('index', ['as' => 'index', 'uses' => 'DisplayController@index']);
            Route::post('store', ['as' => 'store', 'uses' => 'DisplayController@store']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'DisplayController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'DisplayController@destroy']);
        });

        /* Routes related to teh Soundboard */
        Route::group(['prefix' => 'soundboard', 'middleware' => ['permission:sysadmin'], 'as' => 'soundboard::'], function () {
            Route::get('index', ['as' => 'index', 'uses' => 'SoundboardController@index']);
            Route::post('store', ['as' => 'store', 'uses' => 'SoundboardController@store']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'SoundboardController@destroy']);
            Route::get('togglehidden/{id}', ['as' => 'togglehidden', 'uses' => 'SoundboardController@toggleHidden']);
        });
    });

    /* Routes related to calendars. */
    Route::group(['prefix' => 'ical', 'as' => 'ical::'], function () {
        Route::get('calendar/{personal_key?}', ['as' => 'calendar', 'uses' => 'EventController@icalCalendar']);
    });

    /* Routes related to the Achievement system. */
    Route::group(['prefix' => 'achievement', 'as' => 'achievement::'], function () {
        Route::group(['middleware' => ['auth', 'permission:board']], function () {
            Route::get('', ['as' => 'list', 'uses' => 'AchievementController@overview']);
            Route::get('add', ['as' => 'add', 'uses' => 'AchievementController@create']);
            Route::post('add', ['as' => 'add', 'uses' => 'AchievementController@store']);
            Route::get('manage/{id}', ['as' => 'manage', 'uses' => 'AchievementController@manage']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'AchievementController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'AchievementController@destroy']);
            Route::post('award/{id}', ['as' => 'award', 'uses' => 'AchievementController@award']);
            Route::post('give', ['as' => 'give', 'uses' => 'AchievementController@give']);
            Route::get('take/{id}/{user}', ['as' => 'take', 'uses' => 'AchievementController@take']);
            Route::get('takeAll/{id}', ['as' => 'takeAll', 'uses' => 'AchievementController@takeAll']);
            Route::post('{id}/icon', ['as' => 'icon', 'uses' => 'AchievementController@icon']);
        });
        Route::get('gallery', ['as' => 'gallery', 'uses' => 'AchievementController@gallery']);
    });
    Route::get('achieve/{achievement}', ['as' => 'achieve', 'middleware' => ['auth'], 'uses' => 'AchievementController@achieve']);

    /* Routes related to the Welcome Message system. */
    Route::group(['prefix' => 'welcomeMessages', 'middleware' => ['auth', 'permission:board'], 'as' => 'welcomeMessages::'], function () {
        Route::get('', ['as' => 'list', 'uses' => 'WelcomeController@overview']);
        Route::post('add', ['as' => 'add', 'uses' => 'WelcomeController@store']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'WelcomeController@destroy']);
    });

    /* Routes related to Protube TempAdmin */
    Route::group(['prefix' => 'tempadmin', 'as' => 'tempadmin::', 'middleware' => ['auth', 'permission:board']], function () {
        Route::get('make/{id}', ['as' => 'make', 'uses' => 'TempAdminController@make']);
        Route::get('end/{id}', ['as' => 'end', 'uses' => 'TempAdminController@end']);
        Route::get('endId/{id}', ['as' => 'endId', 'uses' => 'TempAdminController@endId']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'TempAdminController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'TempAdminController@update']);
        Route::get('add', ['as' => 'add', 'uses' => 'TempAdminController@create']);
        Route::post('add', ['as' => 'add', 'uses' => 'TempAdminController@store']);
        Route::get('', ['as' => 'index', 'uses' => 'TempAdminController@index']);
    });

    /* Routes related to QR Authentication. */
    Route::group(['prefix' => 'qr', 'as' => 'qr::'], function () {
        Route::get('code/{code}', ['as' => 'code', 'uses' => 'QrAuthController@showCode']);
        Route::post('generate', ['as' => 'generate', 'uses' => 'QrAuthController@generateRequest']);
        Route::get('isApproved', ['as' => 'approved', 'uses' => 'QrAuthController@isApproved']);

        Route::group(['middleware' => ['auth']], function () {
            Route::get('{code}', ['as' => 'dialog', 'uses' => 'QrAuthController@showDialog']);
            Route::get('{code}/approve', ['as' => 'approve', 'uses' => 'QrAuthController@approve']);
        });
    });

    /* Routes related to the Short URL Service */
    Route::group(['prefix' => 'short_url', 'as' => 'short_url::', 'middleware' => ['auth', 'permission:board']], function () {
        Route::get('', ['as' => 'index', 'uses' => 'ShortUrlController@index']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'ShortUrlController@edit']);
        Route::post('edit/{id}', ['as' => 'edit', 'uses' => 'ShortUrlController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'ShortUrlController@destroy']);
    });
    Route::get('go/{short?}', ['as' => 'short_url::go', 'uses' => 'ShortUrlController@go']);

    /* Routes related to the DMX Management. */
    Route::group(['prefix' => 'dmx', 'as' => 'dmx::', 'middleware' => ['auth', 'permission:board|alfred']], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'DmxController@index']);
        Route::get('/add', ['as' => 'add', 'uses' => 'DmxController@create']);
        Route::post('/add', ['as' => 'add', 'uses' => 'DmxController@store']);
        Route::get('/edit/{id}', ['as' => 'edit', 'uses' => 'DmxController@edit']);
        Route::post('/edit/{id}', ['as' => 'edit', 'uses' => 'DmxController@update']);
        Route::get('/delete/{id}', ['as' => 'delete', 'uses' => 'DmxController@delete']);

        Route::group(['prefix' => 'override', 'as' => 'override::'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'DmxController@overrideIndex']);
            Route::get('/add', ['as' => 'add', 'uses' => 'DmxController@overrideCreate']);
            Route::post('/add', ['as' => 'add', 'uses' => 'DmxController@overrideStore']);
            Route::get('/edit/{id}', ['as' => 'edit', 'uses' => 'DmxController@overrideEdit']);
            Route::post('/edit/{id}', ['as' => 'edit', 'uses' => 'DmxController@overrideUpdate']);
            Route::get('/delete/{id}', ['as' => 'delete', 'uses' => 'DmxController@overrideDelete']);
        });
    });

    /* Routes related to the Query system. */
    Route::group(['prefix' => 'queries', 'as' => 'queries::', 'middleware' => ['auth', 'permission:board']], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'QueryController@index']);
        Route::get('/activity_overview', ['as' => 'activity_overview', 'uses' => 'QueryController@activityOverview']);
        Route::get('/membership_totals', ['as' => 'membership_totals', 'uses' => 'QueryController@membershipTotals']);
    });

    /* Routes related to the Minisites */
    Route::group(['prefix' => 'minisites', 'as' => 'minisites::'], function () {
        Route::group(['prefix' => 'isalfredthere', 'as' => 'isalfredthere::'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'IsAlfredThereController@showMiniSite']);
            Route::get('/admin', ['as' => 'admin', 'uses' => 'IsAlfredThereController@getAdminInterface', 'middleware' => ['auth', 'permission:sysadmin|alfred']]);
            Route::post('/admin', ['as' => 'admin', 'uses' => 'IsAlfredThereController@postAdminInterface', 'middleware' => ['auth', 'permission:sysadmin|alfred']]);
        });
    });

    Route::group(['prefix' => 'codex', 'as' => 'codex::', 'middleware' => ['auth', 'permission:senate']], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CodexController@index']);
        Route::get('add-codex', ['as' => 'add-codex', 'uses' => 'CodexController@addCodex']);
        Route::get('add-song', ['as' => 'add-song', 'uses' => 'CodexController@addSong']);
        Route::get('add-song-category', ['as' => 'add-song-category', 'uses' => 'CodexController@addSongCategory']);
        Route::get('add-text-type', ['as' => 'add-text-type', 'uses' => 'CodexController@addTextType']);
        Route::get('add-text', ['as' => 'add-text', 'uses' => 'CodexController@addText']);

        Route::get('edit-codex/{codex}', ['as' => 'edit-codex', 'uses' => 'CodexController@editCodex']);
        Route::get('edit-song/{id}', ['as' => 'edit-song', 'uses' => 'CodexController@editSong']);
        Route::get('edit-song-category/{id}', ['as' => 'edit-song-category', 'uses' => 'CodexController@editSongCategory']);
        Route::get('edit-text-type/{id}', ['as' => 'edit-text-type', 'uses' => 'CodexController@editTextType']);
        Route::get('edit-text/{id}', ['as' => 'edit-text', 'uses' => 'CodexController@editText']);

        Route::get('delete-codex/{codex}', ['as' => 'delete-codex', 'uses' => 'CodexController@deleteCodex']);
        Route::get('delete-song/{id}', ['as' => 'delete-song', 'uses' => 'CodexController@deleteSong']);
        Route::get('delete-song-category/{id}', ['as' => 'delete-song-category', 'uses' => 'CodexController@deleteSongCategory']);
        Route::get('delete-text-type/{id}', ['as' => 'delete-text-type', 'uses' => 'CodexController@deleteTextType']);
        Route::get('delete-text/{id}', ['as' => 'delete-text', 'uses' => 'CodexController@deleteText']);

        Route::post('add-codex', ['as' => 'add-codex', 'uses' => 'CodexController@storeCodex']);
        Route::post('add-song', ['as' => 'add-song', 'uses' => 'CodexController@storeSong']);
        Route::post('add-song-category', ['as' => 'add-song-category', 'uses' => 'CodexController@storeSongCategory']);
        Route::post('add-text-type', ['as' => 'add-text-type', 'uses' => 'CodexController@storeTextType']);
        Route::post('add-text', ['as' => 'add-text', 'uses' => 'CodexController@storeText']);

        Route::post('edit-codex/{codex}', ['as' => 'edit-codex', 'uses' => 'CodexController@updateCodex']);
        Route::post('edit-song/{id}', ['as' => 'edit-song', 'uses' => 'CodexController@updateSong']);
        Route::post('edit-song-category/{id}', ['as' => 'edit-song-category', 'uses' => 'CodexController@updateSongCategory']);
        Route::post('edit-text-type/{id}', ['as' => 'edit-text-type', 'uses' => 'CodexController@updateTextType']);
        Route::post('edit-text/{id}', ['as' => 'edit-text', 'uses' => 'CodexController@updateText']);

        Route::get('export/{id}', ['as' => 'export', 'uses' => 'CodexController@exportCodex']);
    });
});
