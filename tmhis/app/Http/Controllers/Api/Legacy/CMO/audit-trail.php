<?php
/**
 * Tupi Municipal Hospital Information Management System
 * API: Immutable Executive Audit Trail (Role 2)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

use TMHIS\Database;

try {
    $db = Database::getConnection();

    $stmt = $db->query("SELECT * FROM audit_trail ORDER BY logged_at DESC LIMIT 50");
    $logs = $stmt->fetchAll();

    foreach ($logs as &$l) {
        $dt = new DateTime($l['logged_at']);
        $l['formatted_date'] = $dt->format('M d, Y');
        $l['formatted_time'] = $dt->format('h:i A');
    }

    echo json_encode([
        'status' => 'success',
        'count' => count($logs),
        'data' => $logs
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
