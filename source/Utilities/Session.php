<?php

namespace Kouak\BackOfficeApp\Utilities;

class Session
{
    public static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function regenerateSessionId(): void
    {
        session_regenerate_id(true);
    }

    public static function setSession(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function getSession(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function removeSessionVariable(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function removeAllSessionVariables(): void
    {
        session_unset();
    }

    public static function destroySession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::startSession();
        }
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_unset();
        session_destroy();
    }

    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
    }
}
