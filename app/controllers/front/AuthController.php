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
    protected string $csrfScope = 'apprenant';

    public function showLogin(Request $request): string
    {
        return $this->view('front/auth/login', [
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

        [$canAttempt, $lockMessage] = Auth::canAttempt('apprenant', $email);
        if (!$canAttempt) {
            return $this->view('front.auth.login', [
                'title' => 'Login',
                'error' => $lockMessage,
                'error_display' => 'block'
            ]);
        }

        if ($email === '' || $password === '') {
            return $this->view('front.auth.login', [
                'title' => 'Login',
                'error' => 'Email and password are required.',
                'error_display' => 'block'
            ]);
        }

        if (!Auth::attempt($email, $password, 'apprenant')) {
            return $this->view('front.auth.login', [
                'title' => 'Login',
                'error' => Auth::recordFailedAttempt('apprenant', $email),
                'error_display' => 'block'
            ]);
        }

        Auth::clearAttempts('apprenant', $email);
        Response::redirect('/annonces');
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
        if (!$this->validateCSRF($request)) {
            Response::redirect('/login');
            return '';
        }

        Auth::logoutStudent();
        Security::clearCSRFToken($this->csrfScope);
        Response::redirect('/login');
        return ''; 
    }
}
