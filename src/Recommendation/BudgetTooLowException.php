<?php

namespace App\Recommendation;

use RuntimeException;

class BudgetTooLowException extends RuntimeException
{
    public function __construct(public readonly int $minimumCents, Goal $goal, ?GameTarget $target = null)
    {
        $minimum = number_format(ceil($minimumCents / 100), 0, ',', '.');
        $purpose = $target?->label() ?? $goal->label();

        parent::__construct("Voor $purpose heb je minimaal € $minimum nodig.");
    }
}
