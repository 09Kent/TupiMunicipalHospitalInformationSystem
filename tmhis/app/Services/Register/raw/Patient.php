<?php
// models/Patient.php

require_once __DIR__ . '/../config/Database.php';

class Patient
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Generate unique Digital Patient Code (e.g. PAT-2026-0001)
     */
    public function generatePatientCode(): string
    {
        $year = date('Y');
        $prefix = "PAT-{$year}-";

        $stmt = $this->db->prepare("
            SELECT PatientCode FROM patients 
            WHERE PatientCode LIKE :prefix 
            ORDER BY PatientID DESC LIMIT 1
        ");
        $stmt->execute(['prefix' => "{$prefix}%"]);
        $last = $stmt->fetchColumn();

        if ($last) {
            $num = (int)substr($last, strlen($prefix));
            $nextNum = str_pad((string)($num + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return "{$prefix}{$nextNum}";
    }

    /**
     * Create a new patient record
     */
    public function create(array $data): int
    {
        $patientCode = $data['PatientCode'] ?? $this->generatePatientCode();
        
        // Auto compute age if not supplied
        $age = $data['Age'] ?? 0;
        if (!empty($data['DateOfBirth'])) {
            $dob = new DateTime($data['DateOfBirth']);
            $today = new DateTime();
            $age = $today->diff($dob)->y;
        }

        $stmt = $this->db->prepare("
            INSERT INTO patients (
                PatientCode, FirstName, MiddleName, LastName, 
                DateOfBirth, Age, Gender, CivilStatus, 
                ContactNumber, Email, Address, BloodType, 
                PatientCategory, Status, RegisteredBy, CreatedAt
            ) VALUES (
                :code, :first, :middle, :last, 
                :dob, :age, :gender, :civil, 
                :phone, :email, :address, :blood, 
                :category, :status, :reg_by, NOW()
            )
        ");

        $stmt->execute([
            'code'     => $patientCode,
            'first'    => trim($data['FirstName']),
            'middle'   => !empty($data['MiddleName']) ? trim($data['MiddleName']) : null,
            'last'     => trim($data['LastName']),
            'dob'      => $data['DateOfBirth'],
            'age'      => $age,
            'gender'   => $data['Gender'],
            'civil'    => $data['CivilStatus'] ?? 'Single',
            'phone'    => trim($data['ContactNumber']),
            'email'    => trim($data['Email']),
            'address'  => trim($data['Address']),
            'blood'    => $data['BloodType'] ?? 'Unknown',
            'category' => $data['PatientCategory'] ?? 'Outpatient',
            'status'   => $data['Status'] ?? 'Active',
            'reg_by'   => $data['RegisteredBy'] ?? 1
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Find patient by ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE PatientID = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find patient by Code (e.g. PAT-2026-0001)
     */
    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE PatientCode = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Update an existing patient record
     */
    public function update(int $id, array $data): bool
    {
        $age = $data['Age'] ?? null;
        if (!empty($data['DateOfBirth'])) {
            $dob = new DateTime($data['DateOfBirth']);
            $today = new DateTime();
            $age = $today->diff($dob)->y;
        }

        $stmt = $this->db->prepare("
            UPDATE patients SET
                FirstName       = :first,
                MiddleName      = :middle,
                LastName        = :last,
                DateOfBirth     = :dob,
                Age             = :age,
                Gender          = :gender,
                CivilStatus     = :civil,
                ContactNumber   = :phone,
                Email           = :email,
                Address         = :address,
                BloodType       = :blood,
                PatientCategory = :category,
                Status          = :status,
                UpdatedAt       = NOW()
            WHERE PatientID = :id
        ");

        return $stmt->execute([
            'id'       => $id,
            'first'    => trim($data['FirstName']),
            'middle'   => !empty($data['MiddleName']) ? trim($data['MiddleName']) : null,
            'last'     => trim($data['LastName']),
            'dob'      => $data['DateOfBirth'],
            'age'      => $age,
            'gender'   => $data['Gender'],
            'civil'    => $data['CivilStatus'] ?? 'Single',
            'phone'    => trim($data['ContactNumber']),
            'email'    => trim($data['Email']),
            'address'  => trim($data['Address']),
            'blood'    => $data['BloodType'] ?? 'Unknown',
            'category' => $data['PatientCategory'] ?? 'Outpatient',
            'status'   => $data['Status'] ?? 'Active'
        ]);
    }

    /**
     * Get paginated patients (10 per page) with search and filters
     */
    public function getPaginated(int $limit = 10, int $offset = 0, string $search = '', string $category = '', string $status = ''): array
    {
        $sql = "
            SELECT p.*, 
                   (SELECT c.ComplaintDescription FROM complaints c WHERE c.PatientID = p.PatientID ORDER BY c.ComplaintID DESC LIMIT 1) AS ComplaintDescription,
                   (SELECT c.Severity FROM complaints c WHERE c.PatientID = p.PatientID ORDER BY c.ComplaintID DESC LIMIT 1) AS ComplaintSeverity,
                   (SELECT doc.LastName FROM appointments a JOIN doctors doc ON doc.DoctorID = a.DoctorID WHERE a.PatientID = p.PatientID ORDER BY a.AppointmentID DESC LIMIT 1) AS DoctorLast,
                   (SELECT doc.FirstName FROM appointments a JOIN doctors doc ON doc.DoctorID = a.DoctorID WHERE a.PatientID = p.PatientID ORDER BY a.AppointmentID DESC LIMIT 1) AS DoctorFirst,
                   (SELECT doc.Specialty FROM appointments a JOIN doctors doc ON doc.DoctorID = a.DoctorID WHERE a.PatientID = p.PatientID ORDER BY a.AppointmentID DESC LIMIT 1) AS DoctorSpecialty,
                   (SELECT bs.SystemName FROM complaints c JOIN complaint_analysis ca ON ca.ComplaintID = c.ComplaintID JOIN body_systems bs ON bs.BodySystemID = ca.BodySystemID WHERE c.PatientID = p.PatientID ORDER BY c.ComplaintID DESC LIMIT 1) AS RelatedSystem,
                   (SELECT bs.Emoji FROM complaints c JOIN complaint_analysis ca ON ca.ComplaintID = c.ComplaintID JOIN body_systems bs ON bs.BodySystemID = ca.BodySystemID WHERE c.PatientID = p.PatientID ORDER BY c.ComplaintID DESC LIMIT 1) AS SystemEmoji,
                   (SELECT q.QueueNumber FROM patient_queue q WHERE q.PatientID = p.PatientID AND q.QueueDate = CURDATE() ORDER BY q.QueueID DESC LIMIT 1) AS QueueNumber,
                   (SELECT q.QueueStatus FROM patient_queue q WHERE q.PatientID = p.PatientID AND q.QueueDate = CURDATE() ORDER BY q.QueueID DESC LIMIT 1) AS QueueStatus
            FROM patients p
            WHERE 1=1
        ";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND (
                p.PatientCode LIKE :search1 
                OR p.FirstName LIKE :search2 
                OR p.LastName LIKE :search3 
                OR CONCAT(p.FirstName, ' ', p.LastName) LIKE :search4
                OR p.ContactNumber LIKE :search5
                OR p.Email LIKE :search6
            )";
            $searchTerm = "%{$search}%";
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
            $params['search5'] = $searchTerm;
            $params['search6'] = $searchTerm;
        }

        if (!empty($category)) {
            $sql .= " AND p.PatientCategory = :category";
            $params['category'] = $category;
        }

        if (!empty($status)) {
            $sql .= " AND p.Status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY p.PatientID DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue(":{$k}", $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total patients matching search & filters for pagination
     */
    public function countTotal(string $search = '', string $category = '', string $status = ''): int
    {
        $sql = "SELECT COUNT(DISTINCT p.PatientID) FROM patients p WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (
                p.PatientCode LIKE :search1 
                OR p.FirstName LIKE :search2 
                OR p.LastName LIKE :search3 
                OR CONCAT(p.FirstName, ' ', p.LastName) LIKE :search4
                OR p.ContactNumber LIKE :search5
                OR p.Email LIKE :search6
            )";
            $searchTerm = "%{$search}%";
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
            $params['search5'] = $searchTerm;
            $params['search6'] = $searchTerm;
        }

        if (!empty($category)) {
            $sql .= " AND p.PatientCategory = :category";
            $params['category'] = $category;
        }

        if (!empty($status)) {
            $sql .= " AND p.Status = :status";
            $params['status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get Complete Full Profile for a Patient (All relations joined)
     */
    public function getFullProfile(int $id): ?array
    {
        $patient = $this->findById($id);
        if (!$patient) return null;

        // Emergency Contact
        $stmtEmg = $this->db->prepare("SELECT * FROM emergency_contacts WHERE PatientID = :id LIMIT 1");
        $stmtEmg->execute(['id' => $id]);
        $patient['EmergencyContact'] = $stmtEmg->fetch() ?: null;

        // Medical History
        $stmtMed = $this->db->prepare("SELECT * FROM medical_histories WHERE PatientID = :id LIMIT 1");
        $stmtMed->execute(['id' => $id]);
        $patient['MedicalHistory'] = $stmtMed->fetch() ?: null;

        // Complaint & Classification
        $stmtComp = $this->db->prepare("
            SELECT c.*, 
                   ca.AnalysisID, ca.RelevanceLevel, ca.ConfidenceLevel, ca.ClinicalNotes,
                   bs.BodySystemID, bs.SystemName, bs.SystemCode, bs.Emoji AS SystemEmoji, bs.Description AS SystemDesc,
                   bl.BodyLocationID, bl.LocationName, bl.LocationCode, bl.SubRegion, bl.FrontBack
            FROM complaints c
            LEFT JOIN complaint_analysis ca ON ca.ComplaintID = c.ComplaintID
            LEFT JOIN body_systems bs ON bs.BodySystemID = ca.BodySystemID
            LEFT JOIN body_locations bl ON bl.BodyLocationID = ca.BodyLocationID
            WHERE c.PatientID = :id
            ORDER BY c.ComplaintID DESC LIMIT 1
        ");
        $stmtComp->execute(['id' => $id]);
        $complaint = $stmtComp->fetch();

        if ($complaint) {
            // Selected Symptoms
            $stmtSym = $this->db->prepare("
                SELECT s.SymptomID, s.SymptomName, s.Description, s.Icon 
                FROM patient_symptoms ps
                JOIN symptoms s ON s.SymptomID = ps.SymptomID
                WHERE ps.ComplaintID = :cid
            ");
            $stmtSym->execute(['cid' => $complaint['ComplaintID']]);
            $complaint['Symptoms'] = $stmtSym->fetchAll();

            // Possible Related Conditions
            $stmtCond = $this->db->prepare("
                SELECT pc.ConditionID, pc.ConditionName, pc.Description 
                FROM complaint_conditions cc
                JOIN possible_conditions pc ON pc.ConditionID = cc.ConditionID
                WHERE cc.ComplaintID = :cid
            ");
            $stmtCond->execute(['cid' => $complaint['ComplaintID']]);
            $complaint['Conditions'] = $stmtCond->fetchAll();
        }
        $patient['Complaint'] = $complaint ?: null;

        // Appointments History
        $stmtApp = $this->db->prepare("
            SELECT a.*, d.FirstName AS DocFirst, d.LastName AS DocLast, d.Specialty, d.Clinic, d.ClinicRoom, d.ProfileImage
            FROM appointments a
            JOIN doctors d ON d.DoctorID = a.DoctorID
            WHERE a.PatientID = :id
            ORDER BY a.AppointmentDate DESC, a.AppointmentTime ASC
        ");
        $stmtApp->execute(['id' => $id]);
        $patient['Appointments'] = $stmtApp->fetchAll();

        // Queue info
        $stmtQ = $this->db->prepare("
            SELECT q.*, d.FirstName AS DocFirst, d.LastName AS DocLast, d.Specialty
            FROM patient_queue q
            JOIN doctors d ON d.DoctorID = q.DoctorID
            WHERE q.PatientID = :id AND q.QueueDate = CURDATE()
            ORDER BY q.QueueID DESC LIMIT 1
        ");
        $stmtQ->execute(['id' => $id]);
        $patient['TodayQueue'] = $stmtQ->fetch() ?: null;

        // Registration History
        $stmtHist = $this->db->prepare("
            SELECT h.*, u.FirstName AS UserFirst, u.LastName AS UserLast
            FROM patient_registration_history h
            LEFT JOIN users u ON u.UserID = h.UserID
            WHERE h.PatientID = :id
            ORDER BY h.CreatedAt DESC
        ");
        $stmtHist->execute(['id' => $id]);
        $patient['History'] = $stmtHist->fetchAll();

        return $patient;
    }
}
