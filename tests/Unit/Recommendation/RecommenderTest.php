<?php

namespace Tests\Unit\Recommendation;

use App\Compatibility\CompatibilityChecker;
use App\Model\Category;
use App\Recommendation\BudgetTooLowException;
use App\Recommendation\Goal;
use App\Recommendation\Recommendation;
use App\Recommendation\RecommendedChoice;
use App\Recommendation\Recommender;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class RecommenderTest extends TestCase
{
    private const SHARES = [
        'cpu' => 15,
        'motherboard' => 10,
        'ram' => 15,
        'gpu' => 40,
        'cooler' => 5,
        'case' => 5,
        'storage' => 5,
        'psu' => 5,
    ];

    private const GOALS = [
        'gaming' => [
            'shares' => self::SHARES,
            'min_ram_gb' => 16,
            'min_storage_gb' => 1000,
            'min_ram_slots' => 4,
            'upgrade_order' => ['gpu', 'cpu', 'ram', 'storage'],
        ],
        'work' => [
            'shares' => self::SHARES,
            'min_ram_gb' => 16,
            'min_storage_gb' => 500,
            'min_ram_slots' => 4,
            'upgrade_order' => ['cpu', 'ram', 'storage'],
        ],
        'streaming' => [
            'shares' => self::SHARES,
            'min_ram_gb' => 32,
            'min_storage_gb' => 1000,
            'min_ram_slots' => 4,
            'upgrade_order' => ['cpu', 'gpu', 'ram', 'storage'],
        ],
    ];

    // Goedkoopste build voor gamen: 100 + 80 + 100 + 400 + 30 + 60 + 100 + 50 = 920 euro
    private const MINIMUM_GAMING_EUR = 920;

    public function testBudgetBelowMinimumGivesMinimumAmount(): void
    {
        try {
            $this->recommend(self::MINIMUM_GAMING_EUR - 1);
            $this->fail('Budget had te laag moeten zijn');
        } catch (BudgetTooLowException $e) {
            $this->assertSame(self::MINIMUM_GAMING_EUR * 100, $e->minimumCents);
        }
    }

    public function testEmptyBudgetIsTooLow(): void
    {
        $this->expectException(BudgetTooLowException::class);

        $this->recommend(0);
    }

    public function testExactlyMinimumBudgetGivesCheapestBuild(): void
    {
        $recommendation = $this->recommend(self::MINIMUM_GAMING_EUR);

        $this->assertSame(self::MINIMUM_GAMING_EUR * 100, $recommendation->build->totalPriceCents());
        $this->assertSame(0, $recommendation->leftoverCents());
    }

    public function testMinimumDependsOnGoal(): void
    {
        try {
            $this->recommend(self::MINIMUM_GAMING_EUR, Goal::Streaming);
            $this->fail('Streamen heeft 32 GB nodig en is dus duurder');
        } catch (BudgetTooLowException $e) {
            $this->assertSame(102000, $e->minimumCents);
        }
    }

    public function testEveryBudgetGivesCompleteCompatibleBuildWithinBudget(): void
    {
        $checker = CompatibilityChecker::withAllRules(30);

        foreach (Goal::cases() as $goal) {
            for ($budget = 1020; $budget <= 4000; $budget += 37) {
                $recommendation = $this->recommend($budget, $goal);
                $report = $checker->check($recommendation->build);

                $this->assertTrue($recommendation->build->isComplete());
                $this->assertFalse($report->hasProblems(), "Probleem bij $budget voor {$goal->value}");
                $this->assertLessThanOrEqual($budget * 100, $recommendation->build->totalPriceCents());
            }
        }
    }

    public function testPicksBestPartWithinCategoryBudget(): void
    {
        $recommendation = $this->recommend(3000);

        $this->assertSame(100000, $recommendation->build->gpu()->priceCents);
    }

    public function testSkipsCpuWhenBuildCouldNotBeFinishedAfterwards(): void
    {
        $goals = self::GOALS;
        $goals['gaming']['shares']['cpu'] = 30;

        // De AM5-processor past in het deelbudget, maar DDR5 is te duur om de build af te maken.
        $recommendation = $this->recommender($goals)->recommend(1200, Goal::Gaming);

        $this->assertSame('AM4', $recommendation->build->cpu()->socket);
    }

    public function testMemoryMeetsMinimumOfGoal(): void
    {
        $gaming = $this->recommend(self::MINIMUM_GAMING_EUR);
        $streaming = $this->recommend(1100, Goal::Streaming);

        $this->assertSame(16, $gaming->build->ram()->capacityGb);
        $this->assertSame(32, $streaming->build->ram()->capacityGb);
    }

    public function testStorageMeetsMinimumOfGoal(): void
    {
        $this->assertSame(500, $this->recommend(900, Goal::Work)->build->storage()->capacityGb);
        $this->assertSame(1000, $this->recommend(self::MINIMUM_GAMING_EUR)->build->storage()->capacityGb);
    }

    public function testBoardWithTooFewRamSlotsIsNotChosen(): void
    {
        $smallBoard = Parts::motherboard('AM4', 'DDR4', Parts::microAtx(), priceCents: 100, ramSlots: 2);
        $parts = [...$this->catalog(), $smallBoard];

        $recommendation = (new Recommender($parts, CompatibilityChecker::withAllRules(30), self::GOALS))
            ->recommend(self::MINIMUM_GAMING_EUR, Goal::Gaming);

        $this->assertSame(4, $recommendation->build->motherboard()->ramSlots);
    }

    public function testLeftoverGoesToWhatMattersForTheGoal(): void
    {
        $gaming = $this->recommend(1800, Goal::Gaming);
        $work = $this->recommend(1800, Goal::Work);

        $this->assertSame(100000, $gaming->build->gpu()->priceCents);
        $this->assertSame(40000, $work->build->gpu()->priceCents);
    }

    public function testBetterGpuGetsAStrongerPsu(): void
    {
        $recommendation = $this->recommend(1800);

        $this->assertSame(300, $recommendation->build->gpu()->powerW);
        $this->assertSame(850, $recommendation->build->psu()->wattageW);
    }

    public function testLeftoverIsBudgetMinusTotal(): void
    {
        $recommendation = $this->recommend(1000);

        $this->assertSame(100000 - $recommendation->build->totalPriceCents(), $recommendation->leftoverCents());
    }

    public function testEveryCategoryHasOneChoice(): void
    {
        $choices = $this->recommend(1500)->choices;

        $this->assertCount(8, $choices);
    }

    public function testReasonNamesCategoryAndBudget(): void
    {
        $choice = $this->choiceFor($this->recommend(1100), Category::Gpu);

        $this->assertSame(44000, $choice->categoryBudgetCents);
        $this->assertSame('Beste videokaart binnen het deelbudget van € 440.', $choice->reason);
    }

    public function testReasonForMemoryNamesMinimumForGoal(): void
    {
        $choice = $this->choiceFor($this->recommend(1500, Goal::Streaming), Category::Ram);

        $this->assertStringContainsString('minimaal 32 GB voor streamen', $choice->reason);
    }

    public function testReasonSaysWhenCategoryBudgetWasTooLow(): void
    {
        $choice = $this->choiceFor($this->recommend(self::MINIMUM_GAMING_EUR), Category::Storage);

        $this->assertSame(
            'Het deelbudget van € 46 was te krap, daarom de goedkoopste keuze die past.',
            $choice->reason,
        );
    }

    public function testReasonSaysWhenLeftoverPaidForUpgrade(): void
    {
        $choice = $this->choiceFor($this->recommend(1800), Category::Gpu);

        $this->assertStringContainsString('deelbudget was € 720', $choice->reason);
        $this->assertStringContainsString('betere videokaart', $choice->reason);
    }

    public function testSameInputGivesSameResult(): void
    {
        $this->assertEquals($this->recommend(1500), $this->recommend(1500));
    }

    private function recommend(int $budget, Goal $goal = Goal::Gaming): Recommendation
    {
        return $this->recommender(self::GOALS)->recommend($budget, $goal);
    }

    private function recommender(array $goals): Recommender
    {
        return new Recommender($this->catalog(), CompatibilityChecker::withAllRules(30), $goals);
    }

    private function choiceFor(Recommendation $recommendation, Category $category): RecommendedChoice
    {
        foreach ($recommendation->choices as $choice) {
            if ($choice->part->category() === $category) {
                return $choice;
            }
        }

        $this->fail("Geen keuze voor {$category->value}");
    }

    // Een kleine catalogus met een goedkoop AM4/DDR4-platform en een duur AM5/DDR5-platform.
    private function catalog(): array
    {
        return [
            Parts::cpu('AM4', powerW: 65, priceCents: 10000),
            Parts::cpu('AM5', powerW: 100, priceCents: 30000),
            Parts::motherboard('AM4', 'DDR4', Parts::microAtx(), priceCents: 8000),
            Parts::motherboard('AM5', 'DDR5', Parts::atx(), priceCents: 15000),
            Parts::ram('DDR4', capacityGb: 16, priceCents: 10000),
            Parts::ram('DDR4', capacityGb: 32, priceCents: 20000),
            Parts::ram('DDR5', capacityGb: 16, priceCents: 30000),
            Parts::ram('DDR5', capacityGb: 32, priceCents: 60000),
            Parts::gpu(lengthMm: 250, powerW: 150, priceCents: 40000),
            Parts::gpu(lengthMm: 300, powerW: 300, priceCents: 100000),
            Parts::cooler(heightMm: 150, priceCents: 3000),
            Parts::cooler(heightMm: 160, priceCents: 8000),
            Parts::pcCase(Parts::atx(), priceCents: 6000),
            Parts::pcCase(Parts::atx(), priceCents: 12000),
            Parts::storage(capacityGb: 500, priceCents: 5000),
            Parts::storage(capacityGb: 1000, priceCents: 10000),
            Parts::storage(capacityGb: 2000, priceCents: 20000),
            Parts::psu(500, priceCents: 5000),
            Parts::psu(850, priceCents: 12000),
        ];
    }
}
