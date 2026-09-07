<?php
// models/PatientHistory.php

require_once __DIR__ . '/../config/Database.php';

class PatientHistory
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function log(int $patientId, string $action, string $description, ?int $userId = 1): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO patient_registration_history (PatientID, UserID, Action, Description, CreatedAt)
            VALUES (:pid, :uid, :action, :desc, NOW())
        ");

        $stmt->execute([
            'pid'    => $patientId,
            'uid'    => $userId,
            'action' => trim($action),
            'desc'   => trim($description)
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getByPatientId(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT h.*, u.FirstName AS UserFirst, u.LastName AS UserLast, u.Role
            FROM patient_registration_history h
            LEFT JOIN users u ON u.UserID = h.UserID
            WHERE h.PatientID = :pid
            ORDER BY h.CreatedAt DESC
        ");
        $stmt->execute(['pid' => $patientId]);
        return $stmt->fetchAll();
    }
}
