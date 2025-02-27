<?php

namespace Kouak\BackOfficeApp\Utilities;

class Helpers
{
    /**
     * Initialize session if not already started.
     */
    public static function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if a user is logged in.
     */
    public static function checkUserLoggedIn()
    {
        self::initSession();

        if (!isset($_SESSION["user_id"])) {
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

        if ($_SESSION["role"] !== "admin") {
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
