<?php
// Doctor/models/LaboratoryRequest.php

class LaboratoryRequest
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function generateCode(): string
    {
        $year = date('Y');
        $stmt = $this->db->query("SELECT MAX(RequestID) AS max_id FROM laboratory_requests");
        $next = ((int)($stmt->fetchColumn() ?: 0)) + 1;
        return sprintf("LAB-%s-%04d", $year, $next);
    }

    public function create(array $data): int
    {
        $code = $data['request_code'] ?? $this->generateCode();

        $stmt = $this->db->prepare("
            INSERT INTO laboratory_requests (
                RequestCode, PatientID, DoctorID, AppointmentID,
                TestType, Priority, ClinicalNotes, Status, RequestedDate
            ) VALUES (
                :code, :patient_id, :doctor_id, :appointment_id,
                :test_type, :priority, :notes, :status, :requested_date
            )
        ");

        $stmt->execute([
            ':code'          => $code,
            ':patient_id'    => $data['patient_id'],
            ':doctor_id'     => $data['doctor_id'],
            ':appointment_id'=> $data['appointment_id'] ?? null,
            ':test_type'     => $data['test_type'],
            ':priority'      => $data['priority'] ?? 'Routine',
            ':notes'         => $data['clinical_notes'] ?? null,
            ':status'        => $data['status'] ?? 'Pending',
            ':requested_date'=> $data['requested_date'] ?? date('Y-m-d')
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $requestId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE laboratory_requests SET Status = :status WHERE RequestID = :id");
        return $stmt->execute([':status' => $status, ':id' => $requestId]);
    }

    public function findById(int $requestId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT lr.*, 
                   doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty,
                   p.FirstName AS PatientFirstName, p.MiddleName, p.LastName AS PatientLastName, p.PatientCode,
                   p.Age, p.Gender, p.ContactNumber
            FROM laboratory_requests lr
            JOIN doctors doc ON lr.DoctorID = doc.DoctorID
            JOIN patients p ON lr.PatientID = p.PatientID
            WHERE lr.RequestID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $requestId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT lr.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty,
                   (SELECT COUNT(*) FROM laboratory_results WHERE RequestID = lr.RequestID) AS ResultsCount
            FROM laboratory_requests lr
            JOIN doctors doc ON lr.DoctorID = doc.DoctorID
            WHERE lr.PatientID = :patient_id
            ORDER BY lr.RequestedDate DESC, lr.CreatedAt DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function getAll(int $doctorId, int $limit = 50, string $status = ''): array
    {
        $sql = "
            SELECT lr.*, p.PatientCode, p.FirstName AS PatientFirstName, p.LastName AS PatientLastName,
                   p.Age, p.Gender,
                   (SELECT COUNT(*) FROM laboratory_results WHERE RequestID = lr.RequestID) AS ResultsCount
            FROM laboratory_requests lr
            JOIN patients p ON lr.PatientID = p.PatientID
            WHERE lr.DoctorID = :docId
        ";
        $params = [':docId' => $doctorId];

        if (!empty($status)) {
            $sql .= " AND lr.Status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY lr.RequestedDate DESC, lr.CreatedAt DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPendingCount(int $doctorId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM laboratory_requests 
            WHERE DoctorID = :docId AND Status IN ('Pending', 'In Progress', 'Sample Collected')
        ");
        $stmt->execute([':docId' => $doctorId]);
        return (int)$stmt->fetchColumn();
    }
}
