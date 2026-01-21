<?php
// test.php - خارج المجلدات
require_once 'app/core/Database.php';
require_once 'app/models/Announcement.php';
require_once 'app/models/Company.php';

echo "<h1>Testing Models</h1>";

// 1. اختبر Announcement Model
echo "<h3>1. Testing Announcement Model:</h3>";
try {
    $annonce = new models\Announcement();
    $offers = $annonce->getActiveOffers();
    
    echo "✅ Announcement model loaded successfully<br>";
    echo "Number of active offers: " . count($offers) . "<br>";
    
    if (!empty($offers)) {
        echo "First offer title: " . htmlspecialchars($offers[0]['titre']) . "<br>";
        echo "<pre>";
        print_r($offers[0]);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Error in Announcement model: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 2. اختبر Company Model
echo "<h3>2. Testing Company Model:</h3>";
try {
    $company = new models\Company();
    $companies = $company->getAll();
    
    echo "✅ Company model loaded successfully<br>";
    echo "Number of companies: " . count($companies) . "<br>";
    
    if (!empty($companies)) {
        echo "First company: " . htmlspecialchars($companies[0]['nom']) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error in Company model: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 3. اختبر findById
echo "<h3>3. Testing findById:</h3>";
try {
    $annonce = new models\Announcement();
    $offer = $annonce->findById(1);
    
    if ($offer) {
        echo "✅ Found offer ID 1: " . htmlspecialchars($offer['titre']) . "<br>";
        echo "Deleted status: " . ($offer['deleted_at'] ? 'Archived' : 'Active') . "<br>";
    } else {
        echo "❌ Offer ID 1 not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error in findById: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 4. اختبر قاعدة البيانات مباشرة
echo "<h3>4. Direct Database Test:</h3>";
try {
    $pdo = core\Database::getConnection();
    echo "✅ Database connection successful<br>";
    
    // عدّد العروض النشطة
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM annonces WHERE deleted_at = false");
    $result = $stmt->fetch();
    echo "Active offers in DB: " . $result['count'] . "<br>";
    
    // عدّد الشركات
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM entreprises");
    $result = $stmt->fetch();
    echo "Companies in DB: " . $result['count'] . "<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "If all tests show ✅, then your models are ready!<br>";
echo "You can now proceed with Task 3 (Offer Details).";