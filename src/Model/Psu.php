<?php

namespace App\Model;

use DateTimeImmutable;

readonly class Psu extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public int $wattageW,
        public string $efficiencyRating,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Psu;
    }

    public function specs(): array
    {
        return [
            'wattage_w' => $this->wattageW,
            'efficiency_rating' => $this->efficiencyRating,
        ];
    }
}
