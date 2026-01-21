<?php
// test_manual_fixed.php - مع كل الـ dependencies

// 1. قائمة بكل core files اللي تحتاجها
$core_files = [
    'app/core/Request.php',
    'app/core/Router.php',
    'app/core/Database.php',   // ⬅️ مهم!
    'app/core/View.php',       // ⬅️ مهم للـ render!
    'app/core/Session.php',
    'app/core/Auth.php',
    'app/core/Validator.php',
    'app/core/Security.php',
    'app/core/Controller.php',
    'app/core/Model.php'
];

// 2. حمل كل الـ core files
foreach ($core_files as $file) {
    if (file_exists($file)) {
        require_once $file;
        echo "✅ Loaded: $file<br>";
    } else {
        echo "⚠️ Not found: $file<br>";
    }
}

// 3. حمل الـ models
$model_files = [
    'app/models/Announcement.php',
    'app/models/Company.php',
    'app/models/User.php',
    'app/models/Student.php'
];

foreach ($model_files as $file) {
    if (file_exists($file)) {
        require_once $file;
        echo "✅ Loaded: $file<br>";
    }
}

// 4. حمل الـ controllers
require_once 'app/controllers/front/JobController.php';
echo "✅ Loaded: JobController.php<br>";

// 5. أنشئ الراوتر
$router = new core\Router();

// 6. سجل الراوتات
$router->get('/', 'controllers\\front\\JobController@index');
$router->get('/annonces/{id}', 'controllers\\front\\JobController@show');

// 7. اختبر
echo "<hr><h1>Testing Router...</h1>";

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

try {
    $result = $router->dispatch();
    echo "<h2 style='color:green'>✅ SUCCESS! Application is working!</h2>";
    echo "Output preview: " . substr(strip_tags($result), 0, 200) . "...";
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ ERROR:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}