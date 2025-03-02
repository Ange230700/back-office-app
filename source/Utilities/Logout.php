<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Session;

class Logout
{
    public static function run()
    {
        Session::destroy();

        header("Location: /back-office-app/index.php");
        exit;
    }
}
