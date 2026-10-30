<?php

namespace Tests\Pages;

class RecommendationPageTest extends PageTestCase
{
    public function testEmptyFormGivesNoError(): void
    {
        $response = $this->get('/aanbeveling');

        $this->assertSame(200, $response->status);
        $this->assertPageContains('name="budget"', $response);
        $this->assertPageContains('value="gaming"', $response);
        $this->assertStringNotContainsString('role="alert"', $response->body);
    }

    public function testResultShowsPartsWithReasonAndLeftover(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '2000', 'goal' => 'gaming']);

        $this->assertSame(200, $response->status);
        $this->assertPageContains('Sapphire Pulse Radeon RX 9070 XT', $response);
        $this->assertPageContains('Beste videokaart binnen het deelbudget van € 1.000.', $response);
        $this->assertPageContains('€ 15,46', $response);
        $this->assertPageContains('In orde', $response);
    }

    public function testFormKeepsItsValues(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '2000', 'goal' => 'work']);

        $this->assertStringContainsString('value="2000"', $response->body);
        $this->assertMatchesRegularExpression('/value="work"\s+checked/', $response->body);
    }

    public function testEmptyBudgetGivesMessage(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '', 'goal' => 'gaming']);

        $this->assertSame(422, $response->status);
        $this->assertStringContainsString('role="alert"', $response->body);
        $this->assertPageContains("Vul een budget in hele euro's in", $response);
    }

    public function testInvalidBudgetsGiveMessage(): void
    {
        foreach (['0', '-5', 'abc', '12.50'] as $budget) {
            $response = $this->get('/aanbeveling', ['budget' => $budget, 'goal' => 'gaming']);

            $this->assertPageContains("Vul een budget in hele euro's in", $response);
        }
    }

    public function testBudgetBelowMinimumNamesTheMinimum(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '1000', 'goal' => 'gaming']);

        $this->assertSame(422, $response->status);
        $this->assertPageContains('Voor gamen heb je minimaal € 1.172 nodig.', $response);
    }

    public function testUnknownGoalGivesMessage(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '2000', 'goal' => 'wandelen']);

        $this->assertSame(422, $response->status);
        $this->assertPageContains('Kies een doel', $response);
    }

    public function testResultHasButtonToAdoptTheBuild(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '2000', 'goal' => 'gaming']);

        $this->assertPageContains('/aanbeveling/overnemen?budget=2000&goal=gaming', $response);
    }

    public function testAdoptingPutsThePartsInThePlanner(): void
    {
        $response = $this->postForm('/aanbeveling/overnemen', [], ['budget' => '2000', 'goal' => 'gaming']);

        $this->assertRedirectsTo('/overzicht', $response);
        $overview = $this->get('/overzicht');
        $this->assertPageContains('Sapphire Pulse Radeon RX 9070 XT', $overview);
        $this->assertSame(0, substr_count($overview->body, 'Nog niet gekozen'));
    }

    public function testAdoptedPartCanBeChanged(): void
    {
        $this->postForm('/aanbeveling/overnemen', [], ['budget' => '2000', 'goal' => 'gaming']);

        $this->postForm('/bouwen/cooler/kiezen', ['part_id' => $this->partId('AK400')]);

        $this->assertPageContains('DeepCool AK400', $this->get('/overzicht'));
    }

    public function testAdoptingReplacesTheEarlierBuild(): void
    {
        $this->postForm('/bouwen/cpu/kiezen', ['part_id' => $this->partId('Ryzen 5 5600 Boxed')]);

        $this->postForm('/aanbeveling/overnemen', [], ['budget' => '2000', 'goal' => 'gaming']);

        $this->assertPageNotContains('Ryzen 5 5600 Boxed', $this->get('/overzicht'));
    }

    public function testAdoptingWorkBuildGivesCompleteBuildWithoutGpu(): void
    {
        $this->postForm('/aanbeveling/overnemen', [], ['budget' => '1500', 'goal' => 'work']);

        $overview = $this->get('/overzicht');

        $this->assertPageContains('In orde', $overview);
        $this->assertSame(1, substr_count($overview->body, 'Nog niet gekozen'));
    }

    public function testAdoptingWithTooLowBudgetGivesErrorPage(): void
    {
        $response = $this->postForm('/aanbeveling/overnemen', [], ['budget' => '100', 'goal' => 'gaming']);

        $this->assertSame(422, $response->status);
        $this->assertPageContains('heb je minimaal', $response);
    }

    public function testInputIsEscaped(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '"><script>alert(1)</script>', 'goal' => 'gaming']);

        $this->assertStringNotContainsString('<script>alert(1)', $response->body);
    }
}
