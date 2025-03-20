<?php

// source\Utilities\CollectionDelete.php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;

class CollectionDelete
{
    public static function runCollectionDeletion($collection_id): void
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();
        $baseUrl = Helpers::getBaseUrl();
        if (empty($collection_id)) {
            header("Location: " . $baseUrl . "/collection-list");
            exit;
        }
        $collectionId = $collection_id;
        $controller = new CollectionController($pdo);
        $controller->eraseCollection($collectionId);
        header("Location: " . $baseUrl . "/collection-list");
        exit;
    }
}
