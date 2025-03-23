<?php

// source\Utilities\Helpers.php

namespace Kouak\BackOfficeApp\Utilities;

use Symfony\Component\HttpFoundation\RedirectResponse;

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
            $response = new RedirectResponse(UrlGenerator::generate('login'));
            $response->send();
            exit();
        }
    }

    public static function checkUserAdmin(): void
    {
        self::checkUserLoggedIn();
        $role = Session::getSession("role");
        if ($role !== "admin") {
            $response = new RedirectResponse(UrlGenerator::generate('collection-list'));
            $response->send();
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
