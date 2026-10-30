<?php

namespace App\Http\Controller;

use App\Compatibility\CompatibilityChecker;
use App\Http\HttpException;
use App\Http\Input;
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
    public function __construct(
        private PartRepository $parts,
        private CompatibilityChecker $checker,
        private array $goals,
    ) {
    }

    public function show(Request $request): Response
    {
        $budget = Input::budget($request->query('budget'));
        $goal = Input::goal($request->query('goal'));
        $recommender = new Recommender($this->parts->findByFilter(new PartFilter()), $this->checker, $this->goals);

        try {
            return Response::json($this->toArray($recommender->recommend($budget, $goal), $goal));
        } catch (BudgetTooLowException $e) {
            throw new HttpException(422, 'budget_too_low', $e->getMessage());
        }
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
