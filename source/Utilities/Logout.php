<?php

namespace Kouak\BackOfficeApp\Utilities;

class Logout
{
    /**
     * Log the user out by clearing and destroying the session,
     * then redirect to the login route.
     */
    public static function run()
    {
        // Ensure the session is started.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();

        // Redirect to the login page using the front controller route.
        header("Location: /back-office-app/index.php");
        exit;
    }
}
