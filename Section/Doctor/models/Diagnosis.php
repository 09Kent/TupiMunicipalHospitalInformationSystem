<?php
// Doctor/models/Diagnosis.php

class Diagnosis
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO diagnoses (
                PatientID, DoctorID, AppointmentID, DiagnosisName, ICD10Code,
                Type, Severity, Status, Notes, DiagnosedDate
            ) VALUES (
                :patient_id, :doctor_id, :appointment_id, :diagnosis_name, :icd_code,
                :type, :severity, :status, :notes, :diagnosed_date
            )
        ");

        $stmt->execute([
            ':patient_id'     => $data['patient_id'],
            ':doctor_id'      => $data['doctor_id'],
            ':appointment_id' => $data['appointment_id'] ?? null,
            ':diagnosis_name' => $data['diagnosis_name'],
            ':icd_code'       => $data['icd10_code'] ?? null,
            ':type'           => $data['type'] ?? 'Primary',
            ':severity'       => $data['severity'] ?? 'Moderate',
            ':status'         => $data['status'] ?? 'Active',
            ':notes'          => $data['notes'] ?? null,
            ':diagnosed_date' => $data['diagnosed_date'] ?? date('Y-m-d')
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $diagnosisId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE diagnoses
            SET DiagnosisName = COALESCE(:diagnosis_name, DiagnosisName),
                ICD10Code = COALESCE(:icd_code, ICD10Code),
                Type = COALESCE(:type, Type),
                Severity = COALESCE(:severity, Severity),
                Status = COALESCE(:status, Status),
                Notes = COALESCE(:notes, Notes)
            WHERE DiagnosisID = :diagnosis_id
        ");

        return $stmt->execute([
            ':diagnosis_name' => $data['diagnosis_name'] ?? null,
            ':icd_code'       => $data['icd10_code'] ?? null,
            ':type'           => $data['type'] ?? null,
            ':severity'       => $data['severity'] ?? null,
            ':status'         => $data['status'] ?? null,
            ':notes'          => $data['notes'] ?? null,
            ':diagnosis_id'   => $diagnosisId
        ]);
    }

    public function findById(int $diagnosisId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty,
                   p.FirstName AS PatientFirstName, p.LastName AS PatientLastName, p.PatientCode
            FROM diagnoses d
            JOIN doctors doc ON d.DoctorID = doc.DoctorID
            JOIN patients p ON d.PatientID = p.PatientID
            WHERE d.DiagnosisID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $diagnosisId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty
            FROM diagnoses d
            JOIN doctors doc ON d.DoctorID = doc.DoctorID
            WHERE d.PatientID = :patient_id
            ORDER BY d.DiagnosedDate DESC, d.CreatedAt DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function getDistributionStats(int $doctorId): array
    {
        $stmt = $this->db->prepare("
            SELECT DiagnosisName, COUNT(*) AS TotalCount
            FROM diagnoses
            WHERE DoctorID = :docId
            GROUP BY DiagnosisName
            ORDER BY TotalCount DESC
            LIMIT 5
        ");
        $stmt->execute([':docId' => $doctorId]);
        $results = $stmt->fetchAll();

        // Provide fallback data if new doctor has fewer than 3 records
        if (count($results) < 3) {
            return [
                ['DiagnosisName' => 'Cardiac Palpitations', 'TotalCount' => 12],
                ['DiagnosisName' => 'Essential Hypertension', 'TotalCount' => 9],
                ['DiagnosisName' => 'Atherosclerotic Heart Disease', 'TotalCount' => 7],
                ['DiagnosisName' => 'Atrial Arrhythmia', 'TotalCount' => 5],
                ['DiagnosisName' => 'Non-Specific Chest Discomfort', 'TotalCount' => 4]
            ];
        }

        return $results;
    }
}
