-- Medical Registration & Pre-Consultation Database Schema
-- Database: MedicalRegistrationDB

CREATE DATABASE IF NOT EXISTS `MedicalRegistrationDB` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `MedicalRegistrationDB`;

-- 1. Users Table (System Registrators, Admins, Doctors)
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` INT AUTO_INCREMENT PRIMARY KEY,
  `FirstName` VARCHAR(100) NOT NULL,
  `LastName` VARCHAR(100) NOT NULL,
  `Username` VARCHAR(60) NOT NULL UNIQUE,
  `PasswordHash` VARCHAR(255) NOT NULL,
  `Role` VARCHAR(50) NOT NULL DEFAULT 'Registrator',
  `Email` VARCHAR(120) NULL,
  `Status` ENUM('Active', 'Inactive', 'Suspended') NOT NULL DEFAULT 'Active',
  `remember_token` VARCHAR(100) NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_login` (`Username`, `Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Patients Table
CREATE TABLE IF NOT EXISTS `patients` (
  `PatientID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientCode` VARCHAR(30) NOT NULL UNIQUE,
  `FirstName` VARCHAR(100) NOT NULL,
  `MiddleName` VARCHAR(100) NULL,
  `LastName` VARCHAR(100) NOT NULL,
  `DateOfBirth` DATE NOT NULL,
  `Age` INT NOT NULL,
  `Gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `CivilStatus` ENUM('Single', 'Married', 'Divorced', 'Widowed') NOT NULL DEFAULT 'Single',
  `ContactNumber` VARCHAR(30) NOT NULL,
  `Email` VARCHAR(120) NOT NULL,
  `Address` TEXT NOT NULL,
  `BloodType` VARCHAR(10) NOT NULL DEFAULT 'Unknown',
  `PatientCategory` ENUM('Outpatient', 'Emergency', 'Inpatient', 'Consultation', 'Admitted') NOT NULL DEFAULT 'Outpatient',
  `Status` ENUM('Active', 'Inactive', 'Discharged', 'Transferred') NOT NULL DEFAULT 'Active',
  `RegisteredBy` INT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`RegisteredBy`) REFERENCES `users`(`UserID`) ON DELETE SET NULL,
  INDEX `idx_patient_search` (`FirstName`, `LastName`, `ContactNumber`),
  INDEX `idx_patient_code` (`PatientCode`),
  INDEX `idx_patient_category` (`PatientCategory`),
  INDEX `idx_patient_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Emergency Contacts Table
CREATE TABLE IF NOT EXISTS `emergency_contacts` (
  `EmergencyContactID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `ContactName` VARCHAR(150) NOT NULL,
  `Relationship` VARCHAR(60) NOT NULL,
  `ContactNumber` VARCHAR(30) NOT NULL,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  INDEX `idx_emg_patient` (`PatientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Medical Histories Table
CREATE TABLE IF NOT EXISTS `medical_histories` (
  `MedicalHistoryID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `Allergies` TEXT NULL,
  `ExistingConditions` TEXT NULL,
  `CurrentMedications` TEXT NULL,
  `PreviousHospitalization` TEXT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  INDEX `idx_med_patient` (`PatientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Body Systems Table
CREATE TABLE IF NOT EXISTS `body_systems` (
  `BodySystemID` INT AUTO_INCREMENT PRIMARY KEY,
  `SystemName` VARCHAR(100) NOT NULL UNIQUE,
  `SystemCode` VARCHAR(50) NOT NULL UNIQUE,
  `Icon` VARCHAR(50) NOT NULL DEFAULT 'activity',
  `Emoji` VARCHAR(10) NOT NULL DEFAULT '🩺',
  `Description` TEXT NOT NULL,
  `Status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Body Locations Table
CREATE TABLE IF NOT EXISTS `body_locations` (
  `BodyLocationID` INT AUTO_INCREMENT PRIMARY KEY,
  `BodySystemID` INT NULL,
  `LocationName` VARCHAR(100) NOT NULL,
  `LocationCode` VARCHAR(50) NOT NULL UNIQUE,
  `FrontBack` ENUM('Front', 'Back', 'Both') NOT NULL DEFAULT 'Front',
  `SubRegion` VARCHAR(150) NOT NULL,
  `Description` TEXT NULL,
  FOREIGN KEY (`BodySystemID`) REFERENCES `body_systems`(`BodySystemID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Symptoms Master Table
CREATE TABLE IF NOT EXISTS `symptoms` (
  `SymptomID` INT AUTO_INCREMENT PRIMARY KEY,
  `SymptomName` VARCHAR(100) NOT NULL UNIQUE,
  `Description` VARCHAR(255) NULL,
  `Icon` VARCHAR(50) NULL DEFAULT 'activity',
  `Status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Complaints Table
CREATE TABLE IF NOT EXISTS `complaints` (
  `ComplaintID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `ComplaintDescription` TEXT NOT NULL,
  `Severity` INT NOT NULL DEFAULT 3,
  `Duration` VARCHAR(50) NOT NULL DEFAULT '1–3 days ago',
  `AggravatingFactors` TEXT NULL,
  `RelievingFactors` TEXT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  INDEX `idx_complaint_patient` (`PatientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Patient Symptoms Junction Table
CREATE TABLE IF NOT EXISTS `patient_symptoms` (
  `PatientSymptomID` INT AUTO_INCREMENT PRIMARY KEY,
  `ComplaintID` INT NOT NULL,
  `SymptomID` INT NOT NULL,
  FOREIGN KEY (`ComplaintID`) REFERENCES `complaints`(`ComplaintID`) ON DELETE CASCADE,
  FOREIGN KEY (`SymptomID`) REFERENCES `symptoms`(`SymptomID`) ON DELETE CASCADE,
  UNIQUE KEY `unique_complaint_symptom` (`ComplaintID`, `SymptomID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Complaint Analysis Table (Symptom Classification Results)
CREATE TABLE IF NOT EXISTS `complaint_analysis` (
  `AnalysisID` INT AUTO_INCREMENT PRIMARY KEY,
  `ComplaintID` INT NOT NULL,
  `BodySystemID` INT NOT NULL,
  `BodyLocationID` INT NULL,
  `RelevanceLevel` INT NOT NULL DEFAULT 90,
  `ConfidenceLevel` VARCHAR(30) NOT NULL DEFAULT 'High (95%)',
  `ClinicalNotes` TEXT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`ComplaintID`) REFERENCES `complaints`(`ComplaintID`) ON DELETE CASCADE,
  FOREIGN KEY (`BodySystemID`) REFERENCES `body_systems`(`BodySystemID`) ON DELETE RESTRICT,
  FOREIGN KEY (`BodyLocationID`) REFERENCES `body_locations`(`BodyLocationID`) ON DELETE SET NULL,
  INDEX `idx_analysis_complaint` (`ComplaintID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Possible Conditions Master Table
CREATE TABLE IF NOT EXISTS `possible_conditions` (
  `ConditionID` INT AUTO_INCREMENT PRIMARY KEY,
  `ConditionName` VARCHAR(150) NOT NULL,
  `BodySystemID` INT NOT NULL,
  `Description` TEXT NOT NULL,
  `Status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  FOREIGN KEY (`BodySystemID`) REFERENCES `body_systems`(`BodySystemID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Complaint Conditions Junction Table
CREATE TABLE IF NOT EXISTS `complaint_conditions` (
  `ComplaintConditionID` INT AUTO_INCREMENT PRIMARY KEY,
  `ComplaintID` INT NOT NULL,
  `ConditionID` INT NOT NULL,
  FOREIGN KEY (`ComplaintID`) REFERENCES `complaints`(`ComplaintID`) ON DELETE CASCADE,
  FOREIGN KEY (`ConditionID`) REFERENCES `possible_conditions`(`ConditionID`) ON DELETE CASCADE,
  UNIQUE KEY `unique_complaint_condition` (`ComplaintID`, `ConditionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Doctors Table
CREATE TABLE IF NOT EXISTS `doctors` (
  `DoctorID` INT AUTO_INCREMENT PRIMARY KEY,
  `FirstName` VARCHAR(100) NOT NULL,
  `LastName` VARCHAR(100) NOT NULL,
  `Title` VARCHAR(50) NOT NULL DEFAULT 'MD',
  `Specialty` VARCHAR(120) NOT NULL,
  `LicenseNumber` VARCHAR(60) NOT NULL UNIQUE,
  `ContactNumber` VARCHAR(30) NOT NULL,
  `Email` VARCHAR(120) NOT NULL,
  `ExperienceYears` INT NOT NULL DEFAULT 5,
  `Clinic` VARCHAR(150) NOT NULL,
  `ClinicRoom` VARCHAR(100) NOT NULL DEFAULT 'Suite 101',
  `ProfileImage` VARCHAR(255) NULL,
  `Rating` DECIMAL(3,2) NOT NULL DEFAULT 4.90,
  `ReviewsCount` INT NOT NULL DEFAULT 50,
  `ConsultationFee` VARCHAR(50) NOT NULL DEFAULT '$80.00',
  `Bio` TEXT NULL,
  `Education` TEXT NULL,
  `Languages` VARCHAR(255) NOT NULL DEFAULT 'English',
  `Status` ENUM('Available', 'Busy', 'On Leave', 'Inactive') NOT NULL DEFAULT 'Available',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_doc_specialty` (`Specialty`),
  INDEX `idx_doc_status` (`Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Doctor Body Systems Junction Table
CREATE TABLE IF NOT EXISTS `doctor_body_systems` (
  `DoctorSystemID` INT AUTO_INCREMENT PRIMARY KEY,
  `DoctorID` INT NOT NULL,
  `BodySystemID` INT NOT NULL,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE CASCADE,
  FOREIGN KEY (`BodySystemID`) REFERENCES `body_systems`(`BodySystemID`) ON DELETE CASCADE,
  UNIQUE KEY `unique_doctor_system` (`DoctorID`, `BodySystemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Appointments Table
CREATE TABLE IF NOT EXISTS `appointments` (
  `AppointmentID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `AppointmentDate` DATE NOT NULL,
  `AppointmentTime` VARCHAR(30) NOT NULL,
  `ConsultationType` ENUM('In-Person Consultation', 'Secure Telehealth Video', 'Follow-up Review', 'Emergency Triage') NOT NULL DEFAULT 'In-Person Consultation',
  `Reason` TEXT NOT NULL,
  `Priority` ENUM('Normal', 'Urgent', 'High Priority', 'Follow-up') NOT NULL DEFAULT 'Normal',
  `Notes` TEXT NULL,
  `Status` ENUM('Scheduled', 'Confirmed', 'Waiting', 'In Consultation', 'Completed', 'Cancelled', 'No Show') NOT NULL DEFAULT 'Scheduled',
  `CreatedBy` INT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  FOREIGN KEY (`CreatedBy`) REFERENCES `users`(`UserID`) ON DELETE SET NULL,
  INDEX `idx_app_date` (`AppointmentDate`, `Status`),
  INDEX `idx_app_patient` (`PatientID`),
  INDEX `idx_app_doctor` (`DoctorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16. Patient Queue Table
CREATE TABLE IF NOT EXISTS `patient_queue` (
  `QueueID` INT AUTO_INCREMENT PRIMARY KEY,
  `AppointmentID` INT NULL,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `QueueNumber` VARCHAR(10) NOT NULL,
  `QueueDate` DATE NOT NULL,
  `QueueStatus` ENUM('Waiting', 'Called', 'In Consultation', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Waiting',
  `Priority` ENUM('Normal', 'Priority', 'Emergency') NOT NULL DEFAULT 'Normal',
  `CalledAt` DATETIME NULL,
  `ConsultationStartedAt` DATETIME NULL,
  `CompletedAt` DATETIME NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`AppointmentID`) REFERENCES `appointments`(`AppointmentID`) ON DELETE SET NULL,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  INDEX `idx_queue_daily` (`QueueDate`, `QueueStatus`),
  INDEX `idx_queue_num` (`QueueDate`, `QueueNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 17. Patient Registration History Table
CREATE TABLE IF NOT EXISTS `patient_registration_history` (
  `HistoryID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `UserID` INT NULL,
  `Action` VARCHAR(100) NOT NULL,
  `Description` TEXT NOT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`UserID`) REFERENCES `users`(`UserID`) ON DELETE SET NULL,
  INDEX `idx_hist_patient` (`PatientID`, `CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
