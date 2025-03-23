<?php
// public\index.php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;
use \Kouak\BackOfficeApp\Errors\DatabaseException;
use \Kouak\BackOfficeApp\Errors\ValidationException;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/logs/app.log', Level::Error));

$routes = new RouteCollection();

$routes->add('login', new Route('/login', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\Login::render']));
$routes->add('collection-list', new Route('/collection-list', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\CollectionList::render']));
$routes->add('collection-add', new Route('/collection-add', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\CollectionAdd::render']));
$routes->add('collection-edit', new Route('/collection-edit/{collection_id}', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\CollectionEdit::render']));
$routes->add('collection-delete', new Route('/collection-delete/{collection_id}', ['_controller' => 'Kouak\BackOfficeApp\Utilities\CollectionDelete::runCollectionDeletion']));
$routes->add('volunteer-list', new Route('/volunteer-list', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\VolunteerList::render']));
$routes->add('volunteer-add', new Route('/volunteer-add', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\VolunteerAdd::render']));
$routes->add('volunteer-edit', new Route('/volunteer-edit/{volunteer_id}', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\VolunteerEdit::render']));
$routes->add('volunteer-delete', new Route('/volunteer-delete/{volunteer_id}', ['_controller' => 'Kouak\BackOfficeApp\Utilities\VolunteerDelete::runVolunteerDeletion']));
$routes->add('logout', new Route('/logout', ['_controller' => 'Kouak\BackOfficeApp\Utilities\Logout::run']));
$routes->add('my-account', new Route('/my-account', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\MyAccount::render']));
$routes->add('home', new Route('/home', ['_controller' => 'Kouak\BackOfficeApp\Views\Pages\Home::render']));

$request = Request::createFromGlobals();

$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    $controller = $parameters['_controller'];
    unset($parameters['_controller'], $parameters['_route']);
    $result = call_user_func_array($controller, $parameters);
    if ($result instanceof Response) {
        $result->send();
    }
} catch (ResourceNotFoundException $e) {
    $logger->error('Route not found', ['exception' => $e]);
    $response = new Response('Not Found', 404);
    $response->send();
} catch (DatabaseException $e) {
    $logger->error('Database error', ['exception' => $e]);
    $response = new Response('Une erreur est survenue lors de la mise à jour des données. Veuillez réessayer plus tard.', 500);
    $response->send();
} catch (ValidationException $e) {
    $logger->warning('Validation error', ['exception' => $e]);
    $response = new Response($e->getMessage(), 400);
    $response->send();
} catch (Exception $e) {
    $logger->error('An error occurred', ['exception' => $e]);
    $response = new Response('An error occurred: ' . $e->getMessage(), 500);
    $response->send();
}
