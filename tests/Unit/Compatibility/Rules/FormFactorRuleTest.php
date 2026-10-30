<?php

namespace Tests\Unit\Compatibility\Rules;

use App\Compatibility\CheckStatus;
use App\Compatibility\Rules\FormFactorRule;
use App\Model\Build;
use PHPUnit\Framework\TestCase;
use Tests\Support\Parts;

class FormFactorRuleTest extends TestCase
{
    public function testSmallerBoardFitsInBiggerCase(): void
    {
        $build = new Build(
            Parts::motherboard(formFactor: Parts::microAtx()),
            Parts::pcCase(maxFormFactor: Parts::atx()),
        );

        $result = (new FormFactorRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testSameSizeFits(): void
    {
        $build = new Build(
            Parts::motherboard(formFactor: Parts::atx()),
            Parts::pcCase(maxFormFactor: Parts::atx()),
        );

        $result = (new FormFactorRule())->evaluate($build);

        $this->assertSame(CheckStatus::Ok, $result->status);
    }

    public function testBiggerBoardGivesErrorWithBothSizes(): void
    {
        $build = new Build(
            Parts::motherboard(formFactor: Parts::atx()),
            Parts::pcCase(maxFormFactor: Parts::miniItx()),
        );

        $result = (new FormFactorRule())->evaluate($build);

        $this->assertSame(CheckStatus::Error, $result->status);
        $this->assertSame('Het moederbord is ATX, maar in deze kast past maximaal Mini-ITX.', $result->message);
    }
}
