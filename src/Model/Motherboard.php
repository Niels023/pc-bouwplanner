<?php

namespace App\Model;

use DateTimeImmutable;

readonly class Motherboard extends Part
{
    public function __construct(
        int $id,
        string $brand,
        string $name,
        int $priceCents,
        DateTimeImmutable $priceDate,
        int $powerW,
        public string $socket,
        public string $ramType,
        public FormFactor $formFactor,
        public string $chipset,
        public int $ramSlots,
    ) {
        parent::__construct($id, $brand, $name, $priceCents, $priceDate, $powerW);
    }

    public function category(): Category
    {
        return Category::Motherboard;
    }

    public function specs(): array
    {
        return [
            'socket' => $this->socket,
            'ram_type' => $this->ramType,
            'form_factor' => $this->formFactor->name,
            'chipset' => $this->chipset,
            'ram_slots' => $this->ramSlots,
        ];
    }
}
