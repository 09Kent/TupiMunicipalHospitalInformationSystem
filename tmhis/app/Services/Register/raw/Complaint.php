<?php
// models/Complaint.php

require_once __DIR__ . '/../config/Database.php';

class Complaint
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO complaints (
                PatientID, ComplaintDescription, Severity, 
                Duration, AggravatingFactors, RelievingFactors, CreatedAt
            ) VALUES (
                :pid, :desc, :sev, 
                :dur, :agg, :rel, NOW()
            )
        ");

        $stmt->execute([
            'pid'  => $data['PatientID'],
            'desc' => trim($data['ComplaintDescription']),
            'sev'  => (int)($data['Severity'] ?? 3),
            'dur'  => $data['Duration'] ?? '1–3 days ago',
            'agg'  => !empty($data['AggravatingFactors']) ? trim($data['AggravatingFactors']) : null,
            'rel'  => !empty($data['RelievingFactors']) ? trim($data['RelievingFactors']) : null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getById(int $complaintId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM complaints WHERE ComplaintID = :cid LIMIT 1");
        $stmt->execute(['cid' => $complaintId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getLatestByPatientId(int $patientId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   ca.AnalysisID, ca.RelevanceLevel, ca.ConfidenceLevel, ca.ClinicalNotes,
                   bs.BodySystemID, bs.SystemName, bs.SystemCode, bs.Emoji AS SystemEmoji,
                   bl.BodyLocationID, bl.LocationName, bl.LocationCode, bl.SubRegion
            FROM complaints c
            LEFT JOIN complaint_analysis ca ON ca.ComplaintID = c.ComplaintID
            LEFT JOIN body_systems bs ON bs.BodySystemID = ca.BodySystemID
            LEFT JOIN body_locations bl ON bl.BodyLocationID = ca.BodyLocationID
            WHERE c.PatientID = :pid
            ORDER BY c.ComplaintID DESC LIMIT 1
        ");
        $stmt->execute(['pid' => $patientId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
