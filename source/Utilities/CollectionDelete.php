<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Collection\CollectionController;

class CollectionDelete
{
    /**
     * Execute the deletion of a collection.
     */
    public static function run()
    {
        // Ensure only admin users can delete a collection.
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        // Check for a valid collection ID in the query string.
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
