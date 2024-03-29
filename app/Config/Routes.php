<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true); 
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::Index');

/*
$routes->get('/auth', 'Auth::Index');
$routes->get('/logout', 'Auth::Logout');
$routes->post('/auth/login', 'Auth::Login');
*/


service('auth')->routes($routes);

$routes->get('/report', 'Dashboard::Index', ['filter' => 'AuthCheck']);
$routes->get('/delete', 'Dashboard::delete', ['filter' => 'AuthCheck']);
$routes->get('/configuration', 'Configuration::Index', ['filter' => 'AuthCheck']);
$routes->post('/configuration/update', 'Configuration::Update', ['filter' => 'AuthCheck']);
$routes->get('/content-editor', 'ContentEditor::Index', ['filter' => 'AuthCheck']);

$routes->group("api", ["namespace" => "App\Controllers\Api", 'filter' => 'AuthCheck'] , function($routes){
    $routes->match(["get", "post"], "content", "ApiController::openAIPrompt");
    $routes->post("search", "ApiController::searchResults");
    $routes->get("progress", "ApiController::reportProgress");
    $routes->get("locations", "ApiController::fetchLocations");
    $routes->get("serp", "ApiController::serpAPIAccountInfo");
    $routes->get("extractor", "ApiController::fetchKeywordExtractor");
    $routes->get("reports", "ApiController::fetchSavedReports");
    $routes->post("deletereport", "ApiController::deleteSavedReport");
    $routes->post("saveblog", "ApiController::saveBlogPostDraft");
    $routes->post("deleteblog", "ApiController::deleteBlogPostDraft");
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
