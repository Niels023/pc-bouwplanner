<?php

namespace App\Model;

readonly class Build
{
    /** @var array<string, Part> */
    private array $parts;

    public function __construct(Part ...$parts)
    {
        $byCategory = [];
        foreach ($parts as $part) {
            $byCategory[$part->category()->value] = $part;
        }
        $this->parts = $byCategory;
    }

    public function hasPart(Category $category): bool
    {
        return isset($this->parts[$category->value]);
    }

    public function isEmpty(): bool
    {
        return $this->parts === [];
    }

    public function isComplete(): bool
    {
        return count($this->parts) === count(Category::cases());
    }

    /** @return Part[] */
    public function parts(): array
    {
        return array_values($this->parts);
    }

    public function totalPriceCents(): int
    {
        return array_sum(array_map(fn (Part $part) => $part->priceCents, $this->parts));
    }

    public function estimatedPowerW(): int
    {
        return array_sum(array_map(fn (Part $part) => $part->powerW, $this->parts));
    }

    public function cpu(): Cpu
    {
        return $this->parts[Category::Cpu->value];
    }

    public function motherboard(): Motherboard
    {
        return $this->parts[Category::Motherboard->value];
    }

    public function ram(): RamKit
    {
        return $this->parts[Category::Ram->value];
    }

    public function gpu(): Gpu
    {
        return $this->parts[Category::Gpu->value];
    }

    public function cooler(): Cooler
    {
        return $this->parts[Category::Cooler->value];
    }

    public function pcCase(): PcCase
    {
        return $this->parts[Category::PcCase->value];
    }

    public function psu(): Psu
    {
        return $this->parts[Category::Psu->value];
    }
}
