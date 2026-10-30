<?php

namespace App\Model;

use DateTimeImmutable;

readonly class RamKit extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public string $ramType,
        public int $capacityGb,
        public int $moduleCount,
        public int $speedMts,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Ram;
    }
}
