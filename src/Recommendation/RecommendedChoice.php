<?php

namespace App\Recommendation;

use App\Model\Part;

readonly class RecommendedChoice
{
    public function __construct(
        public Part $part,
        public int $categoryBudgetCents,
        public string $reason,
    ) {
    }
}
