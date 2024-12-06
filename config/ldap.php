<?php

return [
    'proxy' => [
        'utwente' => [
            'post_url' => getenv('POST_LDAP_PROXY_URL'),
            'key' => getenv('LDAP_PROXY_KEY'),
        ],
    ],
];
