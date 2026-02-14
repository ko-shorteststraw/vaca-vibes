<?php

declare(strict_types=1);

namespace App\Service;

use PDO;

class DatabaseService
{
    private PDO $pdo;

    public function __construct(string $dbPath)
    {
        $dir = dirname($dbPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('PRAGMA journal_mode=WAL');
        $this->pdo->exec('PRAGMA foreign_keys=ON');

        $this->initSchema();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    private function initSchema(): void
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS vacations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                destination TEXT NOT NULL,
                start_date TEXT,
                end_date TEXT,
                budget REAL DEFAULT 0,
                notes TEXT,
                image_url TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS itinerary_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                vacation_id INTEGER NOT NULL,
                day_number INTEGER NOT NULL DEFAULT 1,
                title TEXT NOT NULL,
                description TEXT,
                time TEXT,
                cost REAL DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (vacation_id) REFERENCES vacations(id) ON DELETE CASCADE
            )
        ');

        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS expenses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                vacation_id INTEGER NOT NULL,
                description TEXT NOT NULL,
                amount REAL NOT NULL DEFAULT 0,
                category TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (vacation_id) REFERENCES vacations(id) ON DELETE CASCADE
            )
        ');
    }
}
