<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;
use Kouak\BackOfficeApp\Utilities\View;

class CollectionList
{
    public static function render()
    {
        Helpers::checkUserLoggedIn();
        $pdo = Configuration::getPdo();

        $controller = new CollectionController($pdo);
        $collectedWastesTotalQuantity = $controller->getCollectedWastesTotalQuantity();
        $mostRecentCollection = $controller->getMostRecentCollection();
        $nextCollection = $controller->getNextCollection();

        list($collectionsList, $totalPages) = $controller->getCollectionsListPaginated();
        $dateFormat = "d/m/Y";

        $role = Session::getSession("role");

        $dashboardData = [
            'totalPages' => $totalPages,
            'collectedWastesTotalQuantity' => $collectedWastesTotalQuantity,
            'mostRecentCollection' => $mostRecentCollection,
            'nextCollection' => $nextCollection,
            'dateFormat' => $dateFormat,
        ];

        $pageNumber = $_GET['pageNumber'] ?? 1;

        $twig = View::getTwig();
        echo $twig->render('Pages/collection_list.twig', [
            'collections' => $collectionsList,
            'totalPages'  => $totalPages,
            'dateFormat'  => $dateFormat,
            'role'        => $role,
            'dashboard'   => $dashboardData,
            'pageNumber'  => $pageNumber,
            'route'       => 'collection-list',
            'session'     => $_SESSION,
        ]);
    }
}
