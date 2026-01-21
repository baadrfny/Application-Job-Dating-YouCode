<?php

namespace models;

use core\Model;

class EntrepriseModel extends Model {
    protected string $table = 'entreprises';

    // التحقق من وجود إعلانات مرتبطة قبل الحذف (Delete Policy)
    public function canDelete(int $id): bool {
        $sql = "SELECT COUNT(*) FROM annonces WHERE entreprise_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() == 0; // if not exist ; returns true 
    }

    // (Unique)
    public function isEmailExists(string $email): bool {
        return $this->count("email = :email", ['email' => $email]) > 0;
    }
}