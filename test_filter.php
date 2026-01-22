<?php
// test_filter.php - اختبار دالة الفلتر مباشرة
require_once 'app/core/Database.php';
require_once 'app/core/Model.php';
require_once 'app/core/Request.php';
require_once 'app/models/AnnonceModel.php';
require_once 'app/models/Company.php';
require_once 'app/controllers/front/JobController.php';

echo "<h1>Testing Filter Function</h1>";

// محاكاة Request
class SimpleRequest {
    private $data;
    public function __construct($getParams = []) {
        $this->data = $getParams;
    }
    public function get($key, $default = null) {
        return $this->data[$key] ?? $default;
    }
}

// 1. اختبار الكنترولر مباشرة
$controller = new controllers\front\JobController();

// 2. اختبار بدون فلاتر
echo "<h2>Test 1: No filters</h2>";
$request1 = new SimpleRequest();
$result1 = $controller->filter($request1);
echo "Result: " . substr($result1, 0, 200) . "...<br>";

// 3. اختبار مع فلتر شركة
echo "<h2>Test 2: Company ID 1</h2>";
$request2 = new SimpleRequest(['company_id' => 1]);
$result2 = $controller->filter($request2);
$data2 = json_decode($result2, true);
echo "Number of offers: " . count($data2) . "<br>";
if (!empty($data2)) {
    echo "First offer: " . $data2[0]['titre'] . "<br>";
}

// 4. اختبار مع فلتر نوع العقد
echo "<h2>Test 3: Contract type CDI</h2>";
$request3 = new SimpleRequest(['contract_type' => 'CDI']);
$result3 = $controller->filter($request3);
$data3 = json_decode($result3, true);
echo "Number of offers: " . count($data3) . "<br>";

// 5. اختبار مع الفلترين معاً
echo "<h2>Test 4: Company 1 + CDI</h2>";
$request4 = new SimpleRequest(['company_id' => 1, 'contract_type' => 'CDI']);
$result4 = $controller->filter($request4);
$data4 = json_decode($result4, true);
echo "Number of offers: " . count($data4) . "<br>";