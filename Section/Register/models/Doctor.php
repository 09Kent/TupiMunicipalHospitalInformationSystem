<?php
// models/Doctor.php

require_once __DIR__ . '/../config/Database.php';

class Doctor
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT d.*, 
                   GROUP_CONCAT(bs.SystemName SEPARATOR ', ') AS LinkedSystems,
                   GROUP_CONCAT(bs.BodySystemID) AS SystemIDs
            FROM doctors d
            LEFT JOIN doctor_body_systems dbs ON dbs.DoctorID = d.DoctorID
            LEFT JOIN body_systems bs ON bs.BodySystemID = dbs.BodySystemID
            WHERE d.Status = 'Available'
            GROUP BY d.DoctorID
            ORDER BY d.Rating DESC, d.ExperienceYears DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, 
                   GROUP_CONCAT(bs.SystemName SEPARATOR ', ') AS LinkedSystems,
                   GROUP_CONCAT(bs.BodySystemID) AS SystemIDs
            FROM doctors d
            LEFT JOIN doctor_body_systems dbs ON dbs.DoctorID = d.DoctorID
            LEFT JOIN body_systems bs ON bs.BodySystemID = dbs.BodySystemID
            WHERE d.DoctorID = :id
            GROUP BY d.DoctorID LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get Recommended Doctors prioritized by the patient's classified Body System
     */
    public function getRecommendedDoctors(int $bodySystemId): array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, 
                   GROUP_CONCAT(bs.SystemName SEPARATOR ', ') AS LinkedSystems,
                   (CASE WHEN EXISTS (
                       SELECT 1 FROM doctor_body_systems dbs 
                       WHERE dbs.DoctorID = d.DoctorID AND dbs.BodySystemID = :sys_id
                   ) THEN 1 ELSE 0 END) AS IsTopMatch
            FROM doctors d
            LEFT JOIN doctor_body_systems dbs ON dbs.DoctorID = d.DoctorID
            LEFT JOIN body_systems bs ON bs.BodySystemID = dbs.BodySystemID
            WHERE d.Status = 'Available'
            GROUP BY d.DoctorID
            ORDER BY IsTopMatch DESC, d.Rating DESC, d.ExperienceYears DESC
        ");
        $stmt->execute(['sys_id' => $bodySystemId]);
        return $stmt->fetchAll();
    }
}
