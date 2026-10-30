<?php

namespace Tests\Pages;

class SavedBuildPageTest extends PageTestCase
{
    public function testSavedBuildShowsNameCodePartsAndCheck(): void
    {
        $code = $this->saveBuild('Terug te vinden');

        $response = $this->get("/build/$code");

        $this->assertSame(200, $response->status);
        $this->assertPageContains('Terug te vinden', $response);
        $this->assertPageContains($code, $response);
        $this->assertPageContains('Ryzen 5 5600 Boxed', $response);
        $this->assertPageContains('€ 124,96', $response);
        $this->assertPageContains('Kies eerst een moederbord.', $response);
    }

    public function testUnknownCodeGivesNotFoundPage(): void
    {
        $response = $this->get('/build/onbekend');

        $this->assertSame(404, $response->status);
        $this->assertPageContains('Geen build gevonden met deze code', $response);
    }

    public function testCodeFormRedirectsToBuild(): void
    {
        $this->assertRedirectsTo('/build/abc23456', $this->get('/build/open', ['code' => ' abc23456 ']));
    }

    public function testEmptyCodeFormGoesBackHome(): void
    {
        $this->assertRedirectsTo('/', $this->get('/build/open', ['code' => '']));
    }

    public function testNameIsEscaped(): void
    {
        $code = $this->saveBuild('<script>alert(1)</script>');

        $response = $this->get("/build/$code");

        $this->assertStringNotContainsString('<script>alert(1)', $response->body);
        $this->assertStringContainsString('&lt;script&gt;', $response->body);
    }

    private function saveBuild(string $name): string
    {
        $this->postForm('/bouwen/cpu/kiezen', ['part_id' => $this->partId('Ryzen 5 5600 Boxed')]);
        $location = $this->postForm('/opslaan', ['name' => $name])->headers['Location'];

        return basename($location);
    }
}
