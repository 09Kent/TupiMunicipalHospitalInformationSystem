<?php
// Section/Medical_Officer/config/Database.php

class Database
{
    private static ?PDO $instance = null;
    private static string $host = '127.0.0.1';
    private static string $dbName = 'MedicalRegistrationDB';
    private static string $username = 'root';
    private static string $password = '';
    private static int $port = 3306;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbName . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];

                self::$instance = new PDO($dsn, self::$username, self::$password, $options);
                self::$instance->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
            } catch (PDOException $e) {
                if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database connection failed: ' . $e->getMessage()
                    ]);
                    exit;
                }
                die("<h1>Database Connection Error</h1><p>" . htmlspecialchars($e->getMessage()) . "</p><p>Please ensure MySQL service is active.</p>");
            }
        }

        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
}
