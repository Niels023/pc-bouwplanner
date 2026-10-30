<?php

namespace App\Recommendation;

use RuntimeException;

class NoBuildPossibleException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Geen enkele videokaart haalt dit. Kies een lager scherm of lichtere games.');
    }
}
