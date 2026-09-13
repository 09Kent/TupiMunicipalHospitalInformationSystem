<?php
// Doctor/models/ConsultationNote.php

class ConsultationNote
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO consultation_notes (
                PatientID, DoctorID, AppointmentID, Subjective, Objective,
                Assessment, Plan, ClinicalNotes, VitalSigns
            ) VALUES (
                :patient_id, :doctor_id, :appointment_id, :subjective, :objective,
                :assessment, :plan, :clinical_notes, :vitals
            )
        ");

        $stmt->execute([
            ':patient_id'     => $data['patient_id'],
            ':doctor_id'      => $data['doctor_id'],
            ':appointment_id' => $data['appointment_id'] ?? null,
            ':subjective'     => $data['subjective'] ?? null,
            ':objective'      => $data['objective'] ?? null,
            ':assessment'     => $data['assessment'] ?? null,
            ':plan'           => $data['plan'] ?? null,
            ':clinical_notes' => $data['clinical_notes'] ?? ($data['subjective'] . ' ' . $data['assessment']),
            ':vitals'         => isset($data['vitals']) ? (is_array($data['vitals']) ? json_encode($data['vitals']) : $data['vitals']) : null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $noteId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE consultation_notes
            SET Subjective = COALESCE(:subjective, Subjective),
                Objective = COALESCE(:objective, Objective),
                Assessment = COALESCE(:assessment, Assessment),
                Plan = COALESCE(:plan, Plan),
                ClinicalNotes = COALESCE(:clinical_notes, ClinicalNotes),
                VitalSigns = COALESCE(:vitals, VitalSigns),
                UpdatedAt = NOW()
            WHERE NoteID = :note_id
        ");

        return $stmt->execute([
            ':subjective'     => $data['subjective'] ?? null,
            ':objective'      => $data['objective'] ?? null,
            ':assessment'     => $data['assessment'] ?? null,
            ':plan'           => $data['plan'] ?? null,
            ':clinical_notes' => $data['clinical_notes'] ?? null,
            ':vitals'         => isset($data['vitals']) ? (is_array($data['vitals']) ? json_encode($data['vitals']) : $data['vitals']) : null,
            ':note_id'        => $noteId
        ]);
    }

    public function findById(int $noteId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT cn.*, d.FirstName AS DoctorFirstName, d.LastName AS DoctorLastName, d.Specialty,
                   p.FirstName AS PatientFirstName, p.LastName AS PatientLastName, p.PatientCode
            FROM consultation_notes cn
            JOIN doctors d ON cn.DoctorID = d.DoctorID
            JOIN patients p ON cn.PatientID = p.PatientID
            WHERE cn.NoteID = :note_id
            LIMIT 1
        ");
        $stmt->execute([':note_id' => $noteId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT cn.*, d.FirstName AS DoctorFirstName, d.LastName AS DoctorLastName, d.Specialty, d.Title
            FROM consultation_notes cn
            JOIN doctors d ON cn.DoctorID = d.DoctorID
            WHERE cn.PatientID = :patient_id
            ORDER BY cn.CreatedAt DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function findByAppointment(int $appointmentId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT cn.*, d.FirstName AS DoctorFirstName, d.LastName AS DoctorLastName, d.Specialty
            FROM consultation_notes cn
            JOIN doctors d ON cn.DoctorID = d.DoctorID
            WHERE cn.AppointmentID = :appointment_id
            ORDER BY cn.CreatedAt DESC
            LIMIT 1
        ");
        $stmt->execute([':appointment_id' => $appointmentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAll(int $doctorId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT cn.*, p.PatientCode, p.FirstName AS PatientFirstName, p.LastName AS PatientLastName,
                   p.Age, p.Gender
            FROM consultation_notes cn
            JOIN patients p ON cn.PatientID = p.PatientID
            WHERE cn.DoctorID = :docId
            ORDER BY cn.CreatedAt DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':docId', $doctorId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
