<?php

namespace App\Http;

use App\Model\Build;
use App\Model\Category;
use App\Model\Resolution;
use App\Recommendation\GameTarget;
use App\Recommendation\GameType;
use App\Recommendation\Goal;
use App\Repository\PartFilter;

// Alle controles op invoer van de gebruiker, zodat de API en de pagina's dezelfde regels gebruiken.
class Input
{
    private const MAX_NAME_LENGTH = 60;

    private const MAX_BUDGET_EUR = 100000;

    public static function category(?string $value): ?Category
    {
        if ($value === null) {
            return null;
        }

        $category = Category::tryFrom($value);
        if ($category === null) {
            $allowed = implode(', ', array_column(Category::cases(), 'value'));
            throw new HttpException(422, 'invalid_category', "Onbekende categorie '$value'. Kies uit: $allowed.");
        }

        return $category;
    }

    public static function maxPriceCents(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (!is_numeric($value) || (float) $value <= 0) {
            throw new HttpException(422, 'invalid_max_price', 'De maximale prijs moet een getal boven 0 zijn.');
        }

        return (int) round((float) $value * 100);
    }

    public static function partFilter(Request $request, ?Category $category = null): PartFilter
    {
        return new PartFilter(
            category: $category ?? self::category($request->query('category')),
            brand: $request->query('brand'),
            maxPriceCents: self::maxPriceCents($request->query('max_price')),
            socket: $request->query('socket'),
            ramType: $request->query('ram_type'),
            formFactor: $request->query('form_factor'),
            sort: self::sort($request->query('sort')),
        );
    }

    public static function sort(?string $value): string
    {
        if ($value === null) {
            return PartFilter::SORT_OPTIONS[0];
        }

        if (!in_array($value, PartFilter::SORT_OPTIONS, true)) {
            $allowed = implode(', ', PartFilter::SORT_OPTIONS);
            throw new HttpException(422, 'invalid_sort', "Onbekende sortering '$value'. Kies uit: $allowed.");
        }

        return $value;
    }

    public static function buildName(mixed $name): string
    {
        $name = is_string($name) ? trim($name) : '';

        if ($name === '' || mb_strlen($name) > self::MAX_NAME_LENGTH) {
            $message = 'Geef de build een naam van 1 tot ' . self::MAX_NAME_LENGTH . ' tekens.';
            throw new HttpException(422, 'invalid_name', $message);
        }

        return $name;
    }

    public static function buildToSave(Build $build): Build
    {
        if ($build->isEmpty()) {
            throw new HttpException(422, 'empty_build', 'Kies minstens één onderdeel voordat je de build opslaat.');
        }

        return $build;
    }

    // Leeg betekent: geef de goedkoopste build die past.
    public static function budget(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (!ctype_digit($value) || (int) $value < 1 || (int) $value > self::MAX_BUDGET_EUR) {
            $maximum = number_format(self::MAX_BUDGET_EUR, 0, ',', '.');
            throw new HttpException(422, 'invalid_budget', "Vul een budget in hele euro's in, van 1 tot $maximum.");
        }

        return (int) $value;
    }

    /** @param array<string, array{min_fps: int}> $gameTypes */
    public static function gameTarget(Request $request, Goal $goal, array $gameTypes): ?GameTarget
    {
        $resolution = $request->query('resolution');
        $gameType = $request->query('game_type');

        if ($goal === Goal::Work || ($resolution === null && $gameType === null)) {
            return null;
        }

        $chosenResolution = Resolution::tryFrom((string) $resolution);
        $chosenGameType = GameType::tryFrom((string) $gameType);

        if ($chosenResolution === null || $chosenGameType === null) {
            throw new HttpException(422, 'invalid_game_target', 'Kies je scherm en het soort games dat je speelt.');
        }

        return new GameTarget($chosenResolution, $chosenGameType, $gameTypes[$chosenGameType->value]['min_fps']);
    }

    public static function goal(?string $value): Goal
    {
        $goal = Goal::tryFrom((string) $value);

        if ($goal === null) {
            $allowed = implode(', ', array_column(Goal::cases(), 'value'));
            throw new HttpException(422, 'invalid_goal', "Kies een doel: $allowed.");
        }

        return $goal;
    }
}
