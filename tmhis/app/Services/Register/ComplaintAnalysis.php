<?php

namespace App\Services\Register;

use PDO;
use Exception;
use DateTime;

// models/ComplaintAnalysis.php



class ComplaintAnalysis
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? \Illuminate\Support\Facades\DB::connection()->getPdo();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO complaint_analysis (
                ComplaintID, BodySystemID, BodyLocationID, 
                RelevanceLevel, ConfidenceLevel, ClinicalNotes, CreatedAt
            ) VALUES (
                :cid, :sys_id, :loc_id, 
                :rel, :conf, :notes, NOW()
            )
        ");

        $stmt->execute([
            'cid'    => $data['ComplaintID'],
            'sys_id' => $data['BodySystemID'],
            'loc_id' => $data['BodyLocationID'] ?? null,
            'rel'    => (int)($data['RelevanceLevel'] ?? 90),
            'conf'   => $data['ConfidenceLevel'] ?? 'High (90%)',
            'notes'  => $data['ClinicalNotes'] ?? 'Automated symptom correlation for consultation reference.'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getByComplaintId(int $complaintId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT ca.*, 
                   bs.SystemName, bs.SystemCode, bs.Emoji AS SystemEmoji,
                   bl.LocationName, bl.LocationCode, bl.SubRegion
            FROM complaint_analysis ca
            JOIN body_systems bs ON bs.BodySystemID = ca.BodySystemID
            LEFT JOIN body_locations bl ON bl.BodyLocationID = ca.BodyLocationID
            WHERE ca.ComplaintID = :cid LIMIT 1
        ");
        $stmt->execute(['cid' => $complaintId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
