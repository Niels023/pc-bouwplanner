<?php

namespace App\Recommendation;

use RuntimeException;

class BudgetTooLowException extends RuntimeException
{
    public function __construct(public readonly int $minimumCents, Goal $goal)
    {
        $minimum = number_format(ceil($minimumCents / 100), 0, ',', '.');

        parent::__construct("Voor {$goal->label()} heb je minimaal € $minimum nodig.");
    }
}
