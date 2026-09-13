<?php
// models/Symptom.php

require_once __DIR__ . '/../config/Database.php';

class Symptom
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM symptoms WHERE Status = 'Active' ORDER BY SymptomName ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM symptoms WHERE SymptomID = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM symptoms WHERE SymptomName = :name LIMIT 1");
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Attach symptoms to a complaint (Many-to-Many)
     */
    public function saveComplaintSymptoms(int $complaintId, array $symptomIdsOrNames): void
    {
        if (empty($symptomIdsOrNames)) return;

        // Clear existing
        $stmtDel = $this->db->prepare("DELETE FROM patient_symptoms WHERE ComplaintID = :cid");
        $stmtDel->execute(['cid' => $complaintId]);

        $stmtIns = $this->db->prepare("
            INSERT IGNORE INTO patient_symptoms (ComplaintID, SymptomID)
            VALUES (:cid, :sid)
        ");

        foreach ($symptomIdsOrNames as $item) {
            $symptomId = null;
            if (is_numeric($item)) {
                $symptomId = (int)$item;
            } else {
                $sym = $this->getByName((string)$item);
                if ($sym) {
                    $symptomId = (int)$sym['SymptomID'];
                }
            }

            if ($symptomId) {
                $stmtIns->execute(['cid' => $complaintId, 'sid' => $symptomId]);
            }
        }
    }

    public function getByComplaintId(int $complaintId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.* 
            FROM patient_symptoms ps
            JOIN symptoms s ON s.SymptomID = ps.SymptomID
            WHERE ps.ComplaintID = :cid
            ORDER BY s.SymptomName ASC
        ");
        $stmt->execute(['cid' => $complaintId]);
        return $stmt->fetchAll();
    }
}
