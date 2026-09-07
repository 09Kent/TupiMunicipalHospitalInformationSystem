<?php
// Section/Medical_Officer/api/records.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/MedicalRecordsManager.php';

$model = new MedicalRecordsManager();
$action = $_REQUEST['action'] ?? 'patients';

function json_response_mo(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

try {
    if ($action === 'patients') {
        $patients = $model->getAllPatients();
        json_response_mo(['success' => true, 'data' => $patients]);
    } elseif ($action === 'history') {
        $id = (int)($_GET['patient_id'] ?? 0);
        $data = $model->getPatientFullHistory($id);
        json_response_mo(['success' => true, 'data' => $data]);
    } elseif ($action === 'requests') {
        $data = $model->getReleaseRequests();
        json_response_mo(['success' => true, 'data' => $data]);
    } elseif ($action === 'archive' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['patient_id'] ?? 0);
        $success = $model->archivePatient($id);
        json_response_mo(['success' => $success, 'message' => $success ? 'Patient record archived.' : 'Failed to archive.']);
    } else {
        json_response_mo(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response_mo(['success' => false, 'message' => $e->getMessage()], 500);
}
