<?php

namespace App\Http;

use App\Database\Connection;
use App\Http\Controller\PartApiController;
use App\Repository\PartRepository;
use Throwable;

class Application
{
    public function __construct(private array $config)
    {
    }

    public function handle(Request $request): Response
    {
        try {
            return $this->createRouter()->dispatch($request);
        } catch (ApiException $e) {
            return $this->errorResponse($e->status, $e->errorCode, $e->getMessage());
        } catch (Throwable $e) {
            error_log((string) $e);
            $message = 'Er ging iets mis op de server. Probeer het later opnieuw.';

            return $this->errorResponse(500, 'server_error', $message);
        }
    }

    private function createRouter(): Router
    {
        $pdo = Connection::connect($this->config['db']);
        $parts = new PartRepository($pdo);

        $partApi = new PartApiController($parts);

        $router = new Router();
        $router->add('GET', '/api/parts', $partApi->index(...));
        $router->add('GET', '/api/parts/{id}', $partApi->show(...));

        return $router;
    }

    private function errorResponse(int $status, string $code, string $message): Response
    {
        return Response::json(['error' => ['code' => $code, 'message' => $message]], $status);
    }
}
