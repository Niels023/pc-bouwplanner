<?php

namespace App\Model;

enum Resolution: string
{
    case FullHd = '1080p';
    case Qhd = '1440p';
    case Uhd = '4k';

    public function label(): string
    {
        return match ($this) {
            self::FullHd => 'Full HD',
            self::Qhd => '1440p',
            self::Uhd => '4K',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FullHd => 'Full HD (1920 x 1080): de meeste schermen',
            self::Qhd => '1440p (2560 x 1440): scherper beeld',
            self::Uhd => '4K (3840 x 2160): heel scherp, vooral grote schermen',
        };
    }
}
