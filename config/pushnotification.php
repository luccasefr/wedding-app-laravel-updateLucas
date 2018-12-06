<?php

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'My_ApiKey',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AAAAa3lT0DA:APA91bH4uZ_TVD_3XBCOJh30bg-k_WSS-szYsk9d7EdKX57TGFkra02-t9M8519X45dEs_n6PNDwyUSrjWE1yGMGrKanFBIzcMpJNosDYt12N7cS7IuHEAvfZnwSg-7Vvl1XW3sAbVsvgH4z1rXqdjswWu2E4CGmEw',
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
        'passPhrase' => '1234', //Optional
        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => true
    ]
];
