<?php

namespace App\Support;

class View
{
    public static function render(string $view, array $data = [], string $layout = 'app'): void
{
    $root = dirname(__DIR__, 2) . '/resources/views';
    $viewPath = $root . '/' . str_replace('.', '/', $view) . '.php';

    if (!is_file($viewPath)) {
        http_response_code(500);
        echo "View not found: " . htmlspecialchars($view);
        return;
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $viewPath;
    $__simap_content = ob_get_clean();

    if ($layout === '' || $layout === null) {
        echo $__simap_content;
        return;
    }

    $layoutPath = $root . '/layouts/' . $layout . '.php';
    if (!is_file($layoutPath)) {
        echo $__simap_content;
        return;
    }

    // Alias agar layout bisa pakai $content seperti sebelumnya
    $content = $__simap_content;
    require $layoutPath;
}

    public static function partial(string $view, array $data = []): string
    {
        $root = dirname(__DIR__, 2) . '/resources/views';
        $viewPath = $root . '/' . str_replace('.', '/', $view) . '.php';
        if (!is_file($viewPath)) {
            return '';
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }
}