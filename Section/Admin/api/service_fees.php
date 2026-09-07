<?php
// Section/Admin/api/service_fees.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/ServiceFee.php';
require_once __DIR__ . '/../models/AuditLog.php';

if (!Session::isAdmin()) {
    json_response(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$model = new ServiceFee();
$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $fees = $model->getAll();
        json_response(['success' => true, 'data' => $fees]);
    } elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->create($_POST);
        AuditLog::log('Create Service Fee', 'Service Fee Management', "Created fee: {$_POST['ServiceName']} ({$_POST['StandardRate']})", (string)$id);
        json_response(['success' => true, 'id' => $id, 'message' => 'Service fee created successfully.']);
    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['FeeID'] ?? 0);
        $success = $model->update($id, $_POST);
        AuditLog::log('Update Service Fee', 'Service Fee Management', "Updated fee ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'Service fee updated successfully.' : 'Failed to update fee.']);
    } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $success = $model->delete($id);
        AuditLog::log('Delete Service Fee', 'Service Fee Management', "Deleted fee ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'Service fee deleted successfully.' : 'Failed to delete fee.']);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
