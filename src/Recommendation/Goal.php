<?php

namespace App\Recommendation;

enum Goal: string
{
    case Gaming = 'gaming';
    case Work = 'work';
    case Streaming = 'streaming';

    public function label(): string
    {
        return match ($this) {
            self::Gaming => 'gamen',
            self::Work => 'werken',
            self::Streaming => 'streamen',
        };
    }
}
