<?php
// Section/Admin/models/ServiceFee.php

require_once __DIR__ . '/../config/Database.php';

class ServiceFee
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT sf.*, d.DepartmentName 
                FROM service_fees sf
                LEFT JOIN departments d ON sf.DepartmentID = d.DepartmentID
                ORDER BY sf.FeeID ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM service_fees WHERE FeeID = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO service_fees (ServiceCode, ServiceName, Category, DepartmentID, StandardRate, PhilHealthCoveredRate, DiscountEligible, Description, Status)
                VALUES (:ServiceCode, :ServiceName, :Category, :DepartmentID, :StandardRate, :PhilHealthCoveredRate, :DiscountEligible, :Description, :Status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ServiceCode' => $data['ServiceCode'],
            ':ServiceName' => $data['ServiceName'],
            ':Category' => $data['Category'] ?? 'Consultation',
            ':DepartmentID' => !empty($data['DepartmentID']) ? (int)$data['DepartmentID'] : null,
            ':StandardRate' => (float)($data['StandardRate'] ?? 0.00),
            ':PhilHealthCoveredRate' => (float)($data['PhilHealthCoveredRate'] ?? 0.00),
            ':DiscountEligible' => isset($data['DiscountEligible']) ? (int)$data['DiscountEligible'] : 1,
            ':Description' => $data['Description'] ?? null,
            ':Status' => $data['Status'] ?? 'Active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE service_fees SET
                ServiceCode = :ServiceCode,
                ServiceName = :ServiceName,
                Category = :Category,
                DepartmentID = :DepartmentID,
                StandardRate = :StandardRate,
                PhilHealthCoveredRate = :PhilHealthCoveredRate,
                DiscountEligible = :DiscountEligible,
                Description = :Description,
                Status = :Status
                WHERE FeeID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':ServiceCode' => $data['ServiceCode'],
            ':ServiceName' => $data['ServiceName'],
            ':Category' => $data['Category'] ?? 'Consultation',
            ':DepartmentID' => !empty($data['DepartmentID']) ? (int)$data['DepartmentID'] : null,
            ':StandardRate' => (float)($data['StandardRate'] ?? 0.00),
            ':PhilHealthCoveredRate' => (float)($data['PhilHealthCoveredRate'] ?? 0.00),
            ':DiscountEligible' => isset($data['DiscountEligible']) ? (int)$data['DiscountEligible'] : 1,
            ':Description' => $data['Description'] ?? null,
            ':Status' => $data['Status'] ?? 'Active'
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM service_fees WHERE FeeID = :id");
        return $stmt->execute([':id' => $id]);
    }
}
