<?php

namespace Tests\Pages;

class CompareBuildsPageTest extends PageTestCase
{
    public function testShowsBothBuildsPerCategory(): void
    {
        [$first, $second] = $this->saveTwoBuilds();

        $response = $this->get('/vergelijken', ['a' => $first, 'b' => $second]);

        $this->assertSame(200, $response->status);
        $this->assertPageContains('Goedkoop', $response);
        $this->assertPageContains('Sneller', $response);
        $this->assertPageContains('Ryzen 5 5600 Boxed', $response);
        $this->assertPageContains('Ryzen 5 7600 Boxed', $response);
        $this->assertPageContains('B550M Pro4', $response);
        $this->assertSame(13, substr_count($response->body, 'Nog niet gekozen'));
    }

    public function testShowsDifferenceInPricePerCategory(): void
    {
        [$first, $second] = $this->saveTwoBuilds();

        $response = $this->get('/vergelijken', ['a' => $first, 'b' => $second]);

        // Processor: 176,82 - 124,96. Moederbord: alleen in de eerste build.
        $this->assertPageContains('€ 51,86 duurder', $response);
        $this->assertPageContains('€ 95,48 goedkoper', $response);
    }

    public function testShowsDifferenceInTotalPriceAndPower(): void
    {
        [$first, $second] = $this->saveTwoBuilds();

        $response = $this->get('/vergelijken', ['a' => $first, 'b' => $second]);

        // Totaal 220,44 en 128 W tegen 176,82 en 88 W.
        $this->assertPageContains('€ 43,62 goedkoper', $response);
        $this->assertPageContains('40 W minder', $response);
    }

    public function testSameBuildHasNoDifference(): void
    {
        [$first] = $this->saveTwoBuilds();

        $response = $this->get('/vergelijken', ['a' => $first, 'b' => $first]);

        $this->assertPageContains('Zelfde prijs', $response);
        $this->assertPageContains('Zelfde verbruik', $response);
    }

    public function testUnknownOrMissingCodeGivesNotFound(): void
    {
        [$first] = $this->saveTwoBuilds();

        foreach ([['a' => $first, 'b' => 'onbekend'], ['a' => $first]] as $query) {
            $response = $this->get('/vergelijken', $query);

            $this->assertSame(404, $response->status);
            $this->assertPageContains('Geen build gevonden met deze code', $response);
        }
    }

    public function testSavedBuildHasFormToCompare(): void
    {
        [$first] = $this->saveTwoBuilds();

        $response = $this->get("/build/$first");

        $this->assertPageContains('action="/vergelijken"', $response);
        $this->assertPageContains('name="b"', $response);
        $this->assertPageContains("name=\"a\" value=\"$first\"", $response);
    }

    /** @return string[] */
    private function saveTwoBuilds(): array
    {
        $this->postForm('/bouwen/cpu/kiezen', ['part_id' => $this->partId('Ryzen 5 5600 Boxed')]);
        $this->postForm('/bouwen/motherboard/kiezen', ['part_id' => $this->partId('B550M Pro4')]);
        $first = basename($this->postForm('/opslaan', ['name' => 'Goedkoop'])->headers['Location']);

        $_SESSION = [];
        $this->postForm('/bouwen/cpu/kiezen', ['part_id' => $this->partId('Ryzen 5 7600 Boxed')]);
        $second = basename($this->postForm('/opslaan', ['name' => 'Sneller'])->headers['Location']);

        return [$first, $second];
    }
}
