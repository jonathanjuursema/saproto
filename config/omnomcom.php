<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OmNomCom Stores
    |--------------------------------------------------------------------------
    |
    | Defines the OmNomCom stores available, which products to show, and which
    | people or IP addresses are allowed to view them.
    |
    */

    'stores' => [
        'protopolis' => [
            'name' => 'Protopolis',
            'categories' => [12, 1, 4, 5, 6, 22, 24, 7, 9, 11, 26],
            'addresses' => ['130.89.190.22', '130.89.190.235', '2001:67c:2564:318:baae:edff:fe79:9aa3'],
            'roles' => ['board', 'omnomcom'],
            'cash_allowed' => false,
            'bank_card_allowed' => false,
            'alcohol_time_constraint' => true,
        ],
        'tipcie' => [
            'name' => 'TIPCie',
            'categories' => [15, 18, 25],
            'addresses' => [],
            'roles' => ['board', 'tipcie', 'drafters'],
            'cash_allowed' => false,
            'bank_card_allowed' => true,
            'col_override' => 3,
            'alcohol_time_constraint' => false,
        ],
    ],

    'cookiemonsters' => [
        (object) [
            'name' => 'valentine',
            'start' => 'February 14',
            'end' => 'February 15',
        ],
        (object) [
            'name' => 'stpatrick',
            'start' => 'March 10',
            'end' => 'March 18',
        ],
        (object) [
            'name' => 'easter',
            'start' => date('M-d-Y', easter_date()),
            'end' => date('M-d-Y', easter_date()).' +1 day',
        ],
        (object) [
            'name' => 'dies',
            'start' => 'April 1',
            'end' => 'April 21',
        ],
        (object) [
            'name' => 'may4th',
            'start' => 'May 4',
            'end' => 'May 5',
        ],
        (object) [
            'name' => 'talklikeapirate',
            'start' => 'September 19',
            'end' => 'September 20',
        ],
        (object) [
            'name' => 'oktoberfest',
            'start' => 'September 22',
            'end' => 'October 3',
        ],
        (object) [
            'name' => 'halloween',
            'start' => 'October 24',
            'end' => 'November 1',
        ],
        (object) [
            'name' => 'sinterklaas',
            'start' => 'November 25',
            'end' => 'December 6',
        ],
        (object) [
            'name' => 'autumn',
            'start' => 'September 23',
            'end' => 'December 22',
        ],
        (object) [
            'name' => 'christmas',
            'start' => 'December 6',
            'end' => 'December 31',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alfred Account
    |--------------------------------------------------------------------------
    |
    | Defines the the financial account ID for Alfred's products.
    |
    */

    'alfred-account' => 43,

    /*
    |--------------------------------------------------------------------------
    | OmNomCom Account
    |--------------------------------------------------------------------------
    |
    | Defines the the financial account ID for OmNomCom products.
    |
    */

    'omnomcom-account' => 1,

    /*
    |--------------------------------------------------------------------------
    | TIPCie Account
    |--------------------------------------------------------------------------
    |
    | Defines the financial account used by the TIPCie
    |
    */

    'tipcie-account' => 4,

    /*
    |--------------------------------------------------------------------------
    | Mollie Settings
    |--------------------------------------------------------------------------
    |
    | Defines various configuration options for the Mollie integration.
    | Free methods will not have any fees when use_fees is true, to exclude use the
    | ids as in the mollie api.
    */

    'mollie' => [
        'fee_id' => 887,
        'free_methods' => ['creditcard'],
        'use_fees' => false,
        'has_webhook' => getenv('APP_ENV') !== 'local',
        'paid_statuses' => ['paid', 'paidout'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Membership Fee Settings
    |--------------------------------------------------------------------------
    |
    | Defines the products for membership fees.
    |
    */

    'fee' => [
        'regular' => 292,
        'reduced' => 293,
        'remitted' => 889,
    ],

    /*
    |--------------------------------------------------------------------------
    | ProTube Skip Product
    |--------------------------------------------------------------------------
    |
    | Defines the product for ProTube skip
    |
    */

    'protube-skip' => 40,

    /*
    |--------------------------------------------------------------------------
    | Times for buying alcoholic beverages
    |--------------------------------------------------------------------------
    |
    | Defines the times between which alcohol can be bought in Omnomcom.
    | The end-time needs to be a time on the next day.
    |
    */

    'alcohol-start' => '14:00',
    'alcohol-end' => '08:00',

    /*
    |--------------------------------------------------------------------------
    | OmNomCom Failed Withdrawal Products
    |--------------------------------------------------------------------------
    |
    | This product is used in the failed withdrawal functionality.
    |
    */

    'failed-withdrawal' => 1055,

    /*
    |--------------------------------------------------------------------------
    | Dinner takeAway Product
    |--------------------------------------------------------------------------
    |
    | This product is used in the dinnerform orderlines functionality
    |
    */

    'dinnerform-product' => 1404,

];
