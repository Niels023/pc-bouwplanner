<?php

namespace App\Http;

readonly class Request
{
    public function __construct(
        public string $method,
        public string $path,
        public array $queryParams = [],
        public string $body = '',
        public array $routeParams = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'],
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
            $_GET,
            file_get_contents('php://input'),
        );
    }

    public function withRouteParams(array $routeParams): self
    {
        return new self($this->method, $this->path, $this->queryParams, $this->body, $routeParams);
    }

    // Een leeg filter of iets als ?brand[]=x telt als niet ingevuld.
    public function query(string $name): ?string
    {
        $value = $this->queryParams[$name] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function json(): array
    {
        $data = json_decode($this->body, true);

        if (!is_array($data)) {
            throw new ApiException(400, 'invalid_json', 'De body is geen geldige JSON.');
        }

        return $data;
    }
}
