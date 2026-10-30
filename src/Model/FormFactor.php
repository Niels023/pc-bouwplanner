<?php

namespace App\Model;

readonly class FormFactor
{
    public function __construct(
        public string $name,
        public int $sizeRank,
    ) {
    }

    public function fitsIn(FormFactor $other): bool
    {
        return $this->sizeRank <= $other->sizeRank;
    }
}
