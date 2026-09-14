<?php
// Section/Admin/models/BackupManager.php

require_once __DIR__ . '/../config/Database.php';

class BackupManager
{
    private PDO $db;
    private string $backupDir;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->backupDir = function_exists('storage_path') ? storage_path('app/backups') : (__DIR__ . '/../backups');
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0775, true);
        }
    }

    public function getBackupHistory(): array
    {
        $stmt = $this->db->query("SELECT * FROM backup_logs ORDER BY BackupID DESC");
        return $stmt->fetchAll();
    }

    public function createBackup(string $type = 'Full Database'): array
    {
        $filename = 'tmhis_backup_' . date('Ymd_His') . '.sql';
        $filepath = $this->backupDir . '/' . $filename;

        // Generate full SQL dump using PDO
        $sqlDump = "-- Tupi Municipal Hospital Database Backup\n";
        $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sqlDump .= "-- Backup Type: $type\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tablesStmt = $this->db->query("SHOW TABLES");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $createStmt = $this->db->query("SHOW CREATE TABLE `$table`")->fetch();
            $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
            $sqlDump .= $createStmt['Create Table'] . ";\n\n";

            if ($type !== 'Schema Only') {
                $rows = $this->db->query("SELECT * FROM `$table`")->fetchAll();
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $keys = array_keys($row);
                        $values = array_map(function($v) {
                            if ($v === null) return 'NULL';
                            return $this->db->quote((string)$v);
                        }, array_values($row));
                        $sqlDump .= "INSERT INTO `$table` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sqlDump .= "\n";
                }
            }
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";
        file_put_contents($filepath, $sqlDump);

        $filesizeKb = round(filesize($filepath) / 1024, 2) . ' KB';

        $stmt = $this->db->prepare("INSERT INTO backup_logs (FileName, FileSize, BackupType, CreatedBy, Status, Notes)
                                    VALUES (:FileName, :FileSize, :BackupType, :CreatedBy, 'Completed', :Notes)");
        $stmt->execute([
            ':FileName' => $filename,
            ':FileSize' => $filesizeKb,
            ':BackupType' => $type,
            ':CreatedBy' => $_SESSION['user_id'] ?? 1,
            ':Notes' => 'Automated snapshot backup created successfully.'
        ]);

        return [
            'success' => true,
            'filename' => $filename,
            'filesize' => $filesizeKb
        ];
    }
}
