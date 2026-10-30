<?php

namespace App\Compatibility;

readonly class RuleResult
{
    public function __construct(
        public string $ruleName,
        public CheckStatus $status,
        public string $message,
    ) {
    }
}
