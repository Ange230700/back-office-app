<?php

// source\Utilities\Helpers.php

namespace Kouak\BackOfficeApp\Utilities;

class Helpers
{
    public static function initSession(): void
    {
        Session::startSession();
    }

    public static function checkUserLoggedIn(): void
    {
        self::initSession();
        $userId = Session::getSession("user_id");
        if (!isset($userId)) {
            $baseUrl = Helpers::getBaseUrl();
            header('Location: ' . $baseUrl . '/login');
            exit();
        }
    }

    public static function checkUserAdmin(): void
    {
        self::checkUserLoggedIn();
        $role = Session::getSession("role");
        if ($role !== "admin") {
            $baseUrl = Helpers::getBaseUrl();
            header("Location: " . $baseUrl . "/collection-list");
            exit();
        }
    }

    public static function getPaginationParams($defaultLimit = 3): array
    {
        $limit = $defaultLimit;
        $pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
        $offset = ($pageNumber - 1) * $limit;
        return compact('limit', 'pageNumber', 'offset');
    }

    public static function getBaseUrl(): string
    {
        return (Helpers::isDevelopment())
            ? '/back-office-app/public'
            : '';
    }

    public static function isDevelopment(): bool
    {
        return !isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] === 'development';
    }
}
