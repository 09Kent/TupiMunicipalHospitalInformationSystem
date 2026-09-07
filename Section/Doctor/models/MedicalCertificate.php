<?php
// Doctor/models/MedicalCertificate.php

class MedicalCertificate
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function generateCode(): string
    {
        $year = date('Y');
        $stmt = $this->db->query("SELECT MAX(CertificateID) AS max_id FROM medical_certificates");
        $next = ((int)($stmt->fetchColumn() ?: 0)) + 1;
        return sprintf("MED-CERT-%s-%04d", $year, $next);
    }

    public function create(array $data): int
    {
        $code = $data['certificate_code'] ?? $this->generateCode();

        // Calculate days excused
        $start = new DateTime($data['duration_start']);
        $end = new DateTime($data['duration_end']);
        $diff = $start->diff($end);
        $days = max(1, $diff->days + 1);

        $stmt = $this->db->prepare("
            INSERT INTO medical_certificates (
                CertificateCode, PatientID, DoctorID, CertificateType,
                Diagnosis, DurationStart, DurationEnd, DaysExcused, Remarks, IssueDate
            ) VALUES (
                :code, :patient_id, :doctor_id, :type,
                :diagnosis, :start_date, :end_date, :days, :remarks, :issue_date
            )
        ");

        $stmt->execute([
            ':code'       => $code,
            ':patient_id' => $data['patient_id'],
            ':doctor_id'  => $data['doctor_id'],
            ':type'       => $data['certificate_type'] ?? 'Fit to Work',
            ':diagnosis'  => $data['diagnosis'],
            ':start_date' => $data['duration_start'],
            ':end_date'   => $data['duration_end'],
            ':days'       => $data['days_excused'] ?? $days,
            ':remarks'    => $data['remarks'] ?? null,
            ':issue_date' => $data['issue_date'] ?? date('Y-m-d')
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findById(int $certificateId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT mc.*,
                   doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Title AS DoctorTitle,
                   doc.Specialty, doc.LicenseNumber, doc.ContactNumber AS DoctorContact, doc.Clinic, doc.ClinicRoom,
                   p.FirstName AS PatientFirstName, p.MiddleName, p.LastName AS PatientLastName, p.PatientCode,
                   p.DateOfBirth, p.Age, p.Gender, p.Address, p.CivilStatus
            FROM medical_certificates mc
            JOIN doctors doc ON mc.DoctorID = doc.DoctorID
            JOIN patients p ON mc.PatientID = p.PatientID
            WHERE mc.CertificateID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $certificateId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT mc.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty
            FROM medical_certificates mc
            JOIN doctors doc ON mc.DoctorID = doc.DoctorID
            WHERE mc.PatientID = :patient_id
            ORDER BY mc.IssueDate DESC, mc.CreatedAt DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function getAll(int $doctorId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT mc.*, p.PatientCode, p.FirstName AS PatientFirstName, p.LastName AS PatientLastName,
                   p.Age, p.Gender
            FROM medical_certificates mc
            JOIN patients p ON mc.PatientID = p.PatientID
            WHERE mc.DoctorID = :docId
            ORDER BY mc.IssueDate DESC, mc.CreatedAt DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':docId', $doctorId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
