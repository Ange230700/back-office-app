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
            $loader = new FilesystemLoader(dirname(__DIR__) . '/Templates/');
            self::$twig = new Environment($loader, [
                'cache' => dirname(__DIR__, 2) . '/cache/twig',
                'debug' => true,
            ]);
            self::$twig->addGlobal('flash_success', Session::getSession("flash_success"));
            self::$twig->addGlobal('flash_error', Session::getSession("flash_error"));
        }
        return self::$twig;
    }
}
