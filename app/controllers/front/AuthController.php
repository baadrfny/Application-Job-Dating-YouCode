<?php

namespace controllers\front;

use core\Controller;
use core\Request;
use core\Response;
use core\Auth;

class AuthController extends Controller
{
    public function showLogin(Request $request): string
    {
        return $this->view('Auth.login', [
            'title' => 'Login'
        ]);
    }

    public function login(Request $request): string
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');

        if ($email === '' || $password === '') {
            return $this->view('Auth.login', [
                'title' => 'Login',
                'error' => 'Email and password are required.'
            ]);
        }

        if (!Auth::attempt($email, $password)) {
            return $this->view('Auth.login', [
                'title' => 'Login',
                'error' => 'Invalid credentials.'
            ]);
        }

        Response::redirect('/dashboard');
        return ''; // unreachable
    }

    public function logout(Request $request): string
    {
        Auth::logout();
        Response::redirect('/');
        return ''; // unreachable
    }
}
