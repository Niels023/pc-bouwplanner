<?php

namespace Tests\Pages;

class HomePageTest extends PageTestCase
{
    public function testHomePageShowsBothChoicesAndCodeField(): void
    {
        $response = $this->get('/');

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('text/html', $response->headers['Content-Type']);
        $this->assertPageContains('href="/bouwen/cpu"', $response);
        $this->assertPageContains('href="/aanbeveling"', $response);
        $this->assertPageContains('name="code"', $response);
    }

    public function testPageHasLanguageTitleAndSkipLink(): void
    {
        $response = $this->get('/');

        $this->assertPageContains('<html lang="nl">', $response);
        $this->assertPageContains('<title>', $response);
        $this->assertPageContains('href="#inhoud"', $response);
    }

    public function testUnknownPageGivesHtmlNotFound(): void
    {
        $response = $this->get('/bestaat-niet');

        $this->assertSame(404, $response->status);
        $this->assertStringContainsString('text/html', $response->headers['Content-Type']);
        $this->assertPageContains('Deze pagina bestaat niet', $response);
    }

    public function testApiPathStillGivesJsonError(): void
    {
        $response = $this->get('/api/bestaat-niet');

        $this->assertStringContainsString('application/json', $response->headers['Content-Type']);
    }
}
