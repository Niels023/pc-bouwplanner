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
