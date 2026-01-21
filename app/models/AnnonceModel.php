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
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at = FALSE ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}