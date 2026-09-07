<?php
// Doctor/models/Consultation.php

class Consultation
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function startConsultation(int $appointmentId, int $doctorId): bool
    {
        $this->db->beginTransaction();
        try {
            // Update appointment status
            $stmtApp = $this->db->prepare("
                UPDATE appointments 
                SET Status = 'In Consultation', UpdatedAt = NOW() 
                WHERE AppointmentID = :appId
            ");
            $stmtApp->execute([':appId' => $appointmentId]);

            // Update queue status
            $stmtQueue = $this->db->prepare("
                UPDATE patient_queue 
                SET QueueStatus = 'In Consultation', 
                    CalledAt = COALESCE(CalledAt, NOW()), 
                    ConsultationStartedAt = NOW()
                WHERE AppointmentID = :appId
            ");
            $stmtQueue->execute([':appId' => $appointmentId]);

            // Update doctor status to Busy
            $stmtDoc = $this->db->prepare("UPDATE doctors SET Status = 'Busy' WHERE DoctorID = :docId");
            $stmtDoc->execute([':docId' => $doctorId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function completeConsultation(int $appointmentId, int $doctorId, ?string $summaryNotes = null): bool
    {
        $this->db->beginTransaction();
        try {
            // Update appointment status to Completed
            $stmtApp = $this->db->prepare("
                UPDATE appointments 
                SET Status = 'Completed', Notes = COALESCE(:notes, Notes), UpdatedAt = NOW() 
                WHERE AppointmentID = :appId
            ");
            $stmtApp->execute([
                ':notes' => $summaryNotes,
                ':appId' => $appointmentId
            ]);

            // Update queue status to Completed
            $stmtQueue = $this->db->prepare("
                UPDATE patient_queue 
                SET QueueStatus = 'Completed', CompletedAt = NOW()
                WHERE AppointmentID = :appId
            ");
            $stmtQueue->execute([':appId' => $appointmentId]);

            // Set doctor back to Available
            $stmtDoc = $this->db->prepare("UPDATE doctors SET Status = 'Available' WHERE DoctorID = :docId");
            $stmtDoc->execute([':docId' => $doctorId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getActiveConsultation(int $doctorId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, p.PatientCode, p.FirstName, p.LastName, p.Age, p.Gender, p.ContactNumber, p.BloodType,
                   pq.QueueNumber, pq.QueueStatus, pq.ConsultationStartedAt,
                   ca.BodySystemID, bs.SystemName, c.ComplaintDescription
            FROM appointments a
            JOIN patients p ON a.PatientID = p.PatientID
            LEFT JOIN patient_queue pq ON a.AppointmentID = pq.AppointmentID
            LEFT JOIN complaints c ON p.PatientID = c.PatientID
            LEFT JOIN complaint_analysis ca ON c.ComplaintID = ca.ComplaintID
            LEFT JOIN body_systems bs ON ca.BodySystemID = bs.BodySystemID
            WHERE a.DoctorID = :docId 
            AND (a.Status = 'In Consultation' OR pq.QueueStatus = 'In Consultation')
            ORDER BY a.UpdatedAt DESC
            LIMIT 1
        ");
        $stmt->execute([':docId' => $doctorId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
