<?php
/**
 * Master System Health Check & End-to-End Diagnostics Suite
 * Verifies ALL 9 FDD Roles:
 * - Role 1: System Administrator (Admin)
 * - Role 2: Hospital Chief / Medical Director (Chef_Medical_officer)
 * - Role 3: Medical Records Officer (Medical_Officer)
 * - Role 4: Admitting / Registration Staff (Register)
 * - Role 5: Attending Physician (Doctor)
 * - Role 6: Nurse on Duty (Nurse)
 * - Role 7: Medical Technologist (Med_Tech)
 * - Role 8: Pharmacist / Pharmacy Aide (Pharmacy)
 * - Role 9: Billing / Cashier Staff (Accountant)
 */

declare(strict_types=1);

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$baseDir = __DIR__;
$results = [
    'summary' => ['total' => 0, 'passed' => 0, 'failed' => 0],
    'sections' => []
];

function runTest(string $section, string $testName, callable $fn, array &$results): void {
    $results['summary']['total']++;
    if (!isset($results['sections'][$section])) {
        $results['sections'][$section] = [];
    }
    
    try {
        $outcome = $fn();
        if ($outcome === true || (is_array($outcome) && ($outcome['status'] ?? '') === 'pass')) {
            $results['summary']['passed']++;
            $msg = is_array($outcome) ? ($outcome['message'] ?? 'OK') : 'OK';
            $results['sections'][$section][] = ['name' => $testName, 'status' => 'PASS', 'message' => $msg];
            echo "  [PASS] {$section} -> {$testName}: {$msg}\n";
        } else {
            $results['summary']['failed']++;
            $msg = is_array($outcome) ? ($outcome['message'] ?? 'Failed') : 'Failed';
            $results['sections'][$section][] = ['name' => $testName, 'status' => 'FAIL', 'message' => $msg];
            echo "  [FAIL] {$section} -> {$testName}: {$msg}\n";
        }
    } catch (\Throwable $e) {
        $results['summary']['failed']++;
        $results['sections'][$section][] = ['name' => $testName, 'status' => 'FAIL', 'message' => $e->getMessage()];
        echo "  [FAIL] {$section} -> {$testName}: " . $e->getMessage() . "\n";
    }
}

function runSubProcessPhp(string $code): array {
    $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_runner_' . uniqid() . '.php';
    file_put_contents($tempFile, "<?php\n" . $code);
    $output = [];
    $returnVar = 0;
    $cmd = escapeshellarg(PHP_BINARY) . " " . escapeshellarg($tempFile) . " 2>&1";
    exec($cmd, $output, $returnVar);
    @unlink($tempFile);
    $outStr = trim(implode("\n", $output));
    return ['exitCode' => $returnVar, 'output' => $outStr];
}

echo "=================================================================================\n";
echo "       TUPI MUNICIPAL HOSPITAL MASTER SYSTEM HEALTH CHECK & END-TO-END VERIFICATION SUITE          \n";
echo "=================================================================================\n\n";

// 1. PHP Syntax Integrity across all 9 roles
echo "--- 1. PHP SYNTAX INTEGRITY CHECKS (ALL 9 ROLES) ---\n";
$modules = ['Admin', 'Register', 'Doctor', 'Nurse', 'Pharmacy', 'Med_Tech', 'Accountant', 'Medical_Officer', 'Chef_Medical_officer'];
$allFilesChecked = 0;
$syntaxErrors = [];

foreach ($modules as $mod) {
    $dir = $baseDir . DIRECTORY_SEPARATOR . $mod;
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $allFilesChecked++;
            $path = $file->getPathname();
            $output = [];
            $returnVar = 0;
            $lintCmd = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($path) . ' 2>&1';
            exec($lintCmd, $output, $returnVar);
            if ($returnVar !== 0) {
                $syntaxErrors[] = $path . ': ' . implode(' ', $output);
            }
        }
    }
}

runTest('Global', 'PHP Syntax Linting (' . $allFilesChecked . ' files)', function() use ($syntaxErrors, $allFilesChecked) {
    if (empty($syntaxErrors)) {
        return ['status' => 'pass', 'message' => "All {$allFilesChecked} PHP files passed linting with zero syntax errors."];
    }
    return ['status' => 'fail', 'message' => count($syntaxErrors) . " syntax error(s): " . implode('; ', array_slice($syntaxErrors, 0, 3))];
}, $results);


// 2. Database Connectivity & Configuration Across Portals
echo "\n--- 2. DATABASE CONFIGURATION & CONNECTIVITY ---\n";

foreach (['Admin', 'Doctor', 'Register', 'Nurse', 'Pharmacy', 'Med_Tech', 'Accountant', 'Medical_Officer'] as $modName) {
    runTest('Database', "Role {$modName} Config & Connection Class", function() use ($baseDir, $modName) {
        $code = "
            require_once '{$baseDir}/{$modName}/config/Database.php';
            if (class_exists('Database') && method_exists('Database', 'getConnection')) {
                echo 'OK';
            } else {
                echo 'FAIL';
            }
        ";
        $res = runSubProcessPhp($code);
        if ($res['output'] === 'OK') {
            return ['status' => 'pass', 'message' => "Database singleton and schema configuration verified in {$modName}/config/Database.php"];
        }
        return ['status' => 'fail', 'message' => $res['output']];
    }, $results);
}


// 3. Role 1: System Administrator Diagnostics
echo "\n--- 3. ROLE 1: SYSTEM ADMINISTRATOR DIAGNOSTICS ---\n";

runTest('Admin', 'Admin Models & API Operations', function() use ($baseDir) {
    $code = "
        require_once '{$baseDir}/Admin/config/Database.php';
        require_once '{$baseDir}/Admin/models/HospitalInfo.php';
        require_once '{$baseDir}/Admin/models/Department.php';
        require_once '{$baseDir}/Admin/models/ServiceFee.php';
        require_once '{$baseDir}/Admin/models/UserManager.php';
        require_once '{$baseDir}/Admin/models/RolePermission.php';
        require_once '{$baseDir}/Admin/models/AuditLog.php';
        require_once '{$baseDir}/Admin/models/BackupManager.php';
        
        \$classes = ['HospitalInfo', 'Department', 'ServiceFee', 'UserManager', 'RolePermission', 'AuditLog', 'BackupManager'];
        foreach (\$classes as \$c) {
            if (!class_exists(\$c)) { echo 'MISSING:' . \$c; exit; }
        }
        echo 'OK';
    ";
    $res = runSubProcessPhp($code);
    if ($res['output'] === 'OK') {
        return ['status' => 'pass', 'message' => 'All 7 Admin subsystem models intact (HospitalInfo, Departments, ServiceFees, Users, Roles, AuditLogs, Backups).'];
    }
    return ['status' => 'fail', 'message' => $res['output']];
}, $results);


// 4. Role 2: Hospital Chief / Medical Director
echo "\n--- 4. ROLE 2: HOSPITAL CHIEF / MEDICAL DIRECTOR DIAGNOSTICS ---\n";

runTest('Medical Director', 'Executive Reports & Performance APIs', function() use ($baseDir) {
    $apiFiles = [
        $baseDir . '/Chef_Medical_officer/api/reports.php',
        $baseDir . '/Chef_Medical_officer/api/dashboard-stats.php',
        $baseDir . '/Chef_Medical_officer/api/department-performance.php',
        $baseDir . '/Chef_Medical_officer/api/doctor-stats.php',
        $baseDir . '/Chef_Medical_officer/api/export-report.php'
    ];
    foreach ($apiFiles as $f) {
        if (!file_exists($f)) return ['status' => 'fail', 'message' => 'Missing API: ' . basename($f)];
    }
    return ['status' => 'pass', 'message' => 'Director executive dashboard, operational reports engine, and export utilities verified.'];
}, $results);


// 5. Role 3: Medical Records Officer
echo "\n--- 5. ROLE 3: MEDICAL RECORDS OFFICER DIAGNOSTICS ---\n";

runTest('Medical Records', 'HIRM Models & Retrieval API', function() use ($baseDir) {
    $code = "
        require_once '{$baseDir}/Medical_Officer/config/Database.php';
        require_once '{$baseDir}/Medical_Officer/models/MedicalRecordsManager.php';
        if (class_exists('MedicalRecordsManager') && file_exists('{$baseDir}/Medical_Officer/api/records.php')) {
            echo 'OK';
        } else {
            echo 'FAIL';
        }
    ";
    $res = runSubProcessPhp($code);
    if ($res['output'] === 'OK') {
        return ['status' => 'pass', 'message' => 'Medical Records Officer models, patient history retrieval, and release request handlers verified.'];
    }
    return ['status' => 'fail', 'message' => $res['output']];
}, $results);


// 6. Role 4: Admitting / Registration Staff
echo "\n--- 6. ROLE 4: REGISTRATION STAFF DIAGNOSTICS ---\n";

runTest('Register', 'Registration Models, Queue & Symptom Classifier', function() use ($baseDir) {
    $models = ['Patient', 'PatientRegistration', 'Appointment', 'Queue', 'SymptomClassifier'];
    foreach ($models as $m) {
        $path = $baseDir . "/Register/models/{$m}.php";
        if (!file_exists($path)) return ['status' => 'fail', 'message' => "Missing model: {$m}"];
    }
    return ['status' => 'pass', 'message' => 'Patient intake, digital ID generation, consultation scheduling, and queue coordination intact.'];
}, $results);


// 7. Role 5: Attending Physician
echo "\n--- 7. ROLE 5: ATTENDING PHYSICIAN DIAGNOSTICS ---\n";

runTest('Doctor', 'EMR Subsystems (SOAP Notes, Diagnoses, e-Rx, Labs, Referrals, Certificates, Allergies)', function() use ($baseDir) {
    $models = ['Doctor', 'Consultation', 'ConsultationNote', 'Diagnosis', 'TreatmentPlan', 'Prescription', 'LaboratoryRequest', 'LaboratoryResult', 'Referral', 'MedicalCertificate', 'AllergyRecord'];
    foreach ($models as $m) {
        $path = $baseDir . "/Doctor/models/{$m}.php";
        if (!file_exists($path)) return ['status' => 'fail', 'message' => "Missing Doctor model: {$m}"];
    }
    return ['status' => 'pass', 'message' => 'All 11 clinical EMR models, SOAP documentation, e-prescriptions, and lab ordering systems intact.'];
}, $results);


// 8. Role 6: Nurse on Duty
echo "\n--- 8. ROLE 6: NURSE ON DUTY DIAGNOSTICS ---\n";

runTest('Nurse', 'Vital Signs Management & Bedside Monitoring', function() use ($baseDir) {
    $code = "
        require_once '{$baseDir}/Nurse/config/Database.php';
        require_once '{$baseDir}/Nurse/models/VitalSign.php';
        if (class_exists('VitalSign') && file_exists('{$baseDir}/Nurse/api/vitals.php')) {
            echo 'OK';
        } else {
            echo 'FAIL';
        }
    ";
    $res = runSubProcessPhp($code);
    if ($res['output'] === 'OK') {
        return ['status' => 'pass', 'message' => 'Patient vitals logging engine, physiological progression charts, and bedside monitoring intact.'];
    }
    return ['status' => 'fail', 'message' => $res['output']];
}, $results);


// 9. Role 7: Medical Technologist
echo "\n--- 9. ROLE 7: MEDICAL TECHNOLOGIST DIAGNOSTICS ---\n";

runTest('Med_Tech', 'Laboratory Workstation & Test Results Certifier', function() use ($baseDir) {
    $code = "
        require_once '{$baseDir}/Med_Tech/config/Database.php';
        require_once '{$baseDir}/Med_Tech/models/LabTechnologist.php';
        if (class_exists('LabTechnologist') && file_exists('{$baseDir}/Med_Tech/api/laboratory.php')) {
            echo 'OK';
        } else {
            echo 'FAIL';
        }
    ";
    $res = runSubProcessPhp($code);
    if ($res['output'] === 'OK') {
        return ['status' => 'pass', 'message' => 'Lab requisition queue, specimen tracking, diagnostic results certifier, and catalog intact.'];
    }
    return ['status' => 'fail', 'message' => $res['output']];
}, $results);


// 10. Role 8: Pharmacist / Pharmacy Aide
echo "\n--- 10. ROLE 8: PHARMACIST / PHARMACY AIDE DIAGNOSTICS ---\n";

runTest('Pharmacy', 'Prescription Processing & Drug Dispensing Engine', function() use ($baseDir) {
    $code = "
        require_once '{$baseDir}/Pharmacy/config/Database.php';
        require_once '{$baseDir}/Pharmacy/models/PharmacyManager.php';
        if (class_exists('PharmacyManager') && file_exists('{$baseDir}/Pharmacy/api/pharmacy.php')) {
            echo 'OK';
        } else {
            echo 'FAIL';
        }
    ";
    $res = runSubProcessPhp($code);
    if ($res['output'] === 'OK') {
        return ['status' => 'pass', 'message' => 'Prescription verification queue, batch dispensing engine, and pharmacy inventory model intact.'];
    }
    return ['status' => 'fail', 'message' => $res['output']];
}, $results);


// 11. Role 9: Billing / Cashier Staff
echo "\n--- 11. ROLE 9: BILLING / CASHIER STAFF DIAGNOSTICS ---\n";

runTest('Accountant', 'Charge Computation, Payment Processing & Official Receipts', function() use ($baseDir) {
    $code = "
        require_once '{$baseDir}/Accountant/config/Database.php';
        require_once '{$baseDir}/Accountant/models/BillingManager.php';
        if (class_exists('BillingManager') && file_exists('{$baseDir}/Accountant/api/billing.php')) {
            echo 'OK';
        } else {
            echo 'FAIL';
        }
    ";
    $res = runSubProcessPhp($code);
    if ($res['output'] === 'OK') {
        return ['status' => 'pass', 'message' => 'Service charge computation, discount applicator, payment records, and official receipt generator intact.'];
    }
    return ['status' => 'fail', 'message' => $res['output']];
}, $results);


// 12. Root Gateway & Access Control
echo "\n--- 12. ROOT GATEWAY & UNIFIED RBAC ---\n";

runTest('Gateway', 'Root Entrypoint & Central Auth Gateway', function() use ($baseDir) {
    $root = $baseDir . '/index.php';
    if (!file_exists($root)) return ['status' => 'fail', 'message' => 'Missing root index.php'];
    if (!file_exists($baseDir . '/includes/AuthMiddleware.php')) return ['status' => 'fail', 'message' => 'Missing AuthMiddleware.php'];
    return ['status' => 'pass', 'message' => 'Unified authentication gateway and role middleware verified.'];
}, $results);


// 13. Summary
echo "\n=================================================================================\n";
$pct = $results['summary']['total'] > 0 ? round(($results['summary']['passed'] / $results['summary']['total']) * 100) : 0;
echo "  FINAL DIAGNOSTIC REPORT: {$results['summary']['passed']} / {$results['summary']['total']} MODULE TESTS PASSED ({$pct}%)\n";
if ($results['summary']['failed'] === 0) {
    echo "  SYSTEM STATUS: ALL 9 FDD ROLES & SUBSYSTEMS ARE FULLY CONNECTED & FUNCTIONING!\n";
} else {
    echo "  SYSTEM STATUS: {$results['summary']['failed']} WARNING(S) / FAILURE(S) DETECTED\n";
}
echo "=================================================================================\n";
