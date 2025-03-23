<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Logout
{
    public static function run()
    {
        Session::destroySession();
        return new RedirectResponse(UrlGenerator::generate('home'));
    }
}
