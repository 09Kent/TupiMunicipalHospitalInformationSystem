<?php
/**
 * Tupi Municipal Hospital Information Management System
 * API: Administrative Notifications (Role 2)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

use TMHIS\Database;

try {
    $db = Database::getConnection();

    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        $stmt = $db->query("SELECT * FROM administrative_notifications ORDER BY created_at DESC");
        $notifs = $stmt->fetchAll();

        foreach ($notifs as &$n) {
            $dt = new DateTime($n['created_at']);
            $now = new DateTime();
            $diff = $now->diff($dt);
            if ($diff->days === 0 && $diff->h === 0) {
                $n['relative_time'] = $diff->i . ' minutes ago';
            } elseif ($diff->days === 0) {
                $n['relative_time'] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            } else {
                $n['relative_time'] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
            }
        }

        $unreadCount = $db->query("SELECT COUNT(*) FROM administrative_notifications WHERE is_read = 0")->fetchColumn();

        echo json_encode([
            'status' => 'success',
            'unread_count' => (int)$unreadCount,
            'data' => $notifs
        ]);
        exit;
    }

    if ($action === 'mark_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE administrative_notifications SET is_read = 1 WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $db->exec("UPDATE administrative_notifications SET is_read = 1 WHERE is_read = 0");
        }

        echo json_encode(['status' => 'success', 'message' => 'Notifications marked as read']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
