<?php

namespace App\Model;

use DateTimeImmutable;

readonly class Cpu extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public string $socket,
        public int $cores,
        public int $threads,
        public float $boostClockGhz,
        public bool $hasIntegratedGraphics,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Cpu;
    }

    public function specs(): array
    {
        return [
            'socket' => $this->socket,
            'cores' => $this->cores,
            'threads' => $this->threads,
            'boost_clock_ghz' => $this->boostClockGhz,
            'has_integrated_graphics' => $this->hasIntegratedGraphics,
        ];
    }
}
