<?php

declare(strict_types=1);

namespace App\Service;

class ExpenseRepository
{
    public function __construct(private DatabaseService $db)
    {
    }

    public function findByVacation(int $vacationId): array
    {
        $stmt = $this->db->pdo()->prepare(
            'SELECT * FROM expenses WHERE vacation_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$vacationId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM expenses WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->pdo()->prepare('
            INSERT INTO expenses (vacation_id, description, amount, category)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['vacation_id'],
            $data['description'] ?? '',
            $data['amount'] ?? 0,
            $data['category'] ?? null,
        ]);
        return (int) $this->db->pdo()->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->pdo()->prepare('DELETE FROM expenses WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function sumByVacation(int $vacationId): float
    {
        $stmt = $this->db->pdo()->prepare(
            'SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE vacation_id = ?'
        );
        $stmt->execute([$vacationId]);
        return (float) $stmt->fetchColumn();
    }
}
