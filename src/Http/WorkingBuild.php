<?php

namespace App\Http;

use App\Model\Build;
use App\Model\Category;
use App\Model\Part;
use App\Repository\PartRepository;

// De build waar de gebruiker mee bezig is. Staat in de sessie als id per categorie.
class WorkingBuild
{
    private const SESSION_KEY = 'build_part_ids';

    public function __construct(private PartRepository $parts)
    {
    }

    public function build(): Build
    {
        return new Build(...$this->parts->findByIds(array_values($_SESSION[self::SESSION_KEY] ?? [])));
    }

    public function choose(Part $part): void
    {
        $_SESSION[self::SESSION_KEY][$part->category()->value] = $part->id;
    }

    public function remove(Category $category): void
    {
        unset($_SESSION[self::SESSION_KEY][$category->value]);
    }

    public function replaceWith(Build $build): void
    {
        $_SESSION[self::SESSION_KEY] = [];
        foreach ($build->parts() as $part) {
            $this->choose($part);
        }
    }
}
