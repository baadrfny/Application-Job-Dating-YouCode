<?php

$authApprenant = function (\core\Request $request) {
    if (!\core\Session::checkTimeout()) {
        \core\Response::redirect('/login');
        return '';
    }

    if (!\core\Auth::checkStudent()) {
        \core\Response::redirect('/login');
        return '';
    }

    return true;
};

$authAdmin = function (\core\Request $request) {
    if (!\core\Session::checkTimeout()) {
        \core\Response::redirect('/login');
        return '';
    }

    if (!\core\Auth::checkAdmin()) {
        \core\Response::redirect('/login');
        return '';
    }

    return true;
};

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

$router->get('/', 'controllers\\front\\JobController@index');
$router->get('/annonces', 'controllers\\front\\JobController@index');
$router->get('/annonces/{id}', 'controllers\\front\\JobController@show');
$router->get('/annonces/filter', 'controllers\\front\\JobController@filter');

$router->get('/dashboard', 'controllers\\back\\DashboardController@index');
// $router->get('/annonces/filter', 'controllers\\front\\JobController@filter');



// Back Office Routes (Admin)
$router->get('/admin/login', function (\core\Request $request) {
    \core\Response::redirect('/login');
    return '';
});
$router->get('/admin/dashboard', 'controllers\\back\\DashboardController@index');
$router->get('/admin/annonces', 'controllers\\back\\AnnouncementController@index');
$router->get('/admin/annonces/create', 'controllers\\back\\AnnouncementController@create');
$router->get('/admin/archives', 'controllers\\back\\AnnouncementController@archived');
$router->get('/admin/entreprises', 'controllers\\back\\CompanyController@index');
$router->get('/admin/entreprises/create', 'controllers\\back\\CompanyController@create');
$router->get('/admin/apprenants', 'controllers\\back\\StudentController@index');
$router->post('/admin/logout', 'controllers\\back\\AuthController@logout');

$router->addMiddleware('/', $authApprenant);
$router->addMiddleware('/annonces', $authApprenant);
$router->addMiddleware('/annonces/{id}', $authApprenant);
$router->addMiddleware('/annonces/filter', $authApprenant);

$router->addMiddleware('/admin/dashboard', $authAdmin);
$router->addMiddleware('/admin/annonces', $authAdmin);
$router->addMiddleware('/admin/annonces/create', $authAdmin);
$router->addMiddleware('/admin/archives', $authAdmin);
$router->addMiddleware('/admin/entreprises', $authAdmin);
$router->addMiddleware('/admin/entreprises/create', $authAdmin);
$router->addMiddleware('/admin/apprenants', $authAdmin);
