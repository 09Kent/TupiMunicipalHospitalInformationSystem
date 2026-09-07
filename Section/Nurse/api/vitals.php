<?php
// Section/Nurse/api/vitals.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/VitalSign.php';

$model = new VitalSign();
$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $patientId = (int)($_GET['patient_id'] ?? 0);
        if ($patientId > 0) {
            $data = $model->getByPatient($patientId);
        } else {
            $data = $model->getLatestAll();
        }
        json_response(['success' => true, 'data' => $data]);
    } elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->create($_POST);
        json_response([
            'success' => true,
            'id' => $id,
            'message' => 'Vital signs logged successfully in patient chart.'
        ]);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
