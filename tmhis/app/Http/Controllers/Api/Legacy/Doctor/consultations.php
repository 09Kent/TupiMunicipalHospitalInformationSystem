<?php
// Doctor/api/consultations.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Consultation.php';
require_once __DIR__ . '/../models/ConsultationNote.php';
require_once __DIR__ . '/../models/Diagnosis.php';
require_once __DIR__ . '/../models/TreatmentPlan.php';
require_once __DIR__ . '/../models/Patient.php';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$action = $_REQUEST['action'] ?? '';

try {
    $consultationModel = new Consultation();
    $noteModel = new ConsultationNote();
    $diagnosisModel = new Diagnosis();
    $treatmentModel = new TreatmentPlan();
    $patientModel = new Patient();

    if ($action === 'start') {
        $appId = (int)($_POST['appointment_id'] ?? 0);
        if (!$appId) {
            json_response(['success' => false, 'message' => 'Appointment ID is required.'], 400);
        }
        $success = $consultationModel->startConsultation($appId, $doctorId);
        json_response(['success' => $success, 'message' => $success ? 'Consultation started.' : 'Could not start consultation.']);
    } elseif ($action === 'complete') {
        $appId = (int)($_POST['appointment_id'] ?? 0);
        $summary = $_POST['summary'] ?? null;
        if (!$appId) {
            json_response(['success' => false, 'message' => 'Appointment ID is required.'], 400);
        }
        $success = $consultationModel->completeConsultation($appId, $doctorId, $summary);
        json_response(['success' => $success, 'message' => $success ? 'Consultation marked as completed.' : 'Could not complete consultation.']);
    } elseif ($action === 'save_note') {
        $pId = (int)($_POST['patient_id'] ?? 0);
        if (!$pId || !$patientModel->hasDoctorAccess($doctorId, $pId)) {
            json_response(['success' => false, 'message' => 'Access Denied: You are not authorized to save notes for this patient.'], 403);
        }
        $data = [
            'patient_id'     => $pId,
            'doctor_id'      => $doctorId,
            'appointment_id' => !empty($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : null,
            'subjective'     => $_POST['subjective'] ?? null,
            'objective'      => $_POST['objective'] ?? null,
            'assessment'     => $_POST['assessment'] ?? null,
            'plan'           => $_POST['plan'] ?? null,
            'clinical_notes' => $_POST['clinical_notes'] ?? 'Clinical observation recorded.',
            'vitals'         => $_POST['vitals'] ?? null
        ];
        $noteId = $noteModel->create($data);
        json_response(['success' => (bool)$noteId, 'note_id' => $noteId]);
    } else {
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Exception $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
