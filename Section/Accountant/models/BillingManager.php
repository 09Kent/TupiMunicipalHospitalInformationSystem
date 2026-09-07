<?php
// Section/Accountant/models/BillingManager.php

require_once __DIR__ . '/../config/Database.php';

class BillingManager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getCharges(): array
    {
        $sql = "SELECT bc.*, p.FirstName, p.LastName, p.PatientCode, p.ContactNumber
                FROM billing_charges bc
                JOIN patients p ON bc.PatientID = p.PatientID
                ORDER BY bc.ChargeID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getInvoices(): array
    {
        $sql = "SELECT bi.*, p.FirstName, p.LastName, p.PatientCode
                FROM billing_invoices bi
                JOIN patients p ON bi.PatientID = p.PatientID
                ORDER BY bi.InvoiceID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getPayments(): array
    {
        $sql = "SELECT bp.*, p.FirstName, p.LastName, p.PatientCode, bi.InvoiceNumber
                FROM billing_payments bp
                JOIN patients p ON bp.PatientID = p.PatientID
                JOIN billing_invoices bi ON bp.InvoiceID = bi.InvoiceID
                ORDER BY bp.PaymentID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function createCharge(array $data): int
    {
        $unitPrice = (float)($data['UnitPrice'] ?? 0.00);
        $qty = (int)($data['Quantity'] ?? 1);
        $subtotal = $unitPrice * $qty;
        $discount = (float)($data['DiscountAmount'] ?? 0.00);
        $net = max(0, $subtotal - $discount);

        $sql = "INSERT INTO billing_charges (PatientID, ChargeCategory, ItemDescription, Quantity, UnitPrice, SubTotal, DiscountAmount, NetAmount, BillingStatus)
                VALUES (:PatientID, :ChargeCategory, :ItemDescription, :Quantity, :UnitPrice, :SubTotal, :DiscountAmount, :NetAmount, 'Unbilled')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':PatientID' => (int)$data['PatientID'],
            ':ChargeCategory' => $data['ChargeCategory'] ?? 'Consultation',
            ':ItemDescription' => $data['ItemDescription'],
            ':Quantity' => $qty,
            ':UnitPrice' => $unitPrice,
            ':SubTotal' => $subtotal,
            ':DiscountAmount' => $discount,
            ':NetAmount' => $net
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function processPayment(array $data): array
    {
        $invoiceId = (int)$data['InvoiceID'];
        $patientId = (int)$data['PatientID'];
        $amountPaid = (float)$data['AmountPaid'];
        $receiptNo = 'OR-2026-' . str_pad((string)rand(100, 9999), 6, '0', STR_PAD_LEFT);
        $words = $data['AmountInWords'] ?? 'Pesos Only';

        $sql = "INSERT INTO billing_payments (ReceiptNumber, InvoiceID, PatientID, AmountPaid, PaymentMethod, ReferenceNumber, AmountInWords, CashierName)
                VALUES (:ReceiptNumber, :InvoiceID, :PatientID, :AmountPaid, :PaymentMethod, :ReferenceNumber, :AmountInWords, :CashierName)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ReceiptNumber' => $receiptNo,
            ':InvoiceID' => $invoiceId,
            ':PatientID' => $patientId,
            ':AmountPaid' => $amountPaid,
            ':PaymentMethod' => $data['PaymentMethod'] ?? 'Cash',
            ':ReferenceNumber' => $data['ReferenceNumber'] ?? null,
            ':AmountInWords' => $words,
            ':CashierName' => $data['CashierName'] ?? 'Maria Santos'
        ]);

        // Update Invoice
        $up = $this->db->prepare("UPDATE billing_invoices SET AmountPaid = AmountPaid + :paid, BalanceDue = GREATEST(0, TotalPayable - (AmountPaid + :paid2)), PaymentStatus = 'Paid In Full' WHERE InvoiceID = :invId");
        $up->execute([
            ':paid' => $amountPaid,
            ':paid2' => $amountPaid,
            ':invId' => $invoiceId
        ]);

        return [
            'success' => true,
            'receipt_number' => $receiptNo,
            'amount_paid' => $amountPaid
        ];
    }
}
