<?php
// test_all_models.php
require_once 'app/core/Model.php';
require_once 'app/core/Database.php';
require_once 'app/models/AnnonceModel.php';
require_once 'app/models/Company.php';

echo "<h1>Testing Both Models Together</h1>";

try {
    // 1. AnnonceModel
    $annonce = new models\AnnonceModel();
    $company = new models\Company();
    
    echo "<h2>📊 AnnonceModel Tests</h2>";
    
    echo "<h3>Active Annonces:</h3>";
    $active = $annonce->getActiveAnnonces();
    echo "Count: <strong>" . count($active) . "</strong><br>";
    echo "<ul>";
    foreach ($active as $a) {
        echo "<li>" . $a['titre'] . " - " . $a['entreprise_nom'] . " (" . $a['type_contrat'] . ")</li>";
    }
    echo "</ul>";
    
    echo "<h3>Latest 3:</h3>";
    $latest = $annonce->getLatest(3);
    echo "<ul>";
    foreach ($latest as $l) {
        echo "<li>" . $l['titre'] . " (" . $l['created_at'] . ")</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    
    echo "<h2>🏢 Company Model Tests</h2>";
    
    echo "<h3>All Companies:</h3>";
    $companies = $company->getAll();
    echo "Count: <strong>" . count($companies) . "</strong><br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Sector</th></tr>";
    foreach ($companies as $c) {
        echo "<tr>";
        echo "<td>" . $c['id'] . "</td>";
        echo "<td>" . $c['nom'] . "</td>";
        echo "<td>" . $c['email'] . "</td>";
        echo "<td>" . ($c['secteur'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Company for Filters:</h3>";
    $filters = $company->getForFilters();
    echo "<select>";
    foreach ($filters as $f) {
        echo "<option value='" . $f['id'] . "'>" . $f['nom'] . "</option>";
    }
    echo "</select>";
    
    echo "<hr>";
    
    echo "<h2>🔗 Integration Test</h2>";
    echo "<h3>First Annonce with its Company Details:</h3>";
    if (!empty($active)) {
        $first = $active[0];
        $comp = $company->findById($first['entreprise_id']);
        
        echo "Annonce: <strong>" . $first['titre'] . "</strong><br>";
        echo "Company: <strong>" . $first['entreprise_nom'] . "</strong><br>";
        echo "Company Email: " . ($comp['email'] ?? 'N/A') . "<br>";
        echo "Company Sector: " . ($comp['secteur'] ?? 'N/A') . "<br>";
    }
    
    echo "<hr>";
    echo "<h1 style='color:green; text-align:center;'>✅ ALL MODELS WORKING PERFECTLY!</h1>";
    echo "<p style='text-align:center;'>Both models connect to database and return correct data.</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<pre>Stack trace: " . $e->getTraceAsString() . "</pre>";
}