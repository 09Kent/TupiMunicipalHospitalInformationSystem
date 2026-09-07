<?php
// Doctor/models/TreatmentPlan.php

class TreatmentPlan
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO treatment_plans (
                PatientID, DoctorID, AppointmentID, DiagnosisID, Goal,
                LifestyleRecommendations, MedicationPlan, FollowUpSchedule,
                FollowUpDate, Status, Notes
            ) VALUES (
                :patient_id, :doctor_id, :appointment_id, :diagnosis_id, :goal,
                :lifestyle, :medication_plan, :follow_up_sched,
                :follow_up_date, :status, :notes
            )
        ");

        $stmt->execute([
            ':patient_id'        => $data['patient_id'],
            ':doctor_id'         => $data['doctor_id'],
            ':appointment_id'    => $data['appointment_id'] ?? null,
            ':diagnosis_id'      => $data['diagnosis_id'] ?? null,
            ':goal'              => $data['goal'],
            ':lifestyle'         => $data['lifestyle_recommendations'] ?? null,
            ':medication_plan'   => $data['medication_plan'] ?? null,
            ':follow_up_sched'   => $data['follow_up_schedule'] ?? null,
            ':follow_up_date'    => !empty($data['follow_up_date']) ? $data['follow_up_date'] : null,
            ':status'            => $data['status'] ?? 'Active',
            ':notes'             => $data['notes'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $planId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE treatment_plans
            SET Goal = COALESCE(:goal, Goal),
                LifestyleRecommendations = COALESCE(:lifestyle, LifestyleRecommendations),
                MedicationPlan = COALESCE(:medication_plan, MedicationPlan),
                FollowUpSchedule = COALESCE(:follow_up_sched, FollowUpSchedule),
                FollowUpDate = COALESCE(:follow_up_date, FollowUpDate),
                Status = COALESCE(:status, Status),
                Notes = COALESCE(:notes, Notes),
                UpdatedAt = NOW()
            WHERE PlanID = :plan_id
        ");

        return $stmt->execute([
            ':goal'             => $data['goal'] ?? null,
            ':lifestyle'        => $data['lifestyle_recommendations'] ?? null,
            ':medication_plan'  => $data['medication_plan'] ?? null,
            ':follow_up_sched'  => $data['follow_up_schedule'] ?? null,
            ':follow_up_date'   => !empty($data['follow_up_date']) ? $data['follow_up_date'] : null,
            ':status'           => $data['status'] ?? null,
            ':notes'            => $data['notes'] ?? null,
            ':plan_id'          => $planId
        ]);
    }

    public function findById(int $planId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT tp.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty,
                   p.FirstName AS PatientFirstName, p.LastName AS PatientLastName, p.PatientCode,
                   d.DiagnosisName
            FROM treatment_plans tp
            JOIN doctors doc ON tp.DoctorID = doc.DoctorID
            JOIN patients p ON tp.PatientID = p.PatientID
            LEFT JOIN diagnoses d ON tp.DiagnosisID = d.DiagnosisID
            WHERE tp.PlanID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $planId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("
            SELECT tp.*, doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName, doc.Specialty,
                   d.DiagnosisName
            FROM treatment_plans tp
            JOIN doctors doc ON tp.DoctorID = doc.DoctorID
            LEFT JOIN diagnoses d ON tp.DiagnosisID = d.DiagnosisID
            WHERE tp.PatientID = :patient_id
            ORDER BY tp.CreatedAt DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }
}
