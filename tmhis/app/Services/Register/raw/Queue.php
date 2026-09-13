<?php
// models/Queue.php

require_once __DIR__ . '/../config/Database.php';

class Queue
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Generate unique sequential 2-digit queue number for current day (01, 02, 03...)
     */
    public function generateQueueNumber(): string
    {
        $stmt = $this->db->prepare("
            SELECT QueueNumber FROM patient_queue 
            WHERE QueueDate = CURDATE() 
            ORDER BY QueueID DESC LIMIT 1
        ");
        $stmt->execute();
        $last = $stmt->fetchColumn();

        if ($last) {
            $next = (int)$last + 1;
        } else {
            $next = 1;
        }

        return str_pad((string)$next, 2, '0', STR_PAD_LEFT);
    }

    public function create(array $data): int
    {
        $queueNum = $data['QueueNumber'] ?? $this->generateQueueNumber();

        $stmt = $this->db->prepare("
            INSERT INTO patient_queue (
                AppointmentID, PatientID, DoctorID, 
                QueueNumber, QueueDate, QueueStatus, Priority, CreatedAt
            ) VALUES (
                :app_id, :pid, :doc_id, 
                :num, CURDATE(), :status, :prio, NOW()
            )
        ");

        $stmt->execute([
            'app_id' => $data['AppointmentID'] ?? null,
            'pid'    => $data['PatientID'],
            'doc_id' => $data['DoctorID'],
            'num'    => $queueNum,
            'status' => $data['QueueStatus'] ?? 'Waiting',
            'prio'   => $data['Priority'] ?? 'Normal'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getTodayQueue(): array
    {
        $stmt = $this->db->prepare("
            SELECT q.*, 
                   p.PatientCode, p.FirstName AS PatFirst, p.LastName AS PatLast, p.Age, p.Gender,
                   d.FirstName AS DocFirst, d.LastName AS DocLast, d.Specialty, d.ClinicRoom
            FROM patient_queue q
            JOIN patients p ON p.PatientID = q.PatientID
            JOIN doctors d ON d.DoctorID = q.DoctorID
            WHERE q.QueueDate = CURDATE()
            ORDER BY (CASE WHEN q.QueueStatus = 'In Consultation' THEN 1 WHEN q.QueueStatus = 'Called' THEN 2 WHEN q.QueueStatus = 'Waiting' THEN 3 ELSE 4 END), q.QueueID ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNowServing(): ?array
    {
        $stmt = $this->db->prepare("
            SELECT q.*, 
                   p.PatientCode, p.FirstName AS PatFirst, p.LastName AS PatLast, p.Age, p.Gender,
                   d.FirstName AS DocFirst, d.LastName AS DocLast, d.Specialty, d.ClinicRoom, d.ProfileImage
            FROM patient_queue q
            JOIN patients p ON p.PatientID = q.PatientID
            JOIN doctors d ON d.DoctorID = q.DoctorID
            WHERE q.QueueDate = CURDATE() AND q.QueueStatus IN ('In Consultation', 'Called')
            ORDER BY (CASE WHEN q.QueueStatus = 'In Consultation' THEN 1 ELSE 2 END), q.CalledAt DESC LIMIT 1
        ");
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $queueId, string $status): bool
    {
        $calledAt = null;
        $startedAt = null;
        $completedAt = null;

        if ($status === 'Called') {
            $calledAt = date('Y-m-d H:i:s');
        } elseif ($status === 'In Consultation') {
            $startedAt = date('Y-m-d H:i:s');
        } elseif ($status === 'Completed') {
            $completedAt = date('Y-m-d H:i:s');
        }

        $stmt = $this->db->prepare("
            UPDATE patient_queue SET
                QueueStatus           = :status,
                CalledAt              = COALESCE(:called, CalledAt),
                ConsultationStartedAt = COALESCE(:started, ConsultationStartedAt),
                CompletedAt           = COALESCE(:completed, CompletedAt)
            WHERE QueueID = :id
        ");

        return $stmt->execute([
            'id'        => $queueId,
            'status'    => $status,
            'called'    => $calledAt,
            'started'   => $startedAt,
            'completed' => $completedAt
        ]);
    }
}
