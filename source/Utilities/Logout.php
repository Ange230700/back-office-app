<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Session;

class Logout
{
    public static function run()
    {
        Session::destroySession();

        header("Location: /back-office-app/public/");
        exit;
    }
}
