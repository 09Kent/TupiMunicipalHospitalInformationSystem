<?php

namespace App\Services\Register;

use PDO;
use Exception;
use DateTime;

// models/MedicalHistory.php



class MedicalHistory
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? \Illuminate\Support\Facades\DB::connection()->getPdo();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO medical_histories (
                PatientID, Allergies, ExistingConditions, 
                CurrentMedications, PreviousHospitalization, CreatedAt
            ) VALUES (
                :pid, :allergies, :conditions, 
                :medications, :hospitalization, NOW()
            )
        ");

        $stmt->execute([
            'pid'             => $data['PatientID'],
            'allergies'       => !empty($data['Allergies']) ? trim($data['Allergies']) : 'None reported',
            'conditions'      => !empty($data['ExistingConditions']) ? trim($data['ExistingConditions']) : 'None reported',
            'medications'     => !empty($data['CurrentMedications']) ? trim($data['CurrentMedications']) : 'None reported',
            'hospitalization' => !empty($data['PreviousHospitalization']) ? trim($data['PreviousHospitalization']) : 'None reported'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getByPatientId(int $patientId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM medical_histories WHERE PatientID = :pid LIMIT 1");
        $stmt->execute(['pid' => $patientId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function update(int $patientId, array $data): bool
    {
        $existing = $this->getByPatientId($patientId);
        if (!$existing) {
            $data['PatientID'] = $patientId;
            return (bool)$this->create($data);
        }

        $stmt = $this->db->prepare("
            UPDATE medical_histories SET
                Allergies               = :allergies,
                ExistingConditions      = :conditions,
                CurrentMedications      = :medications,
                PreviousHospitalization = :hospitalization,
                UpdatedAt               = NOW()
            WHERE PatientID = :pid
        ");

        return $stmt->execute([
            'pid'             => $patientId,
            'allergies'       => !empty($data['Allergies']) ? trim($data['Allergies']) : 'None reported',
            'conditions'      => !empty($data['ExistingConditions']) ? trim($data['ExistingConditions']) : 'None reported',
            'medications'     => !empty($data['CurrentMedications']) ? trim($data['CurrentMedications']) : 'None reported',
            'hospitalization' => !empty($data['PreviousHospitalization']) ? trim($data['PreviousHospitalization']) : 'None reported'
        ]);
    }
}
