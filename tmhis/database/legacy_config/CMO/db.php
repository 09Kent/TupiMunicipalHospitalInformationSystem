<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Role 2: Hospital Chief / Medical Director Database Connection (MySQL PDO)
 */

declare(strict_types=1);

namespace TMHIS;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;
    private static string $host = '127.0.0.1';
    private static string $dbName = 'MedicalRegistrationDB';
    private static string $username = 'root';
    private static string $password = '';
    private static int $port = 3306;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $sqliteFile = __DIR__ . '/../database/hospital.sqlite';
            try {
                self::$instance = new PDO('sqlite:' . $sqliteFile);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                try {
                    $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbName . ";charset=utf8mb4";
                    self::$instance = new PDO($dsn, self::$username, self::$password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);
                } catch (PDOException $e2) {
                    die(json_encode([
                        'status' => 'error',
                        'message' => 'Database connection failed: ' . $e->getMessage()
                    ]));
                }
            }
        }

        return self::$instance;
    }

    public static function logAudit(string $action, string $report = '-', string $details = ''): void {
        try {
            $db = self::getConnection();
            $stmt = $db->prepare("
                INSERT INTO system_audit_logs (UserName, UserRole, Action, Module, Details)
                VALUES ('Dr. Maria Santos', 'Medical Director', :action, 'Director Oversight', :details)
            ");
            $stmt->execute([
                ':action' => $action,
                ':details' => $report . ' - ' . $details
            ]);
        } catch (PDOException $e) {
            // Silently ignore
        }
    }
}
