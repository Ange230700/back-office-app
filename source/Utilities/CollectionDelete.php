<?php

// source\Utilities\CollectionDelete.php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CollectionDelete
{
    public static function runCollectionDeletion($collection_id): RedirectResponse
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();
        if (empty($collection_id)) {
            return new RedirectResponse('/back-office-app/public/collection-list');
        }
        $collectionId = $collection_id;
        $controller = new CollectionController($pdo);
        $controller->eraseCollection($collectionId);
        return new RedirectResponse('/back-office-app/public/collection-list');
    }
}
