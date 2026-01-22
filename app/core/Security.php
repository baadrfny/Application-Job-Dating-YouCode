<?php

namespace core;

class Security
{
    public static function generateCSRFToken(string $scope = 'app'): string
    {
        $key = 'csrf_token_' . $scope;
        if (!Session::get($key)) {
            Session::set($key, bin2hex(random_bytes(32)));
        }
        return Session::get($key);
    }

    public static function verifyCSRFToken(string $token, string $scope = 'app'): bool
    {
        $sessionToken = Session::get('csrf_token_' . $scope);
        return $sessionToken && hash_equals($sessionToken, $token);
    }

    public static function clearCSRFToken(string $scope = 'app'): void
    {
        Session::remove('csrf_token_' . $scope);
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
