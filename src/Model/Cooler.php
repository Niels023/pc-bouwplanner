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
        public ?int $heightMm,
        public ?int $radiatorMm,
        /** @var string[] */
        public array $sockets,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Cooler;
    }

    public function isLiquidCooler(): bool
    {
        return $this->radiatorMm !== null;
    }

    public function specs(): array
    {
        return [
            'height_mm' => $this->heightMm,
            'radiator_mm' => $this->radiatorMm,
            'sockets' => $this->sockets,
        ];
    }

    public function description(): array
    {
        return [
            $this->isLiquidCooler() ? "Waterkoeling, radiator {$this->radiatorMm} mm" : "Hoogte {$this->heightMm} mm",
            'Past op ' . implode(', ', $this->sockets),
        ];
    }
}
