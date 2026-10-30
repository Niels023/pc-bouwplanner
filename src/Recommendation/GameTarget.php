<?php

namespace App\Recommendation;

use App\Model\Gpu;
use App\Model\Resolution;

// Wat je op de PC wilt kunnen spelen.
readonly class GameTarget
{
    public function __construct(
        public Resolution $resolution,
        public GameType $gameType,
        public int $minFps,
    ) {
    }

    public function fits(Gpu $gpu): bool
    {
        return $gpu->fpsAt($this->resolution) >= $this->minFps;
    }

    public function label(): string
    {
        return "{$this->gameType->label()} op {$this->resolution->label()}";
    }

    public function summary(Gpu $gpu): string
    {
        $fps = $gpu->fpsAt($this->resolution);
        $screen = $this->resolution->label();

        if ($this->gameType === GameType::Light) {
            return "Lichte games lopen hierop vloeiend op $screen. Zware games halen op dit scherm ongeveer $fps FPS.";
        }

        return "Deze build haalt gemiddeld ongeveer $fps FPS in {$this->label()}. "
            . "Vanaf {$this->minFps} FPS (beelden per seconde) voelt een game vloeiend.";
    }
}
