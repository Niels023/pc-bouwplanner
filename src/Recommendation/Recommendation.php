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
    ) {
    }

    public function leftoverCents(): int
    {
        return $this->budgetCents - $this->build->totalPriceCents();
    }
}
