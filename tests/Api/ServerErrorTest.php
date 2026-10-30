<?php

namespace Tests\Api;

use App\Http\Application;
use App\Http\Request;
use PHPUnit\Framework\TestCase;

class ServerErrorTest extends TestCase
{
    public function testDatabaseProblemGivesGeneralMessage(): void
    {
        // De echte fout gaat naar het logbestand, dat hoeft niet in de testuitvoer
        $logFile = ini_set('error_log', sys_get_temp_dir() . '/pc-bouwplanner-test.log');
        $config = require __DIR__ . '/../../config/app.php';
        $config['db']['port'] = 1;

        $response = (new Application($config))->handle(new Request('GET', '/api/parts'));

        ini_set('error_log', (string) $logFile);
        $this->assertSame(500, $response->status);
        $error = json_decode($response->body, true)['error'];
        $this->assertSame('server_error', $error['code']);
        $this->assertStringNotContainsString('SQLSTATE', $error['message']);
    }
}
