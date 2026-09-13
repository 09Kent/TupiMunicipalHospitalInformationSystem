<?php
// Section/Admin/models/HospitalInfo.php

require_once __DIR__ . '/../config/Database.php';

class HospitalInfo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function get(): array
    {
        $stmt = $this->db->query("SELECT * FROM hospital_info WHERE HospitalInfoID = 1 LIMIT 1");
        $info = $stmt->fetch();
        if (!$info) {
            return [
                'HospitalInfoID' => 1,
                'HospitalName' => 'Tupi Municipal Hospital',
                'HospitalCode' => 'TMH-REG-12',
                'TaxID' => '004-982-114-000',
                'Address' => 'National Highway, Poblacion, Tupi, South Cotabato 9505, Philippines',
                'ContactNumber' => '+63 (083) 228-0001',
                'EmergencyHotline' => '911 / (083) 228-0002',
                'Email' => 'admin@tupihospital.gov.ph',
                'DOHAccreditation' => 'DOH-ACCRED-L1-2026-084',
                'PhilHealthAccreditation' => 'PH-HOSP-1209384',
                'BedCapacity' => 50,
                'MedicalDirector' => 'Dr. Maria Santos, MD, MHA'
            ];
        }
        return $info;
    }

    public function update(array $data): bool
    {
        $sql = "UPDATE hospital_info SET
                HospitalName = :HospitalName,
                HospitalCode = :HospitalCode,
                TaxID = :TaxID,
                Address = :Address,
                ContactNumber = :ContactNumber,
                EmergencyHotline = :EmergencyHotline,
                Email = :Email,
                DOHAccreditation = :DOHAccreditation,
                PhilHealthAccreditation = :PhilHealthAccreditation,
                BedCapacity = :BedCapacity,
                MedicalDirector = :MedicalDirector
                WHERE HospitalInfoID = 1";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':HospitalName' => $data['HospitalName'] ?? 'Tupi Municipal Hospital',
            ':HospitalCode' => $data['HospitalCode'] ?? 'TMH-REG-12',
            ':TaxID' => $data['TaxID'] ?? '',
            ':Address' => $data['Address'] ?? '',
            ':ContactNumber' => $data['ContactNumber'] ?? '',
            ':EmergencyHotline' => $data['EmergencyHotline'] ?? '',
            ':Email' => $data['Email'] ?? '',
            ':DOHAccreditation' => $data['DOHAccreditation'] ?? '',
            ':PhilHealthAccreditation' => $data['PhilHealthAccreditation'] ?? '',
            ':BedCapacity' => (int)($data['BedCapacity'] ?? 50),
            ':MedicalDirector' => $data['MedicalDirector'] ?? ''
        ]);
    }
}
