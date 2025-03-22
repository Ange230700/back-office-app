<?php

// source\Views\Pages\Home.php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\View;
use Symfony\Component\HttpFoundation\Response;

class Home
{
    public static function render()
    {
        $twig = View::getTwig();
        $content = $twig->render('Pages/home.twig');

        // Remove flash_error after the view has been rendered so it doesn't persist
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
        return new Response($content);
    }
}
