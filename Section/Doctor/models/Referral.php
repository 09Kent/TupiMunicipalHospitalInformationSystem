<?php
// Doctor/models/Referral.php

class Referral
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function generateCode(): string
    {
        $year = date('Y');
        $stmt = $this->db->query("SELECT MAX(ReferralID) AS max_id FROM referrals");
        $next = ((int)($stmt->fetchColumn() ?: 0)) + 1;
        return sprintf("REF-%s-%04d", $year, $next);
    }

    public function create(array $data): int
    {
        $code = $data['referral_code'] ?? $this->generateCode();

        $stmt = $this->db->prepare("
            INSERT INTO referrals (
                ReferralCode, PatientID, ReferringDoctorID, TargetSpecialtyID,
                TargetDoctorID, Reason, ClinicalSummary, Priority, Status, ResponseNotes
            ) VALUES (
                :code, :patient_id, :ref_doc_id, :target_spec_id,
                :target_doc_id, :reason, :summary, :priority, :status, :notes
            )
        ");

        $stmt->execute([
            ':code'           => $code,
            ':patient_id'     => $data['patient_id'],
            ':ref_doc_id'     => $data['referring_doctor_id'],
            ':target_spec_id' => $data['target_specialty_id'],
            ':target_doc_id'  => !empty($data['target_doctor_id']) ? $data['target_doctor_id'] : null,
            ':reason'         => $data['reason'],
            ':summary'        => $data['clinical_summary'] ?? null,
            ':priority'       => $data['priority'] ?? 'Routine',
            ':status'         => $data['status'] ?? 'Pending',
            ':notes'          => $data['response_notes'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $referralId, string $status, ?string $responseNotes = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE referrals 
            SET Status = :status, ResponseNotes = COALESCE(:notes, ResponseNotes), UpdatedAt = NOW() 
            WHERE ReferralID = :id
        ");
        return $stmt->execute([
            ':status' => $status,
            ':notes'  => $responseNotes,
            ':id'     => $referralId
        ]);
    }

    public function findById(int $referralId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   rd.FirstName AS RefDocFirstName, rd.LastName AS RefDocLastName, rd.Specialty AS RefDocSpecialty,
                   ts.SpecialtyName AS TargetSpecialtyName,
                   td.FirstName AS TargetDocFirstName, td.LastName AS TargetDocLastName,
                   p.FirstName AS PatientFirstName, p.LastName AS PatientLastName, p.PatientCode,
                   p.Age, p.Gender, p.ContactNumber
            FROM referrals r
            JOIN doctors rd ON r.ReferringDoctorID = rd.DoctorID
            JOIN specialties ts ON r.TargetSpecialtyID = ts.SpecialtyID
            LEFT JOIN doctors td ON r.TargetDoctorID = td.DoctorID
            JOIN patients p ON r.PatientID = p.PatientID
            WHERE r.ReferralID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $referralId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getOutgoing(int $doctorId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   ts.SpecialtyName AS TargetSpecialtyName,
                   td.FirstName AS TargetDocFirstName, td.LastName AS TargetDocLastName,
                   p.FirstName AS PatientFirstName, p.LastName AS PatientLastName, p.PatientCode
            FROM referrals r
            JOIN specialties ts ON r.TargetSpecialtyID = ts.SpecialtyID
            LEFT JOIN doctors td ON r.TargetDoctorID = td.DoctorID
            JOIN patients p ON r.PatientID = p.PatientID
            WHERE r.ReferringDoctorID = :docId
            ORDER BY r.CreatedAt DESC
        ");
        $stmt->execute([':docId' => $doctorId]);
        return $stmt->fetchAll();
    }

    public function getIncoming(int $doctorId, int $specialtyId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   rd.FirstName AS RefDocFirstName, rd.LastName AS RefDocLastName, rd.Specialty AS RefDocSpecialty,
                   ts.SpecialtyName AS TargetSpecialtyName,
                   p.FirstName AS PatientFirstName, p.LastName AS PatientLastName, p.PatientCode
            FROM referrals r
            JOIN doctors rd ON r.ReferringDoctorID = rd.DoctorID
            JOIN specialties ts ON r.TargetSpecialtyID = ts.SpecialtyID
            JOIN patients p ON r.PatientID = p.PatientID
            WHERE (r.TargetDoctorID = :docId OR (r.TargetSpecialtyID = :specId AND r.TargetDoctorID IS NULL))
            ORDER BY r.CreatedAt DESC
        ");
        $stmt->execute([':docId' => $doctorId, ':specId' => $specialtyId]);
        return $stmt->fetchAll();
    }
}
