<?php

namespace App\Compatibility;

use App\Compatibility\Rules\CoolerHeightRule;
use App\Compatibility\Rules\FormFactorRule;
use App\Compatibility\Rules\GpuLengthRule;
use App\Compatibility\Rules\PsuWattageRule;
use App\Compatibility\Rules\RamTypeRule;
use App\Compatibility\Rules\SocketRule;
use App\Model\Build;
use App\Model\Category;

class CompatibilityChecker
{
    /** @param CompatibilityRule[] $rules */
    public function __construct(private array $rules)
    {
    }

    public static function withAllRules(int $psuMarginPercent): self
    {
        return new self([
            new SocketRule(),
            new RamTypeRule(),
            new FormFactorRule(),
            new PsuWattageRule($psuMarginPercent),
            new GpuLengthRule(),
            new CoolerHeightRule(),
        ]);
    }

    public function check(Build $build): CheckReport
    {
        $results = array_map(fn (CompatibilityRule $rule) => $this->runRule($rule, $build), $this->rules);

        return new CheckReport($results, $build->isComplete(), $build->totalPriceCents(), $build->estimatedPowerW());
    }

    private function runRule(CompatibilityRule $rule, Build $build): RuleResult
    {
        $missing = array_filter(
            $rule->requiredCategories(),
            fn (Category $category) => !$build->hasPart($category),
        );

        if ($missing !== []) {
            $names = array_map(fn (Category $category) => $category->withArticle(), $missing);
            $message = 'Kies eerst ' . implode(' en ', $names) . '.';

            return new RuleResult($rule->name(), CheckStatus::NotChecked, $message);
        }

        return $rule->evaluate($build);
    }
}
