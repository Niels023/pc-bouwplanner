<?php

namespace App\Http;

use RuntimeException;

// Een fout die de gebruiker te zien mag krijgen.
class HttpException extends RuntimeException
{
    public function __construct(
        public readonly int $status,
        public readonly string $errorCode,
        string $message,
    ) {
        parent::__construct($message);
    }
}
