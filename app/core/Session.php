<?php

namespace core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function checkTimeout(int $seconds = 7200): bool
    {
        self::start();
        $last = $_SESSION['last_activity'] ?? null;

        if ($last && (time() - $last > $seconds)) {
            self::destroy();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function forget(array $keys): void
    {
        self::start();
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }
}
