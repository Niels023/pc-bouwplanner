<?php

namespace Tests\Api;

use App\Http\Response;
use App\Recommendation\Goal;

class RecommendationApiTest extends ApiTestCase
{
    public function testInvalidBudgetGivesError(): void
    {
        foreach (['0', '-5', 'abc', '12.50', '1e3', '100001', ''] as $budget) {
            $response = $this->recommendation(['budget' => $budget, 'goal' => 'gaming']);

            $this->assertError($response, 422, 'invalid_budget');
        }
    }

    public function testMissingBudgetGivesError(): void
    {
        $this->assertError($this->recommendation(['goal' => 'gaming']), 422, 'invalid_budget');
    }

    public function testUnknownGoalGivesErrorWithOptions(): void
    {
        $response = $this->recommendation(['budget' => '2000', 'goal' => 'wandelen']);

        $this->assertError($response, 422, 'invalid_goal');
        $this->assertStringContainsString('gaming, work, streaming', $this->data($response)['error']['message']);
    }

    public function testMissingGoalGivesError(): void
    {
        $this->assertError($this->recommendation(['budget' => '2000']), 422, 'invalid_goal');
    }

    public function testBudgetTooLowNamesMinimumAmount(): void
    {
        $response = $this->recommendation(['budget' => '1', 'goal' => 'gaming']);

        $this->assertError($response, 422, 'budget_too_low');
        $this->assertStringContainsString('Voor gamen heb je minimaal', $this->data($response)['error']['message']);
    }

    public function testMinimumAmountInMessageIsCorrect(): void
    {
        foreach (Goal::cases() as $goal) {
            $minimum = $this->minimumFor($goal);

            $tooLow = $this->recommendation(['budget' => (string) ($minimum - 1), 'goal' => $goal->value]);
            $enough = $this->recommendation(['budget' => (string) $minimum, 'goal' => $goal->value]);

            $this->assertError($tooLow, 422, 'budget_too_low');
            $this->assertSame(200, $enough->status);
        }
    }

    public function testResponseHasBuildReasonsAndLeftover(): void
    {
        $data = $this->data($this->recommendation(['budget' => '2000', 'goal' => 'gaming']));

        $this->assertSame('gaming', $data['goal']);
        $this->assertSame(2000, $data['budget_eur']);
        $this->assertCount(8, $data['choices']);
        foreach ($data['choices'] as $choice) {
            $this->assertNotEmpty($choice['part']['name']);
            $this->assertGreaterThan(0, $choice['category_budget_eur']);
            $this->assertStringContainsString('€', $choice['reason']);
        }
        $this->assertSame('ok', $data['check']['overall_status']);
        $this->assertEqualsWithDelta(2000 - $data['check']['total_price_eur'], $data['leftover_eur'], 0.001);
    }

    public function testEveryBudgetGivesCompleteBuildWithinBudget(): void
    {
        foreach (Goal::cases() as $goal) {
            for ($budget = $this->minimumFor($goal); $budget <= 6000; $budget += 100) {
                $query = ['budget' => (string) $budget, 'goal' => $goal->value];
                $data = $this->data($this->recommendation($query));

                $this->assertSame('ok', $data['check']['overall_status'], "Bij $budget voor {$goal->value}");
                $this->assertLessThanOrEqual($budget, $data['check']['total_price_eur']);
            }
        }
    }

    public function testGamingSpendsMoreOnGpuThanWork(): void
    {
        $gaming = $this->gpuPrice($this->recommendation(['budget' => '2500', 'goal' => 'gaming']));
        $work = $this->gpuPrice($this->recommendation(['budget' => '2500', 'goal' => 'work']));

        $this->assertGreaterThan($work, $gaming);
    }

    public function testSameRequestGivesSameAnswer(): void
    {
        $query = ['budget' => '1500', 'goal' => 'streaming'];

        $this->assertSame($this->recommendation($query)->body, $this->recommendation($query)->body);
    }

    private function recommendation(array $query): Response
    {
        return $this->get('/api/recommendation', $query);
    }

    private function minimumFor(Goal $goal): int
    {
        $response = $this->recommendation(['budget' => '1', 'goal' => $goal->value]);
        preg_match('/€ ([\d.]+)/', $this->data($response)['error']['message'], $matches);

        return (int) str_replace('.', '', $matches[1]);
    }

    private function gpuPrice(Response $response): float
    {
        foreach ($this->data($response)['choices'] as $choice) {
            if ($choice['part']['category'] === 'gpu') {
                return $choice['part']['price_eur'];
            }
        }

        $this->fail('Geen videokaart in de aanbeveling');
    }
}
