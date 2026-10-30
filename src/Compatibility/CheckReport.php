<?php

namespace App\Compatibility;

readonly class CheckReport
{
    /** @param RuleResult[] $results */
    public function __construct(
        public array $results,
        public int $totalPriceCents,
        public int $estimatedPowerW,
    ) {
    }

    public function overallStatus(): OverallStatus
    {
        $statuses = array_map(fn (RuleResult $result) => $result->status, $this->results);

        return match (true) {
            in_array(CheckStatus::Error, $statuses, true) => OverallStatus::Error,
            in_array(CheckStatus::Warning, $statuses, true) => OverallStatus::Warning,
            in_array(CheckStatus::NotChecked, $statuses, true) => OverallStatus::Incomplete,
            default => OverallStatus::Ok,
        };
    }
}
