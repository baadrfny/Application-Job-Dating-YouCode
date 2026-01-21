<?php
namespace models;



class Company {
    protected $table = 'entreprises';
    
    public function getAll() {
        $pdo = \core\Database::getConnection();
        
        $sql = "SELECT * FROM entreprises ORDER BY nom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function findById($id) {
        $pdo = \core\Database::getConnection();
        
        $sql = "SELECT * FROM entreprises WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    

    public function getForFilters() {
        $pdo = \core\Database::getConnection();
        
        $sql = "SELECT id, nom FROM entreprises ORDER BY nom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}