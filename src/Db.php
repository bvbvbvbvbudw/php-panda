<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Db {
    private $pdo;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $dbHost = $_ENV['DB_HOST'];
        $dbPort = $_ENV['DB_PORT'];
        $dbName = $_ENV['DB_DATABASE'];
        $dbUser = $_ENV['DB_USERNAME'];
        $dbPass = $_ENV['DB_PASSWORD'];

        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";

        try {
            $this->pdo = new PDO($dsn, $dbUser, $dbPass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getSubscriptions() {
        return $this->query('SELECT * FROM subscriptions WHERE user_confirmed = 1')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertPriceChange($adLink, $price) {
        return $this->query('INSERT INTO price_changes (ad_link, price, checked_at) VALUES (?, ?, NOW())', [$adLink, $price]);
    }

    public function updateSubscriptionPrice($id, $price) {
        return $this->query('UPDATE subscriptions SET last_checked_price = ? WHERE id = ?', [$price, $id]);
    }
}
