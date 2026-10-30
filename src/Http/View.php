<?php

namespace App\Http;

use DateTimeImmutable;

class View
{
    private const TEMPLATE_DIR = __DIR__ . '/../../templates/';

    public function render(string $template, array $data = [], int $status = 200): Response
    {
        $content = $this->partial($template, $data);
        $title = $data['title'] ?? 'PC-bouwplanner';

        return Response::html($this->partial('layout', ['content' => $content, 'title' => $title]), $status);
    }

    public function partial(string $template, array $data = []): string
    {
        extract($data);

        ob_start();
        require self::TEMPLATE_DIR . $template . '.php';

        return ob_get_clean();
    }

    public function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public function money(int $cents): string
    {
        return '€ ' . number_format($cents / 100, 2, ',', '.');
    }

    public function date(DateTimeImmutable $date): string
    {
        return $date->format('d-m-Y');
    }
}
