<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Session;

class Logout
{
    public static function run()
    {
        Session::destroySession();
        $baseUrl = Helpers::getBaseUrl();
        header("Location: " . $baseUrl);
        exit;
    }
}
