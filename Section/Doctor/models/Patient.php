<?php
// Doctor/models/Patient.php

class Patient
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Get paginated patient list for doctor (Assigned to doctor OR matching doctor's specialty/body system)
     */
    public function getDoctorPatients(int $doctorId, int $limit = 10, int $offset = 0, array $filters = [], ?int $bodySystemId = null): array
    {
        // If bodySystemId not provided, get the doctor's body systems and specialty
        if ($bodySystemId === null) {
            $stmtDoc = $this->db->prepare("
                SELECT d.DoctorID, d.SpecialtyID, s.BodySystemID
                FROM doctors d
                LEFT JOIN specialties s ON d.SpecialtyID = s.SpecialtyID
                WHERE d.DoctorID = :docId
            ");
            $stmtDoc->execute([':docId' => $doctorId]);
            $docInfo = $stmtDoc->fetch();
            $bodySystemId = $docInfo['BodySystemID'] ?? null;
        }

        $sql = "
            SELECT DISTINCT p.PatientID, p.PatientCode, p.FirstName, p.MiddleName, p.LastName,
                   p.DateOfBirth, p.Age, p.Gender, p.ContactNumber, p.Email, p.BloodType, p.Status,
                   p.CreatedAt AS RegisteredDate,
                   c.ComplaintID, c.ComplaintDescription, c.Severity, c.Duration,
                   bs.BodySystemID, bs.SystemName, bs.Icon AS SystemIcon, bs.Emoji AS SystemEmoji,
                   bl.LocationName, bl.SubRegion,
                   a.AppointmentID, a.AppointmentDate, a.AppointmentTime, a.Status AS AppointmentStatus, a.Priority AS AppointmentPriority,
                   pq.QueueNumber, pq.QueueStatus, pq.QueueDate
            FROM patients p
            LEFT JOIN complaints c ON p.PatientID = c.PatientID
            LEFT JOIN complaint_analysis ca ON c.ComplaintID = ca.ComplaintID
            LEFT JOIN body_systems bs ON ca.BodySystemID = bs.BodySystemID
            LEFT JOIN body_locations bl ON ca.BodyLocationID = bl.BodyLocationID
            LEFT JOIN appointments a ON p.PatientID = a.PatientID
            LEFT JOIN patient_queue pq ON a.AppointmentID = pq.AppointmentID
            WHERE (a.DoctorID = :docId OR pq.DoctorID = :docId2" . ($bodySystemId ? " OR ca.BodySystemID = :bsId" : "") . ")
        ";

        $params = [
            ':docId'  => $doctorId,
            ':docId2' => $doctorId
        ];
        if ($bodySystemId) {
            $params[':bsId'] = $bodySystemId;
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.FirstName LIKE :search1 OR p.LastName LIKE :search2 OR p.PatientCode LIKE :search3 OR c.ComplaintDescription LIKE :search4)";
            $term = '%' . $filters['search'] . '%';
            $params[':search1'] = $term;
            $params[':search2'] = $term;
            $params[':search3'] = $term;
            $params[':search4'] = $term;
        }

        if (!empty($filters['status'])) {
            $sql .= " AND (a.Status = :status1 OR pq.QueueStatus = :status2 OR p.Status = :status3)";
            $params[':status1'] = $filters['status'];
            $params[':status2'] = $filters['status'];
            $params[':status3'] = $filters['status'];
        }

        if (!empty($filters['date'])) {
            $sql .= " AND (a.AppointmentDate = :date1 OR DATE(p.CreatedAt) = :date2)";
            $params[':date1'] = $filters['date'];
            $params[':date2'] = $filters['date'];
        }

        $sql .= " ORDER BY a.AppointmentDate DESC, a.AppointmentTime ASC, p.PatientID DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count total patients for doctor pagination
     */
    public function countDoctorPatients(int $doctorId, array $filters = [], ?int $bodySystemId = null): int
    {
        if ($bodySystemId === null) {
            $stmtDoc = $this->db->prepare("
                SELECT d.DoctorID, d.SpecialtyID, s.BodySystemID
                FROM doctors d
                LEFT JOIN specialties s ON d.SpecialtyID = s.SpecialtyID
                WHERE d.DoctorID = :docId
            ");
            $stmtDoc->execute([':docId' => $doctorId]);
            $docInfo = $stmtDoc->fetch();
            $bodySystemId = $docInfo['BodySystemID'] ?? null;
        }

        $sql = "
            SELECT COUNT(DISTINCT p.PatientID)
            FROM patients p
            LEFT JOIN complaints c ON p.PatientID = c.PatientID
            LEFT JOIN complaint_analysis ca ON c.ComplaintID = ca.ComplaintID
            LEFT JOIN appointments a ON p.PatientID = a.PatientID
            LEFT JOIN patient_queue pq ON a.AppointmentID = pq.AppointmentID
            WHERE (a.DoctorID = :docId OR pq.DoctorID = :docId2" . ($bodySystemId ? " OR ca.BodySystemID = :bsId" : "") . ")
        ";

        $params = [
            ':docId'  => $doctorId,
            ':docId2' => $doctorId
        ];
        if ($bodySystemId) {
            $params[':bsId'] = $bodySystemId;
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.FirstName LIKE :search1 OR p.LastName LIKE :search2 OR p.PatientCode LIKE :search3)";
            $term = '%' . $filters['search'] . '%';
            $params[':search1'] = $term;
            $params[':search2'] = $term;
            $params[':search3'] = $term;
        }

        if (!empty($filters['status'])) {
            $sql .= " AND (a.Status = :status1 OR pq.QueueStatus = :status2 OR p.Status = :status3)";
            $params[':status1'] = $filters['status'];
            $params[':status2'] = $filters['status'];
            $params[':status3'] = $filters['status'];
        }

        if (!empty($filters['date'])) {
            $sql .= " AND (a.AppointmentDate = :date1 OR DATE(p.CreatedAt) = :date2)";
            $params[':date1'] = $filters['date'];
            $params[':date2'] = $filters['date'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retrieve complete patient profile from Registrator intake and medical history
     */
    public function getFullProfile(int $patientId): ?array
    {
        // 1. Core Patient Info
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE PatientID = :id LIMIT 1");
        $stmt->execute([':id' => $patientId]);
        $patient = $stmt->fetch();
        if (!$patient) {
            return null;
        }

        // 2. Emergency Contact
        $stmtEmg = $this->db->prepare("SELECT * FROM emergency_contacts WHERE PatientID = :id LIMIT 1");
        $stmtEmg->execute([':id' => $patientId]);
        $patient['EmergencyContact'] = $stmtEmg->fetch() ?: null;

        // 3. Medical History
        $stmtMed = $this->db->prepare("SELECT * FROM medical_histories WHERE PatientID = :id LIMIT 1");
        $stmtMed->execute([':id' => $patientId]);
        $patient['MedicalHistory'] = $stmtMed->fetch() ?: null;

        // 4. Complaint
        $stmtCmp = $this->db->prepare("SELECT * FROM complaints WHERE PatientID = :id ORDER BY CreatedAt DESC LIMIT 1");
        $stmtCmp->execute([':id' => $patientId]);
        $complaint = $stmtCmp->fetch();
        $patient['Complaint'] = $complaint ?: null;

        if ($complaint) {
            // Symptoms
            $stmtSym = $this->db->prepare("
                SELECT s.SymptomName, s.Icon
                FROM patient_symptoms ps
                JOIN symptoms s ON ps.SymptomID = s.SymptomID
                WHERE ps.ComplaintID = :cid
            ");
            $stmtSym->execute([':cid' => $complaint['ComplaintID']]);
            $patient['Symptoms'] = $stmtSym->fetchAll();

            // Complaint Analysis & Body System
            $stmtAna = $this->db->prepare("
                SELECT ca.*, bs.SystemName, bs.SystemCode, bs.Icon AS SystemIcon, bs.Emoji AS SystemEmoji,
                       bl.LocationName, bl.LocationCode, bl.FrontBack, bl.SubRegion
                FROM complaint_analysis ca
                JOIN body_systems bs ON ca.BodySystemID = bs.BodySystemID
                LEFT JOIN body_locations bl ON ca.BodyLocationID = bl.BodyLocationID
                WHERE ca.ComplaintID = :cid
                LIMIT 1
            ");
            $stmtAna->execute([':cid' => $complaint['ComplaintID']]);
            $patient['Analysis'] = $stmtAna->fetch() ?: null;

            // Possible Conditions
            $stmtCond = $this->db->prepare("
                SELECT pc.ConditionName, pc.Description
                FROM complaint_conditions cc
                JOIN possible_conditions pc ON cc.ConditionID = pc.ConditionID
                WHERE cc.ComplaintID = :cid
            ");
            $stmtCond->execute([':cid' => $complaint['ComplaintID']]);
            $patient['PossibleConditions'] = $stmtCond->fetchAll();
        } else {
            $patient['Symptoms'] = [];
            $patient['Analysis'] = null;
            $patient['PossibleConditions'] = [];
        }

        // 5. Appointments & Assigned Doctor
        $stmtApp = $this->db->prepare("
            SELECT a.*, d.FirstName AS DoctorFirstName, d.LastName AS DoctorLastName, d.Title AS DoctorTitle,
                   d.Specialty, d.ClinicRoom, pq.QueueNumber, pq.QueueStatus
            FROM appointments a
            LEFT JOIN doctors d ON a.DoctorID = d.DoctorID
            LEFT JOIN patient_queue pq ON a.AppointmentID = pq.AppointmentID
            WHERE a.PatientID = :id
            ORDER BY a.AppointmentDate DESC, a.AppointmentTime DESC
            LIMIT 1
        ");
        $stmtApp->execute([':id' => $patientId]);
        $patient['Appointment'] = $stmtApp->fetch() ?: null;

        // 6. Allergies
        $stmtAllg = $this->db->prepare("SELECT * FROM allergy_records WHERE PatientID = :id ORDER BY CreatedAt DESC");
        $stmtAllg->execute([':id' => $patientId]);
        $patient['Allergies'] = $stmtAllg->fetchAll();

        // 7. Recent Prescriptions
        $stmtRx = $this->db->prepare("SELECT * FROM prescriptions WHERE PatientID = :id ORDER BY IssuedDate DESC LIMIT 5");
        $stmtRx->execute([':id' => $patientId]);
        $patient['Prescriptions'] = $stmtRx->fetchAll();

        // 8. Recent Labs
        $stmtLab = $this->db->prepare("SELECT * FROM laboratory_requests WHERE PatientID = :id ORDER BY RequestedDate DESC LIMIT 5");
        $stmtLab->execute([':id' => $patientId]);
        $patient['LaboratoryRequests'] = $stmtLab->fetchAll();

        return $patient;
    }

    /**
     * Get Today's Queue List for Doctor
     */
    public function getTodayQueue(int $doctorId): array
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT pq.*, a.AppointmentTime, a.Reason, a.Priority AS AppPriority, a.Status AS AppStatus,
                   p.PatientCode, p.FirstName, p.LastName, p.Age, p.Gender, p.BloodType,
                   c.ComplaintDescription, bs.SystemName, bs.SystemCode
            FROM patient_queue pq
            JOIN appointments a ON pq.AppointmentID = a.AppointmentID
            JOIN patients p ON pq.PatientID = p.PatientID
            LEFT JOIN complaints c ON p.PatientID = c.PatientID
            LEFT JOIN complaint_analysis ca ON c.ComplaintID = ca.ComplaintID
            LEFT JOIN body_systems bs ON ca.BodySystemID = bs.BodySystemID
            WHERE (pq.DoctorID = :docId OR a.DoctorID = :docId2)
            AND pq.QueueDate = :today
            ORDER BY 
                CASE pq.Priority WHEN 'Emergency' THEN 1 WHEN 'Priority' THEN 2 ELSE 3 END,
                pq.QueueNumber ASC
        ");
        $stmt->execute([':docId' => $doctorId, ':docId2' => $doctorId, ':today' => $today]);
        return $stmt->fetchAll();
    }

    /**
     * Check if a doctor has authorization to access a specific patient's confidential records
     */
    public function hasDoctorAccess(int $doctorId, int $patientId): bool
    {
        if ($doctorId <= 0 || $patientId <= 0) {
            return false;
        }

        // Get doctor's specialty and body system ID
        $stmtDoc = $this->db->prepare("
            SELECT d.DoctorID, d.SpecialtyID, s.BodySystemID
            FROM doctors d
            LEFT JOIN specialties s ON d.SpecialtyID = s.SpecialtyID
            WHERE d.DoctorID = :docId
        ");
        $stmtDoc->execute([':docId' => $doctorId]);
        $docInfo = $stmtDoc->fetch();
        $bodySystemId = $docInfo['BodySystemID'] ?? null;
        $specialtyId = $docInfo['SpecialtyID'] ?? null;

        $sql = "
            SELECT 1
            FROM patients p
            LEFT JOIN appointments a ON p.PatientID = a.PatientID
            LEFT JOIN patient_queue pq ON a.AppointmentID = pq.AppointmentID
            LEFT JOIN complaints c ON p.PatientID = c.PatientID
            LEFT JOIN complaint_analysis ca ON c.ComplaintID = ca.ComplaintID
            LEFT JOIN referrals r ON p.PatientID = r.PatientID
            LEFT JOIN consultation_notes cn ON p.PatientID = cn.PatientID
            LEFT JOIN diagnoses diag ON p.PatientID = diag.PatientID
            LEFT JOIN prescriptions pr ON p.PatientID = pr.PatientID
            LEFT JOIN laboratory_requests lr ON p.PatientID = lr.PatientID
            LEFT JOIN medical_certificates mc ON p.PatientID = mc.PatientID
            WHERE p.PatientID = :patientId
            AND (
                a.DoctorID = :docId1
                OR pq.DoctorID = :docId2
                OR cn.DoctorID = :docId3
                OR diag.DoctorID = :docId4
                OR pr.DoctorID = :docId5
                OR lr.DoctorID = :docId6
                OR mc.DoctorID = :docId7
                OR r.ReferringDoctorID = :docId8
                OR r.TargetDoctorID = :docId9
                " . ($specialtyId ? " OR (r.TargetSpecialtyID = :specId AND r.TargetDoctorID IS NULL)" : "") . "
                " . ($bodySystemId ? " OR ca.BodySystemID = :bsId" : "") . "
            )
            LIMIT 1
        ";

        $params = [
            ':patientId' => $patientId,
            ':docId1'    => $doctorId,
            ':docId2'    => $doctorId,
            ':docId3'    => $doctorId,
            ':docId4'    => $doctorId,
            ':docId5'    => $doctorId,
            ':docId6'    => $doctorId,
            ':docId7'    => $doctorId,
            ':docId8'    => $doctorId,
            ':docId9'    => $doctorId,
        ];
        if ($specialtyId) {
            $params[':specId'] = $specialtyId;
        }
        if ($bodySystemId) {
            $params[':bsId'] = $bodySystemId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetchColumn();
    }
}
