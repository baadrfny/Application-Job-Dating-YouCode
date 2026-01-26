<?php

namespace models;

use core\Model;

class Application extends Model
{
    protected string $table = 'applications';

    public function findByStudentAndAnnouncement(int $studentId, int $announcementId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE student_id = :student_id AND announcement_id = :announcement_id LIMIT 1"
        );
        $stmt->execute([
            'student_id' => $studentId,
            'announcement_id' => $announcementId
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByStudent(int $studentId): array
    {
        $sql = "SELECT a.*, an.titre, an.localisation, e.nom AS entreprise_nom
                FROM applications a
                JOIN annonces an ON a.announcement_id = an.id
                JOIN entreprises e ON an.entreprise_id = e.id
                WHERE a.student_id = :student_id
                ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function getByAnnouncement(int $announcementId): array
    {
        $sql = "SELECT a.*, u.name, u.email, ap.promotion, ap.specialisation
                FROM applications a
                JOIN users u ON a.student_id = u.id
                LEFT JOIN apprenants ap ON ap.user_id = u.id
                WHERE a.announcement_id = :announcement_id
                ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['announcement_id' => $announcementId]);
        return $stmt->fetchAll();
    }
}
