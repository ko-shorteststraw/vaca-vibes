<?php

declare(strict_types=1);

namespace App\Service;

class UserRepository
{
    public function __construct(private DatabaseService $db)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT id, username, is_admin, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $username, string $passwordHash, bool $isAdmin = false): int
    {
        $stmt = $this->db->pdo()->prepare('INSERT INTO users (username, password, is_admin) VALUES (?, ?, ?)');
        $stmt->execute([$username, $passwordHash, $isAdmin ? 1 : 0]);
        return (int) $this->db->pdo()->lastInsertId();
    }

    public function findAll(): array
    {
        $stmt = $this->db->pdo()->query('SELECT id, username, is_admin, created_at FROM users ORDER BY created_at');
        return $stmt->fetchAll();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->pdo()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function countAll(): int
    {
        return (int) $this->db->pdo()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
