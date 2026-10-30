<?php

namespace App\Compatibility;

enum OverallStatus: string
{
    case Ok = 'ok';
    case Warning = 'warning';
    case Error = 'error';
    case Incomplete = 'incomplete';
}
