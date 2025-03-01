<?php

namespace Kouak\BackOfficeApp\Utilities;

class Session
{
    /**
     * Start the session if not already started.
     */
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Regenerate the session ID, deleting the old one.
     */
    public static function regenerate(): void
    {
        // Regenerate session ID to prevent fixation attacks.
        session_regenerate_id(true);
    }

    /**
     * Set a session variable.
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable.
     */
    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Unset a session variable.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the current session.
     */
    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
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

    /**
     * Retrieve the CSRF token from session or generate a new one if missing.
     */
    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify the submitted CSRF token.
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
    }
}
