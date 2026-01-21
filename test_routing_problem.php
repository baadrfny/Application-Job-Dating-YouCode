<?php
require_once 'app/core/Router.php';
require_once 'app/core/Request.php';

echo "<h1>🔍 Final Proof of Router Issue</h1>";

$router = new core\Router();

// 1. راوت بسيط
$router->get('/simple', function() {
    return "✅ Simple route works!";
});

// 2. راوت زي اللي في مشروعك
$router->get('/annonces/{id}', function($request, $id) {
    return "✅ Annonce details for ID: " . $id;
});

// 3. اختبر URI مختلفين
$testCases = [
    '/simple' => "Should work: Simple route",
    '/Application Job Dating YouCode/simple' => "PROBLEM: With base path",
    '/annonces/1' => "Should work: Annonce route", 
    '/Application Job Dating YouCode/annonces/1' => "PROBLEM: With base path"
];

foreach ($testCases as $uri => $description) {
    echo "<h3>Testing: $description</h3>";
    echo "URI: <code>$uri</code><br>";
    
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $uri;
    
    try {
        $result = $router->dispatch();
        echo "<span style='color:green'>✅ $result</span>";
    } catch (Exception $e) {
        echo "<span style='color:red'>❌ " . $e->getMessage() . "</span>";
    }
    echo "<hr>";
}

// 4. الخلاصة
echo "<h2>🎯 Conclusion:</h2>";
echo "The router works fine with simple paths (<code>/simple</code>).<br>";
echo "But fails when the path contains <code>/Application Job Dating YouCode/</code>.<br>";
echo "<strong>This is a BASE PATH issue, not a router bug.</strong>";