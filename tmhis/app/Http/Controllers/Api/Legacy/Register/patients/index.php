<?php
// api/patients/index.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/EmergencyContact.php';
require_once __DIR__ . '/../../models/MedicalHistory.php';
require_once __DIR__ . '/../../models/PatientHistory.php';

$patientModel = new Patient();
$userId = Session::get('user_id', 1);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id > 0) {
        $profile = $patientModel->getFullProfile($id);
        if ($profile) {
            json_response(['success' => true, 'patient' => $profile]);
        } else {
            json_response(['success' => false, 'message' => 'Patient not found'], 404);
        }
    }

    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $patients = $patientModel->getPaginated($limit, $offset, $search, $category, $status);
    $total = $patientModel->countTotal($search, $category, $status);

    json_response([
        'success'  => true,
        'patients' => $patients,
        'total'    => $total,
        'page'     => $page,
        'pages'    => ceil($total / $limit)
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: $_POST;

    $patientId = (int)($data['patient_id'] ?? $data['PatientID'] ?? 0);
    if ($patientId <= 0) {
        json_response(['success' => false, 'message' => 'Patient ID is required.'], 400);
    }

    try {
        // Update main patient record
        $patientModel->update($patientId, $data);

        // Update emergency contact if provided
        if (!empty($data['ContactName'])) {
            $emgModel = new EmergencyContact();
            $emgModel->update($patientId, [
                'ContactName'   => $data['ContactName'],
                'Relationship'  => $data['Relationship'] ?? 'Parent',
                'ContactNumber' => $data['EmergencyPhone'] ?? $data['ContactNumber']
            ]);
        }

        // Update medical history if provided
        if (isset($data['Allergies']) || isset($data['ExistingConditions'])) {
            $medModel = new MedicalHistory();
            $medModel->update($patientId, [
                'Allergies'               => $data['Allergies'] ?? 'None',
                'ExistingConditions'      => $data['ExistingConditions'] ?? 'None',
                'CurrentMedications'      => $data['CurrentMedications'] ?? 'None',
                'PreviousHospitalization' => $data['PreviousHospitalization'] ?? 'None'
            ]);
        }

        // Record history log
        $histModel = new PatientHistory();
        $histModel->log($patientId, 'Patient Information Updated', 'Patient demographics and clinical profile updated.', $userId);

        $updatedProfile = $patientModel->getFullProfile($patientId);
        json_response([
            'success' => true,
            'message' => 'Patient information updated successfully.',
            'patient' => $updatedProfile
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
