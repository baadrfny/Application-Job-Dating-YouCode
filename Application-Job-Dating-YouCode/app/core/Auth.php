<?php

namespace core;

use models\User;

class Auth
{
    private const ADMIN_SESSION_KEY = 'admin_user_id';
    private const ADMIN_ROLE_KEY = 'admin_user_role';
    private const STUDENT_SESSION_KEY = 'student_user_id';
    private const STUDENT_ROLE_KEY = 'student_user_role';
    private const LOGIN_ATTEMPT_PREFIX = 'login_attempts_';

    public static function attempt(string $email, string $password, string $guard = 'apprenant'): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) return false;
        if (($user['status'] ?? 'active') !== 'active') return false;
        if (!self::roleMatchesGuard($user['role'] ?? '', $guard)) return false;

        if (!password_verify($password, $user['password'])) return false;

        self::loginUser($user, $guard);
        return true;
    }

    public static function check(): bool
    {
        return self::checkStudent();
    }

    public static function isAdmin(): bool
    {
        return self::checkAdmin();
    }

    public static function isStudent(): bool
    {
        return self::checkStudent();
    }

    public static function checkAdmin(): bool
    {
        return Session::get(self::ADMIN_SESSION_KEY) !== null
            && Session::get(self::ADMIN_ROLE_KEY) === 'admin';
    }

    public static function checkStudent(): bool
    {
        return Session::get(self::STUDENT_SESSION_KEY) !== null
            && Session::get(self::STUDENT_ROLE_KEY) === 'apprenant';
    }

    public static function id(string $guard = 'apprenant'): ?int
    {
        $key = $guard === 'admin' ? self::ADMIN_SESSION_KEY : self::STUDENT_SESSION_KEY;
        $id = Session::get($key);
        return $id !== null ? (int)$id : null;
    }

    public static function logout(): void
    {
        self::logoutStudent();
        self::logoutAdmin();
    }

    public static function logoutStudent(): void
    {
        Session::forget([self::STUDENT_SESSION_KEY, self::STUDENT_ROLE_KEY]);
    }

    public static function logoutAdmin(): void
    {
        Session::forget([self::ADMIN_SESSION_KEY, self::ADMIN_ROLE_KEY]);
    }

    public static function canAttempt(string $guard, string $email, int $maxAttempts = 5, int $lockSeconds = 900): array
    {
        $key = self::attemptKey($guard, $email);
        $data = Session::get($key, ['count' => 0, 'locked_until' => 0]);

        if (!empty($data['locked_until']) && time() < (int) $data['locked_until']) {
            $remaining = (int) $data['locked_until'] - time();
            return [false, "Too many attempts. Try again in {$remaining} seconds."];
        }

        return [true, ''];
    }

    public static function recordFailedAttempt(string $guard, string $email, int $maxAttempts = 5, int $lockSeconds = 900): string
    {
        $key = self::attemptKey($guard, $email);
        $data = Session::get($key, ['count' => 0, 'locked_until' => 0]);

        $data['count'] = (int) ($data['count'] ?? 0) + 1;
        if ($data['count'] >= $maxAttempts) {
            $data['locked_until'] = time() + $lockSeconds;
            $data['count'] = 0;
            Session::set($key, $data);
            return "Too many attempts. Try again later.";
        }

        Session::set($key, $data);
        return "Invalid credentials.";
    }

    public static function clearAttempts(string $guard, string $email): void
    {
        Session::remove(self::attemptKey($guard, $email));
    }

    private static function attemptKey(string $guard, string $email): string
    {
        return self::LOGIN_ATTEMPT_PREFIX . $guard . '_' . strtolower(trim($email));
    }

    private static function roleMatchesGuard(string $role, string $guard): bool
    {
        if ($guard === 'admin') {
            return $role === 'admin';
        }
        return $role === 'apprenant';
    }

    private static function loginUser(array $user, string $guard): void
    {
        if ($guard === 'admin') {
            Session::set(self::ADMIN_SESSION_KEY, (int) $user['id']);
            Session::set(self::ADMIN_ROLE_KEY, $user['role']);
            return;
        }

        Session::set(self::STUDENT_SESSION_KEY, (int) $user['id']);
        Session::set(self::STUDENT_ROLE_KEY, $user['role']);
    }
}
