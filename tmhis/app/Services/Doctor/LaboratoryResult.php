<?php
// Doctor/models/LaboratoryResult.php

class LaboratoryResult
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO laboratory_results (
                RequestID, PatientID, DoctorID, TestName, ResultValue,
                NormalRange, Units, Interpretation, Notes, AttachmentPath, ResultDate
            ) VALUES (
                :request_id, :patient_id, :doctor_id, :test_name, :result_value,
                :normal_range, :units, :interpretation, :notes, :attachment, :result_date
            )
        ");

        $stmt->execute([
            ':request_id'     => $data['request_id'],
            ':patient_id'     => $data['patient_id'],
            ':doctor_id'      => $data['doctor_id'],
            ':test_name'      => $data['test_name'],
            ':result_value'   => $data['result_value'],
            ':normal_range'   => $data['normal_range'],
            ':units'          => $data['units'] ?? '',
            ':interpretation' => $data['interpretation'] ?? 'Normal',
            ':notes'          => $data['notes'] ?? null,
            ':attachment'     => $data['attachment_path'] ?? null,
            ':result_date'    => $data['result_date'] ?? date('Y-m-d')
        ]);

        $resultId = (int)$this->db->lastInsertId();

        // Also update laboratory_requests status to Completed
        $stmtReq = $this->db->prepare("UPDATE laboratory_requests SET Status = 'Completed' WHERE RequestID = :reqId");
        $stmtReq->execute([':reqId' => $data['request_id']]);

        return $resultId;
    }

    public function findByRequest(int $requestId): array
    {
        $stmt = $this->db->prepare("
            SELECT res.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName
            FROM laboratory_results res
            JOIN doctors doc ON res.DoctorID = doc.DoctorID
            WHERE res.RequestID = :req_id
            ORDER BY res.CreatedAt ASC
        ");
        $stmt->execute([':req_id' => $requestId]);
        return $stmt->fetchAll();
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT res.*, req.RequestCode, req.TestType,
                   doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty
            FROM laboratory_results res
            JOIN laboratory_requests req ON res.RequestID = req.RequestID
            JOIN doctors doc ON res.DoctorID = doc.DoctorID
            WHERE res.PatientID = :patient_id
            ORDER BY res.ResultDate DESC, res.CreatedAt DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }
}
