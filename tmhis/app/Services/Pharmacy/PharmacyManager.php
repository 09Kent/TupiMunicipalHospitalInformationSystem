<?php
// Section/Pharmacy/models/PharmacyManager.php

require_once __DIR__ . '/../config/Database.php';

class PharmacyManager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getActivePrescriptions(): array
    {
        $sql = "SELECT rx.*, p.FirstName, p.LastName, p.PatientCode, p.DateOfBirth, p.Age, p.Gender,
                       d.FirstName as DoctorFirstName, d.LastName as DoctorLastName, d.Specialty, d.LicenseNumber
                FROM prescriptions rx
                JOIN patients p ON rx.PatientID = p.PatientID
                JOIN doctors d ON rx.DoctorID = d.DoctorID
                ORDER BY rx.PrescriptionID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function dispense(array $data): int
    {
        $dispenseCode = 'DISP-' . date('Ymd') . '-' . str_pad((string)rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        $sql = "INSERT INTO dispensing_records (DispenseCode, PrescriptionID, PatientID, DispenserName, QuantityDispensed, DosageInstructions, BatchNumber, Status, Notes)
                VALUES (:DispenseCode, :PrescriptionID, :PatientID, :DispenserName, :QuantityDispensed, :DosageInstructions, :BatchNumber, 'Dispensed', :Notes)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':DispenseCode' => $dispenseCode,
            ':PrescriptionID' => (int)$data['PrescriptionID'],
            ':PatientID' => (int)$data['PatientID'],
            ':DispenserName' => $data['DispenserName'] ?? 'Kareen Joy Ramos, RPh',
            ':QuantityDispensed' => $data['QuantityDispensed'] ?? '1 Box',
            ':DosageInstructions' => $data['DosageInstructions'] ?? 'Take as prescribed.',
            ':BatchNumber' => $data['BatchNumber'] ?? 'B-2026-001',
            ':Notes' => $data['Notes'] ?? 'Medication dispensed and patient counseled.'
        ]);

        $dispenseId = (int)$this->db->lastInsertId();

        // Update prescription status to Completed
        $upStmt = $this->db->prepare("UPDATE prescriptions SET Status = 'Completed' WHERE PrescriptionID = :rxId");
        $upStmt->execute([':rxId' => (int)$data['PrescriptionID']]);

        return $dispenseId;
    }

    public function getInventory(): array
    {
        $stmt = $this->db->query("SELECT * FROM pharmacy_inventory ORDER BY InventoryID ASC");
        return $stmt->fetchAll();
    }
}
