<?php

// Prijzen: laagste prijs in de Tweakers Pricewatch op de peildatum. Bronnen staan in docs/data-controle.md.
$priceDate = '2026-10-28';

return [
    'cpu' => [
        [
            'brand' => 'AMD', 'name' => 'Ryzen 5 5600 Boxed', 'price_eur' => 124.96, 'price_date' => $priceDate,
            'power_w' => 88, 'socket' => 'AM4', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 4.4,
            'has_integrated_graphics' => false,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 7 5700X Boxed', 'price_eur' => 194.90, 'price_date' => $priceDate,
            'power_w' => 88, 'socket' => 'AM4', 'cores' => 8, 'threads' => 16, 'boost_clock_ghz' => 4.6,
            'has_integrated_graphics' => false,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 5 7600 Boxed', 'price_eur' => 176.82, 'price_date' => $priceDate,
            'power_w' => 88, 'socket' => 'AM5', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 5.1,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 5 9600X Boxed', 'price_eur' => 181.00, 'price_date' => $priceDate,
            'power_w' => 88, 'socket' => 'AM5', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 5.4,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 7 7800X3D Boxed', 'price_eur' => 356.84, 'price_date' => $priceDate,
            'power_w' => 162, 'socket' => 'AM5', 'cores' => 8, 'threads' => 16, 'boost_clock_ghz' => 5.0,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 7 9800X3D Boxed', 'price_eur' => 429.00, 'price_date' => $priceDate,
            'power_w' => 162, 'socket' => 'AM5', 'cores' => 8, 'threads' => 16, 'boost_clock_ghz' => 5.2,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'Intel', 'name' => 'Core i5-12400F Boxed', 'price_eur' => 145.04, 'price_date' => $priceDate,
            'power_w' => 117, 'socket' => 'LGA1700', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 4.4,
            'has_integrated_graphics' => false,
        ],
        [
            'brand' => 'Intel', 'name' => 'Core i5-14600K Boxed', 'price_eur' => 260.84, 'price_date' => $priceDate,
            'power_w' => 181, 'socket' => 'LGA1700', 'cores' => 14, 'threads' => 20, 'boost_clock_ghz' => 5.3,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'Intel', 'name' => 'Core Ultra 5 245K Boxed', 'price_eur' => 183.00, 'price_date' => $priceDate,
            'power_w' => 159, 'socket' => 'LGA1851', 'cores' => 14, 'threads' => 14, 'boost_clock_ghz' => 5.2,
            'has_integrated_graphics' => true,
        ],
    ],

    'motherboard' => [
        [
            'brand' => 'ASUS', 'name' => 'TUF Gaming B550-PLUS', 'price_eur' => 120.12, 'price_date' => $priceDate,
            'power_w' => 40, 'socket' => 'AM4', 'ram_type' => 'DDR4', 'form_factor' => 'ATX',
            'chipset' => 'B550', 'ram_slots' => 4,
        ],
        [
            'brand' => 'ASRock', 'name' => 'B550M Pro4', 'price_eur' => 95.48, 'price_date' => $priceDate,
            'power_w' => 40, 'socket' => 'AM4', 'ram_type' => 'DDR4', 'form_factor' => 'Micro-ATX',
            'chipset' => 'B550', 'ram_slots' => 4,
        ],
        [
            'brand' => 'Gigabyte', 'name' => 'B850M DS3H', 'price_eur' => 134.00, 'price_date' => $priceDate,
            'power_w' => 40, 'socket' => 'AM5', 'ram_type' => 'DDR5', 'form_factor' => 'Micro-ATX',
            'chipset' => 'B850', 'ram_slots' => 4,
        ],
        [
            'brand' => 'MSI', 'name' => 'B850 GAMING PLUS WIFI', 'price_eur' => 169.00, 'price_date' => $priceDate,
            'power_w' => 40, 'socket' => 'AM5', 'ram_type' => 'DDR5', 'form_factor' => 'ATX',
            'chipset' => 'B850', 'ram_slots' => 4,
        ],
        [
            'brand' => 'ASUS', 'name' => 'ROG Strix B850-I GAMING WIFI', 'price_eur' => 279.00,
            'price_date' => $priceDate, 'power_w' => 40, 'socket' => 'AM5', 'ram_type' => 'DDR5',
            'form_factor' => 'Mini-ITX', 'chipset' => 'B850', 'ram_slots' => 2,
        ],
        [
            'brand' => 'Gigabyte', 'name' => 'B760M DS3H DDR4', 'price_eur' => 99.00, 'price_date' => $priceDate,
            'power_w' => 40, 'socket' => 'LGA1700', 'ram_type' => 'DDR4', 'form_factor' => 'Micro-ATX',
            'chipset' => 'B760', 'ram_slots' => 4,
        ],
        [
            'brand' => 'Gigabyte', 'name' => 'Z790 AORUS ELITE AX', 'price_eur' => 206.99, 'price_date' => $priceDate,
            'power_w' => 40, 'socket' => 'LGA1700', 'ram_type' => 'DDR5', 'form_factor' => 'ATX',
            'chipset' => 'Z790', 'ram_slots' => 4,
        ],
        [
            'brand' => 'MSI', 'name' => 'MAG B860 TOMAHAWK WIFI', 'price_eur' => 218.51, 'price_date' => $priceDate,
            'power_w' => 40, 'socket' => 'LGA1851', 'ram_type' => 'DDR5', 'form_factor' => 'ATX',
            'chipset' => 'B860', 'ram_slots' => 4,
        ],
    ],

    'ram' => [
        [
            'brand' => 'Kingston', 'name' => 'FURY Beast 16GB (2x8GB) DDR4-3200', 'price_eur' => 169.00,
            'price_date' => $priceDate, 'power_w' => 6, 'ram_type' => 'DDR4', 'capacity_gb' => 16,
            'module_count' => 2, 'speed_mts' => 3200,
        ],
        [
            'brand' => 'Kingston', 'name' => 'FURY Beast 32GB (2x16GB) DDR4-3200', 'price_eur' => 299.00,
            'price_date' => $priceDate, 'power_w' => 6, 'ram_type' => 'DDR4', 'capacity_gb' => 32,
            'module_count' => 2, 'speed_mts' => 3200,
        ],
        [
            'brand' => 'Kingston', 'name' => 'FURY Beast 16GB (2x8GB) DDR5-5600', 'price_eur' => 314.10,
            'price_date' => $priceDate, 'power_w' => 6, 'ram_type' => 'DDR5', 'capacity_gb' => 16,
            'module_count' => 2, 'speed_mts' => 5600,
        ],
        [
            'brand' => 'Corsair', 'name' => 'Vengeance 32GB (2x16GB) DDR5-6000 CL30', 'price_eur' => 599.00,
            'price_date' => $priceDate, 'power_w' => 6, 'ram_type' => 'DDR5', 'capacity_gb' => 32,
            'module_count' => 2, 'speed_mts' => 6000,
        ],
        [
            'brand' => 'G.Skill', 'name' => 'Flare X5 32GB (2x16GB) DDR5-6000 CL30', 'price_eur' => 619.00,
            'price_date' => $priceDate, 'power_w' => 6, 'ram_type' => 'DDR5', 'capacity_gb' => 32,
            'module_count' => 2, 'speed_mts' => 6000,
        ],
    ],

    'gpu' => [
        [
            'brand' => 'MSI', 'name' => 'GeForce RTX 5060 8G VENTUS 2X OC', 'price_eur' => 499.00,
            'price_date' => $priceDate, 'power_w' => 145, 'chip' => 'GeForce RTX 5060', 'vram_gb' => 8,
            'length_mm' => 197,
        ],
        [
            'brand' => 'PowerColor', 'name' => 'Reaper Radeon RX 9060 XT 16GB', 'price_eur' => 570.01,
            'price_date' => $priceDate, 'power_w' => 160, 'chip' => 'Radeon RX 9060 XT', 'vram_gb' => 16,
            'length_mm' => 220,
        ],
        [
            'brand' => 'MSI', 'name' => 'GeForce RTX 5060 Ti 16G VENTUS 2X OC PLUS', 'price_eur' => 843.90,
            'price_date' => $priceDate, 'power_w' => 180, 'chip' => 'GeForce RTX 5060 Ti', 'vram_gb' => 16,
            'length_mm' => 227,
        ],
        [
            'brand' => 'MSI', 'name' => 'GeForce RTX 5070 12G VENTUS 2X OC', 'price_eur' => 949.00,
            'price_date' => $priceDate, 'power_w' => 250, 'chip' => 'GeForce RTX 5070', 'vram_gb' => 12,
            'length_mm' => 236,
        ],
        [
            'brand' => 'Sapphire', 'name' => 'Pulse Radeon RX 9070 XT', 'price_eur' => 999.00,
            'price_date' => $priceDate, 'power_w' => 304, 'chip' => 'Radeon RX 9070 XT', 'vram_gb' => 16,
            'length_mm' => 320,
        ],
        [
            'brand' => 'MSI', 'name' => 'GeForce RTX 5080 16G VENTUS 3X OC', 'price_eur' => 1999.00,
            'price_date' => $priceDate, 'power_w' => 360, 'chip' => 'GeForce RTX 5080', 'vram_gb' => 16,
            'length_mm' => 303,
        ],
        [
            'brand' => 'ASUS', 'name' => 'ROG Astral GeForce RTX 5080 16GB GDDR7 OC Edition', 'price_eur' => 2229.00,
            'price_date' => $priceDate, 'power_w' => 360, 'chip' => 'GeForce RTX 5080', 'vram_gb' => 16,
            'length_mm' => 358,
        ],
    ],

    'cooler' => [
        [
            'brand' => 'Noctua', 'name' => 'NH-L12S', 'price_eur' => 57.95, 'price_date' => $priceDate,
            'power_w' => 5, 'height_mm' => 70,
        ],
        [
            'brand' => 'DeepCool', 'name' => 'AK400', 'price_eur' => 32.99, 'price_date' => $priceDate,
            'power_w' => 5, 'height_mm' => 155,
        ],
        [
            'brand' => 'Thermalright', 'name' => 'Peerless Assassin 120 SE', 'price_eur' => 43.00,
            'price_date' => $priceDate, 'power_w' => 5, 'height_mm' => 155,
        ],
        [
            'brand' => 'ARCTIC', 'name' => 'Freezer 36', 'price_eur' => 21.20, 'price_date' => $priceDate,
            'power_w' => 5, 'height_mm' => 159,
        ],
        [
            'brand' => 'DeepCool', 'name' => 'AK620', 'price_eur' => 64.95, 'price_date' => $priceDate,
            'power_w' => 5, 'height_mm' => 160,
        ],
        [
            'brand' => 'Noctua', 'name' => 'NH-D15 G2', 'price_eur' => 138.94, 'price_date' => $priceDate,
            'power_w' => 5, 'height_mm' => 168,
        ],
    ],

    'case' => [
        [
            'brand' => 'Cooler Master', 'name' => 'MasterBox Q300L V2', 'price_eur' => 62.00,
            'price_date' => $priceDate, 'power_w' => 5, 'max_form_factor' => 'Micro-ATX',
            'max_gpu_length_mm' => 360, 'max_cooler_height_mm' => 159,
        ],
        [
            'brand' => 'Corsair', 'name' => '3000D Airflow Tempered Glass', 'price_eur' => 76.00,
            'price_date' => $priceDate, 'power_w' => 5, 'max_form_factor' => 'ATX',
            'max_gpu_length_mm' => 360, 'max_cooler_height_mm' => 170,
        ],
        [
            'brand' => 'Lian Li', 'name' => 'Lancool 216', 'price_eur' => 89.90, 'price_date' => $priceDate,
            'power_w' => 5, 'max_form_factor' => 'ATX', 'max_gpu_length_mm' => 392,
            'max_cooler_height_mm' => 180,
        ],
        [
            'brand' => 'Fractal Design', 'name' => 'North TG Clear Charcoal Black', 'price_eur' => 144.90,
            'price_date' => $priceDate, 'power_w' => 5, 'max_form_factor' => 'ATX',
            'max_gpu_length_mm' => 355, 'max_cooler_height_mm' => 170,
        ],
        [
            'brand' => 'Cooler Master', 'name' => 'MasterBox NR200P V3', 'price_eur' => 103.05,
            'price_date' => $priceDate, 'power_w' => 5, 'max_form_factor' => 'Mini-ITX',
            'max_gpu_length_mm' => 361, 'max_cooler_height_mm' => 70,
        ],
    ],

    'storage' => [
        [
            'brand' => 'Kingston', 'name' => 'NV3 M.2 2280 500GB', 'price_eur' => 104.22, 'price_date' => $priceDate,
            'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 500,
        ],
        [
            'brand' => 'Samsung', 'name' => '870 EVO 500GB', 'price_eur' => 164.95, 'price_date' => $priceDate,
            'power_w' => 5, 'type' => 'SATA', 'capacity_gb' => 500,
        ],
        [
            'brand' => 'Kingston', 'name' => 'NV3 M.2 2280 1TB', 'price_eur' => 149.90, 'price_date' => $priceDate,
            'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 1000,
        ],
        [
            'brand' => 'Samsung', 'name' => '870 EVO 1TB', 'price_eur' => 249.00, 'price_date' => $priceDate,
            'power_w' => 5, 'type' => 'SATA', 'capacity_gb' => 1000,
        ],
        [
            'brand' => 'Western Digital', 'name' => 'WD_BLACK SN850X 1TB', 'price_eur' => 199.00,
            'price_date' => $priceDate, 'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 1000,
        ],
        [
            'brand' => 'Samsung', 'name' => '990 PRO 2TB', 'price_eur' => 339.00, 'price_date' => $priceDate,
            'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 2000,
        ],
    ],

    'psu' => [
        [
            'brand' => 'Corsair', 'name' => 'CX550 (2023)', 'price_eur' => 49.95, 'price_date' => $priceDate,
            'power_w' => 0, 'wattage_w' => 550, 'efficiency_rating' => '80 Plus Bronze',
        ],
        [
            'brand' => 'Corsair', 'name' => 'RM650e (2025)', 'price_eur' => 82.84, 'price_date' => $priceDate,
            'power_w' => 0, 'wattage_w' => 650, 'efficiency_rating' => 'Cybenetics Platinum',
        ],
        [
            'brand' => 'Seasonic', 'name' => 'FOCUS GX ATX 3 (2024) 750W', 'price_eur' => 123.75,
            'price_date' => $priceDate, 'power_w' => 0, 'wattage_w' => 750,
            'efficiency_rating' => 'Cybenetics Gold',
        ],
        [
            'brand' => 'be quiet!', 'name' => 'Pure Power 13 M 850W', 'price_eur' => 125.10,
            'price_date' => $priceDate, 'power_w' => 0, 'wattage_w' => 850,
            'efficiency_rating' => 'Cybenetics Platinum',
        ],
        [
            'brand' => 'Corsair', 'name' => 'RM1000x (2024)', 'price_eur' => 186.45, 'price_date' => $priceDate,
            'power_w' => 0, 'wattage_w' => 1000, 'efficiency_rating' => 'Cybenetics Platinum',
        ],
    ],
];
