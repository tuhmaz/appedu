<?php
return [
    'paths' => ['api/*'], // Specify the paths where CORS should be applied
    'allowed_methods' => ['*'], // All methods allowed
    'allowed_origins' => ['*'], // Allow all origins
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
