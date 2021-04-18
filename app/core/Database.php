<?php

namespace app\core;

use PDO;

/**
 * Class Database
 * @package app\core
 * @todo - implement MigrationInterface (up(), down(), log()) lub LoggingInterface dla wielu klas (Strategia)
 * @todo - add errors table in db and log errors to that table
 */

class Database
{
    public PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $migrationsFiles = scandir(Application::$ROOT_DIR . '/migrations');

        $toApplyMigrations = array_diff($migrationsFiles, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration == '.' || $migration == '..') {
                continue;
            }
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }
        if (! empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log('All migrations are applied');
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
            ) ENGINE=INNODB;");
    }

    private function getAppliedMigrations() : array
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations)
    {
        $valuesString = implode(',', array_map(fn($m) => "('$m')", $migrations));
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $valuesString");
        $stmt->execute();
    }

    protected function log(string $message) : void
    {
        echo '['. date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}
