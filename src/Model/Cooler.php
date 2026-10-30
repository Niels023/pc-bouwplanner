<?php

namespace App\Model;

use DateTimeImmutable;

readonly class Cooler extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public int $heightMm,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Cooler;
    }

    public function specs(): array
    {
        return [
            'height_mm' => $this->heightMm,
        ];
    }
}
