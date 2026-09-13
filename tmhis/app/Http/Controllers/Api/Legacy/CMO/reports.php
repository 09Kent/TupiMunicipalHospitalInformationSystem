<?php
/**
 * Tupi Municipal Hospital Information Management System
 * API: Operational Reports (Role 2)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

use TMHIS\Database;

try {
    $db = Database::getConnection();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        $category = $_GET['category'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT id, report_code, report_name, category, period_type, period_label, status, last_updated, summary_metrics_json FROM operational_reports WHERE 1=1";
        $params = [];

        if ($category !== 'all') {
            $sql .= " AND category = :category";
            $params[':category'] = $category;
        }

        if ($search !== '') {
            $sql .= " AND (report_name LIKE :search OR report_code LIKE :search OR period_label LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY id ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll();

        foreach ($reports as &$r) {
            $r['summary_metrics'] = json_decode($r['summary_metrics_json'] ?? '{}', true);
            unset($r['summary_metrics_json']);
        }

        echo json_encode([
            'status' => 'success',
            'count' => count($reports),
            'data' => $reports
        ]);
        exit;
    }

    if ($action === 'get') {
        $id = (int)($_GET['id'] ?? 0);
        $code = trim($_GET['code'] ?? '');

        if ($id > 0) {
            $stmt = $db->prepare("SELECT * FROM operational_reports WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $stmt = $db->prepare("SELECT * FROM operational_reports WHERE report_code = :code");
            $stmt->execute([':code' => $code]);
        }

        $report = $stmt->fetch();
        if (!$report) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Report not found']);
            exit;
        }

        $report['summary_metrics'] = json_decode($report['summary_metrics_json'] ?? '{}', true);
        $report['detailed_payload'] = json_decode($report['detailed_payload_json'] ?? '{}', true);
        unset($report['summary_metrics_json'], $report['detailed_payload_json']);

        // Fetch live sub-tables based on report code
        if (str_contains($report['report_code'], 'CENSUS')) {
            $cStmt = $db->query("SELECT * FROM patient_census ORDER BY census_date DESC LIMIT 14");
            $report['census_history'] = $cStmt->fetchAll();
        } elseif (str_contains($report['report_code'], 'LAB')) {
            $lStmt = $db->query("SELECT * FROM laboratory_utilization ORDER BY requests_count DESC");
            $report['laboratory_tests'] = $lStmt->fetchAll();
        } elseif (str_contains($report['report_code'], 'PHAR')) {
            $pStmt = $db->query("SELECT * FROM pharmacy_stocks ORDER BY CASE status WHEN 'Critical' THEN 1 WHEN 'Low Stock' THEN 2 WHEN 'Expired' THEN 3 ELSE 4 END, current_stock ASC");
            $report['pharmacy_inventory'] = $pStmt->fetchAll();
        } elseif (str_contains($report['report_code'], 'DOH')) {
            $dStmt = $db->query("SELECT * FROM doh_compliance_items ORDER BY id ASC");
            $report['compliance_matrix'] = $dStmt->fetchAll();
        } elseif (str_contains($report['report_code'], 'APP')) {
            $aStmt = $db->query("SELECT * FROM appointment_summaries ORDER BY summary_date DESC LIMIT 14");
            $report['appointment_history'] = $aStmt->fetchAll();
        }

        // Log audit event
        Database::logAudit("Viewed " . $report['report_name'], $report['report_name'], "Accessed detailed view for {$report['period_label']}");

        echo json_encode([
            'status' => 'success',
            'data' => $report
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
