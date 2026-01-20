<?php

namespace core;

class View
{
    private static function make(string $view, array $data = []): string
    {
        $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $viewPath . ".twig";

        if (!file_exists($file)) {
            http_response_code(500);
            return "View not found: " . htmlspecialchars($file);
        }

        // For now, return simple content - Twig integration can be added later
        return file_get_contents($file);
    }

    public static function render(string $view, array $data = [], string $layout = 'layouts.main'): string
    {
        $content = self::make($view, $data);

        return self::make($layout, array_merge($data, [
            'content' => $content,
        ]));
    }
}
