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
        $report = $this->report(true, CheckStatus::Ok, CheckStatus::Ok);

        $this->assertSame(OverallStatus::Ok, $report->overallStatus());
    }

    public function testErrorAndWarningCountAsProblems(): void
    {
        $this->assertTrue($this->report(true, CheckStatus::Error)->hasProblems());
        $this->assertTrue($this->report(true, CheckStatus::Warning)->hasProblems());
    }

    public function testIncompleteBuildIsNoProblem(): void
    {
        $this->assertFalse($this->report(false, CheckStatus::NotChecked)->hasProblems());
    }

    public function testIncompleteBuildGivesIncomplete(): void
    {
        $report = $this->report(false, CheckStatus::Ok, CheckStatus::NotChecked);

        $this->assertSame(OverallStatus::Incomplete, $report->overallStatus());
    }

    public function testIncompleteBuildWithAllRulesOkIsStillIncomplete(): void
    {
        $report = $this->report(false, CheckStatus::Ok, CheckStatus::Ok);

        $this->assertSame(OverallStatus::Incomplete, $report->overallStatus());
    }

    public function testWarningGoesBeforeIncomplete(): void
    {
        $report = $this->report(false, CheckStatus::NotChecked, CheckStatus::Warning);

        $this->assertSame(OverallStatus::Warning, $report->overallStatus());
    }

    public function testErrorGoesBeforeEverythingElse(): void
    {
        $report = $this->report(false, CheckStatus::Warning, CheckStatus::NotChecked, CheckStatus::Error);

        $this->assertSame(OverallStatus::Error, $report->overallStatus());
    }

    private function report(bool $buildComplete, CheckStatus ...$statuses): CheckReport
    {
        $results = array_map(fn (CheckStatus $status) => new RuleResult('test', $status, 'melding'), $statuses);

        return new CheckReport($results, $buildComplete, 0, 0);
    }
}
