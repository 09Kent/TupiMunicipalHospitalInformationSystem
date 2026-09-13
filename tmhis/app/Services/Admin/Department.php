<?php
// Section/Admin/models/Department.php

require_once __DIR__ . '/../config/Database.php';

class Department
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM departments ORDER BY DepartmentID ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE DepartmentID = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO departments (DepartmentCode, DepartmentName, DepartmentType, HeadOfDepartment, Location, ContactExtension, Status)
                VALUES (:DepartmentCode, :DepartmentName, :DepartmentType, :HeadOfDepartment, :Location, :ContactExtension, :Status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':DepartmentCode' => $data['DepartmentCode'],
            ':DepartmentName' => $data['DepartmentName'],
            ':DepartmentType' => $data['DepartmentType'] ?? 'Clinical',
            ':HeadOfDepartment' => $data['HeadOfDepartment'] ?? null,
            ':Location' => $data['Location'] ?? 'Main Building',
            ':ContactExtension' => $data['ContactExtension'] ?? null,
            ':Status' => $data['Status'] ?? 'Active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE departments SET
                DepartmentCode = :DepartmentCode,
                DepartmentName = :DepartmentName,
                DepartmentType = :DepartmentType,
                HeadOfDepartment = :HeadOfDepartment,
                Location = :Location,
                ContactExtension = :ContactExtension,
                Status = :Status
                WHERE DepartmentID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':DepartmentCode' => $data['DepartmentCode'],
            ':DepartmentName' => $data['DepartmentName'],
            ':DepartmentType' => $data['DepartmentType'] ?? 'Clinical',
            ':HeadOfDepartment' => $data['HeadOfDepartment'] ?? null,
            ':Location' => $data['Location'] ?? 'Main Building',
            ':ContactExtension' => $data['ContactExtension'] ?? null,
            ':Status' => $data['Status'] ?? 'Active'
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM departments WHERE DepartmentID = :id");
        return $stmt->execute([':id' => $id]);
    }
}
