<?php

namespace Tests\Unit\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\Rules\CoolerHeightRule;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class CoolerHeightRuleTest extends TestCase
{
    public function testLowerCoolerFits(): void
    {
        $build = new Build(Parts::cooler(heightMm: 155), Parts::pcCase(maxCoolerHeightMm: 170));

        $result = (new CoolerHeightRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testExactlyMaxHeightFits(): void
    {
        $build = new Build(Parts::cooler(heightMm: 70), Parts::pcCase(maxCoolerHeightMm: 70));

        $result = (new CoolerHeightRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testOneMillimeterTooHighGivesErrorWithBothHeights(): void
    {
        $build = new Build(Parts::cooler(heightMm: 160), Parts::pcCase(maxCoolerHeightMm: 159));

        $result = (new CoolerHeightRule())->evaluate($build);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('De koeler is 160 mm hoog, maar in deze kast past maximaal 159 mm.', $result->message);
    }
}
