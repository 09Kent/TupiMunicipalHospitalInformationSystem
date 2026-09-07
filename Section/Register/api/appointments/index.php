<?php
// api/appointments/index.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Appointment.php';

$appModel = new Appointment();
$userId = Session::get('user_id', 1);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $date = $_GET['date'] ?? '';
        $doctor = $_GET['doctor'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $appointments = $appModel->getPaginated($limit, $offset, $date, $doctor, $status);
        $total = $appModel->countTotal($date, $doctor, $status);

        json_response([
            'success'      => true,
            'appointments' => $appointments,
            'total'        => $total,
            'page'         => $page,
            'pages'        => ceil($total / $limit)
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: $_POST;

    $action = $data['action'] ?? '';
    $appId = (int)($data['appointment_id'] ?? 0);

    try {
        if ($action === 'reschedule') {
            $newDate = $data['appointment_date'] ?? date('Y-m-d');
            $newTime = $data['appointment_time'] ?? '02:30 PM';
            $newDocId = !empty($data['doctor_id']) ? (int)$data['doctor_id'] : null;
            $reason = $data['reason'] ?? 'Requested by patient/doctor';

            $success = $appModel->reschedule($appId, $newDate, $newTime, $newDocId, $reason, $userId);
            json_response(['success' => $success, 'message' => 'Consultation rescheduled successfully.']);
        } elseif ($action === 'cancel') {
            $reason = $data['reason'] ?? 'Cancelled by registrator';
            $success = $appModel->cancel($appId, $reason, $userId);
            json_response(['success' => $success, 'message' => 'Consultation cancelled successfully.']);
        } elseif ($action === 'status') {
            $status = $data['status'] ?? 'Confirmed';
            $success = $appModel->updateStatus($appId, $status, $userId);
            json_response(['success' => $success, 'message' => 'Appointment status updated.']);
        } elseif ($action === 'schedule') {
            $createdId = $appModel->create($data);
            json_response(['success' => true, 'appointment_id' => $createdId, 'message' => 'Consultation scheduled successfully.']);
        } else {
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
        }
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
