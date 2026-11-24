<?php

namespace App\Support;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, callable|array $handler, array $middleware): void
    {
        // Convert {param} to named regex
        $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $path);
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'pattern'    => '#^' . $pattern . '$#',
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');
        if ($uri === '//') {
            $uri = '/';
        }
        if (getenv('APP_DEBUG') === 'true' && isset($_GET['__routes'])) {
    header('Content-Type: text/plain');
    echo "Total routes: " . count($this->routes) . "\n\n";
    foreach ($this->routes as $r) {
        echo $r['method'] . ' ' . $r['path'] . PHP_EOL;
    }
    exit;
}

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);

                foreach ($route['middleware'] as $mw) {
                    $result = (new $mw())->handle();
                    if ($result === false) {
                        return;
                    }
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    (new $class())->$action(...$matches);
                } else {
                    $handler(...$matches);
                }
                return;
            }
        }

        http_response_code(404);
        View::render('shared.404', [], 'app');
    }
}