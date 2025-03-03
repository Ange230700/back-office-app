<?php

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
            header('Location: /back-office-app/index.php?route=login');
            exit();
        }
    }

    public static function checkUserAdmin(): void
    {
        self::checkUserLoggedIn();

        $role = Session::getSession("role");
        if ($role !== "admin") {
            header("Location: /back-office-app/index.php?route=collection-list");
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
}
