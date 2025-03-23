<?php

// source\Utilities\UrlGenerator.php

namespace Kouak\BackOfficeApp\Utilities;

class UrlGenerator
{
    public static function getBaseUrl(): string
    {
        $environment = getenv('APP_ENV');
        return $environment === 'production' ? '' : '/back-office-app/public';
    }

    public static function generate(string $path): string
    {
        return self::getBaseUrl() . $path;
    }
}
