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
    'autoSubscribeMember' => [8],

    /*
    |--------------------------------------------------------------------------
    | Public Timetable Calendar
    |--------------------------------------------------------------------------
    |
    | The Google calendar ID for the imported timetable.
    |
    */

    'google-timetable-id' => '76ambpj6tq40mlht0ok7ibonori7dliv@import.calendar.google.com',

    /*
    |--------------------------------------------------------------------------
    | SmartXp Timetable Calendar
    |--------------------------------------------------------------------------
    |
    | The Google calendar ID for the imported timetable.
    |
    */

    'smartxp-google-timetable-id' => 'sk5jps5kgrmvq5gp6oc20qfrmsvdfin7@import.calendar.google.com',

    /*
    |--------------------------------------------------------------------------
    | Protopeners Calendar
    |--------------------------------------------------------------------------
    |
    | The Google calendar ID for the Protopeners.
    |
    */

    'protopeners-google-timetable-id' => 'student.utwente.nl_fnulbjgdr41qdppgdv4asa17ck@group.calendar.google.com',

    /*
    |--------------------------------------------------------------------------
    | Internal Name
    |--------------------------------------------------------------------------
    |
    | The name that is shown in e-mails as the Officer Internal Affairs.
    |
    */

    'internal' => 'Philippe Tuinman',

    /*
    |--------------------------------------------------------------------------
    | Treasurer Name
    |--------------------------------------------------------------------------
    |
    | The name that is shown in e-mails as the Treasurer.
    |
    */

    'treasurer' => 'Maxim de Leeuw',

    /*
    |--------------------------------------------------------------------------
    | Secretary Name
    |--------------------------------------------------------------------------
    |
    | The name that is shown in e-mails as the Secretary.
    |
    */

    'secretary' => 'Emiel Steegh',

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
    | Default Slack channel
    |--------------------------------------------------------------------------
    |
    | The default Slack channel for messages.
    |
    */

    'slackchannel' => '#hyttioaoac-logs',

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
            'www.protube.nl'
        ],
        'omnomcom' => [
            'omnomcom.nl',
            'www.omnomcom.nl'
        ],
        'smartxp' => [
            'smartxp.nl',
            'www.smartxp.nl',
            'caniworkinthesmartxp.nl',
            'www.caniworkinthesmartxp.nl'
        ],
        'developers' => [
            'haveyoutriedturningitoffandonagain.nl',
            'www.haveyoutriedturningitoffandonagain.nl'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Soundboard Configuration
    |--------------------------------------------------------------------------
    |
    | Some Soundboard sounds are played automatially. Here, the corresponding
    | IDs are being set.
    |
    */

    'soundboardSounds' => [
        '1337' => 9
    ],

    /*
    |--------------------------------------------------------------------------
    | SEPA Withdrawal Info
    |--------------------------------------------------------------------------
    |
    | Info needed to construct SEPA withdrawals.
    |
    */

    'sepa_info' => (object)[
        'iban' => env('SEPA_IBAN'),
        'bic' => env('SEPA_BIC'),
        'creditor_id' => env('SEPA_CI')
    ],

    /*
    |--------------------------------------------------------------------------
    | SEPA Withdrawal Info
    |--------------------------------------------------------------------------
    |
    | Info needed to construct SEPA withdrawals.
    |
    */

    'openiban_url' => env('OPENIBAN_URL', 'https://openiban.proto.utwente.nl'),

];
