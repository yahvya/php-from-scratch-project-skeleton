<?php session_start();

define('ROOT',__DIR__ . '/');

require_once(ROOT . 'vendor/autoload.php');

use \App\App\Router;

use \Controller\Controller\MaintenanceController;

$routes_prefixes = [
	'/' => 'HomeController'
];

new Router(
	ROOT . 'config.json',
	'\Controller\\Controller\\',
	'default',
	$routes_prefixes,
	new MaintenanceController()
);