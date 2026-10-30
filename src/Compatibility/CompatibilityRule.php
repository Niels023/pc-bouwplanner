<?php

namespace App\Compatibility;

use App\Model\Build;
use App\Model\Category;

interface CompatibilityRule
{
    public function name(): string;

    /** @return Category[] */
    public function requiredCategories(): array;

    // Wordt alleen aangeroepen als alle onderdelen uit requiredCategories() gekozen zijn.
    public function evaluate(Build $build): RuleResult;
}
