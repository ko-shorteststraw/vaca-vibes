<?php

declare(strict_types=1);

namespace App\Service;

class ItineraryRepository
{
    public function __construct(private DatabaseService $db)
    {
    }

    public function findByVacation(int $vacationId): array
    {
        $stmt = $this->db->pdo()->prepare(
            'SELECT * FROM itinerary_items WHERE vacation_id = ? ORDER BY day_number, time'
        );
        $stmt->execute([$vacationId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM itinerary_items WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->pdo()->prepare('
            INSERT INTO itinerary_items (vacation_id, day_number, title, description, time, cost)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['vacation_id'],
            $data['day_number'] ?? 1,
            $data['title'] ?? '',
            $data['description'] ?? null,
            $data['time'] ?? null,
            $data['cost'] ?? 0,
        ]);
        return (int) $this->db->pdo()->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->pdo()->prepare('DELETE FROM itinerary_items WHERE id = ?');
        $stmt->execute([$id]);
    }
}
