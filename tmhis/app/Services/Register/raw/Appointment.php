<?php
// models/Appointment.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/PatientHistory.php';

class Appointment
{
    private PDO $db;
    private PatientHistory $historyModel;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
        $this->historyModel = new PatientHistory($this->db);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO appointments (
                PatientID, DoctorID, AppointmentDate, AppointmentTime, 
                ConsultationType, Reason, Priority, Notes, Status, CreatedBy, CreatedAt
            ) VALUES (
                :pid, :doc_id, :app_date, :app_time, 
                :type, :reason, :prio, :notes, :status, :created_by, NOW()
            )
        ");

        $stmt->execute([
            'pid'        => $data['PatientID'],
            'doc_id'     => $data['DoctorID'],
            'app_date'   => $data['AppointmentDate'] ?? date('Y-m-d'),
            'app_time'   => $data['AppointmentTime'] ?? '02:30 PM',
            'type'       => $data['ConsultationType'] ?? 'In-Person Consultation',
            'reason'     => trim($data['Reason'] ?? 'General Consultation'),
            'prio'       => $data['Priority'] ?? 'Normal',
            'notes'      => !empty($data['Notes']) ? trim($data['Notes']) : null,
            'status'     => $data['Status'] ?? 'Scheduled',
            'created_by' => $data['CreatedBy'] ?? 1
        ]);

        $appId = (int)$this->db->lastInsertId();

        // Log to Patient History
        $this->historyModel->log(
            (int)$data['PatientID'],
            'Consultation Scheduled',
            "Consultation appointment #{$appId} booked for {$data['AppointmentDate']} at {$data['AppointmentTime']}.",
            $data['CreatedBy'] ?? 1
        );

        return $appId;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   p.PatientCode, p.FirstName AS PatFirst, p.LastName AS PatLast, p.ContactNumber AS PatPhone, p.Age, p.Gender,
                   d.FirstName AS DocFirst, d.LastName AS DocLast, d.Specialty, d.Clinic, d.ClinicRoom, d.ProfileImage
            FROM appointments a
            JOIN patients p ON p.PatientID = a.PatientID
            JOIN doctors d ON d.DoctorID = a.DoctorID
            WHERE a.AppointmentID = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getTodayAppointments(): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   p.PatientCode, p.FirstName AS PatFirst, p.LastName AS PatLast, p.Age, p.Gender,
                   d.FirstName AS DocFirst, d.LastName AS DocLast, d.Specialty, d.ClinicRoom,
                   q.QueueNumber, q.QueueStatus
            FROM appointments a
            JOIN patients p ON p.PatientID = a.PatientID
            JOIN doctors d ON d.DoctorID = a.DoctorID
            LEFT JOIN patient_queue q ON q.AppointmentID = a.AppointmentID
            WHERE a.AppointmentDate = CURDATE()
            ORDER BY a.AppointmentTime ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPaginated(int $limit = 10, int $offset = 0, string $date = '', string $doctor = '', string $status = ''): array
    {
        $sql = "
            SELECT a.*, 
                   p.PatientCode, p.FirstName AS PatFirst, p.LastName AS PatLast, p.Age, p.Gender, p.ContactNumber AS PatPhone,
                   d.FirstName AS DocFirst, d.LastName AS DocLast, d.Specialty, d.ClinicRoom,
                   q.QueueNumber, q.QueueStatus
            FROM appointments a
            JOIN patients p ON p.PatientID = a.PatientID
            JOIN doctors d ON d.DoctorID = a.DoctorID
            LEFT JOIN patient_queue q ON q.AppointmentID = a.AppointmentID
            WHERE 1=1
        ";

        $params = [];

        if (!empty($date)) {
            $sql .= " AND a.AppointmentDate = :date";
            $params['date'] = $date;
        }

        if (!empty($doctor)) {
            $sql .= " AND a.DoctorID = :doc";
            $params['doc'] = $doctor;
        }

        if (!empty($status)) {
            $sql .= " AND a.Status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY a.AppointmentDate DESC, a.AppointmentTime ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":{$k}", $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countTotal(string $date = '', string $doctor = '', string $status = ''): int
    {
        $sql = "SELECT COUNT(*) FROM appointments a WHERE 1=1";
        $params = [];

        if (!empty($date)) {
            $sql .= " AND a.AppointmentDate = :date";
            $params['date'] = $date;
        }

        if (!empty($doctor)) {
            $sql .= " AND a.DoctorID = :doc";
            $params['doc'] = $doctor;
        }

        if (!empty($status)) {
            $sql .= " AND a.Status = :status";
            $params['status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function reschedule(int $id, string $newDate, string $newTime, ?int $newDoctorId = null, string $reason = '', ?int $userId = 1): bool
    {
        $app = $this->getById($id);
        if (!$app) return false;

        $docId = $newDoctorId ?: $app['DoctorID'];

        $stmt = $this->db->prepare("
            UPDATE appointments SET
                AppointmentDate = :date,
                AppointmentTime = :time,
                DoctorID        = :doc,
                Notes           = CONCAT(COALESCE(Notes, ''), '\nRescheduled: ', :reason),
                UpdatedAt       = NOW()
            WHERE AppointmentID = :id
        ");

        $success = $stmt->execute([
            'id'     => $id,
            'date'   => $newDate,
            'time'   => $newTime,
            'doc'    => $docId,
            'reason' => $reason
        ]);

        if ($success) {
            $this->historyModel->log(
                (int)$app['PatientID'],
                'Consultation Rescheduled',
                "Appointment #{$id} rescheduled to {$newDate} at {$newTime}. Reason: {$reason}",
                $userId
            );
        }

        return $success;
    }

    public function updateStatus(int $id, string $status, ?int $userId = 1): bool
    {
        $app = $this->getById($id);
        if (!$app) return false;

        $stmt = $this->db->prepare("UPDATE appointments SET Status = :status, UpdatedAt = NOW() WHERE AppointmentID = :id");
        $success = $stmt->execute(['id' => $id, 'status' => $status]);

        if ($success) {
            $this->historyModel->log(
                (int)$app['PatientID'],
                'Appointment Status Updated',
                "Consultation appointment #{$id} status changed to '{$status}'.",
                $userId
            );
        }

        return $success;
    }

    public function cancel(int $id, string $reason = '', ?int $userId = 1): bool
    {
        $app = $this->getById($id);
        if (!$app) return false;

        $stmt = $this->db->prepare("
            UPDATE appointments SET 
                Status = 'Cancelled', 
                Notes = CONCAT(COALESCE(Notes, ''), '\nCancelled: ', :reason), 
                UpdatedAt = NOW() 
            WHERE AppointmentID = :id
        ");
        $success = $stmt->execute(['id' => $id, 'reason' => $reason]);

        if ($success) {
            $this->historyModel->log(
                (int)$app['PatientID'],
                'Consultation Cancelled',
                "Appointment #{$id} was cancelled. Reason: {$reason}",
                $userId
            );
        }

        return $success;
    }
}
