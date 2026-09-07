<?php
// Section/Admin/models/AuditLog.php

require_once __DIR__ . '/../config/Database.php';

class AuditLog
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public static function log(string $action, string $module, ?string $details = '', ?string $recordId = null): void
    {
        try {
            $db = Database::getConnection();
            $user = $_SESSION['username'] ?? 'system';
            $role = $_SESSION['role'] ?? 'System';
            $userId = $_SESSION['user_id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);

            $stmt = $db->prepare("INSERT INTO system_audit_logs (UserID, UserName, UserRole, Action, Module, RecordID, Details, IPAddress, UserAgent)
                                  VALUES (:UserID, :UserName, :UserRole, :Action, :Module, :RecordID, :Details, :IPAddress, :UserAgent)");
            $stmt->execute([
                ':UserID' => $userId,
                ':UserName' => $user,
                ':UserRole' => $role,
                ':Action' => $action,
                ':Module' => $module,
                ':RecordID' => $recordId,
                ':Details' => $details,
                ':IPAddress' => $ip,
                ':UserAgent' => $ua
            ]);
        } catch (Throwable $e) {
            // Fail gracefully
        }
    }

    public function getActivityLogs(int $limit = 50, string $moduleFilter = ''): array
    {
        $sql = "SELECT * FROM system_audit_logs WHERE 1=1";
        $params = [];
        if (!empty($moduleFilter)) {
            $sql .= " AND Module = :module";
            $params[':module'] = $moduleFilter;
        }
        $sql .= " ORDER BY LogID DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getErrorLogs(int $limit = 50): array
    {
        $stmt = $this->db->prepare("SELECT * FROM system_error_logs ORDER BY ErrorID DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
