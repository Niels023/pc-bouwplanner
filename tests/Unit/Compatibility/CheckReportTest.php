<?php

namespace Tests\Unit\Compatibility;

use App\Compatibility\CheckReport;
use App\Compatibility\CheckStatus;
use App\Compatibility\OverallStatus;
use App\Compatibility\RuleResult;
use PHPUnit\Framework\TestCase;

class CheckReportTest extends TestCase
{
    public function testAllOkGivesOk(): void
    {
        $report = $this->report(CheckStatus::Ok, CheckStatus::Ok);

        $this->assertSame(OverallStatus::Ok, $report->overallStatus());
    }

    public function testNotCheckedGivesIncomplete(): void
    {
        $report = $this->report(CheckStatus::Ok, CheckStatus::NotChecked);

        $this->assertSame(OverallStatus::Incomplete, $report->overallStatus());
    }

    public function testWarningGoesBeforeIncomplete(): void
    {
        $report = $this->report(CheckStatus::NotChecked, CheckStatus::Warning);

        $this->assertSame(OverallStatus::Warning, $report->overallStatus());
    }

    public function testErrorGoesBeforeEverythingElse(): void
    {
        $report = $this->report(CheckStatus::Warning, CheckStatus::NotChecked, CheckStatus::Error, CheckStatus::Ok);

        $this->assertSame(OverallStatus::Error, $report->overallStatus());
    }

    private function report(CheckStatus ...$statuses): CheckReport
    {
        $results = array_map(fn (CheckStatus $status) => new RuleResult('test', $status, 'melding'), $statuses);

        return new CheckReport($results, 0, 0);
    }
}
