<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = "localhost";
        $dbname = "courtbooking";
        $username = "root";
        $password = "banawe27"; /* change depending on what machine you're using */
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                $options
            );
        } catch (PDOException $e) {
            error_log("Database Connection Failure: " . $e->getMessage());
            die("Critical Error: Database connection dropped.");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
?>