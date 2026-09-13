<?php
// api/doctors/index.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Doctor.php';

try {
    $doctorModel = new Doctor();
    $systemId = isset($_GET['system_id']) ? (int)$_GET['system_id'] : 0;

    if ($systemId > 0) {
        $doctors = $doctorModel->getRecommendedDoctors($systemId);
    } else {
        $doctors = $doctorModel->getAll();
    }

    json_response([
        'success' => true,
        'doctors' => $doctors
    ]);
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Error retrieving doctors: ' . $e->getMessage()
    ], 500);
}
