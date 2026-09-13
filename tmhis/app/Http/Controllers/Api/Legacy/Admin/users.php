<?php
// Section/Admin/api/users.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/UserManager.php';
require_once __DIR__ . '/../models/AuditLog.php';

if (!Session::isAdmin()) {
    json_response(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$model = new UserManager();
$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $users = $model->getAll();
        json_response(['success' => true, 'data' => $users]);
    } elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->create($_POST);
        AuditLog::log('Create User Account', 'User Account Administration', "Created user: {$_POST['Username']} (Role: {$_POST['Role']})", (string)$id);
        json_response(['success' => true, 'id' => $id, 'message' => 'User account created successfully with encrypted password.']);
    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['UserID'] ?? 0);
        $success = $model->update($id, $_POST);
        AuditLog::log('Update User Account', 'User Account Administration', "Updated user ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'User account updated successfully.' : 'Failed to update user.']);
    } elseif ($action === 'toggle_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'Active';
        $success = $model->toggleStatus($id, $status);
        AuditLog::log('Toggle User Status', 'User Account Administration', "Set user ID $id status to $status", (string)$id);
        json_response(['success' => $success, 'message' => $success ? "User status changed to $status." : 'Failed to update status.']);
    } elseif ($action === 'reset_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';
        if (strlen($newPassword) < 6) {
            json_response(['success' => false, 'message' => 'Password must be at least 6 characters.'], 400);
        }
        $success = $model->resetPassword($id, $newPassword);
        AuditLog::log('Reset Password', 'User Account Administration', "Reset password for user ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'Password reset successfully with secure bcrypt hash.' : 'Failed to reset password.']);
    } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $success = $model->delete($id);
        AuditLog::log('Delete User Account', 'User Account Administration', "Deleted user ID: $id", (string)$id);
        json_response(['success' => $success, 'message' => $success ? 'User deleted successfully.' : 'Cannot delete system administrator.']);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
