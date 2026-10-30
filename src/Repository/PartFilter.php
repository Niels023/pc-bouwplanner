<?php

namespace App\Repository;

use App\Model\Category;

readonly class PartFilter
{
    public const SORT_OPTIONS = ['price_asc', 'price_desc', 'name'];

    public function __construct(
        public ?Category $category = null,
        public ?string $brand = null,
        public ?int $maxPriceCents = null,
        public ?string $socket = null,
        public ?string $ramType = null,
        public ?string $formFactor = null,
        public string $sort = 'price_asc',
    ) {
    }
}
