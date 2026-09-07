<?php
// Section/Nurse/models/VitalSign.php

require_once __DIR__ . '/../config/Database.php';

class VitalSign
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM patient_vitals WHERE PatientID = :patientId ORDER BY VitalID DESC");
        $stmt->execute([':patientId' => $patientId]);
        return $stmt->fetchAll();
    }

    public function getLatestAll(): array
    {
        $sql = "SELECT pv.*, p.FirstName, p.LastName, p.PatientCode, p.DateOfBirth, p.Age, p.Gender
                FROM patient_vitals pv
                INNER JOIN (
                    SELECT PatientID, MAX(VitalID) as MaxVitalID
                    FROM patient_vitals
                    GROUP BY PatientID
                ) latest ON pv.VitalID = latest.MaxVitalID
                JOIN patients p ON pv.PatientID = p.PatientID
                ORDER BY pv.CreatedAt DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO patient_vitals (PatientID, BloodPressure, HeartRate, RespiratoryRate, Temperature, OxygenSaturation, PainScale, WeightKg, HeightCm, BMI, ClinicalNotes, RecordedByName)
                VALUES (:PatientID, :BloodPressure, :HeartRate, :RespiratoryRate, :Temperature, :OxygenSaturation, :PainScale, :WeightKg, :HeightCm, :BMI, :ClinicalNotes, :RecordedByName)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':PatientID' => (int)$data['PatientID'],
            ':BloodPressure' => $data['BloodPressure'],
            ':HeartRate' => (int)($data['HeartRate'] ?? 75),
            ':RespiratoryRate' => (int)($data['RespiratoryRate'] ?? 16),
            ':Temperature' => (float)($data['Temperature'] ?? 36.5),
            ':OxygenSaturation' => (int)($data['OxygenSaturation'] ?? 98),
            ':PainScale' => (int)($data['PainScale'] ?? 0),
            ':WeightKg' => !empty($data['WeightKg']) ? (float)$data['WeightKg'] : null,
            ':HeightCm' => !empty($data['HeightCm']) ? (float)$data['HeightCm'] : null,
            ':BMI' => !empty($data['BMI']) ? (float)$data['BMI'] : null,
            ':ClinicalNotes' => $data['ClinicalNotes'] ?? null,
            ':RecordedByName' => $data['RecordedByName'] ?? 'Nurse Elena Gomez, RN'
        ]);
        return (int)$this->db->lastInsertId();
    }
}
