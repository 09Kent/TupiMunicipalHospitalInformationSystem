<?php
// models/PatientRegistration.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Patient.php';
require_once __DIR__ . '/EmergencyContact.php';
require_once __DIR__ . '/MedicalHistory.php';
require_once __DIR__ . '/Complaint.php';
require_once __DIR__ . '/Symptom.php';
require_once __DIR__ . '/BodySystem.php';
require_once __DIR__ . '/BodyLocation.php';
require_once __DIR__ . '/ComplaintAnalysis.php';
require_once __DIR__ . '/PossibleCondition.php';
require_once __DIR__ . '/Appointment.php';
require_once __DIR__ . '/Queue.php';
require_once __DIR__ . '/PatientHistory.php';
require_once __DIR__ . '/Doctor.php';

class PatientRegistration
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Process full multi-step Patient Registration & Pre-Consultation Intake atomically
     */
    public function registerCompletePatient(array $data, ?int $userId = 1): array
    {
        $this->db->beginTransaction();

        try {
            $patientModel = new Patient($this->db);
            $emgModel = new EmergencyContact($this->db);
            $medModel = new MedicalHistory($this->db);
            $complaintModel = new Complaint($this->db);
            $symptomModel = new Symptom($this->db);
            $bodyLocationModel = new BodyLocation($this->db);
            $bodySystemModel = new BodySystem($this->db);
            $analysisModel = new ComplaintAnalysis($this->db);
            $conditionModel = new PossibleCondition($this->db);
            $appointmentModel = new Appointment($this->db);
            $queueModel = new Queue($this->db);
            $historyModel = new PatientHistory($this->db);

            // 1. Patient Record
            $patientCode = $patientModel->generatePatientCode();
            $patientId = $patientModel->create([
                'PatientCode'     => $patientCode,
                'FirstName'       => $data['personal']['firstName'] ?? $data['firstName'] ?? '',
                'MiddleName'      => $data['personal']['middleName'] ?? $data['middleName'] ?? null,
                'LastName'        => $data['personal']['lastName'] ?? $data['lastName'] ?? '',
                'DateOfBirth'     => $data['personal']['dob'] ?? $data['dob'] ?? date('Y-m-d'),
                'Age'             => $data['personal']['age'] ?? $data['age'] ?? 0,
                'Gender'          => $data['personal']['gender'] ?? $data['gender'] ?? 'Male',
                'CivilStatus'     => $data['personal']['civilStatus'] ?? $data['civilStatus'] ?? 'Single',
                'ContactNumber'   => $data['personal']['phone'] ?? $data['phone'] ?? '',
                'Email'           => $data['personal']['email'] ?? $data['email'] ?? '',
                'Address'         => $data['personal']['address'] ?? $data['address'] ?? '',
                'BloodType'       => $data['medical']['bloodType'] ?? $data['bloodType'] ?? 'Unknown',
                'PatientCategory' => $data['patientCategory'] ?? 'Outpatient',
                'Status'          => 'Active',
                'RegisteredBy'    => $userId
            ]);

            // 2. Emergency Contact
            $emgModel->create([
                'PatientID'     => $patientId,
                'ContactName'   => $data['emergency']['name'] ?? $data['emgName'] ?? '',
                'Relationship'  => $data['emergency']['relationship'] ?? $data['emgRelationship'] ?? 'Parent',
                'ContactNumber' => $data['emergency']['phone'] ?? $data['emgPhone'] ?? ''
            ]);

            // 3. Medical History
            $medModel->create([
                'PatientID'               => $patientId,
                'Allergies'               => $data['medical']['allergies'] ?? $data['allergies'] ?? 'None',
                'ExistingConditions'      => $data['medical']['conditions'] ?? $data['conditions'] ?? 'None',
                'CurrentMedications'      => $data['medical']['medications'] ?? $data['medications'] ?? 'None',
                'PreviousHospitalization' => $data['medical']['hospitalization'] ?? $data['hospitalization'] ?? 'None'
            ]);

            // 4. Chief Complaint
            $complaintText = $data['complaint'] ?? 'General Consultation';
            $complaintId = $complaintModel->create([
                'PatientID'            => $patientId,
                'ComplaintDescription' => $complaintText,
                'Severity'             => (int)($data['severity'] ?? 3),
                'Duration'             => $data['duration'] ?? '1–3 days ago',
                'AggravatingFactors'   => $data['aggravating'] ?? null,
                'RelievingFactors'     => $data['relieving'] ?? null
            ]);

            // 5. Symptoms Junction
            $symptoms = $data['symptoms'] ?? [];
            if (!empty($symptoms)) {
                $symptomModel->saveComplaintSymptoms($complaintId, $symptoms);
            }

            // 6. Body System & Anatomical Location Resolution
            $bodySystemId = (int)($data['bodySystemId'] ?? 1);
            if (empty($bodySystemId) || $bodySystemId <= 0) {
                $bodySystemId = 1;
            }

            $bodyLocationCode = $data['bodyLocation'] ?? 'abdomen';
            $locRecord = $bodyLocationModel->getByCode($bodyLocationCode);
            $bodyLocationId = $locRecord ? (int)$locRecord['BodyLocationID'] : 4;

            // 7. Complaint Analysis (Classification Record)
            $relevanceLevel = (int)($data['systemRelevanceScore'] ?? 95);
            $confidenceLevel = "High ({$relevanceLevel}%)";
            $clinicalNotes = "Automated pre-consultation symptom classification matched to " . ($locRecord['LocationName'] ?? 'Anatomical region');

            $analysisModel->create([
                'ComplaintID'    => $complaintId,
                'BodySystemID'   => $bodySystemId,
                'BodyLocationID' => $bodyLocationId,
                'RelevanceLevel' => $relevanceLevel,
                'ConfidenceLevel'=> $confidenceLevel,
                'ClinicalNotes'  => $clinicalNotes
            ]);

            // 8. Possible Conditions Junction
            $conditions = $data['possibleConditions'] ?? [];
            if (!empty($conditions)) {
                $conditionModel->saveComplaintConditions($complaintId, $conditions);
            } else {
                // Attach default conditions for system
                $dbConds = $conditionModel->getByBodySystemId($bodySystemId);
                if (!empty($dbConds)) {
                    $condIds = array_column($dbConds, 'ConditionID');
                    $conditionModel->saveComplaintConditions($complaintId, $condIds);
                }
            }

            // 9. Doctor Selection & Appointment Scheduling
            $doctorId = (int)($data['doctorId'] ?? ($data['selectedDoctor']['DoctorID'] ?? $data['selectedDoctor']['id'] ?? 1));
            if ($doctorId <= 0) $doctorId = 1;

            $appDate = $data['appointmentDate'] ?? date('Y-m-d');
            $appTime = $data['selectedSlot'] ?? '02:30 PM';
            $consType = $data['consultationType'] ?? 'In-Person Consultation';

            $appId = $appointmentModel->create([
                'PatientID'        => $patientId,
                'DoctorID'         => $doctorId,
                'AppointmentDate'  => $appDate,
                'AppointmentTime'  => $appTime,
                'ConsultationType' => $consType,
                'Reason'           => substr($complaintText, 0, 200),
                'Priority'         => ($data['severity'] ?? 3) >= 4 ? 'Urgent' : 'Normal',
                'Notes'            => 'Pre-consultation digital intake registration.',
                'Status'           => 'Scheduled',
                'CreatedBy'        => $userId
            ]);

            // 10. Patient Queue Ticket
            $queueNum = $queueModel->generateQueueNumber();
            $queueId = $queueModel->create([
                'AppointmentID' => $appId,
                'PatientID'     => $patientId,
                'DoctorID'      => $doctorId,
                'QueueNumber'   => $queueNum,
                'QueueStatus'   => 'Waiting',
                'Priority'      => ($data['severity'] ?? 3) >= 4 ? 'Priority' : 'Normal'
            ]);

            // 11. Comprehensive Audit Trail (Registration History)
            $fullName = trim(($data['personal']['firstName'] ?? '') . ' ' . ($data['personal']['lastName'] ?? ''));
            $historyModel->log($patientId, 'Patient Registered', "Patient {$fullName} registered with Digital Code {$patientCode}.", $userId);
            $historyModel->log($patientId, 'Complaint Recorded', "Chief complaint recorded: " . substr($complaintText, 0, 100), $userId);
            $historyModel->log($patientId, 'Symptom Classification', "Pre-consultation analysis matched to {$relevanceLevel}% relevance.", $userId);
            $historyModel->log($patientId, 'Doctor Assigned', "Consultation booked with Doctor ID #{$doctorId} for {$appDate} {$appTime}.", $userId);
            $historyModel->log($patientId, 'Queue Generated', "Assigned Daily Queue Ticket #{$queueNum}.", $userId);

            // Fetch Doctor details for response
            $doctorModel = new Doctor($this->db);
            $docInfo = $doctorModel->getById($doctorId);

            $this->db->commit();

            return [
                'success'        => true,
                'patient_id'     => $patientId,
                'patient_code'   => $patientCode,
                'appointment_id' => $appId,
                'queue_id'       => $queueId,
                'queue_number'   => $queueNum,
                'doctor'         => $docInfo,
                'message'        => 'Patient successfully registered and queued for consultation.'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }
}
