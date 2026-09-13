<?php
// models/PossibleCondition.php

require_once __DIR__ . '/../config/Database.php';

class PossibleCondition
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT pc.*, bs.SystemName, bs.Emoji AS SystemEmoji
            FROM possible_conditions pc
            JOIN body_systems bs ON bs.BodySystemID = pc.BodySystemID
            WHERE pc.Status = 'Active'
            ORDER BY pc.ConditionID ASC
        ");
        return $stmt->fetchAll();
    }

    public function getByBodySystemId(int $systemId): array
    {
        $stmt = $this->db->prepare("
            SELECT pc.*, bs.SystemName
            FROM possible_conditions pc
            JOIN body_systems bs ON bs.BodySystemID = pc.BodySystemID
            WHERE pc.BodySystemID = :sid AND pc.Status = 'Active'
            ORDER BY pc.ConditionID ASC
        ");
        $stmt->execute(['sid' => $systemId]);
        return $stmt->fetchAll();
    }

    public function saveComplaintConditions(int $complaintId, array $conditionIdsOrNames): void
    {
        if (empty($conditionIdsOrNames)) return;

        // Clear existing
        $stmtDel = $this->db->prepare("DELETE FROM complaint_conditions WHERE ComplaintID = :cid");
        $stmtDel->execute(['cid' => $complaintId]);

        $stmtIns = $this->db->prepare("
            INSERT IGNORE INTO complaint_conditions (ComplaintID, ConditionID)
            VALUES (:cid, :cond_id)
        ");

        foreach ($conditionIdsOrNames as $item) {
            $conditionId = null;
            if (is_numeric($item)) {
                $conditionId = (int)$item;
            } else {
                $stmtFind = $this->db->prepare("SELECT ConditionID FROM possible_conditions WHERE ConditionName = :name LIMIT 1");
                $stmtFind->execute(['name' => trim((string)$item)]);
                $conditionId = $stmtFind->fetchColumn();
            }

            if ($conditionId) {
                $stmtIns->execute(['cid' => $complaintId, 'cond_id' => $conditionId]);
            }
        }
    }

    public function getByComplaintId(int $complaintId): array
    {
        $stmt = $this->db->prepare("
            SELECT pc.* 
            FROM complaint_conditions cc
            JOIN possible_conditions pc ON pc.ConditionID = cc.ConditionID
            WHERE cc.ComplaintID = :cid
            ORDER BY pc.ConditionName ASC
        ");
        $stmt->execute(['cid' => $complaintId]);
        return $stmt->fetchAll();
    }
}
