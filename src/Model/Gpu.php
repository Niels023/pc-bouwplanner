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
        public int $fps1080p,
        public int $fps1440p,
        public int $fps4k,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Gpu;
    }

    public function fpsAt(Resolution $resolution): int
    {
        return match ($resolution) {
            Resolution::FullHd => $this->fps1080p,
            Resolution::Qhd => $this->fps1440p,
            Resolution::Uhd => $this->fps4k,
        };
    }

    public function specs(): array
    {
        return [
            'chip' => $this->chip,
            'vram_gb' => $this->vramGb,
            'length_mm' => $this->lengthMm,
            'fps_1080p' => $this->fps1080p,
            'fps_1440p' => $this->fps1440p,
            'fps_4k' => $this->fps4k,
        ];
    }

    public function description(): array
    {
        return [
            $this->chip,
            "{$this->vramGb} GB videogeheugen",
            "Lengte {$this->lengthMm} mm",
            "Zware games: {$this->fps1080p} FPS op Full HD, {$this->fps1440p} op 1440p, {$this->fps4k} op 4K",
        ];
    }
}
