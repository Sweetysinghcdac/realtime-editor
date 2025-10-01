<?php

return [

    'paths' => ['*'], // <-- IMPORTANT: Broadcasting is here!

    'allowed_methods' => ['*'],

    // 'allowed_origins' => [
    //     'http://localhost:5173',    // <-- ADD YOUR FRONTEND URL
    //     'http://127.0.0.1:5173',    // <-- ADD YOUR FRONTEND URL
    // ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,
    

    // 'supports_credentials' => false, // Set to true if you are using cookies/sessions
    'supports_credentials' => true, // Set to true if you are using cookies/sessions

    'allowed_origins' => explode(',', env('CORS_ALLOW_ORIGINS', 'http://localhost:5173','http://127.0.0.1:5173',)), 
];