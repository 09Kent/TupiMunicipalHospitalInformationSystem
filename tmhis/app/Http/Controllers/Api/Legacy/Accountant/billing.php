<?php
// Section/Accountant/api/billing.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BillingManager.php';

$model = new BillingManager();
$action = $_REQUEST['action'] ?? 'charges';

function json_out(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

try {
    if ($action === 'charges') {
        $charges = $model->getCharges();
        json_out(['success' => true, 'data' => $charges]);
    } elseif ($action === 'invoices') {
        $invoices = $model->getInvoices();
        json_out(['success' => true, 'data' => $invoices]);
    } elseif ($action === 'payments') {
        $payments = $model->getPayments();
        json_out(['success' => true, 'data' => $payments]);
    } elseif ($action === 'create_charge' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->createCharge($_POST);
        json_out(['success' => true, 'charge_id' => $id, 'message' => 'Service charge added to patient ledger.']);
    } elseif ($action === 'pay' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $res = $model->processPayment($_POST);
        json_out([
            'success' => true,
            'receipt_number' => $res['receipt_number'],
            'message' => "Payment processed. Official Receipt {$res['receipt_number']} generated."
        ]);
    } else {
        json_out(['success' => false, 'message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    json_out(['success' => false, 'message' => $e->getMessage()], 500);
}
