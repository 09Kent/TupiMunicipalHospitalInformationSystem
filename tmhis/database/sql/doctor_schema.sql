-- Doctor Portal Database Schema & Enhancements
USE `MedicalRegistrationDB`;

-- 1. Specialties Table
CREATE TABLE IF NOT EXISTS `specialties` (
  `SpecialtyID` INT AUTO_INCREMENT PRIMARY KEY,
  `SpecialtyName` VARCHAR(100) NOT NULL UNIQUE,
  `SpecialtyCode` VARCHAR(50) NOT NULL UNIQUE,
  `Description` TEXT NULL,
  `BodySystemID` INT NULL,
  `Icon` VARCHAR(50) NOT NULL DEFAULT 'stethoscope',
  `Status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  FOREIGN KEY (`BodySystemID`) REFERENCES `body_systems`(`BodySystemID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Specialties (All 15 as requested)
INSERT INTO `specialties` (`SpecialtyID`, `SpecialtyName`, `SpecialtyCode`, `Description`, `BodySystemID`, `Icon`) VALUES
(1, 'General Practitioner', 'gp', 'Primary healthcare and general medical complaints', 9, 'stethoscope'),
(2, 'Cardiologist', 'cardio', 'Heart, blood pressure, and cardiovascular system', 2, 'heart-pulse'),
(3, 'Neurologist', 'neuro', 'Brain, spinal cord, nerves, and neurological conditions', 3, 'brain'),
(4, 'Pulmonologist', 'pulmo', 'Lungs, airways, and respiratory conditions', 4, 'wind'),
(5, 'Gastroenterologist', 'gastro', 'Digestive tract, stomach, intestines, liver, gallbladder', 1, 'activity'),
(6, 'Orthopedic', 'ortho', 'Bones, joints, muscles, and musculoskeletal trauma', 5, 'bone'),
(7, 'Pediatrician', 'pedia', 'Comprehensive child healthcare and pediatric development', 9, 'baby'),
(8, 'General Surgeon', 'surgeon', 'Surgical consultations, wound management, operative care', 9, 'scissors'),
(9, 'Dermatologist', 'derma', 'Skin conditions, cutaneous infections, dermal health', 6, 'shield'),
(10, 'ENT', 'ent', 'Ear, nose, throat, head and neck conditions', 3, 'headphones'),
(11, 'Urologist', 'uro', 'Urinary tract, bladder, and male reproductive health', 7, 'droplet'),
(12, 'OB-GYN', 'obgyn', 'Obstetrics, gynecology, and female reproductive health', 9, 'users'),
(13, 'Psychiatrist', 'psych', 'Mental health, behavioral wellness, and cognitive support', 3, 'smile'),
(14, 'Ophthalmologist', 'opht', 'Vision care, ocular diagnostics, and eye conditions', 3, 'eye'),
(15, 'Nephrologist', 'nephro', 'Kidney function, renal disease, and electrolyte balance', 7, 'droplets')
ON DUPLICATE KEY UPDATE `SpecialtyName`=VALUES(`SpecialtyName`), `BodySystemID`=VALUES(`BodySystemID`);

-- Update/Alter doctors table to support UserID and SpecialtyID
SET @dbname = DATABASE();
SET @tablename = "doctors";
SET @columnname = "UserID";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_NAME = @tablename)
      AND (TABLE_SCHEMA = @dbname)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `doctors` ADD COLUMN `UserID` INT NULL AFTER `DoctorID`, ADD CONSTRAINT `fk_doc_user` FOREIGN KEY (`UserID`) REFERENCES `users`(`UserID`) ON DELETE SET NULL"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = "SpecialtyID";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_NAME = @tablename)
      AND (TABLE_SCHEMA = @dbname)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `doctors` ADD COLUMN `SpecialtyID` INT NULL AFTER `LastName`, ADD CONSTRAINT `fk_doc_specialty` FOREIGN KEY (`SpecialtyID`) REFERENCES `specialties`(`SpecialtyID`) ON DELETE SET NULL"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Doctor Specialties Table (For multiple specialties in future)
CREATE TABLE IF NOT EXISTS `doctor_specialties` (
  `DoctorSpecialtyID` INT AUTO_INCREMENT PRIMARY KEY,
  `DoctorID` INT NOT NULL,
  `SpecialtyID` INT NOT NULL,
  `IsPrimary` TINYINT(1) NOT NULL DEFAULT 1,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE CASCADE,
  FOREIGN KEY (`SpecialtyID`) REFERENCES `specialties`(`SpecialtyID`) ON DELETE CASCADE,
  UNIQUE KEY `unique_doc_spec` (`DoctorID`, `SpecialtyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Consultation Notes Table
CREATE TABLE IF NOT EXISTS `consultation_notes` (
  `NoteID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `AppointmentID` INT NULL,
  `Subjective` TEXT NULL,
  `Objective` TEXT NULL,
  `Assessment` TEXT NULL,
  `Plan` TEXT NULL,
  `ClinicalNotes` TEXT NOT NULL,
  `VitalSigns` JSON NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  FOREIGN KEY (`AppointmentID`) REFERENCES `appointments`(`AppointmentID`) ON DELETE SET NULL,
  INDEX `idx_note_patient` (`PatientID`),
  INDEX `idx_note_doctor` (`DoctorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Diagnoses Table
CREATE TABLE IF NOT EXISTS `diagnoses` (
  `DiagnosisID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `AppointmentID` INT NULL,
  `DiagnosisName` VARCHAR(255) NOT NULL,
  `ICD10Code` VARCHAR(30) NULL,
  `Type` ENUM('Primary', 'Secondary', 'Differential', 'Provisional') NOT NULL DEFAULT 'Primary',
  `Severity` ENUM('Mild', 'Moderate', 'Severe', 'Critical') NOT NULL DEFAULT 'Moderate',
  `Status` ENUM('Active', 'Resolved', 'Chronic', 'Under Investigation') NOT NULL DEFAULT 'Active',
  `Notes` TEXT NULL,
  `DiagnosedDate` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  FOREIGN KEY (`AppointmentID`) REFERENCES `appointments`(`AppointmentID`) ON DELETE SET NULL,
  INDEX `idx_diag_patient` (`PatientID`),
  INDEX `idx_diag_doctor` (`DoctorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Treatment Plans Table
CREATE TABLE IF NOT EXISTS `treatment_plans` (
  `PlanID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `AppointmentID` INT NULL,
  `DiagnosisID` INT NULL,
  `Goal` VARCHAR(255) NOT NULL,
  `LifestyleRecommendations` TEXT NULL,
  `MedicationPlan` TEXT NULL,
  `FollowUpSchedule` VARCHAR(100) NULL,
  `FollowUpDate` DATE NULL,
  `Status` ENUM('Active', 'Completed', 'Discontinued', 'Modified') NOT NULL DEFAULT 'Active',
  `Notes` TEXT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  FOREIGN KEY (`AppointmentID`) REFERENCES `appointments`(`AppointmentID`) ON DELETE SET NULL,
  FOREIGN KEY (`DiagnosisID`) REFERENCES `diagnoses`(`DiagnosisID`) ON DELETE SET NULL,
  INDEX `idx_treat_patient` (`PatientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Prescriptions Table
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `PrescriptionID` INT AUTO_INCREMENT PRIMARY KEY,
  `PrescriptionCode` VARCHAR(50) NOT NULL UNIQUE,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `AppointmentID` INT NULL,
  `MedicineName` VARCHAR(200) NOT NULL,
  `Dosage` VARCHAR(100) NOT NULL,
  `Frequency` VARCHAR(100) NOT NULL,
  `Duration` VARCHAR(100) NOT NULL,
  `Instructions` TEXT NOT NULL,
  `Quantity` VARCHAR(50) NULL DEFAULT '1 Box',
  `Refills` INT NOT NULL DEFAULT 0,
  `Status` ENUM('Active', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Active',
  `IssuedDate` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  FOREIGN KEY (`AppointmentID`) REFERENCES `appointments`(`AppointmentID`) ON DELETE SET NULL,
  INDEX `idx_rx_patient` (`PatientID`),
  INDEX `idx_rx_doctor` (`DoctorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Laboratory Requests Table
CREATE TABLE IF NOT EXISTS `laboratory_requests` (
  `RequestID` INT AUTO_INCREMENT PRIMARY KEY,
  `RequestCode` VARCHAR(50) NOT NULL UNIQUE,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `AppointmentID` INT NULL,
  `TestType` VARCHAR(150) NOT NULL,
  `Priority` ENUM('Routine', 'Urgent', 'STAT') NOT NULL DEFAULT 'Routine',
  `ClinicalNotes` TEXT NULL,
  `Status` ENUM('Pending', 'Sample Collected', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `RequestedDate` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  FOREIGN KEY (`AppointmentID`) REFERENCES `appointments`(`AppointmentID`) ON DELETE SET NULL,
  INDEX `idx_lab_patient` (`PatientID`),
  INDEX `idx_lab_doctor` (`DoctorID`),
  INDEX `idx_lab_status` (`Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Laboratory Results Table
CREATE TABLE IF NOT EXISTS `laboratory_results` (
  `ResultID` INT AUTO_INCREMENT PRIMARY KEY,
  `RequestID` INT NOT NULL,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `TestName` VARCHAR(150) NOT NULL,
  `ResultValue` VARCHAR(255) NOT NULL,
  `NormalRange` VARCHAR(150) NOT NULL,
  `Units` VARCHAR(50) NULL,
  `Interpretation` ENUM('Normal', 'High', 'Low', 'Critical', 'Abnormal') NOT NULL DEFAULT 'Normal',
  `Notes` TEXT NULL,
  `AttachmentPath` VARCHAR(255) NULL,
  `ResultDate` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`RequestID`) REFERENCES `laboratory_requests`(`RequestID`) ON DELETE CASCADE,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  INDEX `idx_res_request` (`RequestID`),
  INDEX `idx_res_patient` (`PatientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Referrals Table
CREATE TABLE IF NOT EXISTS `referrals` (
  `ReferralID` INT AUTO_INCREMENT PRIMARY KEY,
  `ReferralCode` VARCHAR(50) NOT NULL UNIQUE,
  `PatientID` INT NOT NULL,
  `ReferringDoctorID` INT NOT NULL,
  `TargetSpecialtyID` INT NOT NULL,
  `TargetDoctorID` INT NULL,
  `Reason` TEXT NOT NULL,
  `ClinicalSummary` TEXT NULL,
  `Priority` ENUM('Routine', 'Urgent', 'Emergency') NOT NULL DEFAULT 'Routine',
  `Status` ENUM('Pending', 'Accepted', 'Completed', 'Declined') NOT NULL DEFAULT 'Pending',
  `ResponseNotes` TEXT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`ReferringDoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  FOREIGN KEY (`TargetSpecialtyID`) REFERENCES `specialties`(`SpecialtyID`) ON DELETE RESTRICT,
  FOREIGN KEY (`TargetDoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE SET NULL,
  INDEX `idx_ref_patient` (`PatientID`),
  INDEX `idx_ref_doctor` (`ReferringDoctorID`),
  INDEX `idx_ref_target_spec` (`TargetSpecialtyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Medical Certificates Table
CREATE TABLE IF NOT EXISTS `medical_certificates` (
  `CertificateID` INT AUTO_INCREMENT PRIMARY KEY,
  `CertificateCode` VARCHAR(50) NOT NULL UNIQUE,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `CertificateType` ENUM('Fit to Work', 'Fit to School', 'Medical Leave', 'General Medical Certificate') NOT NULL DEFAULT 'Fit to Work',
  `Diagnosis` TEXT NOT NULL,
  `DurationStart` DATE NOT NULL,
  `DurationEnd` DATE NOT NULL,
  `DaysExcused` INT NOT NULL DEFAULT 1,
  `Remarks` TEXT NULL,
  `IssueDate` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  INDEX `idx_cert_patient` (`PatientID`),
  INDEX `idx_cert_doctor` (`DoctorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Allergy Records Table
CREATE TABLE IF NOT EXISTS `allergy_records` (
  `AllergyID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `DoctorID` INT NOT NULL,
  `Allergen` VARCHAR(150) NOT NULL,
  `AllergyType` ENUM('Drug', 'Food', 'Environmental', 'Latex', 'Insect', 'Other') NOT NULL DEFAULT 'Drug',
  `Severity` ENUM('Mild', 'Moderate', 'Severe', 'Life-threatening') NOT NULL DEFAULT 'Moderate',
  `Reaction` VARCHAR(255) NOT NULL,
  `Status` ENUM('Active', 'Inactive', 'Suspected') NOT NULL DEFAULT 'Active',
  `ConfirmedDate` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`) ON DELETE RESTRICT,
  INDEX `idx_allg_patient` (`PatientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Create Users for all Doctor Roles (Password: password123)
-- Hash: $2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm
INSERT INTO `users` (`UserID`, `FirstName`, `LastName`, `Username`, `PasswordHash`, `Role`, `Email`, `Status`) VALUES
(101, 'Daniel', 'Lewis', 'cardio', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'cardio@hospital.com', 'Active'),
(102, 'Elena', 'Villanueva', 'neuro', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'neuro@hospital.com', 'Active'),
(103, 'Sophia', 'Miller', 'pedia', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'pedia@hospital.com', 'Active'),
(104, 'James', 'Wilson', 'surgeon', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'surgeon@hospital.com', 'Active'),
(105, 'Maria', 'Santos', 'gastro', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'gastro@hospital.com', 'Active'),
(106, 'Marcus', 'Tan', 'pulmo', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'pulmo@hospital.com', 'Active'),
(107, 'Gabriel', 'Navarro', 'ortho', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'ortho@hospital.com', 'Active'),
(108, 'Kenneth', 'Garcia', 'gp', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Doctor', 'gp@hospital.com', 'Active'),
(109, 'Hospital', 'Registrator', 'registrator_main', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Registrator', 'registrator@hospital.com', 'Active')
ON DUPLICATE KEY UPDATE `Email`=VALUES(`Email`), `Role`=VALUES(`Role`);

-- Seed Doctors mapped to Users & Specialties
INSERT INTO `doctors` (`DoctorID`, `UserID`, `FirstName`, `LastName`, `SpecialtyID`, `Title`, `Specialty`, `LicenseNumber`, `ContactNumber`, `Email`, `ExperienceYears`, `Clinic`, `ClinicRoom`, `ProfileImage`, `Rating`, `ReviewsCount`, `ConsultationFee`, `Bio`, `Education`, `Languages`, `Status`) VALUES
(11, 101, 'Daniel', 'Lewis', 2, 'MD, FACC', 'Cardiologist', 'LIC-MED-CARD-101', '+1 (555) 441-2001', 'cardio@hospital.com', 15, 'TMHIS Heart Center', 'Suite 501, Cardiology Wing', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300', 4.98, 240, '$120.00', 'Senior Cardiologist specializing in ischemic heart disease, hypertension, cardiovascular diagnostics, and heart rhythm management.', 'Harvard Medical School (MD), Cleveland Clinic (Cardiology Fellowship)', 'English, Spanish', 'Available'),
(12, 102, 'Elena', 'Villanueva', 3, 'MD, FCN', 'Neurologist', 'LIC-MED-NEUR-102', '+1 (555) 441-2002', 'neuro@hospital.com', 12, 'Tupi Municipal Hospital', 'Suite 302, North Wing', 'https://images.unsplash.com/photo-1594824813570-5b12852b7a4b?auto=format&fit=crop&q=80&w=300&h=300', 4.92, 165, '$95.00', 'Dedicated to comprehensive headache diagnostics, migraine therapeutics, peripheral neuropathy, and vertigo management.', 'Columbia University Medical Center (Fellowship), St. Luke\'s College of Medicine (MD)', 'English, Tagalog', 'Available'),
(13, 103, 'Sophia', 'Miller', 7, 'MD, FAAP', 'Pediatrician', 'LIC-MED-PED-103', '+1 (555) 441-2003', 'pedia@hospital.com', 11, 'TMHIS Childrens Center', 'Suite 104, Pediatric Wing', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300&h=300', 4.96, 180, '$75.00', 'Expert in infant and adolescent medicine, immunization schedules, growth tracking, and acute childhood illnesses.', 'Boston Children\'s Hospital (Fellowship), Johns Hopkins (MD)', 'English, French', 'Available'),
(14, 104, 'James', 'Wilson', 8, 'MD, FACS', 'General Surgeon', 'LIC-MED-SURG-104', '+1 (555) 441-2004', 'surgeon@hospital.com', 18, 'TMHIS Surgical Pavilion', 'Suite 601, Surgical Wing', 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300&h=300', 4.94, 290, '$150.00', 'Board-certified General Surgeon specializing in laparoscopic procedures, trauma surgery, and soft tissue interventions.', 'Mayo Clinic (Surgical Residency), Yale School of Medicine (MD)', 'English', 'Available'),
(15, 105, 'Maria', 'Santos', 5, 'MD, FPCP, FPSG', 'Gastroenterologist', 'LIC-MED-GAST-105', '+1 (555) 441-2005', 'gastro@hospital.com', 14, 'Tupi Municipal Hospital', 'Suite 405, East Wing', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300&h=300', 4.95, 142, '$85.00', 'Board-certified specialist in digestive wellness, endoscopy, acid reflux management, gastritis, and inflammatory bowel disorders.', 'Johns Hopkins Medicine (Fellowship), UST Faculty of Medicine (MD)', 'English, Tagalog, Spanish', 'Available'),
(16, 106, 'Marcus', 'Tan', 4, 'MD, FPCCP', 'Pulmonologist', 'LIC-MED-PULM-106', '+1 (555) 441-2006', 'pulmo@hospital.com', 15, 'TMHIS Lung Center', 'Room 108, Pavilion B', 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=300&h=300', 4.89, 130, '$90.00', 'Specializing in bronchial asthma, acute and persistent cough evaluation, post-viral respiratory care, and breathing disorders.', 'Mayo Clinic College of Medicine (Pulmonary Fellowship), UP-PGH (MD)', 'English, Hokkien', 'Available'),
(17, 107, 'Gabriel', 'Navarro', 6, 'MD, FPOA', 'Orthopedic', 'LIC-MED-ORTH-107', '+1 (555) 441-2007', 'ortho@hospital.com', 13, 'Tupi Municipal Hospital', 'Suite 101, Ortho Pavilion', 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?auto=format&fit=crop&q=80&w=300&h=300', 4.93, 175, '$85.00', 'Expert in back pain rehabilitation, spine ergonomics, knee and shoulder joint injuries, arthritis, and sports-related strains.', 'Singapore General Hospital (Fellowship), UST Medicine (MD)', 'English, Tagalog', 'Available'),
(18, 108, 'Kenneth', 'Garcia', 1, 'MD, FAFP', 'General Practitioner', 'LIC-MED-GP-108', '+1 (555) 441-2008', 'gp@hospital.com', 8, 'Tupi Municipal Hospital', 'Room 102, Ground Floor', 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=300&h=300', 4.85, 89, '$55.00', 'Comprehensive family medicine, holistic health checks, initial symptom evaluation, and multi-specialty coordination.', 'St. Luke\'s Medical Center (Residency), Ateneo SOM (MD)', 'English, Tagalog', 'Available')
ON DUPLICATE KEY UPDATE `UserID`=VALUES(`UserID`), `SpecialtyID`=VALUES(`SpecialtyID`), `Email`=VALUES(`Email`);

-- Update doctor_specialties
INSERT INTO `doctor_specialties` (`DoctorID`, `SpecialtyID`, `IsPrimary`) VALUES
(11, 2, 1),
(12, 3, 1),
(13, 7, 1),
(14, 8, 1),
(15, 5, 1),
(16, 4, 1),
(17, 6, 1),
(18, 1, 1)
ON DUPLICATE KEY UPDATE `IsPrimary`=VALUES(`IsPrimary`);

-- Link doctor_body_systems for automatic routing
INSERT INTO `doctor_body_systems` (`DoctorID`, `BodySystemID`) VALUES
(11, 2), -- Dr. Lewis -> Cardiovascular
(12, 3), -- Dr. Villanueva -> Nervous
(13, 9), -- Dr. Miller -> Pediatric / General
(14, 9), -- Dr. Wilson -> Surgery / General
(15, 1), -- Dr. Santos -> Digestive
(16, 4), -- Dr. Tan -> Respiratory
(17, 5), -- Dr. Navarro -> Musculoskeletal
(18, 9)  -- Dr. Garcia -> General
ON DUPLICATE KEY UPDATE `DoctorID`=`DoctorID`;

-- Connect existing and new demo appointments for Dr. Daniel Lewis (Cardiologist) & Dr. Elena Villanueva (Neurologist)
INSERT INTO `appointments` (`AppointmentID`, `PatientID`, `DoctorID`, `AppointmentDate`, `AppointmentTime`, `ConsultationType`, `Reason`, `Priority`, `Notes`, `Status`, `CreatedBy`) VALUES
(101, 3, 11, CURDATE(), '09:00 AM', 'In-Person Consultation', 'Precordial chest tightness, fluttering palpitations during light exertion', 'High Priority', 'Requires 12-lead ECG review and blood pressure evaluation.', 'Waiting', 1),
(102, 6, 11, CURDATE(), '10:30 AM', 'In-Person Consultation', 'Cardiovascular follow-up for chronic hypertension and cholesterol review', 'Normal', 'Scheduled fasting lipid profile requested.', 'Scheduled', 1),
(103, 8, 11, CURDATE(), '02:00 PM', 'In-Person Consultation', 'Exertional dyspnea and tachycardia when climbing stairs', 'Urgent', 'Arrhythmia assessment.', 'Scheduled', 1),
(104, 10, 11, CURDATE(), '03:30 PM', 'In-Person Consultation', 'Routine cardiac wellness audit and post-infarction rehabilitation follow-up', 'Normal', 'Echo results to be reviewed.', 'Completed', 1),
(105, 2, 12, CURDATE(), '09:30 AM', 'In-Person Consultation', 'Severe unilateral throbbing migraine with photophobia and dizziness', 'Urgent', 'Quiet environment requested.', 'In Consultation', 1),
(106, 7, 12, CURDATE(), '11:15 AM', 'In-Person Consultation', 'Recurring tension headaches and cervical neck stiffness', 'Normal', 'Postural and stress assessment.', 'Waiting', 1)
ON DUPLICATE KEY UPDATE `DoctorID`=VALUES(`DoctorID`), `Status`=VALUES(`Status`), `AppointmentDate`=VALUES(`AppointmentDate`);

-- Queue Entries
INSERT INTO `patient_queue` (`QueueID`, `AppointmentID`, `PatientID`, `DoctorID`, `QueueNumber`, `QueueDate`, `QueueStatus`, `Priority`, `CalledAt`, `ConsultationStartedAt`) VALUES
(101, 101, 3, 11, 'C-01', CURDATE(), 'Waiting', 'Priority', NULL, NULL),
(102, 102, 6, 11, 'C-02', CURDATE(), 'Waiting', 'Normal', NULL, NULL),
(103, 103, 8, 11, 'C-03', CURDATE(), 'Waiting', 'Emergency', NULL, NULL),
(104, 104, 10, 11, 'C-04', CURDATE(), 'Completed', 'Normal', '2026-08-16 08:30:00', '2026-08-16 08:35:00'),
(105, 105, 2, 12, 'N-01', CURDATE(), 'In Consultation', 'Priority', '2026-08-16 09:30:00', '2026-08-16 09:35:00'),
(106, 106, 7, 12, 'N-02', CURDATE(), 'Waiting', 'Normal', NULL, NULL)
ON DUPLICATE KEY UPDATE `DoctorID`=VALUES(`DoctorID`), `QueueStatus`=VALUES(`QueueStatus`), `QueueDate`=VALUES(`QueueDate`);

-- Consultation Notes Sample
INSERT INTO `consultation_notes` (`NoteID`, `PatientID`, `DoctorID`, `AppointmentID`, `Subjective`, `Objective`, `Assessment`, `Plan`, `ClinicalNotes`, `VitalSigns`) VALUES
(1, 3, 11, 101, 'Patient reports sudden onset of palpitations and mid-sternal tightness after climbing stairs.', 'BP: 138/88 mmHg, HR: 88 bpm, SpO2: 98%, Heart sounds normal S1/S2 without murmurs.', 'Suspected Paroxysmal Tachycardia vs Stress-induced Palpitations.', '12-lead ECG, Lipid Profile, Metoprolol 25mg daily, avoid excessive caffeine.', 'Patient is alert and oriented. Mid-chest flutter exacerbated by stress and caffeine.', '{"bp": "138/88", "hr": "88", "spo2": "98%", "temp": "36.7°C", "weight": "74 kg"}'),
(2, 10, 11, 104, 'Routine 6-month cardiac check. No angina episodes reported. Adherent to statin therapy.', 'BP: 122/78 mmHg, HR: 68 bpm regular, clear lung fields.', 'Stable Coronary Artery Disease - well controlled.', 'Maintain current regimen, continue daily 30-min walking exercise, re-check in 6 months.', 'Good functional capacity. Echocardiogram shows preserved EF 55%.', '{"bp": "122/78", "hr": "68", "spo2": "99%", "temp": "36.5°C", "weight": "80 kg"}')
ON DUPLICATE KEY UPDATE `DoctorID`=VALUES(`DoctorID`);

-- Diagnoses Sample
INSERT INTO `diagnoses` (`DiagnosisID`, `PatientID`, `DoctorID`, `AppointmentID`, `DiagnosisName`, `ICD10Code`, `Type`, `Severity`, `Status`, `Notes`) VALUES
(1, 3, 11, 101, 'Cardiac Palpitations', 'R00.2', 'Primary', 'Moderate', 'Active', 'Fluttering sensations triggered by exertion/stress.'),
(2, 3, 11, 101, 'Essential (Primary) Hypertension', 'I10', 'Secondary', 'Mild', 'Active', 'Stage 1 hypertension noted during triage.'),
(3, 10, 11, 104, 'Atherosclerotic Heart Disease', 'I25.1', 'Primary', 'Moderate', 'Chronic', 'Post-stent stable follow-up.'),
(4, 2, 12, 105, 'Migraine without Aura', 'G43.0', 'Primary', 'Severe', 'Active', 'Unilateral throbbing with severe photophobia and nausea.')
ON DUPLICATE KEY UPDATE `DiagnosisName`=VALUES(`DiagnosisName`);

-- Treatment Plans Sample
INSERT INTO `treatment_plans` (`PlanID`, `PatientID`, `DoctorID`, `AppointmentID`, `DiagnosisID`, `Goal`, `LifestyleRecommendations`, `MedicationPlan`, `FollowUpSchedule`, `FollowUpDate`, `Status`) VALUES
(1, 3, 11, 101, 1, 'Restore stable resting rhythm and keep BP < 130/80 mmHg', 'Reduce sodium intake to < 2g/day. Limit caffeine and energy drinks. Implement daily cardiovascular aerobic walking.', 'Metoprolol Tartrate 25mg PO BID with meals. Amlodipine 5mg PO OD morning.', 'Follow-up in 2 weeks with ECG results', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Active'),
(2, 10, 11, 104, 3, 'Maintain long-term cardiovascular stability and LDL < 70 mg/dL', 'Mediterranean heart-healthy diet, regular physical exercise 150 min/week, maintain smoking cessation.', 'Atorvastatin 40mg PO at bedtime. Aspirin 81mg PO daily after breakfast.', 'Annual cardiac audit in 6 months', DATE_ADD(CURDATE(), INTERVAL 180 DAY), 'Active')
ON DUPLICATE KEY UPDATE `Goal`=VALUES(`Goal`);

-- Prescriptions Sample
INSERT INTO `prescriptions` (`PrescriptionID`, `PrescriptionCode`, `PatientID`, `DoctorID`, `AppointmentID`, `MedicineName`, `Dosage`, `Frequency`, `Duration`, `Instructions`, `Quantity`, `Refills`, `Status`, `IssuedDate`) VALUES
(1, 'RX-2026-0001', 3, 11, 101, 'Metoprolol Tartrate', '25mg', 'Twice daily (Every 12 hours)', '30 Days', 'Take with or immediately following a meal. Monitor resting heart rate.', '60 Tablets', 1, 'Active', CURDATE()),
(2, 'RX-2026-0002', 3, 11, 101, 'Amlodipine Besylate', '5mg', 'Once daily (Every 24 hours)', '30 Days', 'Take in the morning with a full glass of water.', '30 Tablets', 2, 'Active', CURDATE()),
(3, 'RX-2026-0003', 10, 11, 104, 'Atorvastatin Calcium', '40mg', 'Once daily at bedtime', '90 Days', 'Take in the evening. Avoid grapefruit juice.', '90 Tablets', 3, 'Active', CURDATE()),
(4, 'RX-2026-0004', 2, 12, 105, 'Sumatriptan Succinate', '50mg', 'At onset of migraine attack', 'As needed (PRN)', 'Take 1 tablet at initial onset. May repeat once after 2 hours if headache persists. Max 200mg/24h.', '6 Tablets', 1, 'Active', CURDATE())
ON DUPLICATE KEY UPDATE `PrescriptionCode`=VALUES(`PrescriptionCode`);

-- Laboratory Requests Sample
INSERT INTO `laboratory_requests` (`RequestID`, `RequestCode`, `PatientID`, `DoctorID`, `AppointmentID`, `TestType`, `Priority`, `ClinicalNotes`, `Status`, `RequestedDate`) VALUES
(1, 'LAB-2026-0001', 3, 11, 101, '12-Lead Electrocardiogram (ECG)', 'Urgent', 'Evaluate rhythm flutter, rule out supraventricular tachycardia or ischemic changes.', 'In Progress', CURDATE()),
(2, 'LAB-2026-0002', 3, 11, 101, 'Lipid Profile & Fasting Blood Sugar', 'Routine', 'Baseline cardiovascular lipid risk assessment (Total Chol, HDL, LDL, Triglycerides).', 'Pending', CURDATE()),
(3, 'LAB-2026-0003', 10, 11, 104, 'Complete Blood Count (CBC)', 'Routine', 'Annual routine hematology audit.', 'Completed', DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
(4, 'LAB-2026-0004', 2, 12, 105, 'Brain MRI (without contrast)', 'Urgent', 'Rule out structural vascular lesions or intracranial hypertension.', 'Pending', CURDATE())
ON DUPLICATE KEY UPDATE `RequestCode`=VALUES(`RequestCode`);

-- Laboratory Results Sample
INSERT INTO `laboratory_results` (`ResultID`, `RequestID`, `PatientID`, `DoctorID`, `TestName`, `ResultValue`, `NormalRange`, `Units`, `Interpretation`, `Notes`, `ResultDate`) VALUES
(1, 3, 10, 11, 'Hemoglobin', '14.8', '13.5 - 17.5', 'g/dL', 'Normal', 'Adequate oxygen-carrying capacity.', DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
(2, 3, 10, 11, 'White Blood Cells (WBC)', '6.4', '4.5 - 11.0', 'x10^3/uL', 'Normal', 'No signs of acute leukocytosis or active infection.', DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
(3, 3, 10, 11, 'Platelet Count', '245', '150 - 450', 'x10^3/uL', 'Normal', 'Normal coagulation reserve.', DATE_SUB(CURDATE(), INTERVAL 1 DAY))
ON DUPLICATE KEY UPDATE `RequestID`=VALUES(`RequestID`);

-- Referrals Sample
INSERT INTO `referrals` (`ReferralID`, `ReferralCode`, `PatientID`, `ReferringDoctorID`, `TargetSpecialtyID`, `TargetDoctorID`, `Reason`, `ClinicalSummary`, `Priority`, `Status`, `ResponseNotes`) VALUES
(1, 'REF-2026-0001', 3, 11, 3, 12, 'Patient with palpitations also exhibits bilateral tension headaches and lightheadedness. Requesting comprehensive neurological review.', 'Patient has mild BP elevation and occasional dizziness during exertion. Normal S1/S2. Need to rule out neuro-vascular etiology.', 'Routine', 'Pending', NULL),
(2, 'REF-2026-0002', 2, 12, 2, 11, 'Severe migraine accompanied by transient chest racing during acute pain episodes. Cardiovascular clearance requested.', '29 yo female with recurrent migraines. Episodes accompanied by sinus tachycardia.', 'Routine', 'Accepted', 'Scheduled for cardio assessment on next clinic day.')
ON DUPLICATE KEY UPDATE `ReferralCode`=VALUES(`ReferralCode`);

-- Medical Certificates Sample
INSERT INTO `medical_certificates` (`CertificateID`, `CertificateCode`, `PatientID`, `DoctorID`, `CertificateType`, `Diagnosis`, `DurationStart`, `DurationEnd`, `DaysExcused`, `Remarks`, `IssueDate`) VALUES
(1, 'MED-CERT-2026-0001', 3, 11, 'Medical Leave', 'Paroxysmal Tachycardia and Exertional Palpitations', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 3, 'Patient is advised strict rest and avoidance of physical or psychological workplace stress during cardiac diagnostics.', CURDATE()),
(2, 'MED-CERT-2026-0002', 10, 11, 'Fit to Work', 'Stable Coronary Artery Disease (Post-Rehab)', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 365 DAY), 365, 'Patient has been clinically re-evaluated and cleared for normal sedentary office employment duties.', CURDATE())
ON DUPLICATE KEY UPDATE `CertificateCode`=VALUES(`CertificateCode`);

-- Allergy Records Sample
INSERT INTO `allergy_records` (`AllergyID`, `PatientID`, `DoctorID`, `Allergen`, `AllergyType`, `Severity`, `Reaction`, `Status`, `ConfirmedDate`) VALUES
(1, 1, 15, 'Penicillin', 'Drug', 'Moderate', 'Erythematous skin rash, pruritus, mild facial flushing', 'Active', '2026-08-15'),
(2, 3, 11, 'Sulfa Drugs / Sulfonamides', 'Drug', 'Severe', 'Urticarial hives, periorbital edema, mild wheezing', 'Active', '2026-08-16'),
(3, 4, 17, 'Latex', 'Other', 'Moderate', 'Contact dermatitis, localized erythema and itching upon exposure', 'Active', '2026-08-15'),
(4, 5, 15, 'Aspirin (NSAIDs)', 'Drug', 'Severe', 'Acute epigastric burning and bronchospasm', 'Active', '2026-08-15')
ON DUPLICATE KEY UPDATE `PatientID`=VALUES(`PatientID`), `Allergen`=VALUES(`Allergen`);
