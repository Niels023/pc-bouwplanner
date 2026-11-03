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

    public function testEmptyBudgetGivesTheCheapestBuild(): void
    {
        $response = $this->get('/aanbeveling', ['budget' => '', 'goal' => 'gaming']);

        $this->assertSame(200, $response->status);
        $this->assertPageContains('Dit is de goedkoopste build die bij je keuzes past.', $response);
        $this->assertPageContains('De goedkoopste keuze die past', $response);
        $this->assertPageNotContains('Over van je budget', $response);
    }

    public function testFormAsksForScreenAndGamesWithExamples(): void
    {
        $response = $this->get('/aanbeveling');

        $this->assertPageContains('Full HD', $response);
        $this->assertPageContains('1920 x 1080', $response);
        $this->assertPageContains('Fortnite', $response);
        $this->assertPageContains('Cyberpunk 2077', $response);
        $this->assertPageContains('name="resolution"', $response);
        $this->assertPageContains('name="game_type"', $response);
    }

    public function testChoosingScreenAndGamesShowsWhatTheBuildCanDo(): void
    {
        $query = ['budget' => '', 'goal' => 'gaming', 'resolution' => '1440p', 'game_type' => 'heavy'];

        $response = $this->get('/aanbeveling', $query);

        $this->assertPageContains('ongeveer 67 FPS in zware games op 1440p', $response);
        $this->assertPageContains('Radeon RX 9060 XT', $response);
        $this->assertPageContains('geschikt voor zware games op 1440p', $response);
    }

    public function testChoicesStaySelected(): void
    {
        $query = ['goal' => 'gaming', 'resolution' => '4k', 'game_type' => 'light'];

        $response = $this->get('/aanbeveling', $query);

        $this->assertMatchesRegularExpression('/value="4k"\s+checked/', $response->body);
        $this->assertMatchesRegularExpression('/value="light"\s+checked/', $response->body);
    }

    public function testBudgetTooLowLinksToTheCheapestBuild(): void
    {
        $query = ['budget' => '1300', 'goal' => 'gaming', 'resolution' => '4k', 'game_type' => 'heavy'];

        $response = $this->get('/aanbeveling', $query);

        $this->assertSame(422, $response->status);
        $this->assertPageContains('Voor zware games op 4K heb je minimaal', $response);
        $this->assertPageContains('/aanbeveling?goal=gaming&resolution=4k&game_type=heavy', $response);
    }

    public function testAdoptingKeepsTheChoices(): void
    {
        $query = ['budget' => '', 'goal' => 'gaming', 'resolution' => '4k', 'game_type' => 'heavy'];

        $this->postForm('/aanbeveling/overnemen', [], $query);

        $this->assertPageContains('Radeon RX 9070 XT', $this->get('/overzicht'));
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
