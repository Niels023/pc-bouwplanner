<?php

namespace App\Http\Controller;

use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityChecker;
use App\Compatibility\RuleResult;
use App\Http\HttpException;
use App\Http\Input;
use App\Http\Request;
use App\Http\Response;
use App\Http\View;
use App\Http\WorkingBuild;
use App\Model\Build;
use App\Model\Category;
use App\Model\Part;
use App\Repository\BuildRepository;
use App\Repository\PartFilter;
use App\Repository\PartRepository;

class BuildPageController
{
    private const FILTER_NAMES = ['brand', 'max_price', 'socket', 'ram_type', 'form_factor'];

    public function __construct(
        private View $view,
        private PartRepository $parts,
        private BuildRepository $builds,
        private CompatibilityChecker $checker,
        private WorkingBuild $working,
    ) {
    }

    public function step(Request $request): Response
    {
        $category = $this->categoryFrom($request);
        $build = $this->working->build();
        $filterError = null;

        try {
            $filter = Input::partFilter($request, $category);
        } catch (HttpException $e) {
            $filter = new PartFilter($category);
            $filterError = $e->getMessage();
        }

        return $this->view->render('step', [
            'title' => ucfirst($category->label()) . ' kiezen',
            'category' => $category,
            'build' => $build,
            'report' => $this->checker->check($build),
            'cards' => $this->cardsFor($filter, $build),
            'options' => $this->filterOptions($this->parts->findByFilter(new PartFilter($category))),
            'filters' => $this->filterValues($request),
            'filterError' => $filterError,
        ]);
    }

    public function choose(Request $request): Response
    {
        $category = $this->categoryFrom($request);
        $part = $this->parts->findById((int) ($request->formParams['part_id'] ?? 0));

        if ($part === null || $part->category() !== $category) {
            throw new HttpException(404, 'part_not_found', 'Dit onderdeel bestaat niet.');
        }

        $this->working->choose($part);

        return Response::redirect($this->stepUrl($category, $request));
    }

    public function remove(Request $request): Response
    {
        $category = $this->categoryFrom($request);
        $this->working->remove($category);

        return Response::redirect($this->stepUrl($category, $request));
    }

    public function overview(Request $request): Response
    {
        return $this->renderOverview();
    }

    public function save(Request $request): Response
    {
        $name = $request->formParams['name'] ?? '';

        try {
            $saved = $this->builds->save(
                Input::buildName($name),
                Input::buildToSave($this->working->build()),
            );
        } catch (HttpException $e) {
            return $this->renderOverview($e->getMessage(), (string) $name, $e->status);
        }

        return Response::redirect('/build/' . $saved->shareCode);
    }

    public function show(Request $request): Response
    {
        $saved = $this->builds->findByShareCode($request->routeParams['code']);

        if ($saved === null) {
            throw new HttpException(404, 'build_not_found', 'Geen build gevonden met deze code.');
        }

        return $this->view->render('saved-build', [
            'title' => $saved->name,
            'saved' => $saved,
            'report' => $this->checker->check($saved->build),
        ]);
    }

    public function open(Request $request): Response
    {
        $code = trim($request->query('code') ?? '');

        return Response::redirect($code === '' ? '/' : '/build/' . rawurlencode($code));
    }

    private function renderOverview(?string $error = null, string $name = '', int $status = 200): Response
    {
        $build = $this->working->build();

        return $this->view->render('overview', [
            'title' => 'Overzicht van je build',
            'build' => $build,
            'report' => $this->checker->check($build),
            'error' => $error,
            'name' => $name,
            'maxNameLength' => Input::MAX_NAME_LENGTH,
        ], $status);
    }

    private function categoryFrom(Request $request): Category
    {
        $category = Category::tryFrom($request->routeParams['category']);

        if ($category === null) {
            throw new HttpException(404, 'not_found', 'Deze pagina bestaat niet.');
        }

        return $category;
    }

    /** @return array<int, array{part: Part, chosen: bool, problems: RuleResult[], conflict: bool}> */
    private function cardsFor(PartFilter $filter, Build $build): array
    {
        $cards = array_map(function (Part $part) use ($build) {
            $problems = $this->checker->problemsFor($build, $part);
            $errors = array_filter($problems, fn (RuleResult $problem) => $problem->status === CheckStatus::Error);

            return [
                'part' => $part,
                'chosen' => $build->partIn($part->category())?->id === $part->id,
                'problems' => $problems,
                'conflict' => $errors !== [],
            ];
        }, $this->parts->findByFilter($filter));

        // Eerst wat past, daarna de grijze onderdelen. usort houdt de volgorde op prijs verder intact.
        usort($cards, fn (array $a, array $b) => $a['conflict'] <=> $b['conflict']);

        return $cards;
    }

    /**
     * Alleen de filters die bij deze onderdelen horen, met de waarden die er echt zijn.
     *
     * @param Part[] $parts
     * @return array<string, string[]>
     */
    private function filterOptions(array $parts): array
    {
        $values = fn (callable $value) => array_values(array_unique(array_filter(array_map($value, $parts))));

        return [
            'brand' => $values(fn (Part $part) => $part->brand),
            'socket' => $values(fn (Part $part) => $part->specs()['socket'] ?? null),
            'ram_type' => $values(fn (Part $part) => $part->specs()['ram_type'] ?? null),
            'form_factor' => $values(
                fn (Part $part) => $part->specs()['form_factor'] ?? $part->specs()['max_form_factor'] ?? null,
            ),
        ];
    }

    /** @return array<string, string> */
    private function filterValues(Request $request): array
    {
        $values = [];
        foreach (self::FILTER_NAMES as $name) {
            $value = $request->query($name);
            if ($value !== null) {
                $values[$name] = $value;
            }
        }

        return $values;
    }

    private function stepUrl(Category $category, Request $request): string
    {
        $query = http_build_query($this->filterValues($request));

        return "/bouwen/{$category->value}" . ($query === '' ? '' : "?$query");
    }
}
