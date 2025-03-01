<?php

namespace Kouak\BackOfficeApp\Utilities;

class Helpers
{
    /**
     * Initialize session if not already started.
     */
    public static function initSession()
    {
        Session::start();
    }

    /**
     * Check if a user is logged in.
     */
    public static function checkUserLoggedIn()
    {
        self::initSession();

        $userId = Session::get("user_id");
        if (!isset($userId)) {
            header('Location: /back-office-app/index.php?route=login');
            exit();
        }
    }

    /**
     * Check if a user is an admin.
     */
    public static function checkUserAdmin()
    {
        self::checkUserLoggedIn();

        $role = Session::get("role");
        if ($role !== "admin") {
            header("Location: /back-office-app/index.php?route=collection-list");
            exit();
        }
    }

    /**
     * Get pagination parameters.
     *
     * @param int $defaultLimit The default limit per page.
     * @return array
     */
    public static function getPaginationParams($defaultLimit = 3)
    {
        $limit = $defaultLimit;
        $pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
        $offset = ($pageNumber - 1) * $limit;
        return compact('limit', 'pageNumber', 'offset');
    }
}
