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

    public static function cpu(
        string $socket = 'AM5',
        int $powerW = 100,
        int $priceCents = 20000,
        bool $hasIntegratedGraphics = true,
    ): Cpu {
        $date = self::date();

        return new Cpu(1, 'AMD', 'Test CPU', $priceCents, $date, $powerW, $socket, 6, 12, 5.1, $hasIntegratedGraphics);
    }

    public static function motherboard(
        string $socket = 'AM5',
        string $ramType = 'DDR5',
        ?FormFactor $formFactor = null,
        int $powerW = 40,
        int $priceCents = 15000,
        int $ramSlots = 4,
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
            $ramSlots,
        );
    }

    public static function ram(
        string $ramType = 'DDR5',
        int $powerW = 6,
        int $capacityGb = 32,
        int $priceCents = 10000,
    ): RamKit {
        $date = self::date();

        return new RamKit(3, 'Kingston', 'Test RAM', $priceCents, $date, $powerW, $ramType, $capacityGb, 2, 6000);
    }

    public static function gpu(int $lengthMm = 250, int $powerW = 200, int $priceCents = 50000): Gpu
    {
        return new Gpu(4, 'MSI', 'Test GPU', $priceCents, self::date(), $powerW, 'Test chip', 12, $lengthMm);
    }

    public static function cooler(int $heightMm = 150, int $powerW = 5, int $priceCents = 5000): Cooler
    {
        return new Cooler(5, 'Noctua', 'Test koeler', $priceCents, self::date(), $powerW, $heightMm);
    }

    public static function pcCase(
        ?FormFactor $maxFormFactor = null,
        int $maxGpuLengthMm = 350,
        int $maxCoolerHeightMm = 170,
        int $powerW = 5,
        int $priceCents = 10000,
    ): PcCase {
        $maxFormFactor ??= self::atx();

        return new PcCase(
            6,
            'Fractal Design',
            'Test kast',
            $priceCents,
            self::date(),
            $powerW,
            $maxFormFactor,
            $maxGpuLengthMm,
            $maxCoolerHeightMm,
        );
    }

    public static function storage(int $powerW = 5, int $capacityGb = 1000, int $priceCents = 10000): StorageDrive
    {
        return new StorageDrive(7, 'Samsung', 'Test SSD', $priceCents, self::date(), $powerW, 'NVMe', $capacityGb);
    }

    public static function psu(int $wattageW = 650, int $priceCents = 8000): Psu
    {
        return new Psu(8, 'Corsair', 'Test voeding', $priceCents, self::date(), 0, $wattageW, '80 Plus Gold');
    }

    private static function date(): DateTimeImmutable
    {
        return new DateTimeImmutable(self::PRICE_DATE);
    }
}
