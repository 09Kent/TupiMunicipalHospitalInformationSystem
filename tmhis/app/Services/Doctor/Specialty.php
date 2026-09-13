<?php
// Doctor/models/Specialty.php

class Specialty
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT s.*, bs.SystemName, bs.SystemCode
            FROM specialties s
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            WHERE s.Status = 'Active'
            ORDER BY s.SpecialtyID ASC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $specialtyId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, bs.SystemName, bs.SystemCode
            FROM specialties s
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            WHERE s.SpecialtyID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $specialtyId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, bs.SystemName, bs.SystemCode
            FROM specialties s
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            WHERE s.SpecialtyCode = :code
            LIMIT 1
        ");
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Map Body System or Complaint Category to matching Doctor Specialty
     */
    public function mapSystemToSpecialty(int|string $bodySystem): ?array
    {
        $sql = "
            SELECT s.*, bs.SystemName 
            FROM specialties s
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            WHERE ";

        if (is_numeric($bodySystem)) {
            $sql .= "s.BodySystemID = :sysId";
            $params = [':sysId' => (int)$bodySystem];
        } else {
            $sql .= "LOWER(bs.SystemName) LIKE :sysName OR LOWER(bs.SystemCode) LIKE :sysName OR LOWER(s.SpecialtyName) LIKE :sysName";
            $params = [':sysName' => '%' . strtolower($bodySystem) . '%'];
        }

        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: $this->findById(1); // Default to GP
    }
}
