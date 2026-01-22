<?php

namespace core;

class View
{
    private static function interpolate(string $template, array $data = [], array $rawKeys = []): string
    {
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $replace = in_array($key, $rawKeys, true)
                ? (string) $value
                : Security::escape((string) $value);

            $template = str_replace(
                ['{{ ' . $key . ' }}', '{{' . $key . '}}'],
                $replace,
                $template
            );
        }

        return $template;
    }

    private static function make(string $view, array $data = [], array $rawKeys = []): string
    {
        $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $viewPath . ".twig";

        if (!file_exists($file)) {
            http_response_code(500);
            return "View not found: " . htmlspecialchars($file);
        }

        // For now, return simple content - Twig integration can be added later
        $template = file_get_contents($file);
        return self::interpolate($template, $data, $rawKeys);
    }

    public static function render(string $view, array $data = [], string $layout = 'layouts.main'): string
    {
        $content = self::make($view, $data);

        return self::make($layout, array_merge($data, [
            'content' => $content,
        ]), ['content']);
    }
}
