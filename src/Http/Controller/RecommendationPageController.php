<?php

namespace App\Http\Controller;

use App\Compatibility\CompatibilityChecker;
use App\Http\HttpException;
use App\Http\Input;
use App\Http\Request;
use App\Http\Response;
use App\Http\View;
use App\Http\WorkingBuild;
use App\Recommendation\BudgetTooLowException;
use App\Recommendation\Goal;
use App\Recommendation\Recommendation;
use App\Recommendation\Recommender;
use App\Repository\PartFilter;
use App\Repository\PartRepository;

class RecommendationPageController
{
    public function __construct(
        private View $view,
        private PartRepository $parts,
        private CompatibilityChecker $checker,
        private WorkingBuild $working,
        private array $goals,
    ) {
    }

    public function show(Request $request): Response
    {
        $data = [
            'title' => 'Laat een build voorstellen',
            'goals' => Goal::cases(),
            'budget' => $request->query('budget') ?? '',
            'selectedGoal' => $request->query('goal') ?? Goal::Gaming->value,
            'recommendation' => null,
            'report' => null,
            'error' => null,
        ];

        // Zonder budget en doel in de URL is het formulier nog niet verstuurd.
        if (!array_key_exists('budget', $request->queryParams)) {
            return $this->view->render('recommendation', $data);
        }

        try {
            $recommendation = $this->recommendationFor($request);
        } catch (HttpException | BudgetTooLowException $e) {
            return $this->view->render('recommendation', ['error' => $e->getMessage()] + $data, 422);
        }

        return $this->view->render('recommendation', [
            'recommendation' => $recommendation,
            'report' => $this->checker->check($recommendation->build),
            'query' => http_build_query($request->queryParams),
        ] + $data);
    }

    // De aanbeveling wordt opnieuw uitgerekend: dezelfde invoer geeft altijd dezelfde build.
    public function adopt(Request $request): Response
    {
        try {
            $recommendation = $this->recommendationFor($request);
        } catch (BudgetTooLowException $e) {
            throw new HttpException(422, 'budget_too_low', $e->getMessage());
        }

        $this->working->replaceWith($recommendation->build);

        return Response::redirect('/overzicht');
    }

    private function recommendationFor(Request $request): Recommendation
    {
        $budget = Input::budget($request->query('budget'));
        $goal = Input::goal($request->query('goal'));
        $parts = $this->parts->findByFilter(new PartFilter());

        return (new Recommender($parts, $this->checker, $this->goals))->recommend($budget, $goal);
    }
}
