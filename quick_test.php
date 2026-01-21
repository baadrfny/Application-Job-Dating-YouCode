<?php
require_once 'app/controllers/front/JobController.php';

$controller = new controllers\front\JobController();
$request = new core\Request();

// اختبر index
echo "<h2>Testing index():</h2>";
$result = $controller->index($request);
echo strlen($result) > 0 ? "✅ يعمل" : "❌ لا يعمل";

// اختبر show
echo "<h2>Testing show():</h2>";
$result = $controller->show($request, 1);
echo strlen($result) > 0 ? "✅ يعمل" : "❌ لا يعمل";