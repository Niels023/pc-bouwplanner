<?php

namespace App\Compatibility;

enum OverallStatus: string
{
    case Ok = 'ok';
    case Warning = 'warning';
    case Error = 'error';
    case Incomplete = 'incomplete';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'In orde',
            self::Warning => 'Waarschuwing',
            self::Error => 'Fout',
            self::Incomplete => 'Nog niet compleet',
        };
    }
}
