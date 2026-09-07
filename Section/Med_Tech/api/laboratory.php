<?php
// Section/Med_Tech/api/laboratory.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/LabTechnologist.php';

$model = new LabTechnologist();
$action = $_REQUEST['action'] ?? 'requests';

try {
    if ($action === 'requests') {
        $data = $model->getPendingRequests();
        json_response(['success' => true, 'data' => $data]);
    } elseif ($action === 'record_result' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->recordResult($_POST);
        json_response([
            'success' => true,
            'result_id' => $id,
            'message' => 'Laboratory test result saved and certified. Request updated to Completed.'
        ]);
    } elseif ($action === 'catalog') {
        $data = $model->getCatalog();
        json_response(['success' => true, 'data' => $data]);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
