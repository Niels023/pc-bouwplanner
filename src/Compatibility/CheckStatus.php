<?php

namespace App\Compatibility;

enum CheckStatus: string
{
    case Ok = 'ok';
    case Warning = 'warning';
    case Error = 'error';
    case NotChecked = 'not_checked';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'In orde',
            self::Warning => 'Waarschuwing',
            self::Error => 'Fout',
            self::NotChecked => 'Nog niet gecontroleerd',
        };
    }
}
