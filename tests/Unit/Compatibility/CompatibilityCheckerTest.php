<?php

namespace Tests\Unit\Compatibility;

use App\Compatibility\CheckReport;
use App\Compatibility\CheckStatus;
use App\Compatibility\CompatibilityChecker;
use App\Compatibility\OverallStatus;
use App\Compatibility\RuleResult;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class CompatibilityCheckerTest extends TestCase
{
    private CompatibilityChecker $checker;

    protected function setUp(): void
    {
        $config = require __DIR__ . '/../../../config/app.php';
        $this->checker = CompatibilityChecker::withAllRules($config['psu_margin_percent']);
    }

    public function testEmptyBuildChecksNothing(): void
    {
        $report = $this->checker->check(new Build());

        $this->assertCount(6, $report->results);
        foreach ($report->results as $result) {
            $this->assertSame(CheckStatus::NotChecked, $result->status);
        }
        $this->assertSame(OverallStatus::Incomplete, $report->overallStatus());
    }

    public function testMissingPartsAreNamedInMessage(): void
    {
        $report = $this->checker->check(new Build());

        $this->assertSame('Kies eerst een processor en een moederbord.', $this->resultFor($report, 'socket')->message);
        $this->assertSame('Kies eerst een moederbord en geheugen.', $this->resultFor($report, 'ram_type')->message);
    }

    public function testOnlyMissingPartIsNamed(): void
    {
        $report = $this->checker->check(new Build(Parts::cpu()));

        $this->assertSame('Kies eerst een moederbord.', $this->resultFor($report, 'socket')->message);
        $this->assertSame('Kies eerst een voeding.', $this->resultFor($report, 'psu_wattage')->message);
    }

    public function testPsuWithoutCpuIsNotChecked(): void
    {
        $report = $this->checker->check(new Build(Parts::psu()));

        $this->assertSame(CheckStatus::NotChecked, $this->resultFor($report, 'psu_wattage')->status);
    }

    public function testCompleteBuildThatFitsIsOk(): void
    {
        $report = $this->checker->check($this->completeBuild());

        $this->assertSame(OverallStatus::Ok, $report->overallStatus());
        foreach ($report->results as $result) {
            $this->assertSame(CheckStatus::Ok, $result->status, $result->message);
        }
    }

    public function testBuildWithoutStorageIsNotComplete(): void
    {
        $build = new Build(
            Parts::cpu(),
            Parts::motherboard(),
            Parts::ram(),
            Parts::gpu(),
            Parts::cooler(),
            Parts::pcCase(),
            Parts::psu(),
        );

        $report = $this->checker->check($build);

        foreach ($report->results as $result) {
            $this->assertSame(CheckStatus::Ok, $result->status, $result->message);
        }
        $this->assertSame(OverallStatus::Incomplete, $report->overallStatus());
    }

    public function testOneProblemMakesTheWholeBuildAnError(): void
    {
        $report = $this->checker->check(new Build(Parts::cpu(socket: 'AM4'), Parts::motherboard(socket: 'AM5')));

        $this->assertSame(CheckStatus::Error, $this->resultFor($report, 'socket')->status);
        $this->assertSame(OverallStatus::Error, $report->overallStatus());
    }

    public function testReportContainsTotals(): void
    {
        $build = $this->completeBuild();

        $report = $this->checker->check($build);

        $this->assertSame($build->totalPriceCents(), $report->totalPriceCents);
        $this->assertSame($build->estimatedPowerW(), $report->estimatedPowerW);
    }

    private function completeBuild(): Build
    {
        return new Build(
            Parts::cpu(),
            Parts::motherboard(),
            Parts::ram(),
            Parts::gpu(),
            Parts::cooler(),
            Parts::pcCase(),
            Parts::storage(),
            Parts::psu(),
        );
    }

    private function resultFor(CheckReport $report, string $ruleName): RuleResult
    {
        foreach ($report->results as $result) {
            if ($result->ruleName === $ruleName) {
                return $result;
            }
        }

        $this->fail("Geen resultaat voor regel $ruleName");
    }
}
