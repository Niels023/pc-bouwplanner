<?php

namespace App\Recommendation;

use App\Model\Build;

readonly class Recommendation
{
    /** @param RecommendedChoice[] $choices */
    public function __construct(
        public Build $build,
        public array $choices,
        public int $budgetCents,
        public bool $budgetGiven = true,
        public ?GameTarget $target = null,
    ) {
    }

    public function leftoverCents(): int
    {
        return $this->budgetCents - $this->build->totalPriceCents();
    }

    public function summary(): ?string
    {
        return $this->target?->summary($this->build->gpu());
    }
}
