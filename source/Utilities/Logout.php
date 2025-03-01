<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Session;

class Logout
{
    /**
     * Log the user out by clearing and destroying the session,
     * then redirect to the login route.
     */
    public static function run()
    {
        Session::destroy();

        // Redirect to the login page using the front controller route.
        header("Location: /back-office-app/index.php");
        exit;
    }
}
