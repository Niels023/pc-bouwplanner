<?php

namespace Tests\Unit\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\Rules\SocketRule;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class SocketRuleTest extends TestCase
{
    public function testSameSocketIsOk(): void
    {
        $build = new Build(Parts::cpu(socket: 'AM5'), Parts::motherboard(socket: 'AM5'));

        $result = (new SocketRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testDifferentSocketGivesErrorWithBothSockets(): void
    {
        $build = new Build(Parts::cpu(socket: 'AM5'), Parts::motherboard(socket: 'AM4'));

        $result = (new SocketRule())->evaluate($build);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('De processor heeft socket AM5, maar het moederbord heeft socket AM4.', $result->message);
    }
}
