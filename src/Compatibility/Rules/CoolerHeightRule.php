<?php

namespace App\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityRule;
use App\Compatibility\RuleResult;
use App\Model\Build;
use App\Model\Category;

class CoolerHeightRule implements CompatibilityRule
{
    public function name(): string
    {
        return 'cooler_height';
    }

    public function requiredCategories(): array
    {
        return [Category::Cooler, Category::PcCase];
    }

    public function evaluate(Build $build): RuleResult
    {
        $height = $build->cooler()->heightMm;
        $maxHeight = $build->pcCase()->maxCoolerHeightMm;

        if ($height > $maxHeight) {
            $message = "De koeler is $height mm hoog, maar in deze kast past maximaal $maxHeight mm.";

            return new RuleResult($this->name(), CheckStatus::Error, $message);
        }

        $message = "De koeler ($height mm) past in deze kast (maximaal $maxHeight mm).";

        return new RuleResult($this->name(), CheckStatus::Ok, $message);
    }
}
