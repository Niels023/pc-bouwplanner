<?php

namespace App\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityRule;
use App\Compatibility\RuleResult;
use App\Model\Build;
use App\Model\Category;

class FormFactorRule implements CompatibilityRule
{
    public function name(): string
    {
        return 'form_factor';
    }

    public function requiredCategories(): array
    {
        return [Category::Motherboard, Category::PcCase];
    }

    public function evaluate(Build $build): RuleResult
    {
        $board = $build->motherboard()->formFactor;
        $case = $build->pcCase()->maxFormFactor;

        if (!$board->fitsIn($case)) {
            $message = "Het moederbord is {$board->name}, maar in deze kast past maximaal {$case->name}.";

            return new RuleResult($this->name(), CheckStatus::Error, $message);
        }

        $message = "Het moederbord ({$board->name}) past in deze kast (maximaal {$case->name}).";

        return new RuleResult($this->name(), CheckStatus::Ok, $message);
    }
}
