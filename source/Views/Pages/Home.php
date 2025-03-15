<?php

// source\Views\Pages\Home.php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\View;
use Kouak\BackOfficeApp\Utilities\Session;

class Home
{
    public static function render()
    {
        // Clear any existing flash messages before processing
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");

        $twig = View::getTwig();
        echo $twig->render('Pages/home.twig');

        // Remove flash_error after the view has been rendered so it doesn't persist
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
    }
}
