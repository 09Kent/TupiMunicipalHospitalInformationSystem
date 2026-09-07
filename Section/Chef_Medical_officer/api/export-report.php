<?php
/**
 * Tupi Municipal Hospital Information Management System
 * API: Report Export Engine (Role 2)
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

use TMHIS\Database;

try {
    $db = Database::getConnection();

    $reportCode = trim($_GET['code'] ?? 'RPT-CENSUS-001');
    $format = strtolower(trim($_GET['format'] ?? 'csv'));

    // Fetch report info
    $stmt = $db->prepare("SELECT * FROM operational_reports WHERE report_code = :code OR id = :id");
    $stmt->execute([':code' => $reportCode, ':id' => (int)$reportCode]);
    $report = $stmt->fetch();

    if (!$report) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Report not found for export']);
        exit;
    }

    $reportName = $report['report_name'];
    $period = $report['period_label'];
    $summary = json_decode($report['summary_metrics_json'] ?? '{}', true);

    // Log Audit Trail
    Database::logAudit("Exported Operational Report", $reportName, "Exported as " . strtoupper($format) . " format");

    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $reportName) . '_' . date('Ymd_His');

    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $out = fopen('php://output', 'w');
        // BOM for UTF-8 in Excel
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, ['TMHIS HEALTHCARE MANAGEMENT SYSTEM']);
        fputcsv($out, ['HOSPITAL OPERATIONS EXECUTIVE REPORT']);
        fputcsv($out, ['Report Name', $reportName]);
        fputcsv($out, ['Reporting Period', $period]);
        fputcsv($out, ['Generated Date', date('F d, Y h:i A')]);
        fputcsv($out, ['Prepared For', 'Dr. Maria Santos - Hospital Chief / Medical Director']);
        fputcsv($out, []);
        fputcsv($out, ['--- EXECUTIVE SUMMARY METRICS ---']);

        foreach ($summary as $k => $v) {
            $label = ucwords(str_replace('_', ' ', (string)$k));
            fputcsv($out, [$label, is_array($v) ? json_encode($v) : $v]);
        }

        fputcsv($out, []);
        fputcsv($out, ['--- DETAILED RECORD BREAKDOWN ---']);

        if (str_contains($report['report_code'], 'CENSUS')) {
            fputcsv($out, ['Date', 'Total Patients', 'Inpatients', 'Outpatients', 'Emergency', 'Discharged', 'Occupancy Rate']);
            $rows = $db->query("SELECT census_date, total_patients, inpatients, outpatients, emergency, discharged, occupancy_rate FROM patient_census ORDER BY census_date DESC LIMIT 30")->fetchAll();
            foreach ($rows as $r) {
                fputcsv($out, [$r['census_date'], $r['total_patients'], $r['inpatients'], $r['outpatients'], $r['emergency'], $r['discharged'], $r['occupancy_rate'] . '%']);
            }
        } elseif (str_contains($report['report_code'], 'LAB')) {
            fputcsv($out, ['Test Name', 'Category', 'Total Requests', 'Completed', 'Pending', 'Utilization Rate']);
            $rows = $db->query("SELECT test_name, category, requests_count, completed_count, pending_count, utilization_rate FROM laboratory_utilization ORDER BY requests_count DESC")->fetchAll();
            foreach ($rows as $r) {
                fputcsv($out, [$r['test_name'], $r['category'], $r['requests_count'], $r['completed_count'], $r['pending_count'], $r['utilization_rate'] . '%']);
            }
        } elseif (str_contains($report['report_code'], 'PHAR')) {
            fputcsv($out, ['Medicine Name', 'Dosage', 'Current Stock', 'Min Level', 'Unit', 'Status', 'Expiry Date']);
            $rows = $db->query("SELECT medicine_name, dosage, current_stock, min_level, unit, status, expiry_date FROM pharmacy_stocks ORDER BY current_stock ASC")->fetchAll();
            foreach ($rows as $r) {
                fputcsv($out, [$r['medicine_name'], $r['dosage'], $r['current_stock'], $r['min_level'], $r['unit'], $r['status'], $r['expiry_date']]);
            }
        } elseif (str_contains($report['report_code'], 'DOH')) {
            fputcsv($out, ['Compliance Domain', 'Score', 'Status', 'Compliant Indicators', 'Total Indicators', 'Notes']);
            $rows = $db->query("SELECT domain_name, compliance_score, status, compliant_indicators, total_indicators, notes FROM doh_compliance_items")->fetchAll();
            foreach ($rows as $r) {
                fputcsv($out, [$r['domain_name'], $r['compliance_score'] . '%', $r['status'], $r['compliant_indicators'], $r['total_indicators'], $r['notes']]);
            }
        } else {
            fputcsv($out, ['Metric Category', 'Score / Status', 'Description']);
            fputcsv($out, ['General Compliance', '96%', 'Compliant with DOH Hospital Licensing Standards']);
            fputcsv($out, ['Inter-departmental Efficiency', '94%', 'Optimal throughput and patient flow']);
        }

        fputcsv($out, []);
        fputcsv($out, ['Official Hospital Executive Document - Confidential']);
        fclose($out);
        exit;
    }

    if ($format === 'excel') {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>' . htmlspecialchars($reportName) . '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
        echo '<body style="font-family: Arial, sans-serif;">';
        echo '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">';
        echo '<tr style="background-color:#0284c7; color:#ffffff; font-weight:bold; font-size:16px;"><th colspan="6" align="left">TMHIS - ' . htmlspecialchars($reportName) . '</th></tr>';
        echo '<tr><td colspan="6"><strong>Period:</strong> ' . htmlspecialchars($period) . ' | <strong>Generated:</strong> ' . date('F d, Y h:i A') . ' | <strong>Prepared For:</strong> Dr. Maria Santos (Hospital Chief)</td></tr>';
        echo '<tr><td colspan="6" style="background-color:#f1f5f9;"><strong>Executive Metrics</strong></td></tr>';
        foreach ($summary as $k => $v) {
            $label = ucwords(str_replace('_', ' ', (string)$k));
            echo '<tr><td colspan="2">' . htmlspecialchars($label) . '</td><td colspan="4"><strong>' . htmlspecialchars(is_array($v) ? json_encode($v) : (string)$v) . '</strong></td></tr>';
        }
        echo '</table>';
        echo '</body></html>';
        exit;
    }

    // Default JSON response
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'message' => 'Report exported successfully.',
        'report_name' => $reportName,
        'period' => $period,
        'generated_by' => 'Dr. Maria Santos - Hospital Chief / Medical Director',
        'generated_at' => date('F d, Y h:i A'),
        'format' => strtoupper($format)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
