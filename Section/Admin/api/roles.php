<?php
// Section/Admin/api/roles.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/RolePermission.php';
require_once __DIR__ . '/../models/AuditLog.php';

if (!Session::isAdmin()) {
    json_response(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$model = new RolePermission();
$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $roles = $model->getRoles();
        json_response(['success' => true, 'data' => $roles]);
    } elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->createRole($_POST);
        AuditLog::log('Create Role', 'Role & Permission Management', "Created custom role: {$_POST['RoleName']}", (string)$id);
        json_response(['success' => true, 'id' => $id, 'message' => 'Role created successfully.']);
    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['RoleID'] ?? 0);
        $success = $model->updateRole($id, $_POST);
        AuditLog::log('Update Role', 'Role & Permission Management', "Updated role ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'Role updated successfully.' : 'Cannot edit core system roles.']);
    } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $success = $model->deleteRole($id);
        AuditLog::log('Delete Role', 'Role & Permission Management', "Deleted role ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'Role deleted successfully.' : 'Cannot delete core system roles.']);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
