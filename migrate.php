<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD']
);

function runMigration($pdo, $migrationFile) {
    $migrationName = basename($migrationFile, '.sql');

    $stmt = $pdo->prepare("SELECT * FROM migrations WHERE migration = :migration");
    $stmt->execute(['migration' => $migrationName]);

    if ($stmt->fetch()) {
        echo "Migration {$migrationName} has already been applied.\n";
        return;
    }

    $sql = file_get_contents($migrationFile);
    $pdo->exec($sql);

    $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
    $stmt->execute(['migration' => $migrationName]);

    echo "Migration {$migrationName} applied successfully.\n";
}

$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
");

$migrationFiles = glob(__DIR__ . '/migrations/*.sql');

foreach ($migrationFiles as $migrationFile) {
    runMigration($pdo, $migrationFile);
}

echo "All migrations applied.\n";
