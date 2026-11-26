<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $localConfig = __DIR__ . '/../.env.local.php';
        if (in_array(PHP_SAPI, ['cli', 'cli-server'], true) && is_file($localConfig)) {
            foreach (require $localConfig as $name => $value) {
                if (getenv($name) === false) {
                    putenv($name . '=' . $value);
                }
            }
        }

        $host = getenv("COURTONE_DB_HOST") ?: "localhost";
        $port = getenv("COURTONE_DB_PORT") ?: "3306";
        $dbname = getenv("COURTONE_DB_NAME") ?: "courtbooking";
        $username = getenv("COURTONE_DB_USER") ?: "root";
<<<<<<< HEAD
        $password = getenv("COURTONE_DB_PASSWORD") !== false ? getenv("COURTONE_DB_PASSWORD") : "p@ssword"; /* change depending on what machine you're using */
=======
        $password = getenv("COURTONE_DB_PASSWORD") !== false ? getenv("COURTONE_DB_PASSWORD") : "01227801jl"; /* change depending on what machine you're using */
>>>>>>> 24163b00168a4657684e7d1b820affb5bbf11b6c

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                $options
            );
        } catch (PDOException $e) {
            error_log("Database Connection Failure: " . $e->getMessage());
<<<<<<< HEAD
            die("Critical Error: " . $e->getMessage());
=======
            http_response_code(503);
            exit('CourtOne is temporarily unavailable. Please try again.');
>>>>>>> 24163b00168a4657684e7d1b820affb5bbf11b6c
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
