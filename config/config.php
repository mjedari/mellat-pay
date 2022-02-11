<?php
// config for Mjedari/MellatPay
return [

    /*
    * Gateway server url:
    * If you want to test use simply replace payanode's mocking server:
    * https://mellat-pay.payanode.com/api/v1
    */
    'server' => "https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl",

    /*
    * Gateway server url:
    * In callback before verifying process starts, we check to be sure
    * that request comes from the real bank server.
    */
    'origin' => "https://bpm.shaparak.ir",

    /*
    * Soap connection timeout
    *
    */
    'timeout' => '60',

    /*
    * Description of time zone:
    *
    */
    'timezone' => 'Asia/Tehran',

    /*
    * Language for errors and messages:
    *
    */
    'local' => 'fa', //en

    /*
    * Description of credentials:
    *
    */
    'credentials' => [
        'username'     => '',
        'password'     => '',
        'terminalId'   => 0000000,
    ],

    /*
    * Gateway's default callback:
    *
    */
    'callback' => '/callback',

    /*
    * Description of table name:
    *
    */
    'table' => 'mellat_transactions',
];
