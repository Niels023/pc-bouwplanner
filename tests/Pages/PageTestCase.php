<?php

namespace Tests\Pages;

use App\Http\Request;
use App\Http\Response;
use Tests\Api\ApiTestCase;

abstract class PageTestCase extends ApiTestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function postForm(string $path, array $form = [], array $query = []): Response
    {
        return $this->handle(new Request('POST', $path, $query, '', [], $form));
    }

    protected function assertRedirectsTo(string $location, Response $response): void
    {
        $this->assertSame(303, $response->status);
        $this->assertSame($location, $response->headers['Location']);
    }

    protected function assertPageContains(string $text, Response $response): void
    {
        $this->assertStringContainsString($text, html_entity_decode($response->body, ENT_QUOTES));
    }

    protected function assertPageNotContains(string $text, Response $response): void
    {
        $this->assertStringNotContainsString($text, html_entity_decode($response->body, ENT_QUOTES));
    }
}
