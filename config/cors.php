<?php
return [
    'paths' => ['*'],  // السماح لجميع المسارات
    'allowed_methods' => ['*'],  // السماح لجميع الطرق
    'allowed_origins' => ['*'],  // السماح لجميع الأصول
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],  // السماح لجميع الرؤوس
    'exposed_headers' => ['*'],
    'max_age' => 0,
    'supports_credentials' => true,
];
