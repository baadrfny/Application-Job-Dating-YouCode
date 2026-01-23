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
$router->post('/annonces/{id}/postuler', 'controllers\\front\\ApplicationController@store');
$router->get('/candidatures', 'controllers\\front\\ApplicationController@index');




// Back Office Routes (Admin)
$router->get('/admin/login', function (\core\Request $request) {
    \core\Response::redirect('/login');
    return '';
});
$router->get('/admin/dashboard', 'controllers\\back\\DashboardController@index');
$router->get('/admin/annonces', 'controllers\\back\\AnnouncementController@index');
$router->get('/admin/annonces/create', 'controllers\\back\\AnnouncementController@create');
$router->post('/admin/annonces', 'controllers\\back\\AnnouncementController@store');
$router->get('/admin/annonces/edit/{id}', 'controllers\\back\\AnnouncementController@edit');
$router->post('/admin/annonces/{id}', 'controllers\\back\\AnnouncementController@update');
$router->post('/admin/annonces/{id}/archive', 'controllers\\back\\AnnouncementController@archive');
$router->post('/admin/annonces/{id}/restore', 'controllers\\back\\AnnouncementController@restore');
$router->get('/admin/archives', 'controllers\\back\\AnnouncementController@archived');
$router->get('/admin/annonces/{id}/candidatures', 'controllers\\back\\ApplicationController@index');
$router->post('/admin/candidatures/{id}/status', 'controllers\\back\\ApplicationController@updateStatus');
$router->get('/admin/candidatures/{id}/cv', 'controllers\\back\\ApplicationController@downloadCv');
$router->get('/admin/entreprises', 'controllers\\back\\CompanyController@index');
$router->get('/admin/entreprises/create', 'controllers\\back\\CompanyController@create');
$router->post('/admin/entreprises', 'controllers\\back\\CompanyController@store');
$router->get('/admin/entreprises/edit/{id}', 'controllers\\back\\CompanyController@edit');
$router->post('/admin/entreprises/{id}', 'controllers\\back\\CompanyController@update');
$router->post('/admin/entreprises/{id}/delete', 'controllers\\back\\CompanyController@delete');
$router->get('/admin/apprenants', 'controllers\\back\\StudentController@index');
$router->post('/admin/logout', 'controllers\\back\\AuthController@logout');

$router->addMiddleware('/', $authApprenant);
$router->addMiddleware('/annonces', $authApprenant);
$router->addMiddleware('/annonces/{id}', $authApprenant);
$router->addMiddleware('/annonces/filter', $authApprenant);
$router->addMiddleware('/annonces/{id}/postuler', $authApprenant);
$router->addMiddleware('/candidatures', $authApprenant);

$router->addMiddleware('/admin/dashboard', $authAdmin);
$router->addMiddleware('/admin/annonces', $authAdmin);
$router->addMiddleware('/admin/annonces/create', $authAdmin);
$router->addMiddleware('/admin/annonces/edit/{id}', $authAdmin);
$router->addMiddleware('/admin/annonces/{id}', $authAdmin);
$router->addMiddleware('/admin/annonces/{id}/archive', $authAdmin);
$router->addMiddleware('/admin/annonces/{id}/restore', $authAdmin);
$router->addMiddleware('/admin/archives', $authAdmin);
$router->addMiddleware('/admin/annonces/{id}/candidatures', $authAdmin);
$router->addMiddleware('/admin/candidatures/{id}/status', $authAdmin);
$router->addMiddleware('/admin/candidatures/{id}/cv', $authAdmin);
$router->addMiddleware('/admin/entreprises', $authAdmin);
$router->addMiddleware('/admin/entreprises/create', $authAdmin);
$router->addMiddleware('/admin/entreprises/edit/{id}', $authAdmin);
$router->addMiddleware('/admin/entreprises/{id}', $authAdmin);
$router->addMiddleware('/admin/entreprises/{id}/delete', $authAdmin);
$router->addMiddleware('/admin/apprenants', $authAdmin);
