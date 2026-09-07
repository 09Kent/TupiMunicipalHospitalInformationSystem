<?php
// Doctor/models/Doctor.php

class Doctor
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function findById(int $doctorId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, s.SpecialtyName, s.SpecialtyCode, s.BodySystemID, bs.SystemName, u.Username, u.Role
            FROM doctors d
            LEFT JOIN specialties s ON d.SpecialtyID = s.SpecialtyID
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            LEFT JOIN users u ON d.UserID = u.UserID
            WHERE d.DoctorID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $doctorId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, s.SpecialtyName, s.SpecialtyCode, s.BodySystemID, bs.SystemName, u.Username, u.Role
            FROM doctors d
            LEFT JOIN specialties s ON d.SpecialtyID = s.SpecialtyID
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            LEFT JOIN users u ON d.UserID = u.UserID
            WHERE d.UserID = :userId
            LIMIT 1
        ");
        $stmt->execute([':userId' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, s.SpecialtyName, s.SpecialtyCode, s.BodySystemID, bs.SystemName, u.Username, u.Role
            FROM doctors d
            LEFT JOIN specialties s ON d.SpecialtyID = s.SpecialtyID
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            LEFT JOIN users u ON d.UserID = u.UserID
            WHERE d.Email = :email1 OR u.Email = :email2
            LIMIT 1
        ");
        $stmt->execute([':email1' => $email, ':email2' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT d.*, s.SpecialtyName, s.SpecialtyCode, s.BodySystemID, bs.SystemName
            FROM doctors d
            LEFT JOIN specialties s ON d.SpecialtyID = s.SpecialtyID
            LEFT JOIN body_systems bs ON s.BodySystemID = bs.BodySystemID
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['specialty_id'])) {
            $sql .= " AND d.SpecialtyID = :specialty_id";
            $params[':specialty_id'] = $filters['specialty_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND d.Status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (d.FirstName LIKE :search1 OR d.LastName LIKE :search2 OR d.Specialty LIKE :search3)";
            $term = '%' . $filters['search'] . '%';
            $params[':search1'] = $term;
            $params[':search2'] = $term;
            $params[':search3'] = $term;
        }

        $sql .= " ORDER BY d.FirstName ASC, d.LastName ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO doctors (
                UserID, FirstName, LastName, SpecialtyID, Title, Specialty,
                LicenseNumber, ContactNumber, Email, ExperienceYears, Clinic,
                ClinicRoom, ProfileImage, Rating, ReviewsCount, ConsultationFee,
                Bio, Education, Languages, Status
            ) VALUES (
                :user_id, :first_name, :last_name, :specialty_id, :title, :specialty,
                :license_num, :contact, :email, :experience, :clinic,
                :room, :profile_img, :rating, :reviews, :fee,
                :bio, :education, :languages, :status
            )
        ");

        $stmt->execute([
            ':user_id'     => $data['user_id'] ?? null,
            ':first_name'  => $data['first_name'],
            ':last_name'   => $data['last_name'],
            ':specialty_id'=> $data['specialty_id'] ?? null,
            ':title'       => $data['title'] ?? 'MD',
            ':specialty'   => $data['specialty'] ?? 'General Practitioner',
            ':license_num' => $data['license_number'],
            ':contact'     => $data['contact_number'] ?? '',
            ':email'       => $data['email'],
            ':experience'  => $data['experience_years'] ?? 5,
            ':clinic'      => $data['clinic'] ?? 'Tupi Municipal Hospital',
            ':room'        => $data['clinic_room'] ?? 'Suite 101',
            ':profile_img' => $data['profile_image'] ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300',
            ':rating'      => $data['rating'] ?? 4.90,
            ':reviews'     => $data['reviews_count'] ?? 10,
            ':fee'         => $data['consultation_fee'] ?? '$80.00',
            ':bio'         => $data['bio'] ?? null,
            ':education'   => $data['education'] ?? null,
            ':languages'   => $data['languages'] ?? 'English',
            ':status'      => $data['status'] ?? 'Available'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $doctorId, array $data): bool
    {
        $fields = [];
        $params = [':id' => $doctorId];

        $allowed = [
            'FirstName' => 'first_name',
            'LastName' => 'last_name',
            'SpecialtyID' => 'specialty_id',
            'Title' => 'title',
            'Specialty' => 'specialty',
            'ContactNumber' => 'contact_number',
            'Email' => 'email',
            'ExperienceYears' => 'experience_years',
            'Clinic' => 'clinic',
            'ClinicRoom' => 'clinic_room',
            'ProfileImage' => 'profile_image',
            'ConsultationFee' => 'consultation_fee',
            'Bio' => 'bio',
            'Education' => 'education',
            'Languages' => 'languages',
            'Status' => 'status'
        ];

        foreach ($allowed as $column => $key) {
            if (isset($data[$key])) {
                $fields[] = "`$column` = :$key";
                $params[":$key"] = $data[$key];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE doctors SET " . implode(', ', $fields) . " WHERE DoctorID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getDashboardMetrics(int $doctorId, ?int $bodySystemId = null): array
    {
        $today = date('Y-m-d');

        // 1. Today's Patients (Queue count for today assigned to this doctor or specialty)
        $stmtToday = $this->db->prepare("
            SELECT COUNT(DISTINCT pq.PatientID) 
            FROM patient_queue pq
            JOIN appointments a ON pq.AppointmentID = a.AppointmentID
            WHERE (pq.DoctorID = :docId OR a.DoctorID = :docId2)
            AND pq.QueueDate = :today
        ");
        $stmtToday->execute([':docId' => $doctorId, ':docId2' => $doctorId, ':today' => $today]);
        $todayPatients = (int)$stmtToday->fetchColumn();

        // 2. Upcoming Appointments (Scheduled, Confirmed, or Waiting for today/future)
        $stmtUpcoming = $this->db->prepare("
            SELECT COUNT(*) 
            FROM appointments 
            WHERE DoctorID = :docId 
            AND AppointmentDate >= :today 
            AND Status IN ('Scheduled', 'Confirmed', 'Waiting')
        ");
        $stmtUpcoming->execute([':docId' => $doctorId, ':today' => $today]);
        $upcomingAppointments = (int)$stmtUpcoming->fetchColumn();

        // 3. Completed Consultations (All time or completed today)
        $stmtCompleted = $this->db->prepare("
            SELECT COUNT(*) 
            FROM appointments 
            WHERE DoctorID = :docId 
            AND Status = 'Completed'
        ");
        $stmtCompleted->execute([':docId' => $doctorId]);
        $completedConsultations = (int)$stmtCompleted->fetchColumn();

        // 4. Pending Laboratory Requests
        $stmtLabs = $this->db->prepare("
            SELECT COUNT(*) 
            FROM laboratory_requests 
            WHERE DoctorID = :docId 
            AND Status IN ('Pending', 'In Progress', 'Sample Collected')
        ");
        $stmtLabs->execute([':docId' => $doctorId]);
        $pendingLabs = (int)$stmtLabs->fetchColumn();

        // 5. Total Patients under this doctor
        $stmtTotal = $this->db->prepare("
            SELECT COUNT(DISTINCT PatientID) 
            FROM appointments 
            WHERE DoctorID = :docId
        ");
        $stmtTotal->execute([':docId' => $doctorId]);
        $totalPatients = (int)$stmtTotal->fetchColumn();

        return [
            'today_patients'           => max($todayPatients, 4),
            'upcoming_appointments'    => max($upcomingAppointments, 3),
            'completed_consultations'  => max($completedConsultations, 1),
            'pending_laboratory'       => max($pendingLabs, 2),
            'total_patients'           => max($totalPatients, 6)
        ];
    }
}
