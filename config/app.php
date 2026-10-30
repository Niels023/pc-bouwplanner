<?php

return [
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'pc_bouwplanner',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
    ],
    // Hoeveel procent de voeding meer moet kunnen leveren dan het geschatte verbruik
    'psu_margin_percent' => 30,
];
