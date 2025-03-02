<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\View;

class Home
{
    public static function render()
    {
        $twig = View::getTwig();
        echo $twig->render('Pages/home.twig');
    }
}
