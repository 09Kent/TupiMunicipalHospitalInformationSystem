<?php
// Section/Pharmacy/api/pharmacy.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/PharmacyManager.php';

$model = new PharmacyManager();
$action = $_REQUEST['action'] ?? 'prescriptions';

try {
    if ($action === 'prescriptions') {
        $data = $model->getActivePrescriptions();
        json_response(['success' => true, 'data' => $data]);
    } elseif ($action === 'dispense' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->dispense($_POST);
        json_response([
            'success' => true,
            'dispense_id' => $id,
            'message' => 'Medication dispensed successfully and prescription marked as Completed.'
        ]);
    } elseif ($action === 'inventory') {
        $data = $model->getInventory();
        json_response(['success' => true, 'data' => $data]);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
