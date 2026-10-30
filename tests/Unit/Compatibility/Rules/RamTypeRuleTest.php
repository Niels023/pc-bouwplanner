<?php

namespace Tests\Unit\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\Rules\RamTypeRule;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class RamTypeRuleTest extends TestCase
{
    public function testSameRamTypeIsOk(): void
    {
        $build = new Build(Parts::motherboard(ramType: 'DDR5'), Parts::ram(ramType: 'DDR5'));

        $result = (new RamTypeRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testDifferentRamTypeGivesErrorWithBothTypes(): void
    {
        $build = new Build(Parts::motherboard(ramType: 'DDR5'), Parts::ram(ramType: 'DDR4'));

        $result = (new RamTypeRule())->evaluate($build);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('Het geheugen is DDR4, maar het moederbord ondersteunt alleen DDR5.', $result->message);
    }
}
