<?php

namespace App\Model;

use DateTimeImmutable;

abstract readonly class Part
{
    public function __construct(
        public int $id,
        public string $brand,
        public string $name,
        public int $priceCents,
        public DateTimeImmutable $priceDate,
        public int $powerW,
    ) {
    }

    abstract public function category(): Category;
}
