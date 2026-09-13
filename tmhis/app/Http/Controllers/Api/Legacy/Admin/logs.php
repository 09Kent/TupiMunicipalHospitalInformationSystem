<?php
// Section/Admin/api/logs.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/AuditLog.php';

if (!Session::isAdmin()) {
    json_response(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$model = new AuditLog();
$type = $_REQUEST['type'] ?? 'activity';
$limit = (int)($_REQUEST['limit'] ?? 100);
$module = $_REQUEST['module'] ?? '';

try {
    if ($type === 'activity') {
        $logs = $model->getActivityLogs($limit, $module);
        json_response(['success' => true, 'data' => $logs]);
    } elseif ($type === 'error') {
        $logs = $model->getErrorLogs($limit);
        json_response(['success' => true, 'data' => $logs]);
    } else {
        json_response(['success' => false, 'message' => 'Invalid log type.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
