<?php

namespace App\Recommendation;

use App\Compatibility\CompatibilityChecker;
use App\Model\Build;
use App\Model\Category;
use App\Model\Cpu;
use App\Model\Motherboard;
use App\Model\Part;
use App\Model\RamKit;
use App\Model\StorageDrive;

class Recommender
{
    // De kast komt na de videokaart en de koeler, want die moeten erin passen.
    // De voeding komt als laatste, want dan is het verbruik bekend.
    private const CATEGORY_ORDER = [
        Category::Cpu,
        Category::Motherboard,
        Category::Ram,
        Category::Gpu,
        Category::Cooler,
        Category::PcCase,
        Category::Storage,
        Category::Psu,
    ];

    /** @var array<string, Part[]> Per categorie, van goedkoop naar duur */
    private array $candidates = [];

    /** @var Category[] De categorieën voor dit doel, in de volgorde waarin ze gekozen worden */
    private array $order = [];

    /**
     * @param Part[] $parts
     * @param array<string, array<string, mixed>> $goals Instellingen per doel uit config/app.php
     */
    public function __construct(
        private array $parts,
        private CompatibilityChecker $checker,
        private array $goals,
    ) {
    }

    public function recommend(int $budgetEuro, Goal $goal): Recommendation
    {
        $settings = $this->goals[$goal->value];
        $this->order = $this->orderFor($settings);
        $this->candidates = $this->candidatesFor($settings);

        $budgetCents = $budgetEuro * 100;
        $minimumCents = $this->cheapestFinish(new Build(), $this->order, PHP_INT_MAX);
        if ($minimumCents > $budgetCents) {
            throw new BudgetTooLowException($minimumCents, $goal);
        }

        $withoutPsu = array_slice($this->order, 0, -1);

        // De voeding als laatste: een upgrade van de videokaart kan een zwaardere voeding nodig maken.
        $choices = $this->chooseParts([], $withoutPsu, $budgetCents, $goal, $settings);
        $choices = $this->spendLeftover($choices, $budgetCents, $settings['upgrade_order']);
        $choices = $this->chooseParts($choices, [Category::Psu], $budgetCents, $goal, $settings);

        return new Recommendation($this->buildFrom($choices), array_values($choices), $budgetCents);
    }

    /** @return Category[] */
    private function orderFor(array $settings): array
    {
        if ($this->needsGpu($settings)) {
            return self::CATEGORY_ORDER;
        }

        return array_values(array_filter(self::CATEGORY_ORDER, fn (Category $c) => $c !== Category::Gpu));
    }

    // Een doel zonder deel van het budget voor de videokaart krijgt er ook geen.
    private function needsGpu(array $settings): bool
    {
        return $settings['shares'][Category::Gpu->value] > 0;
    }

    /** @return array<string, Part[]> */
    private function candidatesFor(array $settings): array
    {
        $candidates = [];
        foreach ($this->parts as $part) {
            if ($this->meetsMinimum($part, $settings)) {
                $candidates[$part->category()->value][] = $part;
            }
        }

        foreach ($candidates as &$parts) {
            usort($parts, fn (Part $a, Part $b) => $a->priceCents <=> $b->priceCents);
        }

        return $candidates;
    }

    private function meetsMinimum(Part $part, array $settings): bool
    {
        return match (true) {
            $part instanceof RamKit => $part->capacityGb >= $settings['min_ram_gb'],
            $part instanceof StorageDrive => $part->capacityGb >= $settings['min_storage_gb'],
            $part instanceof Motherboard => $part->ramSlots >= $settings['min_ram_slots'],
            $part instanceof Cpu => $this->needsGpu($settings) || $part->hasIntegratedGraphics,
            default => true,
        };
    }

    /**
     * @param array<string, RecommendedChoice> $choices
     * @param Category[] $categories
     * @return array<string, RecommendedChoice>
     */
    private function chooseParts(
        array $choices,
        array $categories,
        int $budgetCents,
        Goal $goal,
        array $settings,
    ): array {
        foreach ($categories as $category) {
            $build = $this->buildFrom($choices);
            $limitCents = (int) round($budgetCents * $settings['shares'][$category->value] / 100);
            $remaining = $this->categoriesAfter($build, $category);
            $moneyLeftCents = $budgetCents - $build->totalPriceCents();

            $part = $this->bestPart($build, $category, $limitCents, $remaining, $moneyLeftCents)
                ?? $this->cheapestPart($build, $category, $remaining, $moneyLeftCents);

            $reason = $this->reason($part, $limitCents, $goal, $settings);
            $choices[$category->value] = new RecommendedChoice($part, $limitCents, $reason);
        }

        return $choices;
    }

    /** @return Category[] */
    private function categoriesAfter(Build $build, Category $category): array
    {
        return array_filter(
            $this->order,
            fn (Category $other) => $other !== $category && !$build->hasPart($other),
        );
    }

    // Het duurste onderdeel binnen het deelbudget waarmee de build nog af kan.
    private function bestPart(
        Build $build,
        Category $category,
        int $limitCents,
        array $remaining,
        int $moneyLeftCents,
    ): ?Part {
        foreach (array_reverse($this->candidates[$category->value]) as $part) {
            if ($part->priceCents <= $limitCents && $this->canFinish($build, $part, $remaining, $moneyLeftCents)) {
                return $part;
            }
        }

        return null;
    }

    private function cheapestPart(Build $build, Category $category, array $remaining, int $moneyLeftCents): Part
    {
        foreach ($this->candidates[$category->value] as $part) {
            if ($this->canFinish($build, $part, $remaining, $moneyLeftCents)) {
                return $part;
            }
        }

        throw new \LogicException("Geen passend onderdeel in categorie {$category->value}");
    }

    private function canFinish(Build $build, Part $part, array $remaining, int $moneyLeftCents): bool
    {
        if ($part->priceCents > $moneyLeftCents) {
            return false;
        }

        $next = $build->withPart($part);
        if ($this->checker->check($next)->hasProblems()) {
            return false;
        }

        return $this->cheapestFinish($next, $remaining, $moneyLeftCents - $part->priceCents) !== null;
    }

    /**
     * Wat het minimaal kost om de build af te maken, of null als dat niet lukt binnen de limiet.
     *
     * Probeert per categorie de onderdelen van goedkoop naar duur. Een tak die duurder wordt dan
     * de beste oplossing tot nu toe wordt overgeslagen, anders duurt dit veel te lang.
     *
     * @param Category[] $remaining
     */
    private function cheapestFinish(Build $build, array $remaining, int $limitCents): ?int
    {
        if ($remaining === []) {
            return 0;
        }

        $category = array_shift($remaining);
        $bestCents = null;

        foreach ($this->candidates[$category->value] as $part) {
            if ($part->priceCents > $limitCents) {
                break;
            }

            $next = $build->withPart($part);
            if ($this->checker->check($next)->hasProblems()) {
                continue;
            }

            $restCents = $this->cheapestFinish($next, $remaining, $limitCents - $part->priceCents);
            if ($restCents !== null) {
                $bestCents = $part->priceCents + $restCents;
                $limitCents = $bestCents - 1;
            }
        }

        return $bestCents;
    }

    /**
     * Besteedt wat over is aan de onderdelen die voor dit doel het belangrijkst zijn.
     *
     * @param array<string, RecommendedChoice> $choices
     * @param string[] $upgradeOrder
     * @return array<string, RecommendedChoice>
     */
    private function spendLeftover(array $choices, int $budgetCents, array $upgradeOrder): array
    {
        foreach ($upgradeOrder as $category) {
            $current = $choices[$category];
            $better = $this->upgradeFor($this->buildFrom($choices), $current->part, $budgetCents);

            if ($better !== null) {
                $choices[$category] = new RecommendedChoice(
                    $better,
                    $current->categoryBudgetCents,
                    $this->upgradeReason($better, $current->categoryBudgetCents),
                );
            }
        }

        return $choices;
    }

    private function upgradeFor(Build $build, Part $current, int $budgetCents): ?Part
    {
        $moneyLeftCents = $budgetCents - $build->totalPriceCents() + $current->priceCents;

        foreach (array_reverse($this->candidates[$current->category()->value]) as $part) {
            if ($part->priceCents <= $current->priceCents) {
                return null;
            }
            if ($this->canFinish($build, $part, [Category::Psu], $moneyLeftCents)) {
                return $part;
            }
        }

        return null;
    }

    /** @param array<string, RecommendedChoice> $choices */
    private function buildFrom(array $choices): Build
    {
        return new Build(...array_map(fn (RecommendedChoice $choice) => $choice->part, array_values($choices)));
    }

    private function reason(Part $part, int $limitCents, Goal $goal, array $settings): string
    {
        $category = $part->category();
        $budget = $this->euros($limitCents);

        if ($part->priceCents > $limitCents) {
            return "Het deelbudget van $budget was te krap, daarom de goedkoopste keuze die past.";
        }

        return match ($category) {
            Category::Ram => "Beste {$category->label()} binnen het deelbudget van $budget, "
                . "met minimaal {$settings['min_ram_gb']} GB voor {$goal->label()}.",
            Category::Storage => "Beste {$category->label()} binnen het deelbudget van $budget, "
                . "met minimaal {$settings['min_storage_gb']} GB voor {$goal->label()}.",
            default => "Beste {$category->label()} binnen het deelbudget van $budget.",
        };
    }

    private function upgradeReason(Part $part, int $limitCents): string
    {
        $budget = $this->euros($limitCents);

        return "Het deelbudget was $budget, maar er bleef genoeg over voor deze betere {$part->category()->label()}.";
    }

    private function euros(int $cents): string
    {
        return '€ ' . number_format($cents / 100, 0, ',', '.');
    }
}
