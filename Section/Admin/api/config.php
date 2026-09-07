<?php
// Section/Admin/api/config.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/HospitalInfo.php';
require_once __DIR__ . '/../models/AuditLog.php';

if (!Session::isAdmin()) {
    json_response(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$model = new HospitalInfo();
$action = $_REQUEST['action'] ?? 'get';

try {
    if ($action === 'get') {
        $info = $model->get();
        json_response(['success' => true, 'data' => $info]);
    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $success = $model->update($_POST);
        if ($success) {
            AuditLog::log('Update Hospital Info', 'System Configuration', 'Updated hospital general details & accreditations');
        }
        json_response(['success' => $success, 'message' => $success ? 'Hospital configuration updated successfully.' : 'Failed to update configuration.']);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
