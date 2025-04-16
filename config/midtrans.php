<?php

return [
    'client_key' => env(key:'MIDTRANS_CLIENT_KEY'),
    'server_key' => env(key:'MIDTRANS_SERVER_KEY'),
    'is_production' => env(key:'MIDTRANS_IS_PRODUCTION',default:false),
    'is_sanitized' => env(key:'MIDTRANS_IS_SANITIZED',default:true),
    'is_3ds' => env(key:'MIDTRANS_IS_3DS',default:true),
];