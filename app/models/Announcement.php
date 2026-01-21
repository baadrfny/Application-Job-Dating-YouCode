<?php

namespace models;

use PDO;

class Announcement {
    private $pdo;
    
    public function __construct() {
        
        $this->pdo = new PDO("mysql:host=localhost;dbname=job_dating_youcode", "root", "");
    }
    
    public function getActiveOffers() {
        $sql = "SELECT * FROM annonces WHERE deleted_at = false";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
    $pdo = \core\Database::getConnection();
    
    $sql = "SELECT * FROM annonces WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
}