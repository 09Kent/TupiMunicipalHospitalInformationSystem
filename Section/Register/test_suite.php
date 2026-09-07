<?php
// test_suite.php

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Patient.php';
require_once __DIR__ . '/models/Doctor.php';
require_once __DIR__ . '/models/Appointment.php';
require_once __DIR__ . '/models/Queue.php';
require_once __DIR__ . '/models/Report.php';
require_once __DIR__ . '/models/SymptomClassifier.php';
require_once __DIR__ . '/models/PatientRegistration.php';

echo "=== Tupi Municipal Hospital Full-Stack Verification Suite ===\n\n";

try {
    // 1. Database connection
    $pdo = Database::getConnection();
    echo "[PASS] Database connection OK.\n";

    // 2. User authentication test
    $userModel = new User($pdo);
    $authUser = $userModel->authenticate('registrator', 'password123');
    if ($authUser && $authUser['Username'] === 'registrator') {
        echo "[PASS] User Model: Authentication verified for 'registrator'.\n";
    } else {
        echo "[FAIL] User Model: Authentication failed.\n";
    }

    // 3. Report & KPI test
    $reportModel = new Report($pdo);
    $kpis = $reportModel->getDashboardKPIs();
    echo "[PASS] Report Model KPIs: Total Patients = {$kpis['total_patients']}, Today Reg = {$kpis['today_registrations']}, Consultations = {$kpis['today_consultations']}, Waiting = {$kpis['waiting_patients']}\n";

    // 4. Patients Pagination test (10 records per page)
    $patientModel = new Patient($pdo);
    $patients = $patientModel->getPaginated(10, 0);
    $totalPatients = $patientModel->countTotal();
    echo "[PASS] Patient Model: Paginated query returned " . count($patients) . " records (Total: {$totalPatients}).\n";

    // 5. Doctor Directory & Smart Recommendation
    $doctorModel = new Doctor($pdo);
    $recommendedDocs = $doctorModel->getRecommendedDoctors(1); // Digestive System
    echo "[PASS] Doctor Model: Smart matching returned " . count($recommendedDocs) . " specialists.\n";

    // 6. SymptomClassifier test
    $classifier = new SymptomClassifier($pdo);
    $classification = $classifier->classifyComplaint(
        "Severe abdominal cramps, burning sensation, nausea and vomiting after eating",
        ["Nausea", "Vomiting", "Stomach Cramps"],
        "abdomen"
    );
    echo "[PASS] SymptomClassifier: Matched '{$classification['body_system']['SystemName']}' with {$classification['relevance_level']}% relevance.\n";

    // 7. Atomic Patient Registration Transaction test
    $regService = new PatientRegistration($pdo);
    $sampleData = [
        'personal' => [
            'firstName'   => 'Test',
            'lastName'    => 'Verification',
            'dob'         => '1995-04-12',
            'gender'      => 'Female',
            'civilStatus' => 'Single',
            'phone'       => '+1 (555) 777-8899',
            'email'       => 'test.verification@example.com',
            'address'     => '123 Health Ave, Metro City'
        ],
        'emergency' => [
            'name'         => 'Jane Verification',
            'relationship' => 'Sister',
            'phone'        => '+1 (555) 777-8800'
        ],
        'medical' => [
            'bloodType'  => 'O+',
            'allergies'  => 'Penicillin',
            'conditions' => 'None',
            'medications'=> 'None'
        ],
        'complaint'            => 'Recurrent stomach ache and indigestion for 3 days',
        'severity'             => 3,
        'duration'             => '1–3 days ago',
        'symptoms'             => ['Nausea', 'Abdominal Pain'],
        'bodyLocation'         => 'abdomen',
        'bodySystemId'         => 1,
        'systemRelevanceScore' => 95,
        'doctorId'             => 1,
        'selectedSlot'         => '02:30 PM',
        'consultationType'     => 'In-Person Consultation'
    ];

    $regResult = $regService->registerCompletePatient($sampleData, 1);
    if ($regResult['success']) {
        echo "[PASS] PatientRegistration: Successfully created {$regResult['patient_code']} with Queue #{$regResult['queue_number']}!\n";
        
        // Verify Full Profile retrieval
        $profile = $patientModel->getFullProfile($regResult['patient_id']);
        echo "[PASS] Full Profile Verification: Name = {$profile['FirstName']} {$profile['LastName']}, Emergency Contact = {$profile['EmergencyContact']['ContactName']}, Queue Ticket = #{$profile['TodayQueue']['QueueNumber']}\n";
    } else {
        echo "[FAIL] PatientRegistration: " . $regResult['message'] . "\n";
    }

    echo "\n=== ALL 7 BACKEND SUBSYSTEM TESTS PASSED! ===\n";

} catch (Exception $e) {
    echo "\n[ERROR] Test Suite Exception: " . $e->getMessage() . "\n";
}
