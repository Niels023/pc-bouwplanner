<?php

namespace Tests\Unit\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\Rules\GpuLengthRule;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class GpuLengthRuleTest extends TestCase
{
    public function testNoGpuIsFine(): void
    {
        $result = (new GpuLengthRule())->evaluate(new Build(Parts::pcCase()));

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testShorterCardFits(): void
    {
        $build = new Build(Parts::gpu(lengthMm: 300), Parts::pcCase(maxGpuLengthMm: 355));

        $result = (new GpuLengthRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testExactlyMaxLengthFits(): void
    {
        $build = new Build(Parts::gpu(lengthMm: 355), Parts::pcCase(maxGpuLengthMm: 355));

        $result = (new GpuLengthRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testOneMillimeterTooLongGivesErrorWithBothLengths(): void
    {
        $build = new Build(Parts::gpu(lengthMm: 356), Parts::pcCase(maxGpuLengthMm: 355));

        $result = (new GpuLengthRule())->evaluate($build);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('De videokaart is 356 mm lang, maar in deze kast past maximaal 355 mm.', $result->message);
    }
}
