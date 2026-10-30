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

    public function specs(): array
    {
        return [
            'max_form_factor' => $this->maxFormFactor->name,
            'max_gpu_length_mm' => $this->maxGpuLengthMm,
            'max_cooler_height_mm' => $this->maxCoolerHeightMm,
        ];
    }

    public function description(): array
    {
        return [
            "Moederbord tot {$this->maxFormFactor->name}",
            "Videokaart tot {$this->maxGpuLengthMm} mm",
            "Koeler tot {$this->maxCoolerHeightMm} mm",
        ];
    }
}
