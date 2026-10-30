<?php

namespace App\Http;

class Router
{
    private array $routes = [];

    // In het pad wordt {id} een parameter die de controller kan uitlezen.
    public function add(string $method, string $path, callable $handler): void
    {
        $pattern = '#^' . preg_replace('/\{(\w+)\}/', '(?<$1>[^/]+)', $path) . '$#';
        $this->routes[] = [$method, $pattern, $handler];
    }

    public function dispatch(Request $request): Response
    {
        $pathExists = false;

        foreach ($this->routes as [$method, $pattern, $handler]) {
            if (!preg_match($pattern, $request->path, $matches)) {
                continue;
            }

            $pathExists = true;
            if ($method === $request->method) {
                $routeParams = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return $handler($request->withRouteParams($routeParams));
            }
        }

        if ($pathExists) {
            throw new ApiException(405, 'method_not_allowed', 'Dit pad bestaat, maar niet met deze methode.');
        }

        throw new ApiException(404, 'not_found', 'Dit pad bestaat niet.');
    }
}
