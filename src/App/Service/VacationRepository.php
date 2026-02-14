<?php

declare(strict_types=1);

namespace App\Service;

class VacationRepository
{
    public function __construct(private DatabaseService $db)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->db->pdo()->query('SELECT * FROM vacations ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM vacations WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->pdo()->prepare('
            INSERT INTO vacations (destination, start_date, end_date, budget, notes, image_url)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['destination'] ?? '',
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['budget'] ?? 0,
            $data['notes'] ?? null,
            $data['image_url'] ?? null,
        ]);
        return (int) $this->db->pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->pdo()->prepare('
            UPDATE vacations
            SET destination = ?, start_date = ?, end_date = ?, budget = ?, notes = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ');
        $stmt->execute([
            $data['destination'] ?? '',
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['budget'] ?? 0,
            $data['notes'] ?? null,
            $data['image_url'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->pdo()->prepare('DELETE FROM vacations WHERE id = ?');
        $stmt->execute([$id]);
    }
}
