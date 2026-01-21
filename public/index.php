<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';


spl_autoload_register(function ($class) {
    // controllers\front\JobController → app/controllers/front/JobController.php
    if (strpos($class, 'controllers\\') === 0) {
        $path = str_replace('controllers\\', 'app/controllers/', $class);
        $file = __DIR__ . '/../' . str_replace('\\', '/', $path) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    }
    
    // models\Announcement → app/models/Announcement.php
    if (strpos($class, 'models\\') === 0) {
        $path = str_replace('models\\', 'app/models/', $class);
        $file = __DIR__ . '/../' . str_replace('\\', '/', $path) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

\core\Session::start();
use core\Router;

$router = new Router();
require dirname(__DIR__) . '/config/routes.php';

echo $router->dispatch();