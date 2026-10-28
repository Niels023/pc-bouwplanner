<?php

// Prijzen zijn richtprijzen in euro's. Wat nog gecontroleerd moet worden staat in docs/data-controle.md.
$estimated = '2026-10-28';

return [
    'cpu' => [
        [
            'brand' => 'AMD', 'name' => 'Ryzen 5 5600', 'price_eur' => 99.00, 'price_date' => $estimated,
            'power_w' => 88, 'socket' => 'AM4', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 4.4,
            'has_integrated_graphics' => false,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 7 5700X3D', 'price_eur' => 199.00, 'price_date' => $estimated,
            'power_w' => 142, 'socket' => 'AM4', 'cores' => 8, 'threads' => 16, 'boost_clock_ghz' => 4.1,
            'has_integrated_graphics' => false,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 5 7600', 'price_eur' => 179.00, 'price_date' => $estimated,
            'power_w' => 88, 'socket' => 'AM5', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 5.1,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 5 9600X', 'price_eur' => 209.00, 'price_date' => $estimated,
            'power_w' => 88, 'socket' => 'AM5', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 5.4,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 7 7800X3D', 'price_eur' => 349.00, 'price_date' => $estimated,
            'power_w' => 162, 'socket' => 'AM5', 'cores' => 8, 'threads' => 16, 'boost_clock_ghz' => 5.0,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'AMD', 'name' => 'Ryzen 7 9800X3D', 'price_eur' => 469.00, 'price_date' => $estimated,
            'power_w' => 162, 'socket' => 'AM5', 'cores' => 8, 'threads' => 16, 'boost_clock_ghz' => 5.2,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'Intel', 'name' => 'Core i5-12400F', 'price_eur' => 109.00, 'price_date' => $estimated,
            'power_w' => 117, 'socket' => 'LGA1700', 'cores' => 6, 'threads' => 12, 'boost_clock_ghz' => 4.4,
            'has_integrated_graphics' => false,
        ],
        [
            'brand' => 'Intel', 'name' => 'Core i5-14600K', 'price_eur' => 229.00, 'price_date' => $estimated,
            'power_w' => 181, 'socket' => 'LGA1700', 'cores' => 14, 'threads' => 20, 'boost_clock_ghz' => 5.3,
            'has_integrated_graphics' => true,
        ],
        [
            'brand' => 'Intel', 'name' => 'Core Ultra 5 245K', 'price_eur' => 249.00, 'price_date' => $estimated,
            'power_w' => 159, 'socket' => 'LGA1851', 'cores' => 14, 'threads' => 14, 'boost_clock_ghz' => 5.2,
            'has_integrated_graphics' => true,
        ],
    ],

    'motherboard' => [
        [
            'brand' => 'MSI', 'name' => 'MAG B550 TOMAHAWK', 'price_eur' => 139.00, 'price_date' => $estimated,
            'power_w' => 40, 'socket' => 'AM4', 'ram_type' => 'DDR4', 'form_factor' => 'ATX',
            'chipset' => 'B550', 'ram_slots' => 4,
        ],
        [
            'brand' => 'ASRock', 'name' => 'B550M Pro4', 'price_eur' => 99.00, 'price_date' => $estimated,
            'power_w' => 40, 'socket' => 'AM4', 'ram_type' => 'DDR4', 'form_factor' => 'Micro-ATX',
            'chipset' => 'B550', 'ram_slots' => 4,
        ],
        [
            'brand' => 'Gigabyte', 'name' => 'B650M DS3H', 'price_eur' => 119.00, 'price_date' => $estimated,
            'power_w' => 40, 'socket' => 'AM5', 'ram_type' => 'DDR5', 'form_factor' => 'Micro-ATX',
            'chipset' => 'B650', 'ram_slots' => 4,
        ],
        [
            'brand' => 'MSI', 'name' => 'B650 GAMING PLUS WIFI', 'price_eur' => 169.00, 'price_date' => $estimated,
            'power_w' => 40, 'socket' => 'AM5', 'ram_type' => 'DDR5', 'form_factor' => 'ATX',
            'chipset' => 'B650', 'ram_slots' => 4,
        ],
        [
            'brand' => 'ASUS', 'name' => 'ROG STRIX B650E-I GAMING WIFI', 'price_eur' => 289.00,
            'price_date' => $estimated, 'power_w' => 40, 'socket' => 'AM5', 'ram_type' => 'DDR5',
            'form_factor' => 'Mini-ITX', 'chipset' => 'B650E', 'ram_slots' => 2,
        ],
        [
            'brand' => 'MSI', 'name' => 'PRO B760M-A WIFI DDR4', 'price_eur' => 139.00, 'price_date' => $estimated,
            'power_w' => 40, 'socket' => 'LGA1700', 'ram_type' => 'DDR4', 'form_factor' => 'Micro-ATX',
            'chipset' => 'B760', 'ram_slots' => 4,
        ],
        [
            'brand' => 'Gigabyte', 'name' => 'Z790 AORUS ELITE AX', 'price_eur' => 229.00, 'price_date' => $estimated,
            'power_w' => 40, 'socket' => 'LGA1700', 'ram_type' => 'DDR5', 'form_factor' => 'ATX',
            'chipset' => 'Z790', 'ram_slots' => 4,
        ],
        [
            'brand' => 'MSI', 'name' => 'MAG B860 TOMAHAWK WIFI', 'price_eur' => 219.00, 'price_date' => $estimated,
            'power_w' => 40, 'socket' => 'LGA1851', 'ram_type' => 'DDR5', 'form_factor' => 'ATX',
            'chipset' => 'B860', 'ram_slots' => 4,
        ],
    ],

    'ram' => [
        [
            'brand' => 'Kingston', 'name' => 'FURY Beast 16GB (2x8GB) DDR4-3200', 'price_eur' => 45.00,
            'price_date' => $estimated, 'power_w' => 6, 'ram_type' => 'DDR4', 'capacity_gb' => 16,
            'module_count' => 2, 'speed_mts' => 3200,
        ],
        [
            'brand' => 'Corsair', 'name' => 'Vengeance LPX 32GB (2x16GB) DDR4-3600', 'price_eur' => 79.00,
            'price_date' => $estimated, 'power_w' => 6, 'ram_type' => 'DDR4', 'capacity_gb' => 32,
            'module_count' => 2, 'speed_mts' => 3600,
        ],
        [
            'brand' => 'G.Skill', 'name' => 'Ripjaws V 64GB (2x32GB) DDR4-3600', 'price_eur' => 139.00,
            'price_date' => $estimated, 'power_w' => 6, 'ram_type' => 'DDR4', 'capacity_gb' => 64,
            'module_count' => 2, 'speed_mts' => 3600,
        ],
        [
            'brand' => 'Kingston', 'name' => 'FURY Beast 16GB (2x8GB) DDR5-5600', 'price_eur' => 65.00,
            'price_date' => $estimated, 'power_w' => 6, 'ram_type' => 'DDR5', 'capacity_gb' => 16,
            'module_count' => 2, 'speed_mts' => 5600,
        ],
        [
            'brand' => 'Corsair', 'name' => 'Vengeance 32GB (2x16GB) DDR5-6000 CL30', 'price_eur' => 119.00,
            'price_date' => $estimated, 'power_w' => 6, 'ram_type' => 'DDR5', 'capacity_gb' => 32,
            'module_count' => 2, 'speed_mts' => 6000,
        ],
        [
            'brand' => 'G.Skill', 'name' => 'Flare X5 32GB (2x16GB) DDR5-6000 CL30', 'price_eur' => 125.00,
            'price_date' => $estimated, 'power_w' => 6, 'ram_type' => 'DDR5', 'capacity_gb' => 32,
            'module_count' => 2, 'speed_mts' => 6000,
        ],
        [
            'brand' => 'Corsair', 'name' => 'Vengeance 64GB (2x32GB) DDR5-6000 CL30', 'price_eur' => 239.00,
            'price_date' => $estimated, 'power_w' => 6, 'ram_type' => 'DDR5', 'capacity_gb' => 64,
            'module_count' => 2, 'speed_mts' => 6000,
        ],
    ],

    'gpu' => [
        [
            'brand' => 'Intel', 'name' => 'Arc B580 Limited Edition', 'price_eur' => 279.00,
            'price_date' => $estimated, 'power_w' => 190, 'chip' => 'Arc B580', 'vram_gb' => 12,
            'length_mm' => 272,
        ],
        [
            'brand' => 'MSI', 'name' => 'GeForce RTX 5060 VENTUS 2X OC 8G', 'price_eur' => 309.00,
            'price_date' => $estimated, 'power_w' => 145, 'chip' => 'GeForce RTX 5060', 'vram_gb' => 8,
            'length_mm' => 197,
        ],
        [
            'brand' => 'AMD', 'name' => 'Radeon RX 7600', 'price_eur' => 259.00, 'price_date' => $estimated,
            'power_w' => 165, 'chip' => 'Radeon RX 7600', 'vram_gb' => 8, 'length_mm' => 204,
        ],
        [
            'brand' => 'NVIDIA', 'name' => 'GeForce RTX 5070 Founders Edition', 'price_eur' => 619.00,
            'price_date' => $estimated, 'power_w' => 250, 'chip' => 'GeForce RTX 5070', 'vram_gb' => 12,
            'length_mm' => 242,
        ],
        [
            'brand' => 'AMD', 'name' => 'Radeon RX 7800 XT', 'price_eur' => 479.00, 'price_date' => $estimated,
            'power_w' => 263, 'chip' => 'Radeon RX 7800 XT', 'vram_gb' => 16, 'length_mm' => 267,
        ],
        [
            'brand' => 'Sapphire', 'name' => 'PULSE Radeon RX 9070 XT 16GB', 'price_eur' => 689.00,
            'price_date' => $estimated, 'power_w' => 304, 'chip' => 'Radeon RX 9070 XT', 'vram_gb' => 16,
            'length_mm' => 320,
        ],
        [
            'brand' => 'NVIDIA', 'name' => 'GeForce RTX 5080 Founders Edition', 'price_eur' => 1179.00,
            'price_date' => $estimated, 'power_w' => 360, 'chip' => 'GeForce RTX 5080', 'vram_gb' => 16,
            'length_mm' => 304,
        ],
        [
            'brand' => 'ASUS', 'name' => 'ROG Strix GeForce RTX 4090 OC Edition', 'price_eur' => 1999.00,
            'price_date' => $estimated, 'power_w' => 450, 'chip' => 'GeForce RTX 4090', 'vram_gb' => 24,
            'length_mm' => 358,
        ],
    ],

    'cooler' => [
        [
            'brand' => 'Noctua', 'name' => 'NH-L12S', 'price_eur' => 59.00, 'price_date' => $estimated,
            'power_w' => 5, 'height_mm' => 70,
        ],
        [
            'brand' => 'DeepCool', 'name' => 'AK400', 'price_eur' => 35.00, 'price_date' => $estimated,
            'power_w' => 5, 'height_mm' => 155,
        ],
        [
            'brand' => 'Thermalright', 'name' => 'Peerless Assassin 120 SE', 'price_eur' => 39.00,
            'price_date' => $estimated, 'power_w' => 5, 'height_mm' => 155,
        ],
        [
            'brand' => 'ARCTIC', 'name' => 'Freezer 36', 'price_eur' => 32.00, 'price_date' => $estimated,
            'power_w' => 5, 'height_mm' => 159,
        ],
        [
            'brand' => 'DeepCool', 'name' => 'AK620', 'price_eur' => 59.00, 'price_date' => $estimated,
            'power_w' => 5, 'height_mm' => 160,
        ],
        [
            'brand' => 'Noctua', 'name' => 'NH-D15 G2', 'price_eur' => 149.00, 'price_date' => $estimated,
            'power_w' => 5, 'height_mm' => 168,
        ],
    ],

    'case' => [
        [
            'brand' => 'Cooler Master', 'name' => 'MasterBox Q300L', 'price_eur' => 49.00, 'price_date' => $estimated,
            'power_w' => 5, 'max_form_factor' => 'Micro-ATX', 'max_gpu_length_mm' => 360,
            'max_cooler_height_mm' => 157,
        ],
        [
            'brand' => 'Corsair', 'name' => '3000D Airflow', 'price_eur' => 79.00, 'price_date' => $estimated,
            'power_w' => 5, 'max_form_factor' => 'ATX', 'max_gpu_length_mm' => 360,
            'max_cooler_height_mm' => 170,
        ],
        [
            'brand' => 'Corsair', 'name' => '4000D Airflow', 'price_eur' => 99.00, 'price_date' => $estimated,
            'power_w' => 5, 'max_form_factor' => 'ATX', 'max_gpu_length_mm' => 360,
            'max_cooler_height_mm' => 170,
        ],
        [
            'brand' => 'Fractal Design', 'name' => 'North', 'price_eur' => 139.00, 'price_date' => $estimated,
            'power_w' => 5, 'max_form_factor' => 'ATX', 'max_gpu_length_mm' => 355,
            'max_cooler_height_mm' => 170,
        ],
        [
            'brand' => 'Lian Li', 'name' => 'Lancool 216', 'price_eur' => 99.00, 'price_date' => $estimated,
            'power_w' => 5, 'max_form_factor' => 'ATX', 'max_gpu_length_mm' => 392,
            'max_cooler_height_mm' => 180,
        ],
        [
            'brand' => 'Cooler Master', 'name' => 'MasterBox NR200P', 'price_eur' => 109.00,
            'price_date' => $estimated, 'power_w' => 5, 'max_form_factor' => 'Mini-ITX',
            'max_gpu_length_mm' => 330, 'max_cooler_height_mm' => 155,
        ],
        [
            'brand' => 'SilverStone', 'name' => 'SUGO SG13', 'price_eur' => 59.00, 'price_date' => $estimated,
            'power_w' => 5, 'max_form_factor' => 'Mini-ITX', 'max_gpu_length_mm' => 267,
            'max_cooler_height_mm' => 61,
        ],
    ],

    'storage' => [
        [
            'brand' => 'Crucial', 'name' => 'P3 Plus 500GB', 'price_eur' => 45.00, 'price_date' => $estimated,
            'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 500,
        ],
        [
            'brand' => 'Samsung', 'name' => '870 EVO 500GB', 'price_eur' => 59.00, 'price_date' => $estimated,
            'power_w' => 5, 'type' => 'SATA', 'capacity_gb' => 500,
        ],
        [
            'brand' => 'Kingston', 'name' => 'NV3 1TB', 'price_eur' => 65.00, 'price_date' => $estimated,
            'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 1000,
        ],
        [
            'brand' => 'Crucial', 'name' => 'MX500 1TB', 'price_eur' => 75.00, 'price_date' => $estimated,
            'power_w' => 5, 'type' => 'SATA', 'capacity_gb' => 1000,
        ],
        [
            'brand' => 'Western Digital', 'name' => 'WD_BLACK SN850X 1TB', 'price_eur' => 99.00,
            'price_date' => $estimated, 'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 1000,
        ],
        [
            'brand' => 'Samsung', 'name' => '990 PRO 2TB', 'price_eur' => 179.00, 'price_date' => $estimated,
            'power_w' => 5, 'type' => 'NVMe', 'capacity_gb' => 2000,
        ],
    ],

    'psu' => [
        [
            'brand' => 'be quiet!', 'name' => 'System Power 10 450W', 'price_eur' => 52.00,
            'price_date' => $estimated, 'power_w' => 0, 'wattage_w' => 450,
            'efficiency_rating' => '80 Plus Bronze',
        ],
        [
            'brand' => 'Corsair', 'name' => 'CX550', 'price_eur' => 62.00, 'price_date' => $estimated,
            'power_w' => 0, 'wattage_w' => 550, 'efficiency_rating' => '80 Plus Bronze',
        ],
        [
            'brand' => 'Corsair', 'name' => 'RM650e', 'price_eur' => 89.00, 'price_date' => $estimated,
            'power_w' => 0, 'wattage_w' => 650, 'efficiency_rating' => '80 Plus Gold',
        ],
        [
            'brand' => 'Seasonic', 'name' => 'FOCUS GX-750', 'price_eur' => 115.00, 'price_date' => $estimated,
            'power_w' => 0, 'wattage_w' => 750, 'efficiency_rating' => '80 Plus Gold',
        ],
        [
            'brand' => 'be quiet!', 'name' => 'Pure Power 12 M 850W', 'price_eur' => 125.00,
            'price_date' => $estimated, 'power_w' => 0, 'wattage_w' => 850,
            'efficiency_rating' => '80 Plus Gold',
        ],
        [
            'brand' => 'Corsair', 'name' => 'RM1000x', 'price_eur' => 179.00, 'price_date' => $estimated,
            'power_w' => 0, 'wattage_w' => 1000, 'efficiency_rating' => '80 Plus Gold',
        ],
    ],
];
