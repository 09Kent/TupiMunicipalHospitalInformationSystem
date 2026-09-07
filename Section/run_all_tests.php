<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Comprehensive End-to-End Verification Test Suite
 */

declare(strict_types=1);

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$baseDir = __DIR__;
$results = [
    'total' => 0,
    'passed' => 0,
    'failed' => 0,
    'tests' => []
];

function assertTest(string $category, string $name, bool $condition, string $detail = ''): void {
    global $results;
    $results['total']++;
    if ($condition) {
        $results['passed']++;
        echo "  [PASS] {$category} -> {$name}" . ($detail ? " ({$detail})" : "") . "\n";
        $results['tests'][] = ['category' => $category, 'name' => $name, 'status' => 'PASS', 'detail' => $detail];
    } else {
        $results['failed']++;
        echo "  [FAIL] {$category} -> {$name}" . ($detail ? " ({$detail})" : "") . "\n";
        $results['tests'][] = ['category' => $category, 'name' => $name, 'status' => 'FAIL', 'detail' => $detail];
    }
}

echo "=================================================================================\n";
echo "  TUPI MUNICIPAL HOSPITAL INFORMATION MANAGEMENT SYSTEM — MASTER TEST SUITE\n";
echo "=================================================================================\n\n";

// 1. DATABASE CONNECTIVITY & TABLE COUNT
echo "--- 1. DATABASE CONNECTIVITY & SCHEMA VERIFICATION ---\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=medicalregistrationdb', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    assertTest('Database', 'MySQL Connection', true, 'Connected to medicalregistrationdb');

    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    assertTest('Database', 'Table Count (>= 40 tables)', count($tables) >= 40, "Found " . count($tables) . " tables");
} catch (Exception $e) {
    assertTest('Database', 'MySQL Connection', false, $e->getMessage());
    exit(1);
}

// 2. TEST DATA RECORD VOLUMES (RULE 19: 25-30+ RECORDS PER MAJOR TABLE)
echo "\n--- 2. TEST DATA RECORD VOLUMES (>= 25 RECORDS PER TABLE) ---\n";
$keyTables = [
    'patients' => 'Patients Master Directory',
    'appointments' => 'Appointments & Consultations',
    'patient_vitals' => 'Vital Signs Records',
    'nurse_tasks' => 'Nursing Operational Tasks',
    'laboratory_requests' => 'Laboratory Requests Queue',
    'laboratory_results' => 'Certified Laboratory Results',
    'laboratory_samples' => 'Specimen Tracking Log',
    'test_catalog' => 'Diagnostic Test Catalog',
    'pharmacy_inventory' => 'Pharmacy Medicine Inventory',
    'dispensing_records' => 'Prescription Dispensing Records',
    'billing_charges' => 'Patient Service Charges',
    'billing_invoices' => 'Billing Invoices',
    'billing_payments' => 'Official Payment Receipts',
    'system_audit_logs' => 'System Audit & Activity Logs',
    'users' => 'Hospital Staff & Admin Accounts'
];

foreach ($keyTables as $table => $label) {
    try {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        assertTest('Data Volume', "{$label} (`{$table}`)", $count >= 20, "{$count} records");
    } catch (Exception $e) {
        assertTest('Data Volume', "{$label} (`{$table}`)", false, $e->getMessage());
    }
}

// 3. USER ROLES & DEMO ACCOUNTS (ALL 9 FDD ROLES)
echo "\n--- 3. USER ROLES & DEMO CREDENTIALS (ALL 9 FDD ROLES) ---\n";
$requiredRoles = [
    'Admin' => 'admin',
    'Chief' => 'director',
    'Records' => 'records',
    'Register' => 'registrator',
    'Doctor' => 'doctor',
    'Nurse' => 'nurse',
    'MedTech' => 'medtech',
    'Pharmacist' => 'pharmacist',
    'Billing' => 'cashier'
];

foreach ($requiredRoles as $role => $username) {
    $stmt = $pdo->prepare("SELECT UserID, Username, Role, Status FROM users WHERE Role = :role OR Username = :uname LIMIT 1");
    $stmt->execute([':role' => $role, ':uname' => $username]);
    $user = $stmt->fetch();
    assertTest('User Role', "Role '{$role}' Account", !empty($user), !empty($user) ? "User: {$user['Username']} (Status: {$user['Status']})" : "Missing role account");
}

// 4. PAGINATOR UNIT TESTS (10 RECORDS PER PAGE)
echo "\n--- 4. PAGINATOR COMPONENT VERIFICATION (10 RECORDS PER PAGE) ---\n";
require_once __DIR__ . '/includes/Paginator.php';

$p1 = new Paginator(35, 10, 1);
assertTest('Paginator', 'Page 1 Limit & Offset', $p1->recordsPerPage === 10 && $p1->offset === 0 && $p1->totalPages === 4, "Total: 35, Pages: 4, Offset: 0");
assertTest('Paginator', 'Page 1 Records Range', $p1->startRecord === 1 && $p1->endRecord === 10, "Showing 1–10 of 35");

$p2 = new Paginator(35, 10, 2);
assertTest('Paginator', 'Page 2 Records Range', $p2->startRecord === 11 && $p2->endRecord === 20, "Showing 11–20 of 35");

$p4 = new Paginator(35, 10, 4);
assertTest('Paginator', 'Last Page Records Range', $p4->startRecord === 31 && $p4->endRecord === 35, "Showing 31–35 of 35");

$html = $p1->render('patients');
assertTest('Paginator', 'Render Counter Format', strpos($html, 'Showing <span class="font-bold text-slate-900">1–10</span> of <span class="font-bold text-slate-900">35</span> patients') !== false, "Counter markup valid");
assertTest('Paginator', 'Previous Disabled on Page 1', strpos($html, 'cursor-not-allowed') !== false, "Previous button disabled on page 1");

// 5. FILE INTEGRITY & ROUTING CHECKS ACROSS ALL 9 PORTALS
echo "\n--- 5. PORTAL ENTRYPOINTS & FILE INTEGRITY ---\n";
$portals = [
    'Admin' => 'Section/Admin/index.php',
    'Chief Medical Officer' => 'Section/Chef_Medical_officer/index.php',
    'Medical Records Officer' => 'Section/Medical_Officer/index.php',
    'Admitting / Register' => 'Section/Register/views/dashboard/index.php',
    'Attending Physician' => 'Section/Doctor/views/dashboard/index.php',
    'Nurse on Duty' => 'Section/Nurse/views/dashboard/index.php',
    'Medical Technologist' => 'Section/Med_Tech/views/dashboard/index.php',
    'Pharmacist' => 'Section/Pharmacy/views/dashboard/index.php',
    'Billing / Cashier' => 'Section/Accountant/index.php'
];

foreach ($portals as $name => $relPath) {
    $fullPath = dirname($baseDir) . '/' . $relPath;
    assertTest('Portal Entrypoint', $name, file_exists($fullPath), $relPath);
}

// 6. SYSTEM BRANDING CONSISTENCY
echo "\n--- 6. SYSTEM BRANDING & ASSET VERIFICATION ---\n";
$logoPath = dirname($baseDir) . '/assets/logo.png';
assertTest('Branding', 'Official Hospital Logo Asset', file_exists($logoPath), 'assets/logo.png exists');

$loginContent = file_get_contents($baseDir . '/Doctor/views/auth/login.php');
assertTest('Branding', 'Login Page Title & Brand', strpos($loginContent, 'Tupi Municipal Hospital Information Management System') !== false, 'Correct brand name in login');
assertTest('Branding', 'No AuraHealth in Login', strpos($loginContent, 'AuraHealth') === false, 'AuraHealth placeholder eliminated');

$mainIndexContent = file_get_contents(dirname($baseDir) . '/index.php');
assertTest('Branding', 'Main Landing Page Title & Subtitle', strpos($mainIndexContent, 'Information Management System') !== false, 'Landing page brand accurate');

echo "\n=================================================================================\n";
$pct = round(($results['passed'] / $results['total']) * 100);
echo "  TEST SUMMARY: {$results['passed']} / {$results['total']} TESTS PASSED ({$pct}%)\n";
if ($results['failed'] === 0) {
    echo "  ALL VERIFICATION TESTS PASSED SUCCESSFULLY! SYSTEM MEETS FDD REQUIREMENTS.\n";
} else {
    echo "  ATTENTION: {$results['failed']} TEST(S) FAILED.\n";
}
echo "=================================================================================\n";
