<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;

class CollectionDelete
{
    public static function runCollectionDeletion(): void
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int)$_GET['id'];
            $controller = new CollectionController($pdo);
            $controller->eraseCollection($id);
            header("Location: /back-office-app/index.php?route=collection-list");
            exit();
        } else {
            echo "ID invalide.";
        }
    }
}
