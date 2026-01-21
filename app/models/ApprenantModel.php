<?php

namespace models;

use core\Model;

class ApprenantModel extends Model {
    protected string $table = 'apprenants';

    
    public function getAllApprenants(): array {
        $sql = "SELECT u.name, u.email, a.promotion, a.specialisation 
                FROM apprenants a 
                JOIN users u ON a.user_id = u.id 
                WHERE u.role = 'apprenant' 
                ORDER BY u.name ASC";
        return $this->db->query($sql)->fetchAll();
    }
}