<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Root Committee
    |--------------------------------------------------------------------------
    |
    | The slug of the committee that is considered to have admin access to the application.
    | This committee cannot be deleted.
    |
    */

    'rootcommittee' => 'haveyoutriedturningitoffandonagain',
    'rootrole' => 12,

    /*
    |--------------------------------------------------------------------------
    | Primary e-mail domain
    |--------------------------------------------------------------------------
    |
    | This domain will be prefixed to e-mail slugs in order to complete e-mail addresses.
    |
    */

    'emaildomain' => 'proto.utwente.nl',

    /*
    |--------------------------------------------------------------------------
    | Additional Mailboxes
    |--------------------------------------------------------------------------
    |
    | A lost of additional mailboxes to be created by the DirectAdmin sync.
    | TODO: Should be moved to a database. :)
    |
    */

    'additional_mailboxes' => [
        'boardarchive',
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles that require Two-Factor Authentication
    |--------------------------------------------------------------------------
    |
    | Users that have any of these roles will be forced by the application to enable TFA to enhance security.
    |
    */

    'tfaroles' => ['sysadmin', 'admin', 'board', 'finadmin', 'omnomcom', 'tipcie'],

    /*
    |--------------------------------------------------------------------------
    | Committee Links
    |--------------------------------------------------------------------------
    |
    | Link between committee concepts and actual ID's
    |
    */

    'committee' => [
        'board' => 2108,
        'omnomcom' => 26,
        'tipcie' => 3583,
        'drafters' => 3336,
        'ero' => 1364,
        'protography' => 294,
    ],

    /*
    |--------------------------------------------------------------------------
    | Print product
    |--------------------------------------------------------------------------
    |
    | The product that should be used for printing documents on the site.
    |
    */

    'printproduct' => 17,

    /*
    |--------------------------------------------------------------------------
    | Weekly newsletter
    |--------------------------------------------------------------------------
    |
    | The email list ID for the weekly newsletter.
    |
    */

    'weeklynewsletter' => 1,

    /*
    |--------------------------------------------------------------------------
    | Auto subscribe mailinglist
    |--------------------------------------------------------------------------
    |
    | The email list ID's that a user should be subscribed to.
    |
    */

    'autoSubscribeUser' => [],
    'autoSubscribeMember' => [8, 12],

    /*
    |--------------------------------------------------------------------------
    | Discord Server ID
    |--------------------------------------------------------------------------
    |
    | The Discord server ID used to get Discord widget data.
    |
    */

    'discord_server_id' => '600338792766767289',

    /*
    |--------------------------------------------------------------------------
    | Discord Client ID
    |--------------------------------------------------------------------------
    |
    | The Discord client ID used for OAuth authentication
    |
    */
    'discord_client_id' => env('DISCORD_CLIENT'),

    /*
    |--------------------------------------------------------------------------
    | Discord Secret
    |--------------------------------------------------------------------------
    |
    | The Discord secret used for OAuth authentication
    |
    */
    'discord_secret' => env('DISCORD_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Google Calendar
    |--------------------------------------------------------------------------
    |
    | The Google calendar ID's for the imported CreaTe timetable, SmartXp calendar and ProtOpener calendar.
    |
    */

    'google-calendar' => [
        'timetable-id' => env('TIMETABLE_GOOGLE_CALENDAR_ID'),
        'smartxp-id' => env('SMARTXP_GOOGLE_CALENDAR_ID'),
        'protopeners-id' => env('PROTOPENERS_GOOGLE_CALENDAR_ID'),
    ],

    /*
      |--------------------------------------------------------------------------
      | Timetable translations
      |--------------------------------------------------------------------------
      |
      | The translations for the different types of events in the timetable from timeedit
      |
      */

    'timetable-translations' => [
        'Zelfstudie geen begeleiding' => 'Self-study',
        'Zelfstudie met begeleiding' => 'Self-study with guidance',
        'Presentatie' => 'Presentation',
        'Examen' => 'Exam',
        'Hoorcollege' => 'Lecture',
    ],

    /*
    |--------------------------------------------------------------------------
    | Internal Name
    |--------------------------------------------------------------------------
    |
    | The name that is shown in e-mails as the Officer Internal Affairs.
    |
    */

    'internal' => 'Jona Patig',

    /*
    |--------------------------------------------------------------------------
    | Treasurer Name
    |--------------------------------------------------------------------------
    |
    | The name that is shown in e-mails as the Treasurer.
    |
    */

    'treasurer' => 'Badr Boubric',

    /*
    |--------------------------------------------------------------------------
    | Secretary Name
    |--------------------------------------------------------------------------
    |
    | The name that is shown in e-mails as the Secretary.
    |
    */

    'secretary' => 'Bart van Dorst',

    /*
    |--------------------------------------------------------------------------
    | Board Number
    |--------------------------------------------------------------------------
    |
    | Used when "Board x.0" is printed.
    |
    */

    'boardnumber' => '14.0',

    /*
    |--------------------------------------------------------------------------
    | Main Study
    |--------------------------------------------------------------------------
    |
    | The ID of the study that is prominently featured on the courses page.
    |
    */

    'mainstudy' => 1,

    /*
    |--------------------------------------------------------------------------
    | Max tickets per transaction
    |--------------------------------------------------------------------------
    |
    | The maximum amount of tickets per ticket per transaction someone can buy.
    |
    */

    'maxtickets' => 10,

    /*
    |--------------------------------------------------------------------------
    | Domain Configuration
    |--------------------------------------------------------------------------
    |
    | Domains that are linked to various routes.
    |
    */

    'domains' => [
        'protube' => [
            'protu.be',
            'www.protu.be',
            'protube.nl',
            'www.protube.nl',
        ],
        'omnomcom' => [
            'omnomcom.nl',
            'www.omnomcom.nl',
        ],
        'smartxp' => [
            'smartxp.nl',
            'www.smartxp.nl',
            'caniworkinthesmartxp.nl',
            'www.caniworkinthesmartxp.nl',
        ],
        'developers' => [
            'haveyoutriedturningitoffandonagain.nl',
            'www.haveyoutriedturningitoffandonagain.nl',
        ],
        'isalfredthere' => [
            'isalfredthere.nl',
            'www.isalfredthere.nl',
        ],
        'static' => [
            env('STATIC_URL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Soundboard Configuration
    |--------------------------------------------------------------------------
    |
    | Some Soundboard sounds are played automatically. Here, the corresponding
    | IDs are being set.
    |
    */

    'soundboardSounds' => [
        '1337' => 9,
        'new-member' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | SEPA Withdrawal Info
    |--------------------------------------------------------------------------
    |
    | Info needed to construct SEPA withdrawals.
    |
    */

    'sepa_info' => [
        'iban' => env('SEPA_IBAN'),
        'bic' => env('SEPA_BIC'),
        'creditor_id' => env('SEPA_CI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Website Theme configuration
    |--------------------------------------------------------------------------
    |
    | The different css themes. Must match name of theme SCSS file!
    |
    */

    'themes' => [
        0 => 'light',
        1 => 'dark',
        2 => 'rainbowbarf',
        3 => 'broto',
        4 => 'nightMode',
    ],

    'logoThemes' => [
        0 => 'regular',
        1 => 'inverse',
        2 => 'regular',
        3 => 'broto-inverse',
        4 => 'inverse',
    ],

    // Analytics URL
    'analytics_url' => env('ANALYTICS_URL', ''),
];
