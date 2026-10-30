<?php

namespace App\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityRule;
use App\Compatibility\RuleResult;
use App\Model\Build;
use App\Model\Category;

class IntegratedGraphicsRule implements CompatibilityRule
{
    public function name(): string
    {
        return 'graphics';
    }

    public function requiredCategories(): array
    {
        return [Category::Cpu];
    }

    public function evaluate(Build $build): RuleResult
    {
        if ($build->hasPart(Category::Gpu)) {
            return new RuleResult($this->name(), CheckStatus::Ok, 'Er is een videokaart gekozen, dus de PC geeft beeld.');
        }

        if ($build->cpu()->hasIntegratedGraphics) {
            $message = 'De processor heeft ingebouwde graphics, dus een videokaart is niet nodig.';

            return new RuleResult($this->name(), CheckStatus::Ok, $message);
        }

        // Pas een fout als de rest gekozen is: daarvoor kan de videokaart nog komen.
        if ($build->hasAllExceptGpu()) {
            $message = 'Deze processor heeft geen ingebouwde graphics. Zonder videokaart krijg je geen beeld.';

            return new RuleResult($this->name(), CheckStatus::Error, $message);
        }

        $message = 'Kies nog een videokaart, want deze processor heeft geen ingebouwde graphics.';

        return new RuleResult($this->name(), CheckStatus::NotChecked, $message);
    }
}
