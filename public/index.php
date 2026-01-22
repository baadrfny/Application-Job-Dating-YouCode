<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';



\core\Session::start();
use core\Router;

$router = new Router();
require dirname(__DIR__) . '/config/routes.php';

echo $router->dispatch();