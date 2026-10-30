<?php

namespace App\Http\Controller;

use App\Compatibility\CompatibilityChecker;
use App\Http\ApiException;
use App\Http\Request;
use App\Http\Response;
use App\Recommendation\BudgetTooLowException;
use App\Recommendation\Goal;
use App\Recommendation\Recommendation;
use App\Recommendation\RecommendedChoice;
use App\Recommendation\Recommender;
use App\Repository\PartFilter;
use App\Repository\PartRepository;

class RecommendationApiController
{
    private const MAX_BUDGET_EUR = 100000;

    public function __construct(
        private PartRepository $parts,
        private CompatibilityChecker $checker,
        private array $goals,
    ) {
    }

    public function show(Request $request): Response
    {
        $budget = $this->budget($request->query('budget'));
        $goal = $this->goal($request->query('goal'));
        $recommender = new Recommender($this->parts->findByFilter(new PartFilter()), $this->checker, $this->goals);

        try {
            return Response::json($this->toArray($recommender->recommend($budget, $goal), $goal));
        } catch (BudgetTooLowException $e) {
            $minimum = number_format(ceil($e->minimumCents / 100), 0, ',', '.');
            throw new ApiException(422, 'budget_too_low', "Voor {$goal->label()} heb je minimaal € $minimum nodig.");
        }
    }

    private function budget(?string $value): int
    {
        if ($value === null || !ctype_digit($value) || (int) $value < 1 || (int) $value > self::MAX_BUDGET_EUR) {
            $maximum = number_format(self::MAX_BUDGET_EUR, 0, ',', '.');
            throw new ApiException(422, 'invalid_budget', "Vul een budget in hele euro's in, van 1 tot $maximum.");
        }

        return (int) $value;
    }

    private function goal(?string $value): Goal
    {
        $goal = Goal::tryFrom((string) $value);

        if ($goal === null) {
            $allowed = implode(', ', array_column(Goal::cases(), 'value'));
            throw new ApiException(422, 'invalid_goal', "Kies een doel: $allowed.");
        }

        return $goal;
    }

    /** @return array<string, mixed> */
    private function toArray(Recommendation $recommendation, Goal $goal): array
    {
        return [
            'goal' => $goal->value,
            'budget_eur' => $recommendation->budgetCents / 100,
            'leftover_eur' => $recommendation->leftoverCents() / 100,
            'choices' => array_map(fn (RecommendedChoice $choice) => [
                'part' => $choice->part->toArray(),
                'category_budget_eur' => $choice->categoryBudgetCents / 100,
                'reason' => $choice->reason,
            ], $recommendation->choices),
            'check' => $this->checker->check($recommendation->build)->toArray(),
        ];
    }
}
