<?php

namespace App\Model;

enum Category: string
{
    case Cpu = 'cpu';
    case Motherboard = 'motherboard';
    case Ram = 'ram';
    case Gpu = 'gpu';
    case Cooler = 'cooler';
    case PcCase = 'case';
    case Storage = 'storage';
    case Psu = 'psu';

    public function label(): string
    {
        return match ($this) {
            self::Cpu => 'processor',
            self::Motherboard => 'moederbord',
            self::Ram => 'geheugen',
            self::Gpu => 'videokaart',
            self::Cooler => 'koeler',
            self::PcCase => 'kast',
            self::Storage => 'opslag',
            self::Psu => 'voeding',
        };
    }

    public function stepNumber(): int
    {
        return array_search($this, self::cases(), true) + 1;
    }

    public function previous(): ?self
    {
        return self::cases()[$this->stepNumber() - 2] ?? null;
    }

    public function next(): ?self
    {
        return self::cases()[$this->stepNumber()] ?? null;
    }

    public function withArticle(): string
    {
        return match ($this) {
            self::Cpu => 'een processor',
            self::Motherboard => 'een moederbord',
            self::Ram => 'geheugen',
            self::Gpu => 'een videokaart',
            self::Cooler => 'een koeler',
            self::PcCase => 'een kast',
            self::Storage => 'opslag',
            self::Psu => 'een voeding',
        };
    }
}
