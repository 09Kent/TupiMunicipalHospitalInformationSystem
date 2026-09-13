<?php
// Section/Admin/api/departments.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/AuditLog.php';

if (!Session::isAdmin()) {
    json_response(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$model = new Department();
$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $depts = $model->getAll();
        json_response(['success' => true, 'data' => $depts]);
    } elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->create($_POST);
        AuditLog::log('Create Department', 'Department Management', "Created department: {$_POST['DepartmentName']}", (string)$id);
        json_response(['success' => true, 'id' => $id, 'message' => 'Department created successfully.']);
    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['DepartmentID'] ?? 0);
        $success = $model->update($id, $_POST);
        AuditLog::log('Update Department', 'Department Management', "Updated department ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'Department updated successfully.' : 'Failed to update department.']);
    } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $success = $model->delete($id);
        AuditLog::log('Delete Department', 'Department Management', "Deleted department ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'Department deleted successfully.' : 'Failed to delete department.']);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
