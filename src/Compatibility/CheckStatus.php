<?php

namespace App\Compatibility;

enum CheckStatus: string
{
    case Ok = 'ok';
    case Warning = 'warning';
    case Error = 'error';
    case NotChecked = 'not_checked';
}
