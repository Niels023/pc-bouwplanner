<?php

namespace Tests\Api;

use App\Http\Application;
use App\Http\Request;
use App\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestDatabase;

abstract class ApiTestCase extends TestCase
{
    protected function handle(Request $request): Response
    {
        TestDatabase::seeded();
        $config = require __DIR__ . '/../../config/app.php';

        return (new Application($config))->handle($request);
    }

    protected function get(string $path, array $query = []): Response
    {
        return $this->handle(new Request('GET', $path, $query));
    }

    protected function postJson(string $path, array $data): Response
    {
        return $this->handle(new Request('POST', $path, [], json_encode($data)));
    }

    protected function data(Response $response): array
    {
        return json_decode($response->body, true);
    }

    protected function assertError(Response $response, int $status, string $code): void
    {
        $this->assertSame($status, $response->status);
        $this->assertSame($code, $this->data($response)['error']['code']);
        $this->assertNotEmpty($this->data($response)['error']['message']);
    }

    protected function partId(string $name): int
    {
        $statement = TestDatabase::seeded()->prepare('SELECT id FROM parts WHERE name = ?');
        $statement->execute([$name]);

        return $statement->fetchColumn();
    }
}
