<?php
// api/registration/classify.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/SymptomClassifier.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method Not Allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$complaint = $data['complaint'] ?? '';
$symptoms = $data['symptoms'] ?? [];
$locationCode = $data['bodyLocation'] ?? null;

if (empty(trim($complaint)) && empty($symptoms)) {
    json_response([
        'success' => false,
        'message' => 'Complaint description or symptoms are required.'
    ], 400);
}

try {
    $classifier = new SymptomClassifier();
    $result = $classifier->classifyComplaint($complaint, (array)$symptoms, $locationCode);
    json_response($result);
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Classification error: ' . $e->getMessage()
    ], 500);
}
