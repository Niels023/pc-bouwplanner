<?php

namespace Tests\Support;

use App\Model\Cooler;
use App\Model\Cpu;
use App\Model\FormFactor;
use App\Model\Gpu;
use App\Model\Motherboard;
use App\Model\PcCase;
use App\Model\Psu;
use App\Model\RamKit;
use App\Model\StorageDrive;
use DateTimeImmutable;

// Maakt onderdelen voor tests. Je geeft alleen op wat voor de test belangrijk is.
class Parts
{
    private const PRICE_DATE = '2026-10-28';

    public static function atx(): FormFactor
    {
        return new FormFactor('ATX', 3);
    }

    public static function microAtx(): FormFactor
    {
        return new FormFactor('Micro-ATX', 2);
    }

    public static function miniItx(): FormFactor
    {
        return new FormFactor('Mini-ITX', 1);
    }

    public static function cpu(string $socket = 'AM5', int $powerW = 100, int $priceCents = 20000): Cpu
    {
        return new Cpu(1, 'AMD', 'Test CPU', $priceCents, self::date(), $powerW, $socket, 6, 12, 5.1, true);
    }

    public static function motherboard(
        string $socket = 'AM5',
        string $ramType = 'DDR5',
        ?FormFactor $formFactor = null,
        int $powerW = 40,
        int $priceCents = 15000,
    ): Motherboard {
        $formFactor ??= self::atx();

        return new Motherboard(
            2,
            'MSI',
            'Test bord',
            $priceCents,
            self::date(),
            $powerW,
            $socket,
            $ramType,
            $formFactor,
            'B850',
            4,
        );
    }

    public static function ram(string $ramType = 'DDR5', int $powerW = 6): RamKit
    {
        return new RamKit(3, 'Kingston', 'Test RAM', 10000, self::date(), $powerW, $ramType, 32, 2, 6000);
    }

    public static function gpu(int $lengthMm = 250, int $powerW = 200): Gpu
    {
        return new Gpu(4, 'MSI', 'Test GPU', 50000, self::date(), $powerW, 'Test chip', 12, $lengthMm);
    }

    public static function cooler(int $heightMm = 150, int $powerW = 5): Cooler
    {
        return new Cooler(5, 'Noctua', 'Test koeler', 5000, self::date(), $powerW, $heightMm);
    }

    public static function pcCase(
        ?FormFactor $maxFormFactor = null,
        int $maxGpuLengthMm = 350,
        int $maxCoolerHeightMm = 170,
        int $powerW = 5,
    ): PcCase {
        $maxFormFactor ??= self::atx();

        return new PcCase(
            6,
            'Fractal Design',
            'Test kast',
            10000,
            self::date(),
            $powerW,
            $maxFormFactor,
            $maxGpuLengthMm,
            $maxCoolerHeightMm,
        );
    }

    public static function storage(int $powerW = 5): StorageDrive
    {
        return new StorageDrive(7, 'Samsung', 'Test SSD', 10000, self::date(), $powerW, 'NVMe', 1000);
    }

    public static function psu(int $wattageW = 650): Psu
    {
        return new Psu(8, 'Corsair', 'Test voeding', 8000, self::date(), 0, $wattageW, '80 Plus Gold');
    }

    private static function date(): DateTimeImmutable
    {
        return new DateTimeImmutable(self::PRICE_DATE);
    }
}
