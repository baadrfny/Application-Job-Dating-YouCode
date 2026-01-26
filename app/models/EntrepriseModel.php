<?php

namespace models;

use core\Model;

class EntrepriseModel extends Model {
    protected string $table = 'entreprises';

    // (Unique)
    public function isEmailExists(string $email): bool {
        return $this->count("email = :email", ['email' => $email]) > 0;
    }

    public function isEmailExistsForOther(string $email, int $id): bool {
        return $this->count("email = :email AND id <> :id", ['email' => $email, 'id' => $id]) > 0;
    }
}
