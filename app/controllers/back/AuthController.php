<?php

namespace controllers\back;

use core\Controller;
use core\Request;
use core\Response;
use core\Auth;
use core\Security;

class AuthController extends Controller
{
    protected string $csrfScope = 'admin';

    public function showLogin(Request $request): string
    {
        return $this->view('back.auth.login', [
            'title' => 'Admin Login',
            'error' => '',
            'error_display' => 'none'
        ]);
    }

    public function login(Request $request): string
    {
        if (!$this->validateCSRF($request)) {
            return $this->view('back.auth.login', [
                'title' => 'Admin Login',
                'error' => 'Invalid CSRF token.',
                'error_display' => 'block'
            ]);
        }

        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');

        [$canAttempt, $lockMessage] = Auth::canAttempt('admin', $email);
        if (!$canAttempt) {
            return $this->view('back.auth.login', [
                'title' => 'Admin Login',
                'error' => $lockMessage,
                'error_display' => 'block'
            ]);
        }

        if ($email === '' || $password === '') {
            return $this->view('back.auth.login', [
                'title' => 'Admin Login',
                'error' => 'Email and password are required.',
                'error_display' => 'block'
            ]);
        }

        if (!Auth::attempt($email, $password, 'admin')) {
            return $this->view('back.auth.login', [
                'title' => 'Admin Login',
                'error' => Auth::recordFailedAttempt('admin', $email),
                'error_display' => 'block'
            ]);
        }

        Auth::clearAttempts('admin', $email);
        Response::redirect('/admin/dashboard');
        return '';
    }

    public function logout(Request $request): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/login');
            return '';
        }

        Auth::logoutAdmin();
        Security::clearCSRFToken($this->csrfScope);
        Response::redirect('/admin/login');
        return '';
    }
}
