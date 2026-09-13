<?php
// api/registration/submit.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/PatientRegistration.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method Not Allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

// Server-side Validation
$errors = [];

$firstName = $data['personal']['firstName'] ?? $data['firstName'] ?? '';
$lastName  = $data['personal']['lastName'] ?? $data['lastName'] ?? '';
$dob       = $data['personal']['dob'] ?? $data['dob'] ?? '';
$gender    = $data['personal']['gender'] ?? $data['gender'] ?? '';
$phone     = $data['personal']['phone'] ?? $data['phone'] ?? '';
$email     = $data['personal']['email'] ?? $data['email'] ?? '';
$address   = $data['personal']['address'] ?? $data['address'] ?? '';
$complaint = $data['complaint'] ?? '';

if (empty(trim($firstName))) $errors['firstName'] = 'First name is required.';
if (empty(trim($lastName))) $errors['lastName'] = 'Last name is required.';
if (empty($dob)) $errors['dob'] = 'Date of birth is required.';
if (empty($gender)) $errors['gender'] = 'Gender is required.';
if (empty(trim($phone))) $errors['phone'] = 'Contact number is required.';
if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
if (empty(trim($address))) $errors['address'] = 'Address is required.';
if (empty(trim($complaint))) $errors['complaint'] = 'Chief complaint description is required.';

if (!empty($errors)) {
    json_response([
        'success' => false,
        'message' => 'Validation failed on server.',
        'errors'  => $errors
    ], 422);
}

$userId = Session::get('user_id', 1);

try {
    $regService = new PatientRegistration();
    $result = $regService->registerCompletePatient($data, $userId);
    json_response($result, $result['success'] ? 201 : 400);
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Server registration error: ' . $e->getMessage()
    ], 500);
}
