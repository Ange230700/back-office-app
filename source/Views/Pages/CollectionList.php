<?php

// source\Views\Pages\CollectionList.php

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

        // Clear any existing flash messages before processing
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");

        $collectionController = new CollectionController($pdo);
        $collectedWasteTotalQuantity = $collectionController->getCollectedWastesTotalQuantity();
        $mostRecentCollection = $collectionController->getMostRecentCollection();
        $nextCollection = $collectionController->getNextCollection();

        list($collectionsList, $totalPages) = $collectionController->getCollectionsListPaginated();
        $totalCollections = $collectionController->getTotalCollections();
        $dateFormat = "d/m/Y";

        $role = Session::getSession("role");

        $dashboardData = [
            'totalCollections' => $totalCollections,
            'totalPages' => $totalPages,
            'collectedWasteTotalQuantity' => $collectedWasteTotalQuantity,
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

        // Remove flash_error after the view has been rendered so it doesn't persist
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
    }
}
