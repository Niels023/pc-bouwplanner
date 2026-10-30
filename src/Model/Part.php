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

    /** @return array<string, mixed> */
    abstract public function specs(): array;

    /** @return string[] De specificaties in gewone taal, voor op de kaartjes */
    abstract public function description(): array;

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category()->value,
            'brand' => $this->brand,
            'name' => $this->name,
            'price_eur' => $this->priceCents / 100,
            'price_date' => $this->priceDate->format('Y-m-d'),
            'power_w' => $this->powerW,
            'specs' => $this->specs(),
        ];
    }
}
