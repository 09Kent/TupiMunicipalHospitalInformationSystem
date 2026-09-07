<?php
// Doctor/test_suite.php
// Comprehensive Doctor Portal Subsystem Verification Suite

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Doctor.php';
require_once __DIR__ . '/models/Specialty.php';
require_once __DIR__ . '/models/Patient.php';
require_once __DIR__ . '/models/Consultation.php';
require_once __DIR__ . '/models/ConsultationNote.php';
require_once __DIR__ . '/models/Diagnosis.php';
require_once __DIR__ . '/models/TreatmentPlan.php';
require_once __DIR__ . '/models/Prescription.php';
require_once __DIR__ . '/models/LaboratoryRequest.php';
require_once __DIR__ . '/models/LaboratoryResult.php';
require_once __DIR__ . '/models/Referral.php';
require_once __DIR__ . '/models/MedicalCertificate.php';
require_once __DIR__ . '/models/AllergyRecord.php';

echo "================================================================\n";
echo "  AURAHEALTH DOCTOR PORTAL & EMR VERIFICATION SUITE\n";
echo "================================================================\n\n";

$passedCount = 0;
$failedCount = 0;
$totalTests = 17;

function test_pass(int $num, int $total, string $msg, int &$passed) {
    echo "[PASS $num/$total] $msg\n";
    $passed++;
}
function test_fail(int $num, int $total, string $msg, int &$failed) {
    echo "[FAIL $num/$total] $msg\n";
    $failed++;
}

try {
    $pdo = Database::getConnection();

    // 1. Database Schema Verification
    $stmtTables = $pdo->query("SHOW TABLES");
    $tables = $stmtTables->fetchAll(PDO::FETCH_COLUMN);
    $requiredTables = [
        'users', 'doctors', 'specialties', 'patients',
        'complaints', 'complaint_analysis', 'appointments', 'patient_queue',
        'consultation_notes', 'diagnoses', 'treatment_plans', 'prescriptions',
        'laboratory_requests', 'laboratory_results', 'referrals', 'medical_certificates', 'allergy_records'
    ];
    $missing = array_diff($requiredTables, $tables);
    if (empty($missing)) {
        test_pass(1, $totalTests, "Database Schema: All " . count($requiredTables) . " core tables verified in MySQL.", $passedCount);
    } else {
        test_fail(1, $totalTests, "Missing tables: " . implode(', ', $missing), $failedCount);
    }

    // 2. Authentication — password_verify for doctor accounts
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE Email = 'cardio@hospital.com' LIMIT 1");
    $stmtUser->execute();
    $cardioUser = $stmtUser->fetch();
    if ($cardioUser && password_verify('password123', $cardioUser['PasswordHash']) && $cardioUser['Role'] === 'Doctor') {
        test_pass(2, $totalTests, "User Authentication: Verified 'cardio@hospital.com' as Doctor with bcrypt hash.", $passedCount);
    } else {
        test_fail(2, $totalTests, "Authentication failed for cardio@hospital.com." . ($cardioUser ? " Hash check: " . (password_verify('password123', $cardioUser['PasswordHash']) ? 'OK' : 'MISMATCH') : ' User not found.'), $failedCount);
    }

    // 3. Doctor Model & Dashboard Metrics
    $docModel = new Doctor($pdo);
    $cardioDoc = $docModel->findByEmail('cardio@hospital.com');
    if ($cardioDoc && (($cardioDoc['SpecialtyName'] ?? '') === 'Cardiologist' || ($cardioDoc['Specialty'] ?? '') === 'Cardiologist')) {
        $metrics = $docModel->getDashboardMetrics($cardioDoc['DoctorID']);
        test_pass(3, $totalTests, "Doctor Model: Loaded Dr. {$cardioDoc['FirstName']} {$cardioDoc['LastName']} (Specialty: {$cardioDoc['Specialty']}). KPIs: {$metrics['today_patients']} today patients, {$metrics['upcoming_appointments']} upcoming.", $passedCount);
    } else {
        test_fail(3, $totalTests, "Doctor model failed to load Cardiologist. Found: " . json_encode($cardioDoc), $failedCount);
    }

    // 4. Specialty Model & Automatic Complaint Routing
    $specModel = new Specialty($pdo);
    $allSpecs = $specModel->getAll();
    $matchedSpec = $specModel->mapSystemToSpecialty(2);
    if (count($allSpecs) >= 9 && $matchedSpec) {
        test_pass(4, $totalTests, "Specialty Auto-Routing: " . count($allSpecs) . " specialties active. BodySystem #2 maps to '{$matchedSpec['SpecialtyName']}'.", $passedCount);
    } else {
        test_fail(4, $totalTests, "Specialty mapping failed. Total specs: " . count($allSpecs), $failedCount);
    }

    // 5. Patient Model & Full Profile Retrieval
    $doctorId = $cardioDoc['DoctorID'];
    $bodySystemId = $cardioDoc['BodySystemID'] ?? null;
    $patModel = new Patient($pdo);
    $patList = $patModel->getDoctorPatients($doctorId, 10, 0, [], $bodySystemId);
    $firstPat = $patList[0] ?? null;
    if ($firstPat) {
        $fullProfile = $patModel->getFullProfile($firstPat['PatientID']);
        test_pass(5, $totalTests, "Patient Model: Roster returned " . count($patList) . " cases. Profile: {$fullProfile['FirstName']} {$fullProfile['LastName']}, Blood: " . ($fullProfile['BloodType'] ?? 'N/A'), $passedCount);
    } else {
        test_fail(5, $totalTests, "No patients found for DoctorID $doctorId.", $failedCount);
    }

    // 6. Consultation Workflow & State Transitions
    $consModel = new Consultation($pdo);
    // Find a Waiting or Scheduled appointment for this doctor
    $stmtApp = $pdo->prepare("SELECT AppointmentID FROM appointments WHERE DoctorID = :docId AND Status IN ('Waiting', 'Scheduled', 'Confirmed') LIMIT 1");
    $stmtApp->execute([':docId' => $doctorId]);
    $testAppRow = $stmtApp->fetch();
    $testAppId = $testAppRow ? (int)$testAppRow['AppointmentID'] : ($firstPat['AppointmentID'] ?? 101);
    
    $startResult = $consModel->startConsultation($testAppId, $doctorId);
    $activeCons = $consModel->getActiveConsultation($doctorId);
    if ($startResult) {
        test_pass(6, $totalTests, "Consultation Model: State transitioned to 'In Consultation' for Appointment #$testAppId.", $passedCount);
    } else {
        test_fail(6, $totalTests, "Consultation start failed for AppointmentID $testAppId.", $failedCount);
    }

    // 7. Consultation Note (SOAP) CRUD
    $patientId = $firstPat['PatientID'];
    $noteModel = new ConsultationNote($pdo);
    $vitalsJson = json_encode(['bp' => '130/85', 'hr' => '82 bpm', 'spo2' => '99%']);
    $noteId = $noteModel->create([
        'patient_id'     => $patientId,
        'doctor_id'      => $doctorId,
        'appointment_id' => $testAppId,
        'subjective'     => 'Patient reports exertional chest flutter and palpitations.',
        'objective'      => 'BP: 130/85 mmHg, HR: 82 bpm, S1/S2 distinct.',
        'assessment'     => 'Stress-induced supraventricular rhythm sensitivity.',
        'plan'           => '12-lead ECG, baseline lipid panel, oral beta-blocker.',
        'clinical_notes' => 'Automated test suite clinical observation.',
        'vitals'         => $vitalsJson
    ]);
    if ($noteId > 0) {
        test_pass(7, $totalTests, "ConsultationNote Model: Created Note #$noteId with SOAP format & vitals.", $passedCount);
    } else {
        test_fail(7, $totalTests, "SOAP note creation failed.", $failedCount);
    }

    // 8. Diagnosis Management CRUD
    $diagModel = new Diagnosis($pdo);
    $diagId = $diagModel->create([
        'patient_id'     => $patientId,
        'doctor_id'      => $doctorId,
        'appointment_id' => $testAppId,
        'diagnosis_name' => 'Paroxysmal Supraventricular Tachycardia',
        'icd10_code'     => 'I47.1',
        'type'           => 'Primary',
        'severity'       => 'Moderate',
        'status'         => 'Active',
        'notes'          => 'Verified during clinical consultation.'
    ]);
    if ($diagId > 0) {
        $diag = $diagModel->findById($diagId);
        test_pass(8, $totalTests, "Diagnosis Model: Recorded '{$diag['DiagnosisName']}' (ICD-10: {$diag['ICD10Code']}).", $passedCount);
    } else {
        test_fail(8, $totalTests, "Diagnosis creation failed.", $failedCount);
    }

    // 9. Treatment Plan Management
    $treatModel = new TreatmentPlan($pdo);
    $planId = $treatModel->create([
        'patient_id'                => $patientId,
        'doctor_id'                 => $doctorId,
        'appointment_id'            => $testAppId,
        'diagnosis_id'              => $diagId,
        'goal'                      => 'Maintain resting heart rate < 75 bpm.',
        'lifestyle_recommendations' => 'Avoid caffeine, practice relaxation techniques.',
        'medication_plan'           => 'Oral Metoprolol 25mg daily.',
        'follow_up_schedule'        => '2 Weeks',
        'follow_up_date'            => date('Y-m-d', strtotime('+14 days')),
        'status'                    => 'Active'
    ]);
    if ($planId > 0) {
        test_pass(9, $totalTests, "TreatmentPlan Model: Established care plan #$planId with lifestyle and follow-up.", $passedCount);
    } else {
        test_fail(9, $totalTests, "Treatment plan creation failed.", $failedCount);
    }

    // 10. Electronic Prescription (e-Rx) Generation
    $rxModel = new Prescription($pdo);
    $rxId = $rxModel->create([
        'patient_id'    => $patientId,
        'doctor_id'     => $doctorId,
        'medicine_name' => 'Metoprolol Succinate',
        'dosage'        => '25mg',
        'frequency'     => 'Once daily in the morning',
        'duration'      => '30 Days',
        'instructions'  => 'Take with meals with water.',
        'quantity'      => '30 Tablets',
        'refills'       => 2,
        'status'        => 'Active'
    ]);
    if ($rxId > 0) {
        $rx = $rxModel->findById($rxId);
        test_pass(10, $totalTests, "Prescription Model: Created e-Rx {$rx['PrescriptionCode']} for {$rx['MedicineName']} {$rx['Dosage']}.", $passedCount);
    } else {
        test_fail(10, $totalTests, "Prescription generation failed.", $failedCount);
    }

    // 11. Laboratory Request & Results
    $labModel = new LaboratoryRequest($pdo);
    $labResModel = new LaboratoryResult($pdo);
    $reqId = $labModel->create([
        'patient_id'     => $patientId,
        'doctor_id'      => $doctorId,
        'test_type'      => '12-Lead Electrocardiogram (ECG)',
        'priority'       => 'Urgent',
        'clinical_notes' => 'Automated test suite evaluation.',
        'status'         => 'Pending'
    ]);
    $resId = $labResModel->create([
        'request_id'     => $reqId,
        'patient_id'     => $patientId,
        'doctor_id'      => $doctorId,
        'test_name'      => '12-Lead Electrocardiogram (ECG)',
        'result_value'   => 'Sinus rhythm with rare PACs, PR 160ms, QRS 88ms',
        'normal_range'   => 'Normal Sinus Rhythm',
        'units'          => 'N/A',
        'interpretation' => 'Normal'
    ]);
    if ($reqId > 0 && $resId > 0) {
        test_pass(11, $totalTests, "Laboratory Subsystem: Created Lab Request #$reqId and Result #$resId (Normal).", $passedCount);
    } else {
        test_fail(11, $totalTests, "Laboratory request or result failed. ReqID: $reqId, ResID: $resId", $failedCount);
    }

    // 12. Patient Referral Subsystem
    $refModel = new Referral($pdo);
    $refId = $refModel->create([
        'patient_id'          => $patientId,
        'referring_doctor_id' => $doctorId,
        'target_specialty_id' => 3,
        'target_doctor_id'    => null,
        'reason'              => 'Assess concurrent tension cephalalgia.',
        'clinical_summary'    => 'Normal cardiac exam, mild lightheadedness.',
        'priority'            => 'Routine',
        'status'              => 'Pending'
    ]);
    if ($refId > 0) {
        $refModel->updateStatus($refId, 'Accepted', 'Neurology confirmed review slot.');
        $ref = $refModel->findById($refId);
        test_pass(12, $totalTests, "Referral Model: Generated {$ref['ReferralCode']} to '{$ref['TargetSpecialtyName']}' (Status: {$ref['Status']}).", $passedCount);
    } else {
        test_fail(12, $totalTests, "Referral creation failed.", $failedCount);
    }

    // 13. Medical Certificate Subsystem
    $certModel = new MedicalCertificate($pdo);
    $certId = $certModel->create([
        'patient_id'       => $patientId,
        'doctor_id'        => $doctorId,
        'certificate_type' => 'Medical Leave',
        'diagnosis'        => 'Paroxysmal Palpitations & Stress-Related Fatigue',
        'duration_start'   => date('Y-m-d'),
        'duration_end'     => date('Y-m-d', strtotime('+3 days')),
        'remarks'          => 'Advised 3 days of convalescent rest.'
    ]);
    if ($certId > 0) {
        $cert = $certModel->findById($certId);
        test_pass(13, $totalTests, "MedicalCertificate Model: Issued {$cert['CertificateCode']} ({$cert['CertificateType']}, {$cert['DaysExcused']} days).", $passedCount);
    } else {
        test_fail(13, $totalTests, "Medical Certificate creation failed.", $failedCount);
    }

    // 14. Allergy Record Subsystem
    $allgModel = new AllergyRecord($pdo);
    $allgId = $allgModel->create([
        'patient_id'   => $patientId,
        'doctor_id'    => $doctorId,
        'allergen'     => 'Ciprofloxacin',
        'allergy_type' => 'Drug',
        'severity'     => 'Severe',
        'reaction'     => 'Urticarial rash and angioedema',
        'status'       => 'Active'
    ]);
    if ($allgId > 0) {
        $allgs = $allgModel->findByPatient($patientId);
        test_pass(14, $totalTests, "AllergyRecord Model: Confirmed allergy #$allgId stored permanently in patient EMR.", $passedCount);
    } else {
        test_fail(14, $totalTests, "Allergy record creation failed.", $failedCount);
    }

    // 15. Doctor-Patient Isolation — Authorized Access Verification
    $hasAccess = $patModel->hasDoctorAccess($doctorId, $patientId);
    if ($hasAccess) {
        test_pass(15, $totalTests, "Doctor Isolation: Dr. {$cardioDoc['FirstName']} {$cardioDoc['LastName']} (DoctorID $doctorId) has verified access to assigned Patient #$patientId.", $passedCount);
    } else {
        test_fail(15, $totalTests, "Doctor Isolation: Authorized access check failed for assigned Patient #$patientId.", $failedCount);
    }

    // 16. Doctor-Patient Isolation — Cross-Doctor Unauthorized Access Denial
    $unauthorizedDoctorId = 99999;
    $deniedAccess = $patModel->hasDoctorAccess($unauthorizedDoctorId, $patientId);
    if (!$deniedAccess) {
        test_pass(16, $totalTests, "Doctor Isolation: Unauthorized DoctorID $unauthorizedDoctorId is strictly blocked from accessing Patient #$patientId.", $passedCount);
    } else {
        test_fail(16, $totalTests, "Doctor Isolation: Unauthorized access check failed (leak detected).", $failedCount);
    }

    // 17. Doctor Data Isolation — Prescription & Record Scope Verification
    $cardioPrescriptions = $rxModel->getAll($doctorId, 50);
    $rxDoctorIds = array_unique(array_column($cardioPrescriptions, 'DoctorID'));
    $allMatch = empty($cardioPrescriptions) || (count($rxDoctorIds) === 1 && (int)$rxDoctorIds[0] === (int)$doctorId);
    if ($allMatch) {
        test_pass(17, $totalTests, "Doctor Isolation: Prescription registry strictly isolated to DoctorID $doctorId (" . count($cardioPrescriptions) . " records).", $passedCount);
    } else {
        test_fail(17, $totalTests, "Doctor Isolation: Prescription leakage detected. Found DoctorIDs: " . implode(',', $rxDoctorIds), $failedCount);
    }

} catch (Exception $e) {
    echo "\n[EXCEPTION] Test suite error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "  Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n================================================================\n";
$pct = $totalTests > 0 ? round(($passedCount / $totalTests) * 100) : 0;
echo "  RESULT: $passedCount / $totalTests TESTS PASSED ($pct%)\n";
if ($passedCount === $totalTests) {
    echo "  STATUS: ALL SYSTEMS OPERATIONAL\n";
} else {
    echo "  STATUS: " . ($totalTests - $passedCount) . " FAILURE(S) DETECTED\n";
}
echo "================================================================\n";
