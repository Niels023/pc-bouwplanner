<?php

namespace App\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityRule;
use App\Compatibility\RuleResult;
use App\Model\Build;
use App\Model\Category;

class RamTypeRule implements CompatibilityRule
{
    public function name(): string
    {
        return 'ram_type';
    }

    public function requiredCategories(): array
    {
        return [Category::Motherboard, Category::Ram];
    }

    public function evaluate(Build $build): RuleResult
    {
        $ramType = $build->ram()->ramType;
        $boardRamType = $build->motherboard()->ramType;

        if ($ramType !== $boardRamType) {
            $message = "Het geheugen is $ramType, maar het moederbord ondersteunt alleen $boardRamType.";

            return new RuleResult($this->name(), CheckStatus::Error, $message);
        }

        $message = "Het geheugen en het moederbord gebruiken allebei $ramType.";

        return new RuleResult($this->name(), CheckStatus::Ok, $message);
    }
}
