<?php

namespace App\Model;

use DateTimeImmutable;

readonly class Gpu extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public string $chip,
        public int $vramGb,
        public int $lengthMm,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Gpu;
    }
}
