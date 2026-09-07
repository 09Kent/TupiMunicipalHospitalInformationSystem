<?php
// Doctor/models/AllergyRecord.php

class AllergyRecord
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO allergy_records (
                PatientID, DoctorID, Allergen, AllergyType,
                Severity, Reaction, Status, ConfirmedDate
            ) VALUES (
                :patient_id, :doctor_id, :allergen, :type,
                :severity, :reaction, :status, :confirmed_date
            )
        ");

        $stmt->execute([
            ':patient_id'     => $data['patient_id'],
            ':doctor_id'      => $data['doctor_id'],
            ':allergen'       => $data['allergen'],
            ':type'           => $data['allergy_type'] ?? 'Drug',
            ':severity'       => $data['severity'] ?? 'Moderate',
            ':reaction'       => $data['reaction'],
            ':status'         => $data['status'] ?? 'Active',
            ':confirmed_date' => $data['confirmed_date'] ?? date('Y-m-d')
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $allergyId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE allergy_records
            SET Allergen = COALESCE(:allergen, Allergen),
                AllergyType = COALESCE(:type, AllergyType),
                Severity = COALESCE(:severity, Severity),
                Reaction = COALESCE(:reaction, Reaction),
                Status = COALESCE(:status, Status)
            WHERE AllergyID = :id
        ");

        return $stmt->execute([
            ':allergen'  => $data['allergen'] ?? null,
            ':type'      => $data['allergy_type'] ?? null,
            ':severity'  => $data['severity'] ?? null,
            ':reaction'  => $data['reaction'] ?? null,
            ':status'    => $data['status'] ?? null,
            ':id'        => $allergyId
        ]);
    }

    public function delete(int $allergyId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM allergy_records WHERE AllergyID = :id");
        return $stmt->execute([':id' => $allergyId]);
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT ar.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty
            FROM allergy_records ar
            JOIN doctors doc ON ar.DoctorID = doc.DoctorID
            WHERE ar.PatientID = :patient_id
            ORDER BY ar.CreatedAt DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }
}
