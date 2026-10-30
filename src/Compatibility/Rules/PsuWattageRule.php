<?php

namespace App\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityRule;
use App\Compatibility\RuleResult;
use App\Model\Build;
use App\Model\Category;

class PsuWattageRule implements CompatibilityRule
{
    public function __construct(private int $marginPercent)
    {
    }

    public function name(): string
    {
        return 'psu_wattage';
    }

    public function requiredCategories(): array
    {
        // Zonder processor valt het verbruik zo laag uit dat elke voeding goed lijkt.
        return [Category::Psu, Category::Cpu];
    }

    public function evaluate(Build $build): RuleResult
    {
        $wattage = $build->psu()->wattageW;
        $usage = $build->estimatedPowerW();
        $needed = (int) ceil($usage * (100 + $this->marginPercent) / 100);

        if ($wattage < $usage) {
            $message = "De voeding levert $wattage W, maar de onderdelen verbruiken samen ongeveer $usage W.";

            return new RuleResult($this->name(), CheckStatus::Error, $message);
        }

        if ($wattage < $needed) {
            $message = "De voeding levert $wattage W. Dat is genoeg voor de ongeveer $usage W die de onderdelen "
                . "verbruiken, maar met {$this->marginPercent}% marge is $needed W beter.";

            return new RuleResult($this->name(), CheckStatus::Warning, $message);
        }

        $message = "De voeding levert $wattage W. De onderdelen verbruiken ongeveer $usage W, "
            . "met {$this->marginPercent}% marge is $needed W nodig.";

        return new RuleResult($this->name(), CheckStatus::Ok, $message);
    }
}
