<?php

namespace App\Model;

use DateTimeImmutable;

readonly class StorageDrive extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public string $type,
        public int $capacityGb,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Storage;
    }

    public function specs(): array
    {
        return [
            'type' => $this->type,
            'capacity_gb' => $this->capacityGb,
        ];
    }

    public function description(): array
    {
        return [
            $this->type,
            "{$this->capacityGb} GB",
        ];
    }
}
