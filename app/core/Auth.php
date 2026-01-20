<?php

namespace core;

use models\User;

class Auth
{
    private const SESSION_KEY = 'user_id';
    private const ROLE_KEY = 'user_role';

    public static function attempt(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) return false;
        if (($user['status'] ?? 'active') !== 'active') return false;

        if (!password_verify($password, $user['password'])) return false;

        Session::set(self::SESSION_KEY, (int) $user['id']);
        Session::set(self::ROLE_KEY, $user['role']);
        return true;
    }

    public static function check(): bool
    {
        return Session::get(self::SESSION_KEY) !== null;
    }

    public static function isAdmin(): bool
    {
        return self::check() && Session::get(self::ROLE_KEY) === 'admin';
    }

    public static function isStudent(): bool
    {
        return self::check() && Session::get(self::ROLE_KEY) === 'student';
    }

    public static function id(): ?int
    {
        $id = Session::get(self::SESSION_KEY);
        return $id !== null ? (int)$id : null;
    }

    public static function logout(): void
    {
        Session::destroy();
    }
}
