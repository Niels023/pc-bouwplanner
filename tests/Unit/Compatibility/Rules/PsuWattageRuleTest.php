<?php

namespace Tests\Unit\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\RuleResult;
use App\Compatibility\Rules\PsuWattageRule;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class PsuWattageRuleTest extends TestCase
{
    public function testEnoughWattageIsOk(): void
    {
        $result = $this->check(usageW: 450, wattageW: 650);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testExactlyUsagePlusMarginIsOk(): void
    {
        $result = $this->check(usageW: 450, wattageW: 585);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testOneWattBelowMarginGivesWarning(): void
    {
        $result = $this->check(usageW: 450, wattageW: 584);

        $this->assertSame(CheckStatus::Warning, $result->status);
        $this->assertStringContainsString('585 W', $result->message);
    }

    public function testExactlyUsageGivesWarning(): void
    {
        $result = $this->check(usageW: 450, wattageW: 450);

        $this->assertSame(CheckStatus::Warning, $result->status);
    }

    public function testOneWattBelowUsageGivesError(): void
    {
        $result = $this->check(usageW: 450, wattageW: 449);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertStringContainsString('449 W', $result->message);
        $this->assertStringContainsString('450 W', $result->message);
    }

    public function testNeededWattageIsRoundedUp(): void
    {
        // 583 W met 30% marge is 757,9 W, dus 757 W is net te weinig
        $this->assertSame(CheckStatus::Ok, $this->check(usageW: 583, wattageW: 758)->status);
        $this->assertSame(CheckStatus::Warning, $this->check(usageW: 583, wattageW: 757)->status);
    }

    public function testMarginComesFromConstructor(): void
    {
        $result = $this->check(usageW: 450, wattageW: 450, marginPercent: 0);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testUsageCountsAllChosenParts(): void
    {
        $build = new Build(
            Parts::cpu(powerW: 200),
            Parts::gpu(powerW: 200),
            Parts::motherboard(powerW: 50),
            Parts::psu(wattageW: 449),
        );

        $result = (new PsuWattageRule(30))->evaluate($build);

        $this->assertSame(CheckStatus::Error, $result->status);
    }

    private function check(int $usageW, int $wattageW, int $marginPercent = 30): RuleResult
    {
        $build = new Build(Parts::cpu(powerW: $usageW), Parts::psu(wattageW: $wattageW));

        return (new PsuWattageRule($marginPercent))->evaluate($build);
    }
}
