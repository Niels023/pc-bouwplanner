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

    public function specs(): array
    {
        return [
            'ram_type' => $this->ramType,
            'capacity_gb' => $this->capacityGb,
            'module_count' => $this->moduleCount,
            'speed_mts' => $this->speedMts,
        ];
    }

    public function description(): array
    {
        return [
            $this->ramType,
            "{$this->capacityGb} GB in {$this->moduleCount} modules",
            "{$this->speedMts} MT/s",
        ];
    }
}
