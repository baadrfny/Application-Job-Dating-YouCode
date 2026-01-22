<?php

namespace models;

use core\Model;

class AnnonceModel extends Model {
    
    protected string $table = 'annonces';

    // get only active annonces (deleted_at = false) avec nom d'entreprise  
    public function getActiveAnnonces(): array {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM annonces a 
                JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE a.deleted_at = FALSE 
                ORDER BY a.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    
    public function getArchivedAnnonces(): array {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM annonces a 
                JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE a.deleted_at = TRUE 
                ORDER BY a.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // Soft Delete
    public function archive(int $id): bool {
        return $this->update($id, ['deleted_at' => 1]);
    }

    //  restore
    public function restore(int $id): bool {
        return $this->update($id, ['deleted_at' => 0]);
    }

    //latest 3 annonces
    public function getLatest(int $limit = 3): array {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM annonces a
                JOIN entreprises e ON a.entreprise_id = e.id
                WHERE a.deleted_at = FALSE 
                ORDER BY a.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function getFilteredAnnonces($company_id = null, $contract_type = null, $search = null) {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM annonces a 
                JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE a.deleted_at = FALSE";
        
        $params = [];
        
        if ($company_id) {
            $sql .= " AND a.entreprise_id = :company_id";
            $params['company_id'] = $company_id;
        }
        
        if ($contract_type) {
            $sql .= " AND a.type_contrat = :contract_type";
            $params['contract_type'] = $contract_type;
        }
        
        if ($search) {
            $sql .= " AND (a.titre LIKE :search1 OR a.description LIKE :search2 OR e.nom LIKE :search3)";
            $searchValue = '%' . $search . '%';
            $params['search1'] = $searchValue;
            $params['search2'] = $searchValue;
            $params['search3'] = $searchValue;
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Search announcements by title, company name, and description
    public function searchAnnonces(string $query): array {
        $searchTerm = '%' . $query . '%';
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM annonces a 
                JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE a.deleted_at = FALSE 
                AND (a.titre LIKE :query1 
                     OR a.description LIKE :query2 
                     OR e.nom LIKE :query3)
                ORDER BY a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query1', $searchTerm);
        $stmt->bindValue(':query2', $searchTerm);
        $stmt->bindValue(':query3', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
