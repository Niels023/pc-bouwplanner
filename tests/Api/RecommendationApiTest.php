<?php

namespace Tests\Api;

use App\Http\Response;
use App\Recommendation\Goal;

class RecommendationApiTest extends ApiTestCase
{
    public function testInvalidBudgetGivesError(): void
    {
        foreach (['0', '-5', 'abc', '12.50', '1e3', '100001'] as $budget) {
            $response = $this->recommendation(['budget' => $budget, 'goal' => 'gaming']);

            $this->assertError($response, 422, 'invalid_budget');
        }
    }

    public function testNoBudgetGivesTheCheapestBuild(): void
    {
        $data = $this->data($this->recommendation(['goal' => 'gaming']));

        $this->assertNull($data['budget_eur']);
        $this->assertSame(0, $data['leftover_eur']);
        $this->assertSame('ok', $data['check']['overall_status']);
        $this->assertEqualsWithDelta($this->minimumFor(Goal::Gaming), $data['check']['total_price_eur'], 1);
    }

    public function testEmptyBudgetIsTheSameAsNoBudget(): void
    {
        $none = $this->recommendation(['goal' => 'gaming']);
        $empty = $this->recommendation(['goal' => 'gaming', 'budget' => '']);

        $this->assertSame($none->body, $empty->body);
    }

    public function testTargetPicksAGpuThatIsFastEnough(): void
    {
        $expected = [
            ['1080p', 'heavy', 'GeForce RTX 5060'],
            ['1440p', 'heavy', 'Radeon RX 9060 XT'],
            ['4k', 'heavy', 'Radeon RX 9070 XT'],
            ['1080p', 'light', 'GeForce RTX 5060'],
            ['1440p', 'light', 'GeForce RTX 5060'],
            ['4k', 'light', 'Radeon RX 9060 XT'],
        ];

        foreach ($expected as [$resolution, $gameType, $chip]) {
            $query = ['goal' => 'gaming', 'resolution' => $resolution, 'game_type' => $gameType];

            $this->assertSame($chip, $this->gpuChip($this->recommendation($query)), "$gameType op $resolution");
        }
    }

    public function testSummaryIsInPlainLanguage(): void
    {
        $query = ['goal' => 'gaming', 'resolution' => '1440p', 'game_type' => 'heavy'];

        $summary = $this->data($this->recommendation($query))['summary'];

        $this->assertStringContainsString('ongeveer 67 FPS in zware games op 1440p', $summary);
    }

    public function testBudgetTooLowForTargetNamesTheTarget(): void
    {
        $query = ['budget' => '1300', 'goal' => 'gaming', 'resolution' => '4k', 'game_type' => 'heavy'];

        $response = $this->recommendation($query);

        $this->assertError($response, 422, 'budget_too_low');
        $this->assertStringContainsString('Voor zware games op 4K', $this->data($response)['error']['message']);
    }

    public function testHigherTargetCostsMore(): void
    {
        $light = $this->totalFor(['goal' => 'gaming', 'resolution' => '1080p', 'game_type' => 'light']);
        $heavy = $this->totalFor(['goal' => 'gaming', 'resolution' => '4k', 'game_type' => 'heavy']);

        $this->assertGreaterThan($light, $heavy);
    }

    public function testEveryTargetAndBudgetGivesABuildThatFits(): void
    {
        foreach (['1080p', '1440p', '4k'] as $resolution) {
            foreach (['light', 'heavy'] as $gameType) {
                $target = ['goal' => 'gaming', 'resolution' => $resolution, 'game_type' => $gameType];
                $minimum = (int) ceil($this->totalFor($target));

                for ($budget = $minimum; $budget <= 5000; $budget += 250) {
                    $data = $this->data($this->recommendation($target + ['budget' => (string) $budget]));

                    $this->assertSame('ok', $data['check']['overall_status']);
                    $this->assertLessThanOrEqual($budget, $data['check']['total_price_eur']);
                }
            }
        }
    }

    public function testStreamingAlsoUsesTheTarget(): void
    {
        $query = ['goal' => 'streaming', 'resolution' => '4k', 'game_type' => 'heavy'];

        $this->assertSame('Radeon RX 9070 XT', $this->gpuChip($this->recommendation($query)));
    }

    public function testWorkIgnoresTheTarget(): void
    {
        $query = ['goal' => 'work', 'resolution' => '4k', 'game_type' => 'heavy'];

        $data = $this->data($this->recommendation($query));

        $this->assertNull($data['summary']);
        $this->assertSame('ok', $data['check']['overall_status']);
    }

    public function testResolutionWithoutGameTypeGivesError(): void
    {
        $this->assertError(
            $this->recommendation(['goal' => 'gaming', 'resolution' => '4k']),
            422,
            'invalid_game_target',
        );
    }

    public function testUnknownResolutionOrGameTypeGivesError(): void
    {
        foreach ([['8k', 'heavy'], ['1080p', 'middel']] as [$resolution, $gameType]) {
            $query = ['goal' => 'gaming', 'resolution' => $resolution, 'game_type' => $gameType];

            $this->assertError($this->recommendation($query), 422, 'invalid_game_target');
        }
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

    public function testWorkGetsNoGpu(): void
    {
        $data = $this->data($this->recommendation(['budget' => '2500', 'goal' => 'work']));

        $categories = array_map(fn (array $choice) => $choice['part']['category'], $data['choices']);
        $this->assertNotContains('gpu', $categories);
        $this->assertSame('ok', $data['check']['overall_status']);
    }

    public function testWorkIsCheaperThanGaming(): void
    {
        $this->assertLessThan($this->minimumFor(Goal::Gaming), $this->minimumFor(Goal::Work));
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

    private function totalFor(array $query): float
    {
        return $this->data($this->recommendation($query))['check']['total_price_eur'];
    }

    private function gpuChip(Response $response): string
    {
        foreach ($this->data($response)['choices'] as $choice) {
            if ($choice['part']['category'] === 'gpu') {
                return $choice['part']['specs']['chip'];
            }
        }

        $this->fail('Geen videokaart in de aanbeveling');
    }

    private function minimumFor(Goal $goal): int
    {
        $response = $this->recommendation(['budget' => '1', 'goal' => $goal->value]);
        preg_match('/€ ([\d.]+)/', $this->data($response)['error']['message'], $matches);

        return (int) str_replace('.', '', $matches[1]);
    }
}
