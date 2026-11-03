<?php

namespace Tests\Unit\Compatibility;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityChecker;
use App\Compatibility\RuleResult;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

// Elke compatibiliteitsregel apart, met de randgevallen.
class RulesTest extends TestCase
{
    public function testSameSocketIsOk(): void
    {
        $build = new Build(Parts::cpu(socket: 'AM5'), Parts::motherboard(socket: 'AM5'));

        $result = $this->resultFor($build, 'socket');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testDifferentSocketGivesErrorWithBothSockets(): void
    {
        $build = new Build(Parts::cpu(socket: 'AM5'), Parts::motherboard(socket: 'AM4'));

        $result = $this->resultFor($build, 'socket');

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('De processor heeft socket AM5, maar het moederbord heeft socket AM4.', $result->message);
    }

    public function testSameRamTypeIsOk(): void
    {
        $build = new Build(Parts::motherboard(ramType: 'DDR5'), Parts::ram(ramType: 'DDR5'));

        $result = $this->resultFor($build, 'ram_type');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testDifferentRamTypeGivesErrorWithBothTypes(): void
    {
        $build = new Build(Parts::motherboard(ramType: 'DDR5'), Parts::ram(ramType: 'DDR4'));

        $result = $this->resultFor($build, 'ram_type');

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('Het geheugen is DDR4, maar het moederbord ondersteunt alleen DDR5.', $result->message);
    }

    public function testSmallerBoardFitsInBiggerCase(): void
    {
        $build = new Build(
            Parts::motherboard(formFactor: Parts::microAtx()),
            Parts::pcCase(maxFormFactor: Parts::atx()),
        );

        $result = $this->resultFor($build, 'form_factor');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testSameSizeFits(): void
    {
        $build = new Build(
            Parts::motherboard(formFactor: Parts::atx()),
            Parts::pcCase(maxFormFactor: Parts::atx()),
        );

        $result = $this->resultFor($build, 'form_factor');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testBiggerBoardGivesErrorWithBothSizes(): void
    {
        $build = new Build(
            Parts::motherboard(formFactor: Parts::atx()),
            Parts::pcCase(maxFormFactor: Parts::miniItx()),
        );

        $result = $this->resultFor($build, 'form_factor');

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('Het moederbord is ATX, maar in deze kast past maximaal Mini-ITX.', $result->message);
    }

    public function testEnoughWattageIsOk(): void
    {
        $result = $this->psuResult(usageW: 450, wattageW: 650);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testExactlyUsagePlusMarginIsOk(): void
    {
        $result = $this->psuResult(usageW: 450, wattageW: 585);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testOneWattBelowMarginGivesWarning(): void
    {
        $result = $this->psuResult(usageW: 450, wattageW: 584);

        $this->assertSame(CheckStatus::Warning, $result->status);
        $this->assertStringContainsString('585 W', $result->message);
    }

    public function testExactlyUsageGivesWarning(): void
    {
        $result = $this->psuResult(usageW: 450, wattageW: 450);

        $this->assertSame(CheckStatus::Warning, $result->status);
    }

    public function testOneWattBelowUsageGivesError(): void
    {
        $result = $this->psuResult(usageW: 450, wattageW: 449);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertStringContainsString('449 W', $result->message);
        $this->assertStringContainsString('450 W', $result->message);
    }

    public function testNeededWattageIsRoundedUp(): void
    {
        // 583 W met 30% marge is 757,9 W, dus 757 W is net te weinig
        $this->assertSame(CheckStatus::Ok, $this->psuResult(usageW: 583, wattageW: 758)->status);
        $this->assertSame(CheckStatus::Warning, $this->psuResult(usageW: 583, wattageW: 757)->status);
    }

    public function testMarginComesFromConstructor(): void
    {
        $result = $this->psuResult(usageW: 450, wattageW: 450, marginPercent: 0);

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

        $result = $this->resultFor($build, 'psu_wattage');

        $this->assertSame(CheckStatus::Error, $result->status);
    }

    private function psuResult(int $usageW, int $wattageW, int $marginPercent = 30): RuleResult
    {
        $build = new Build(Parts::cpu(powerW: $usageW), Parts::psu(wattageW: $wattageW));

        return $this->resultFor($build, 'psu_wattage', $marginPercent);
    }

    public function testNoGpuIsFine(): void
    {
        $result = $this->resultFor(new Build(Parts::pcCase()), 'gpu_length');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testShorterCardFits(): void
    {
        $build = new Build(Parts::gpu(lengthMm: 300), Parts::pcCase(maxGpuLengthMm: 355));

        $result = $this->resultFor($build, 'gpu_length');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testExactlyMaxLengthFits(): void
    {
        $build = new Build(Parts::gpu(lengthMm: 355), Parts::pcCase(maxGpuLengthMm: 355));

        $result = $this->resultFor($build, 'gpu_length');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testOneMillimeterTooLongGivesErrorWithBothLengths(): void
    {
        $build = new Build(Parts::gpu(lengthMm: 356), Parts::pcCase(maxGpuLengthMm: 355));

        $result = $this->resultFor($build, 'gpu_length');

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('De videokaart is 356 mm lang, maar in deze kast past maximaal 355 mm.', $result->message);
    }

    public function testLowerCoolerFits(): void
    {
        $build = new Build(Parts::cooler(heightMm: 155), Parts::pcCase(maxCoolerHeightMm: 170));

        $result = $this->resultFor($build, 'cooler_height');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testExactlyMaxHeightFits(): void
    {
        $build = new Build(Parts::cooler(heightMm: 70), Parts::pcCase(maxCoolerHeightMm: 70));

        $result = $this->resultFor($build, 'cooler_height');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testOneMillimeterTooHighGivesErrorWithBothHeights(): void
    {
        $build = new Build(Parts::cooler(heightMm: 160), Parts::pcCase(maxCoolerHeightMm: 159));

        $result = $this->resultFor($build, 'cooler_height');

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('De koeler is 160 mm hoog, maar in deze kast past maximaal 159 mm.', $result->message);
    }

    public function testBuildWithGpuIsOk(): void
    {
        $build = new Build(Parts::cpu(hasIntegratedGraphics: false), Parts::gpu());

        $result = $this->resultFor($build, 'graphics');

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testNoGpuIsOkWithIntegratedGraphics(): void
    {
        $build = new Build(Parts::cpu(hasIntegratedGraphics: true));

        $result = $this->resultFor($build, 'graphics');

        $this->assertSame(CheckStatus::Ok, $result->status);
        $this->assertStringContainsString('ingebouwde graphics', $result->message);
    }

    public function testNoGpuYetWithoutIntegratedGraphicsIsNotAFaultYet(): void
    {
        $build = new Build(Parts::cpu(hasIntegratedGraphics: false), Parts::motherboard());

        $result = $this->resultFor($build, 'graphics');

        $this->assertSame(CheckStatus::NotChecked, $result->status);
        $this->assertStringContainsString('videokaart', $result->message);
    }

    public function testEverythingChosenExceptGpuWithoutIntegratedGraphicsIsAnError(): void
    {
        $build = new Build(
            Parts::cpu(hasIntegratedGraphics: false),
            Parts::motherboard(),
            Parts::ram(),
            Parts::cooler(),
            Parts::pcCase(),
            Parts::storage(),
            Parts::psu(),
        );

        $result = $this->resultFor($build, 'graphics');

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertStringContainsString('geen beeld', $result->message);
    }

    private function resultFor(Build $build, string $rule, int $psuMarginPercent = 30): RuleResult
    {
        foreach ((new CompatibilityChecker($psuMarginPercent))->check($build)->results as $result) {
            if ($result->ruleName === $rule) {
                return $result;
            }
        }

        $this->fail("Regel $rule bestaat niet");
    }
}
