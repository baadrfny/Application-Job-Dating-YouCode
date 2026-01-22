<?php

namespace core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected Environment $twig;
    public function __construct() {
    $loader = new FilesystemLoader(dirname(__DIR__) . '/views');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);
        
    }

    public function render(string $view, array $data = []) {
        $view = str_ends_with($view, '.twig') ? $view : $view . '.twig';

        if (!isset($data['title'])) $data['title'] = 'Job Dating';
    if (!isset($data['error_display'])) $data['error_display'] = 'none';

        echo $this->twig->render($view, $data);
    }


    protected function view(string $view, array $data = [], string $layout = 'layouts.main'): string
    {
        if (!array_key_exists('title', $data)) {
            $data['title'] = '';
        }
        if (!array_key_exists('error', $data)) {
            $data['error'] = '';
        }
        if (!array_key_exists('error_display', $data)) {
            $data['error_display'] = 'none';
        }
        $data['csrf_token'] = Security::generateCSRFToken();
        return View::render($view, $data, $layout);
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            Response::redirect('/login');
            exit;
        }
    }

    protected function requireAdmin(): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/login');
            exit;
        }
    }

    protected function validateCSRF(Request $request): bool
    {
        $token = $request->input('csrf_token', '');
        return Security::verifyCSRFToken($token);
    }



}
