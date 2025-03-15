<?php

// source\Utilities\View.php

namespace Kouak\BackOfficeApp\Utilities;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    private static $twig;

    public static function getTwig(): Environment
    {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(BASE_PATH . '/../source/Templates/');
            self::$twig = new Environment($loader, [
                'cache' => BASE_PATH . '/../cache/twig',
                'debug' => true,
            ]);
            self::$twig->addGlobal('flash_success', Session::getSession("flash_success"));
            self::$twig->addGlobal('flash_error', Session::getSession("flash_error"));
        }
        return self::$twig;
    }
}
