<?php

namespace App\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityRule;
use App\Compatibility\RuleResult;
use App\Model\Build;
use App\Model\Category;

class SocketRule implements CompatibilityRule
{
    public function name(): string
    {
        return 'socket';
    }

    public function requiredCategories(): array
    {
        return [Category::Cpu, Category::Motherboard];
    }

    public function evaluate(Build $build): RuleResult
    {
        $cpuSocket = $build->cpu()->socket;
        $boardSocket = $build->motherboard()->socket;

        if ($cpuSocket !== $boardSocket) {
            $message = "De processor heeft socket $cpuSocket, maar het moederbord heeft socket $boardSocket.";

            return new RuleResult($this->name(), CheckStatus::Error, $message);
        }

        $message = "De processor en het moederbord hebben allebei socket $cpuSocket.";

        return new RuleResult($this->name(), CheckStatus::Ok, $message);
    }
}
