<?php

namespace Tests\Unit\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\Rules\IntegratedGraphicsRule;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class IntegratedGraphicsRuleTest extends TestCase
{
    public function testBuildWithGpuIsOk(): void
    {
        $build = new Build(Parts::cpu(hasIntegratedGraphics: false), Parts::gpu());

        $result = (new IntegratedGraphicsRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testNoGpuIsOkWithIntegratedGraphics(): void
    {
        $build = new Build(Parts::cpu(hasIntegratedGraphics: true));

        $result = (new IntegratedGraphicsRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
        $this->assertStringContainsString('ingebouwde graphics', $result->message);
    }

    public function testNoGpuYetWithoutIntegratedGraphicsIsNotAFaultYet(): void
    {
        $build = new Build(Parts::cpu(hasIntegratedGraphics: false), Parts::motherboard());

        $result = (new IntegratedGraphicsRule())->evaluate($build);

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

        $result = (new IntegratedGraphicsRule())->evaluate($build);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertStringContainsString('geen beeld', $result->message);
    }
}
