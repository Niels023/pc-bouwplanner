<?php

namespace App\Http\Controller;

use App\Http\ApiException;
use App\Http\Request;
use App\Http\Response;
use App\Model\Category;
use App\Model\Part;
use App\Repository\PartFilter;
use App\Repository\PartRepository;

class PartApiController
{
    public function __construct(private PartRepository $parts)
    {
    }

    public function index(Request $request): Response
    {
        $parts = $this->parts->findByFilter($this->filterFrom($request));

        return Response::json([
            'count' => count($parts),
            'parts' => array_map(fn (Part $part) => $part->toArray(), $parts),
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $request->routeParams['id'];
        $part = ctype_digit($id) ? $this->parts->findById((int) $id) : null;

        if ($part === null) {
            throw new ApiException(404, 'part_not_found', 'Dit onderdeel bestaat niet.');
        }

        return Response::json($part->toArray());
    }

    private function filterFrom(Request $request): PartFilter
    {
        return new PartFilter(
            category: $this->category($request->query('category')),
            brand: $request->query('brand'),
            maxPriceCents: $this->maxPriceCents($request->query('max_price')),
            socket: $request->query('socket'),
            ramType: $request->query('ram_type'),
            formFactor: $request->query('form_factor'),
        );
    }

    private function category(?string $value): ?Category
    {
        if ($value === null) {
            return null;
        }

        $category = Category::tryFrom($value);
        if ($category === null) {
            $allowed = implode(', ', array_column(Category::cases(), 'value'));
            throw new ApiException(422, 'invalid_category', "Onbekende categorie '$value'. Kies uit: $allowed.");
        }

        return $category;
    }

    private function maxPriceCents(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (!is_numeric($value) || (float) $value <= 0) {
            throw new ApiException(422, 'invalid_max_price', 'De maximale prijs moet een getal boven 0 zijn.');
        }

        return (int) round((float) $value * 100);
    }
}
