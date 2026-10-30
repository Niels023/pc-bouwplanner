<?php

namespace App\Http;

readonly class Response
{
    public function __construct(
        public int $status,
        public string $body,
        public array $headers = [],
    ) {
    }

    public static function json(array $data, int $status = 200): self
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        return new self($status, $body, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    public static function html(string $body, int $status = 200): self
    {
        return new self($status, $body, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    // 303: de browser doet daarna een GET, dus vernieuwen verstuurt het formulier niet opnieuw.
    public static function redirect(string $location): self
    {
        return new self(303, '', ['Location' => $location]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->body;
    }
}
