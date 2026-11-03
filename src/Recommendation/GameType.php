<?php

namespace App\Recommendation;

enum GameType: string
{
    case Light = 'light';
    case Heavy = 'heavy';

    public function label(): string
    {
        return match ($this) {
            self::Light => 'lichte games',
            self::Heavy => 'zware games',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Light => 'Zoals Fortnite, Minecraft, Valorant, Rocket League of League of Legends',
            self::Heavy => 'Zoals Cyberpunk 2077, Call of Duty, Red Dead Redemption 2 of Hogwarts Legacy',
        };
    }
}
