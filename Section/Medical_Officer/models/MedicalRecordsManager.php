<?php
// Section/Medical_Officer/models/MedicalRecordsManager.php

require_once __DIR__ . '/../config/Database.php';

class MedicalRecordsManager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllPatients(): array
    {
        $sql = "SELECT p.*, mh.Allergies, mh.ExistingConditions, mh.CurrentMedications,
                       ec.ContactName as EmergencyContactName, ec.Relationship, ec.ContactNumber as EmergencyContactPhone
                FROM patients p
                LEFT JOIN medical_histories mh ON p.PatientID = mh.PatientID
                LEFT JOIN emergency_contacts ec ON p.PatientID = ec.PatientID
                ORDER BY p.PatientID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getPatientFullHistory(int $patientId): array
    {
        $stmtPatient = $this->db->prepare("SELECT * FROM patients WHERE PatientID = :id");
        $stmtPatient->execute([':id' => $patientId]);
        $patient = $stmtPatient->fetch();

        if (!$patient) return [];

        // Consultations
        $stmtNotes = $this->db->prepare("SELECT cn.*, d.FirstName as DocFirst, d.LastName as DocLast, d.Specialty 
                                         FROM consultation_notes cn
                                         JOIN doctors d ON cn.DoctorID = d.DoctorID
                                         WHERE cn.PatientID = :id ORDER BY cn.NoteID DESC");
        $stmtNotes->execute([':id' => $patientId]);
        $notes = $stmtNotes->fetchAll();

        // Diagnoses
        $stmtDiag = $this->db->prepare("SELECT * FROM diagnoses WHERE PatientID = :id ORDER BY DiagnosisID DESC");
        $stmtDiag->execute([':id' => $patientId]);
        $diagnoses = $stmtDiag->fetchAll();

        // Prescriptions
        $stmtRx = $this->db->prepare("SELECT rx.*, d.FirstName as DocFirst, d.LastName as DocLast 
                                      FROM prescriptions rx
                                      JOIN doctors d ON rx.DoctorID = d.DoctorID
                                      WHERE rx.PatientID = :id ORDER BY rx.PrescriptionID DESC");
        $stmtRx->execute([':id' => $patientId]);
        $prescriptions = $stmtRx->fetchAll();

        // Laboratory
        $stmtLab = $this->db->prepare("SELECT * FROM laboratory_results WHERE PatientID = :id ORDER BY ResultID DESC");
        $stmtLab->execute([':id' => $patientId]);
        $labs = $stmtLab->fetchAll();

        return [
            'patient' => $patient,
            'consultation_notes' => $notes,
            'diagnoses' => $diagnoses,
            'prescriptions' => $prescriptions,
            'laboratory_results' => $labs
        ];
    }

    public function getReleaseRequests(): array
    {
        $sql = "SELECT r.*, p.FirstName, p.LastName, p.PatientCode 
                FROM record_release_requests r
                JOIN patients p ON r.PatientID = p.PatientID
                ORDER BY r.RequestID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function archivePatient(int $patientId): bool
    {
        $stmt = $this->db->prepare("UPDATE patients SET Status = 'Inactive' WHERE PatientID = :id");
        return $stmt->execute([':id' => $patientId]);
    }
}
