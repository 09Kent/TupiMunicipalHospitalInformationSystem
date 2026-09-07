<?php
// api/symptoms/index.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Symptom.php';
require_once __DIR__ . '/../../models/BodySystem.php';
require_once __DIR__ . '/../../models/BodyLocation.php';

try {
    $symptomModel = new Symptom();
    $bodySystemModel = new BodySystem();
    $bodyLocationModel = new BodyLocation();

    $symptoms = $symptomModel->getAll();
    $systems = $bodySystemModel->getAll();
    $locations = $bodyLocationModel->getAll();

    json_response([
        'success'   => true,
        'symptoms'  => $symptoms,
        'systems'   => $systems,
        'locations' => $locations
    ]);
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Error retrieving symptoms metadata: ' . $e->getMessage()
    ], 500);
}
