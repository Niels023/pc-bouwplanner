<?php

namespace App\Model;

use DateTimeImmutable;

// "Case" kan niet als klassenaam, dat is een gereserveerd woord in PHP.
readonly class PcCase extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public FormFactor $maxFormFactor,
        public int $maxGpuLengthMm,
        public int $maxCoolerHeightMm,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::PcCase;
    }
}
