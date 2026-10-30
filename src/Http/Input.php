<?php

namespace App\Http;

use App\Model\Build;
use App\Model\Category;
use App\Recommendation\Goal;
use App\Repository\PartFilter;

// Alle controles op invoer van de gebruiker, zodat de API en de pagina's dezelfde regels gebruiken.
class Input
{
    public const MAX_NAME_LENGTH = 60;

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
        );
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

    public static function budget(?string $value): int
    {
        if ($value === null || !ctype_digit($value) || (int) $value < 1 || (int) $value > self::MAX_BUDGET_EUR) {
            $maximum = number_format(self::MAX_BUDGET_EUR, 0, ',', '.');
            throw new HttpException(422, 'invalid_budget', "Vul een budget in hele euro's in, van 1 tot $maximum.");
        }

        return (int) $value;
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
