<?php

namespace App\Repository;

use App\Model\Category;

readonly class PartFilter
{
    public function __construct(
        public ?Category $category = null,
        public ?string $brand = null,
        public ?int $maxPriceCents = null,
        public ?string $socket = null,
        public ?string $ramType = null,
        public ?string $formFactor = null,
    ) {
    }
}
