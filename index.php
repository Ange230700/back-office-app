<?php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/vendor/autoload.php';

use Kouak\BackOfficeApp\Views\Pages\Login;
use Kouak\BackOfficeApp\Views\Pages\CollectionList;
use Kouak\BackOfficeApp\Views\Pages\CollectionAdd;
use Kouak\BackOfficeApp\Views\Pages\CollectionEdit;
use Kouak\BackOfficeApp\Views\Pages\VolunteerList;
use Kouak\BackOfficeApp\Views\Pages\VolunteerAdd;
use Kouak\BackOfficeApp\Views\Pages\VolunteerEdit;
use Kouak\BackOfficeApp\Views\Pages\MyAccount;
use Kouak\BackOfficeApp\Views\Pages\Home;

use Kouak\BackOfficeApp\Utilities\CollectionDelete;
use Kouak\BackOfficeApp\Utilities\VolunteerDelete;
use Kouak\BackOfficeApp\Utilities\Logout;

$route = $_GET['route'] ?? 'home';

if ($route === 'login') {
    Login::render();
} elseif ($route === 'collection-list') {
    CollectionList::render();
} elseif ($route === 'collection-add') {
    CollectionAdd::render();
} elseif ($route === 'collection-edit') {
    CollectionEdit::render();
} elseif ($route === 'collection-delete') {
    CollectionDelete::runCollectionDeletion();
} elseif ($route === 'volunteer-list') {
    VolunteerList::render();
} elseif ($route === 'volunteer-add') {
    VolunteerAdd::render();
} elseif ($route === 'volunteer-edit') {
    VolunteerEdit::render();
} elseif ($route === 'volunteer-delete') {
    VolunteerDelete::runVolunteerDeletion();
} elseif ($route === 'logout') {
    Logout::run();
} elseif ($route === 'my-account') {
    MyAccount::render();
} else {
    Home::render();
}
