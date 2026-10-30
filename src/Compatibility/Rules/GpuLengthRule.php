<?php

namespace App\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityRule;
use App\Compatibility\RuleResult;
use App\Model\Build;
use App\Model\Category;

class GpuLengthRule implements CompatibilityRule
{
    public function name(): string
    {
        return 'gpu_length';
    }

    public function requiredCategories(): array
    {
        return [Category::Gpu, Category::PcCase];
    }

    public function evaluate(Build $build): RuleResult
    {
        $length = $build->gpu()->lengthMm;
        $maxLength = $build->pcCase()->maxGpuLengthMm;

        if ($length > $maxLength) {
            $message = "De videokaart is $length mm lang, maar in deze kast past maximaal $maxLength mm.";

            return new RuleResult($this->name(), CheckStatus::Error, $message);
        }

        $message = "De videokaart ($length mm) past in deze kast (maximaal $maxLength mm).";

        return new RuleResult($this->name(), CheckStatus::Ok, $message);
    }
}
