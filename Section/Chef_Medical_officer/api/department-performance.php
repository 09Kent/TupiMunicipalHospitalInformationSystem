<?php
/**
 * Tupi Municipal Hospital Information Management System
 * API: Department Performance Summary (Role 2)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

use TMHIS\Database;

try {
    $db = Database::getConnection();

    $stmt = $db->query("SELECT * FROM departments ORDER BY performance_score DESC");
    $departments = $stmt->fetchAll();

    // Calculate aggregated metrics
    $totalStaff = 0;
    $totalCompleted = 0;
    $totalPending = 0;
    $avgScore = 0.0;
    $count = count($departments);

    foreach ($departments as $d) {
        $totalStaff += (int)$d['total_staff'];
        $totalCompleted += (int)$d['completed_tasks'];
        $totalPending += (int)$d['pending_tasks'];
        $avgScore += (float)$d['performance_score'];
    }

    $avgScore = $count > 0 ? round($avgScore / $count, 1) : 94.0;
    $taskCompletionRate = ($totalCompleted + $totalPending) > 0 ? round(($totalCompleted / ($totalCompleted + $totalPending)) * 100, 1) : 95.0;

    echo json_encode([
        'status' => 'success',
        'summary' => [
            'total_departments' => $count,
            'total_staff' => $totalStaff,
            'total_completed_tasks' => $totalCompleted,
            'total_pending_tasks' => $totalPending,
            'hospital_efficiency_score' => $avgScore,
            'task_completion_rate' => $taskCompletionRate
        ],
        'data' => $departments
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
