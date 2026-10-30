<?php

namespace Tests\Unit\Model;

use App\Model\Build;
use App\Model\Category;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class BuildTest extends TestCase
{
    public function testEmptyBuildCostsNothingAndUsesNoPower(): void
    {
        $build = new Build();

        $this->assertSame(0, $build->totalPriceCents());
        $this->assertSame(0, $build->estimatedPowerW());
    }

    public function testTotalPriceIsSumOfAllParts(): void
    {
        $build = new Build(Parts::cpu(priceCents: 12496), Parts::motherboard(priceCents: 9548));

        $this->assertSame(22044, $build->totalPriceCents());
    }

    public function testEstimatedPowerIsSumOfAllParts(): void
    {
        $build = new Build(Parts::cpu(powerW: 88), Parts::gpu(powerW: 145), Parts::ram(powerW: 6));

        $this->assertSame(239, $build->estimatedPowerW());
    }

    public function testHasPartOnlyForChosenCategories(): void
    {
        $build = new Build(Parts::cpu());

        $this->assertTrue($build->hasPart(Category::Cpu));
        $this->assertFalse($build->hasPart(Category::Gpu));
    }

    public function testWithPartAddsPartAndLeavesOriginalAlone(): void
    {
        $build = new Build(Parts::cpu());

        $bigger = $build->withPart(Parts::gpu());

        $this->assertTrue($bigger->hasPart(Category::Gpu));
        $this->assertFalse($build->hasPart(Category::Gpu));
    }

    public function testWithPartReplacesPartInSameCategory(): void
    {
        $build = (new Build(Parts::cpu(socket: 'AM4')))->withPart(Parts::cpu(socket: 'AM5'));

        $this->assertSame('AM5', $build->cpu()->socket);
        $this->assertCount(1, $build->parts());
    }

    public function testBuildWithoutPartsIsEmpty(): void
    {
        $this->assertTrue((new Build())->isEmpty());
        $this->assertFalse((new Build(Parts::cpu()))->isEmpty());
    }

    public function testPartsGivesAllChosenParts(): void
    {
        $cpu = Parts::cpu();
        $gpu = Parts::gpu();

        $this->assertSame([$cpu, $gpu], (new Build($cpu, $gpu))->parts());
    }

    public function testBuildIsCompleteWithAllEightParts(): void
    {
        $build = new Build(
            Parts::cpu(),
            Parts::motherboard(),
            Parts::ram(),
            Parts::gpu(),
            Parts::cooler(),
            Parts::pcCase(),
            Parts::storage(),
            Parts::psu(),
        );

        $this->assertTrue($build->isComplete());
    }

    public function testBuildWithoutGpuIsCompleteWithIntegratedGraphics(): void
    {
        $build = new Build(
            Parts::cpu(hasIntegratedGraphics: true),
            Parts::motherboard(),
            Parts::ram(),
            Parts::cooler(),
            Parts::pcCase(),
            Parts::storage(),
            Parts::psu(),
        );

        $this->assertTrue($build->isComplete());
    }

    public function testBuildWithoutGpuIsNotCompleteWithoutIntegratedGraphics(): void
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

        $this->assertFalse($build->isComplete());
    }

    public function testBuildIsNotCompleteWithMissingPart(): void
    {
        $build = new Build(
            Parts::cpu(),
            Parts::motherboard(),
            Parts::ram(),
            Parts::gpu(),
            Parts::cooler(),
            Parts::pcCase(),
            Parts::psu(),
        );

        $this->assertFalse($build->isComplete());
    }

    public function testNewPartReplacesPartInSameCategory(): void
    {
        $build = new Build(Parts::cpu(socket: 'AM4'), Parts::cpu(socket: 'AM5'));

        $this->assertSame('AM5', $build->cpu()->socket);
    }

    public function testStorageCountsTowardsTotals(): void
    {
        $build = new Build(Parts::storage(powerW: 5));

        $this->assertSame(10000, $build->totalPriceCents());
        $this->assertSame(5, $build->estimatedPowerW());
    }
}
