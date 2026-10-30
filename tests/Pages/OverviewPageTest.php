<?php

namespace Tests\Pages;

class OverviewPageTest extends PageTestCase
{
    public function testEmptyOverviewShowsAllStepsAsNotChosen(): void
    {
        $response = $this->get('/overzicht');

        $this->assertSame(200, $response->status);
        $this->assertSame(8, substr_count($response->body, 'Nog niet gekozen'));
        $this->assertPageContains('Nog niet compleet', $response);
    }

    public function testChosenPartsAndCheckResultsAreShown(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');
        $this->choose('motherboard', 'B550M Pro4');

        $response = $this->get('/overzicht');

        $this->assertPageContains('Ryzen 5 7600 Boxed', $response);
        $this->assertPageContains('B550M Pro4', $response);
        $this->assertPageContains('De processor heeft socket AM5, maar het moederbord heeft socket AM4.', $response);
        $this->assertPageContains('Kies eerst geheugen.', $response);
    }

    public function testSavingRedirectsToSavedBuild(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $response = $this->postForm('/opslaan', ['name' => 'Mijn pc']);

        $this->assertSame(303, $response->status);
        $this->assertMatchesRegularExpression('#^/build/[a-z2-9]{8}$#', $response->headers['Location']);
        $this->assertPageContains('Mijn pc', $this->get($response->headers['Location']));
    }

    public function testSavingWithoutNameGivesMessage(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $response = $this->postForm('/opslaan', ['name' => '  ']);

        $this->assertSame(422, $response->status);
        $this->assertPageContains('Geef de build een naam van 1 tot 60 tekens.', $response);
    }

    public function testTooLongNameStaysInTheField(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');
        $name = str_repeat('a', 61);

        $response = $this->postForm('/opslaan', ['name' => $name]);

        $this->assertSame(422, $response->status);
        $this->assertStringContainsString("value=\"$name\"", $response->body);
    }

    public function testEmptyBuildCannotBeSaved(): void
    {
        $response = $this->postForm('/opslaan', ['name' => 'Leeg']);

        $this->assertSame(422, $response->status);
        $this->assertPageContains('Kies minstens één onderdeel', $response);
    }

    private function choose(string $category, string $partName): void
    {
        $this->postForm("/bouwen/$category/kiezen", ['part_id' => $this->partId($partName)]);
    }
}
