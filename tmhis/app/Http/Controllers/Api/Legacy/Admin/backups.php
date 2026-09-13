<?php
// Section/Admin/api/backups.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/BackupManager.php';
require_once __DIR__ . '/../models/AuditLog.php';

if (!Session::isAdmin()) {
    json_response(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$model = new BackupManager();
$action = $_REQUEST['action'] ?? 'history';

try {
    if ($action === 'history') {
        $history = $model->getBackupHistory();
        json_response(['success' => true, 'data' => $history]);
    } elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $type = $_POST['type'] ?? 'Full Database';
        $res = $model->createBackup($type);
        AuditLog::log('Create Database Backup', 'Data Backup Operations', "Generated backup snapshot: {$res['filename']} ({$res['filesize']})");
        json_response([
            'success' => true,
            'message' => "Backup {$res['filename']} ({$res['filesize']}) generated successfully.",
            'data' => $res
        ]);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
