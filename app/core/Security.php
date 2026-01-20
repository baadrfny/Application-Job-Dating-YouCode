<?php

namespace core;

class Security
{
    public static function generateCSRFToken(): string
    {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function verifyCSRFToken(string $token): bool
    {
        $sessionToken = Session::get('csrf_token');
        return $sessionToken && hash_equals($sessionToken, $token);
    }

    public static function escape(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitize(string $data): string
    {
        return trim(strip_tags($data));
    }
}