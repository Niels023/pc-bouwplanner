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

    public function description(): string
    {
        return match ($this) {
            self::Gaming => 'Games spelen, met een videokaart die past bij je scherm',
            self::Work => 'Kantoor, school en programmeren, zonder losse videokaart',
            self::Streaming => 'Gamen en tegelijk uitzenden, met extra geheugen',
        };
    }
}
