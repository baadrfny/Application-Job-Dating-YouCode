<?php
// test_routes_final.php
require_once 'app/core/Request.php';
require_once 'app/core/Router.php';
require_once 'config/routes.php';

echo "<h1>Final Route Test</h1>";

// URLs للاختبار
$test_urls = [
    '/' => 'Home page',
    '/annonces' => 'All offers',
    '/annonces/1' => 'Offer details ID 1',
    '/annonces/7' => 'Offer details ID 7 (archived)',
    '/admin/dashboard' => 'Admin dashboard',
    '/nonexistent' => 'Non-existent page'
];

foreach ($test_urls as $url => $description) {
    echo "<h3>Testing: $description ($url)</h3>";
    
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $url;
    
    try {
        $result = $router->dispatch();
        
        // تحقق من النتيجة
        if (strpos($result, '404') !== false) {
            echo "🔍 Result: 404 Not Found (Expected for archived/non-existent pages)<br>";
        } else if (strlen($result) > 0) {
            echo "✅ Route works! ";
            echo "Preview: " . substr(strip_tags($result), 0, 100) . "...<br>";
        } else {
            echo "⚠️ Empty result<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    echo "<hr>";
}