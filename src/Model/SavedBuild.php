<?php

namespace App\Model;

use DateTimeImmutable;

readonly class SavedBuild
{
    public function __construct(
        public string $shareCode,
        public string $name,
        public DateTimeImmutable $createdAt,
        public Build $build,
    ) {
    }
}
