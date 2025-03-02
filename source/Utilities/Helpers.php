<?php

namespace Kouak\BackOfficeApp\Utilities;

class Helpers
{
    public static function initSession()
    {
        Session::start();
    }

    public static function checkUserLoggedIn()
    {
        self::initSession();

        $userId = Session::get("user_id");
        if (!isset($userId)) {
            header('Location: /back-office-app/index.php?route=login');
            exit();
        }
    }

    public static function checkUserAdmin()
    {
        self::checkUserLoggedIn();

        $role = Session::get("role");
        if ($role !== "admin") {
            header("Location: /back-office-app/index.php?route=collection-list");
            exit();
        }
    }

    public static function getPaginationParams($defaultLimit = 3)
    {
        $limit = $defaultLimit;
        $pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
        $offset = ($pageNumber - 1) * $limit;
        return compact('limit', 'pageNumber', 'offset');
    }
}
