<?php

namespace Tests\Unit\Recommendation;

use App\Model\Resolution;
use App\Recommendation\GameTarget;
use App\Recommendation\GameType;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class GameTargetTest extends TestCase
{
    public function testGpuThatReachesTheFpsFits(): void
    {
        $target = new GameTarget(Resolution::Qhd, GameType::Heavy, 60);

        $this->assertTrue($target->fits(Parts::gpu(fps1440p: 67)));
    }

    public function testGpuWithExactlyTheFpsFits(): void
    {
        $target = new GameTarget(Resolution::Qhd, GameType::Heavy, 60);

        $this->assertTrue($target->fits(Parts::gpu(fps1440p: 60)));
    }

    public function testGpuBelowTheFpsDoesNotFit(): void
    {
        $target = new GameTarget(Resolution::Qhd, GameType::Heavy, 60);

        $this->assertFalse($target->fits(Parts::gpu(fps1440p: 59)));
    }

    public function testOnlyTheChosenResolutionCounts(): void
    {
        $target = new GameTarget(Resolution::Uhd, GameType::Heavy, 60);

        $this->assertFalse($target->fits(Parts::gpu(fps1080p: 200, fps1440p: 150, fps4k: 40)));
    }

    public function testLabelIsInPlainLanguage(): void
    {
        $heavy = new GameTarget(Resolution::Uhd, GameType::Heavy, 60);
        $light = new GameTarget(Resolution::FullHd, GameType::Light, 30);

        $this->assertSame('zware games op 4K', $heavy->label());
        $this->assertSame('lichte games op Full HD', $light->label());
    }

    public function testSummaryForHeavyGamesNamesTheFps(): void
    {
        $target = new GameTarget(Resolution::Qhd, GameType::Heavy, 60);

        $summary = $target->summary(Parts::gpu(fps1440p: 67));

        $this->assertStringContainsString('ongeveer 67 FPS', $summary);
        $this->assertStringContainsString('zware games op 1440p', $summary);
    }

    public function testSummaryForLightGamesSaysTheyRunSmoothly(): void
    {
        $target = new GameTarget(Resolution::Uhd, GameType::Light, 30);

        $summary = $target->summary(Parts::gpu(fps4k: 35));

        $this->assertStringContainsString('Lichte games lopen', $summary);
        $this->assertStringContainsString('4K', $summary);
    }
}
