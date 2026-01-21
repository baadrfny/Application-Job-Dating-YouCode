<?php

$router->get('/', 'controllers\\front\\JobController@index');

$router->get('/db-test', function (\core\Request $request) {
    try {
        $pdo = \core\Database::getConnection();
        $row = $pdo->query("SELECT 1 as ok")->fetch();
        return "DB OK = " . ($row['ok'] ?? 'no');
    } catch (\Throwable $e) {
        return "DB ERROR: " . htmlspecialchars($e->getMessage());
    }
});

// Front Office Routes (Students)
$router->get('/login', 'controllers\\front\\AuthController@showLogin');
$router->post('/login', 'controllers\\front\\AuthController@login');
$router->get('/register', 'controllers\\front\\AuthController@showRegister');
$router->post('/register', 'controllers\\front\\AuthController@register');
$router->post('/logout', 'controllers\\front\\AuthController@logout');

$router->get('/annonces', 'controllers\\front\\JobController@index');
$router->get('/annonces/{id}', 'controllers\\front\\JobController@show');

// Back Office Routes (Admin)
$router->get('/admin/login', 'controllers\\back\\AuthController@showLogin');
$router->post('/admin/login', 'controllers\\back\\AuthController@login');
$router->get('/admin/dashboard', 'controllers\\back\\DashboardController@index');
$router->post('/admin/logout', 'controllers\\back\\AuthController@logout');
