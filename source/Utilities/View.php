<?php

namespace Kouak\BackOfficeApp\Utilities;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    private static $twig;

    public static function getTwig(): Environment
    {
        if (self::$twig === null) {
            // BASE_PATH is defined in index.php and points to your project root.
            $loader = new FilesystemLoader(BASE_PATH . '/source/Views/templates/');
            self::$twig = new Environment($loader, [
                'cache' => BASE_PATH . '/cache/twig', // You can disable cache in development
                'debug' => true,
            ]);
        }
        return self::$twig;
    }
}
