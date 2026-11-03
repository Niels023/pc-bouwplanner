<?php

namespace App\Http\Controller;

use App\Compatibility\CompatibilityChecker;
use App\Http\HttpException;
use App\Http\Input;
use App\Http\Request;
use App\Http\Response;
use App\Http\View;
use App\Http\WorkingBuild;
use App\Model\Resolution;
use App\Recommendation\BudgetTooLowException;
use App\Recommendation\GameType;
use App\Recommendation\Goal;
use App\Recommendation\NoBuildPossibleException;
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
        private array $gameTypes,
    ) {
    }

    public function show(Request $request): Response
    {
        $data = [
            'title' => 'Laat een build voorstellen',
            'goals' => Goal::cases(),
            'resolutions' => Resolution::cases(),
            'gameTypes' => GameType::cases(),
            'budget' => $request->query('budget') ?? '',
            'selectedGoal' => $request->query('goal') ?? Goal::Gaming->value,
            'selectedResolution' => $request->query('resolution') ?? Resolution::FullHd->value,
            'selectedGameType' => $request->query('game_type') ?? GameType::Heavy->value,
            'recommendation' => null,
            'report' => null,
            'error' => null,
            'cheapestUrl' => null,
        ];

        // Zonder doel in de URL is het formulier nog niet verstuurd.
        if (!array_key_exists('goal', $request->queryParams)) {
            return $this->view->render('recommendation', $data);
        }

        try {
            $recommendation = $this->recommendationFor($request);
        } catch (BudgetTooLowException $e) {
            $data['cheapestUrl'] = $this->urlWithoutBudget($request);

            return $this->view->render('recommendation', ['error' => $e->getMessage()] + $data, 422);
        } catch (HttpException | NoBuildPossibleException $e) {
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
        } catch (BudgetTooLowException | NoBuildPossibleException $e) {
            throw new HttpException(422, 'no_recommendation', $e->getMessage());
        }

        $this->working->replaceWith($recommendation->build);

        return Response::redirect('/overzicht');
    }

    private function recommendationFor(Request $request): Recommendation
    {
        $budget = Input::budget($request->query('budget'));
        $goal = Input::goal($request->query('goal'));
        $target = Input::gameTarget($request, $goal, $this->gameTypes);
        $parts = $this->parts->findByFilter(new PartFilter());

        return (new Recommender($parts, $this->checker, $this->goals))->recommend($budget, $goal, $target);
    }

    private function urlWithoutBudget(Request $request): string
    {
        $query = $request->queryParams;
        unset($query['budget']);

        return '/aanbeveling?' . http_build_query($query);
    }
}
