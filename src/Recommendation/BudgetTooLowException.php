<?php

namespace App\Recommendation;

use RuntimeException;

class BudgetTooLowException extends RuntimeException
{
    public function __construct(public readonly int $minimumCents)
    {
        parent::__construct('Budget is lager dan de goedkoopste complete build.');
    }
}
