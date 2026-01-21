<?php

namespace controllers\front;

use core\Controller;
use core\Request;
use core\Response;
use core\Auth;
use core\Security;
use models\User;
use models\Apprenant;

class AuthController extends Controller
{
    public function showLogin(Request $request): string
    {
        return $this->view('front.auth.login', [
            'title' => 'Login',
            'error' => '',
            'error_display' => 'none'
        ]);
    }

    public function login(Request $request): string
    {
        if (!$this->validateCSRF($request)) {
            return $this->view('front.auth.login', [
                'title' => 'Login',
                'error' => 'Invalid CSRF token.',
                'error_display' => 'block'
            ]);
        }

        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');

        if ($email === '' || $password === '') {
            return $this->view('front.auth.login', [
                'title' => 'Login',
                'error' => 'Email and password are required.',
                'error_display' => 'block'
            ]);
        }

        if (!Auth::attempt($email, $password)) {
            return $this->view('front.auth.login', [
                'title' => 'Login',
                'error' => 'Invalid credentials.',
                'error_display' => 'block'
            ]);
        }

        Response::redirect('/');
        return ''; 
    }

    public function showRegister(Request $request): string
    {
        return $this->view('front.auth.register', [
            'title' => 'Register',
            'error' => '',
            'error_display' => 'none'
        ]);
    }

    public function register(Request $request): string
    {
        if (!$this->validateCSRF($request)) {
            return $this->view('front.auth.register', [
                'title' => 'Register',
                'error' => 'Invalid CSRF token.',
                'error_display' => 'block'
            ]);
        }

        $name = Security::sanitize((string) $request->input('name', ''));
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $promotion = Security::sanitize((string) $request->input('promotion', ''));
        $specialisation = Security::sanitize((string) $request->input('specialisation', ''));

        if ($name === '' || $email === '' || $password === '') {
            return $this->view('front.auth.register', [
                'title' => 'Register',
                'error' => 'Name, email, and password are required.',
                'error_display' => 'block'
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->view('front.auth.register', [
                'title' => 'Register',
                'error' => 'Invalid email format.',
                'error_display' => 'block'
            ]);
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            return $this->view('front.auth.register', [
                'title' => 'Register',
                'error' => 'Email already exists.',
                'error_display' => 'block'
            ]);
        }

        $userId = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'apprenant'
        ]);

        $apprenantModel = new Apprenant();
        $apprenantModel->create([
            'user_id' => $userId,
            'promotion' => $promotion,
            'specialisation' => $specialisation
        ]);

        Response::redirect('/login');
        return ''; 
    }

    public function logout(Request $request): string
    {
        Auth::logout();
        Response::redirect('/');
        return ''; 
    }
}
