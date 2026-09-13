<?php
/**
 * Tupi Municipal Hospital Information Management System
 * API: Staff Activity Monitoring (Role 2)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

use TMHIS\Database;

try {
    $db = Database::getConnection();

    $search = trim($_GET['search'] ?? '');
    $department = trim($_GET['department'] ?? 'all');
    $role = trim($_GET['role'] ?? 'all');
    $staffMember = trim($_GET['staff_member'] ?? 'all');
    $activityType = trim($_GET['activity_type'] ?? 'all');
    $dateRange = trim($_GET['date_range'] ?? 'all');

    $sql = "SELECT * FROM staff_activity_logs WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND (staff_name LIKE :search OR role_title LIKE :search OR department_name LIKE :search OR activity_description LIKE :search OR activity_type LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if ($department !== 'all') {
        $sql .= " AND department_name = :dept";
        $params[':dept'] = $department;
    }

    if ($role !== 'all') {
        $sql .= " AND role_title = :role";
        $params[':role'] = $role;
    }

    if ($staffMember !== 'all') {
        $sql .= " AND staff_name = :member";
        $params[':member'] = $staffMember;
    }

    if ($activityType !== 'all') {
        $sql .= " AND activity_type = :atype";
        $params[':atype'] = $activityType;
    }

    if ($dateRange === 'today') {
        $sql .= " AND date(logged_at) = date('now', 'localtime')";
    } elseif ($dateRange === 'yesterday') {
        $sql .= " AND date(logged_at) = date('now', 'localtime', '-1 day')";
    } elseif ($dateRange === 'week') {
        $sql .= " AND date(logged_at) >= date('now', 'localtime', '-7 days')";
    } elseif ($dateRange === 'month') {
        $sql .= " AND date(logged_at) >= date('now', 'localtime', '-30 days')";
    }

    $sql .= " ORDER BY logged_at DESC LIMIT 100";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    // Format timestamps for display
    foreach ($logs as &$log) {
        $dt = new DateTime($log['logged_at']);
        $log['formatted_date'] = $dt->format('M d, Y');
        $log['formatted_time'] = $dt->format('h:i A');
    }

    // Get filter options
    $deptOptions = $db->query("SELECT DISTINCT department_name FROM staff_activity_logs ORDER BY department_name ASC")->fetchAll(PDO::FETCH_COLUMN);
    $roleOptions = $db->query("SELECT DISTINCT role_title FROM staff_activity_logs ORDER BY role_title ASC")->fetchAll(PDO::FETCH_COLUMN);
    $memberOptions = $db->query("SELECT DISTINCT staff_name FROM staff_activity_logs ORDER BY staff_name ASC")->fetchAll(PDO::FETCH_COLUMN);
    $typeOptions = $db->query("SELECT DISTINCT activity_type FROM staff_activity_logs ORDER BY activity_type ASC")->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'status' => 'success',
        'count' => count($logs),
        'filters' => [
            'departments' => $deptOptions,
            'roles' => $roleOptions,
            'staff_members' => $memberOptions,
            'activity_types' => $typeOptions
        ],
        'data' => $logs
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
