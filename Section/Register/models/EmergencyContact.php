<?php
// models/EmergencyContact.php

require_once __DIR__ . '/../config/Database.php';

class EmergencyContact
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO emergency_contacts (PatientID, ContactName, Relationship, ContactNumber)
            VALUES (:pid, :name, :rel, :phone)
        ");

        $stmt->execute([
            'pid'   => $data['PatientID'],
            'name'  => trim($data['ContactName']),
            'rel'   => trim($data['Relationship'] ?? 'Parent'),
            'phone' => trim($data['ContactNumber'])
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getByPatientId(int $patientId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM emergency_contacts WHERE PatientID = :pid LIMIT 1");
        $stmt->execute(['pid' => $patientId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function update(int $patientId, array $data): bool
    {
        $existing = $this->getByPatientId($patientId);
        if (!$existing) {
            $data['PatientID'] = $patientId;
            return (bool)$this->create($data);
        }

        $stmt = $this->db->prepare("
            UPDATE emergency_contacts SET
                ContactName   = :name,
                Relationship  = :rel,
                ContactNumber = :phone
            WHERE PatientID = :pid
        ");

        return $stmt->execute([
            'pid'   => $patientId,
            'name'  => trim($data['ContactName']),
            'rel'   => trim($data['Relationship'] ?? 'Parent'),
            'phone' => trim($data['ContactNumber'])
        ]);
    }
}
