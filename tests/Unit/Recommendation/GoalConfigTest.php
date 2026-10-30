<?php

namespace Tests\Unit\Recommendation;

use App\Model\Category;
use App\Recommendation\Goal;
use PHPUnit\Framework\TestCase;

class GoalConfigTest extends TestCase
{
    private array $goals;

    protected function setUp(): void
    {
        $this->goals = (require __DIR__ . '/../../../config/app.php')['goals'];
    }

    public function testEveryGoalHasSettings(): void
    {
        foreach (Goal::cases() as $goal) {
            $this->assertArrayHasKey($goal->value, $this->goals);
        }
    }

    public function testSharesOfEveryGoalAddUpTo100(): void
    {
        foreach ($this->goals as $name => $settings) {
            $this->assertSame(100, array_sum($settings['shares']), "Verdeling van $name");
        }
    }

    public function testEveryGoalHasAShareForEveryCategory(): void
    {
        $categories = array_column(Category::cases(), 'value');

        foreach ($this->goals as $settings) {
            $this->assertEqualsCanonicalizing($categories, array_keys($settings['shares']));
        }
    }

    public function testUpgradeOrderOnlyContainsExistingCategories(): void
    {
        foreach ($this->goals as $settings) {
            foreach ($settings['upgrade_order'] as $category) {
                $this->assertNotNull(Category::tryFrom($category));
            }
        }
    }

    public function testPsuIsNeverUpgraded(): void
    {
        foreach ($this->goals as $settings) {
            $this->assertNotContains(Category::Psu->value, $settings['upgrade_order']);
        }
    }
}
