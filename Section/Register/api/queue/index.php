<?php
// api/queue/index.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Queue.php';
require_once __DIR__ . '/../../models/PatientHistory.php';

$queueModel = new Queue();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $todayQueue = $queueModel->getTodayQueue();
        $nowServing = $queueModel->getNowServing();

        json_response([
            'success'     => true,
            'queue'       => $todayQueue,
            'now_serving' => $nowServing
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: $_POST;

    $action = $data['action'] ?? '';
    $queueId = (int)($data['queue_id'] ?? 0);

    if ($queueId <= 0) {
        json_response(['success' => false, 'message' => 'Invalid Queue ID'], 400);
    }

    try {
        if ($action === 'call') {
            $queueModel->updateStatus($queueId, 'Called');
        } elseif ($action === 'consult') {
            $queueModel->updateStatus($queueId, 'In Consultation');
        } elseif ($action === 'complete') {
            $queueModel->updateStatus($queueId, 'Completed');
        } elseif ($action === 'cancel') {
            $queueModel->updateStatus($queueId, 'Cancelled');
        } else {
            json_response(['success' => false, 'message' => 'Invalid queue action'], 400);
        }

        $todayQueue = $queueModel->getTodayQueue();
        $nowServing = $queueModel->getNowServing();

        json_response([
            'success'     => true,
            'message'     => 'Queue status updated successfully.',
            'queue'       => $todayQueue,
            'now_serving' => $nowServing
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
