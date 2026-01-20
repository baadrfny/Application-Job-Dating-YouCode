<?php

namespace core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;          
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC");
        return $stmt->fetchAll();
    }

    public function find($id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        
        $cols = array_keys($data);
        $fields = implode(',', $cols);
        $placeholders = ':' . implode(',:', $cols);

        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update($id, array $data): bool
    {
        // build: a=:a, b=:b
        $sets = [];
        foreach ($data as $k => $v) {
            $sets[] = "{$k} = :{$k}";
        }
        $setSql = implode(', ', $sets);

        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET {$setSql} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
}
