<?php
// Doctor/api/analytics.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Diagnosis.php';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

try {
    $diagnosisModel = new Diagnosis();
    $stats = $diagnosisModel->getDistributionStats($doctorId);

    json_response([
        'success' => true,
        'weekly_trend' => [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'data'   => [6, 8, 12, 9, 14, 7, 4]
        ],
        'diagnoses' => $stats
    ]);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
