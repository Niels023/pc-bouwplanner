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
    // Instellingen van de aanbeveling per doel. shares is het deel van het budget per categorie in
    // procenten (samen 100), upgrade_order zegt welke onderdelen beter worden als er geld over is.
    'goals' => [
        'gaming' => [
            'shares' => [
                'cpu' => 14,
                'motherboard' => 6,
                'ram' => 13,
                'gpu' => 50,
                'cooler' => 3,
                'case' => 4,
                'storage' => 7,
                'psu' => 3,
            ],
            'min_ram_gb' => 16,
            'min_storage_gb' => 1000,
            'min_ram_slots' => 4,
            'upgrade_order' => ['gpu', 'cpu', 'ram', 'storage'],
        ],
        'work' => [
            'shares' => [
                'cpu' => 34,
                'motherboard' => 11,
                'ram' => 26,
                'gpu' => 0,
                'cooler' => 4,
                'case' => 6,
                'storage' => 14,
                'psu' => 5,
            ],
            'min_ram_gb' => 16,
            'min_storage_gb' => 500,
            'min_ram_slots' => 4,
            'upgrade_order' => ['cpu', 'ram', 'storage'],
        ],
        'streaming' => [
            'shares' => [
                'cpu' => 20,
                'motherboard' => 7,
                'ram' => 20,
                'gpu' => 38,
                'cooler' => 3,
                'case' => 4,
                'storage' => 5,
                'psu' => 3,
            ],
            'min_ram_gb' => 32,
            'min_storage_gb' => 1000,
            'min_ram_slots' => 4,
            'upgrade_order' => ['cpu', 'gpu', 'ram', 'storage'],
        ],
    ],
];
