<?php

return [
    'client' => [
        'base_uri' => 'https://hr.my/go/',
        'cookies' => true,
    ],

    'email' => 'mail@example.com',
    'password' => 'password',

    'check-in' => '8:00 am',
    'check-out' => '+8 hours',
    'timezone' => 'Africa/Cairo',

    // psycho (+0 seconds)
    // normal (+1 to +15 minutes)
    // asshole (+1 to +4 hours)
    'mode' => 'normal',
    'holidays' => [
        'friday',
        'saturday',
        // 2020-08-02
    ],

    'slack' => [
        'hook' => "",
        'settings' => [
            // ...
        ],
    ],
];
