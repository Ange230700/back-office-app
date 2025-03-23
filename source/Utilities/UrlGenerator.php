<?php

// source\Utilities\UrlGenerator.php

namespace Kouak\BackOfficeApp\Utilities;

use Dotenv\Dotenv;

class UrlGenerator
{
    public static function getBaseUrl(): string
    {
        $projectRoot = dirname(__DIR__, 2);
        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->load();
        $environment = $_ENV['APP_ENV'];
        return $environment === 'production' ? '' : '/back-office-app/public';
    }

    public static function generate(string $path): string
    {
        return self::getBaseUrl() . $path;
    }
}
