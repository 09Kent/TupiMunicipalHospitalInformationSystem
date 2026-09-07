-- Tupi Municipal Hospital Information Management System
-- Master Relational Database Schema Expansion
-- Target Database: MedicalRegistrationDB

USE `MedicalRegistrationDB`;

-- =========================================================================
-- 1. SYSTEM CONFIGURATION & ADMIN TABLES (Role 1: System Administrator)
-- =========================================================================

-- Hospital Information
CREATE TABLE IF NOT EXISTS `hospital_info` (
  `HospitalInfoID` INT AUTO_INCREMENT PRIMARY KEY,
  `HospitalName` VARCHAR(200) NOT NULL DEFAULT 'Tupi Municipal Hospital',
  `HospitalCode` VARCHAR(50) NOT NULL DEFAULT 'TMH-REG-12',
  `TaxID` VARCHAR(50) NOT NULL DEFAULT '004-982-114-000',
  `Address` TEXT NOT NULL,
  `ContactNumber` VARCHAR(50) NOT NULL DEFAULT '+63 (083) 228-0001',
  `EmergencyHotline` VARCHAR(50) NOT NULL DEFAULT '911 / (083) 228-0002',
  `Email` VARCHAR(120) NOT NULL DEFAULT 'admin@tupihospital.gov.ph',
  `DOHAccreditation` VARCHAR(100) NOT NULL DEFAULT 'DOH-ACCRED-L1-2026-084',
  `PhilHealthAccreditation` VARCHAR(100) NOT NULL DEFAULT 'PH-HOSP-1209384',
  `BedCapacity` INT NOT NULL DEFAULT 50,
  `MedicalDirector` VARCHAR(150) NOT NULL DEFAULT 'Dr. Maria Santos, MD, MHA',
  `LogoPath` VARCHAR(255) NULL DEFAULT 'assets/logo.png',
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default hospital info
INSERT INTO `hospital_info` (`HospitalInfoID`, `HospitalName`, `HospitalCode`, `TaxID`, `Address`, `ContactNumber`, `EmergencyHotline`, `Email`, `DOHAccreditation`, `PhilHealthAccreditation`, `BedCapacity`, `MedicalDirector`)
VALUES (1, 'Tupi Municipal Hospital', 'TMH-REG-12', '004-982-114-000', 'National Highway, Poblacion, Tupi, South Cotabato 9505, Philippines', '+63 (083) 228-0001', '+63 (083) 228-0002', 'admin@tupihospital.gov.ph', 'DOH-ACCRED-L1-2026-084', 'PH-HOSP-1209384', 50, 'Dr. Maria Santos, MD, MHA')
ON DUPLICATE KEY UPDATE `HospitalName`=VALUES(`HospitalName`);

-- Departments Management
CREATE TABLE IF NOT EXISTS `departments` (
  `DepartmentID` INT AUTO_INCREMENT PRIMARY KEY,
  `DepartmentCode` VARCHAR(50) NOT NULL UNIQUE,
  `DepartmentName` VARCHAR(150) NOT NULL,
  `DepartmentType` ENUM('Clinical', 'Administrative', 'Diagnostic', 'Support') NOT NULL DEFAULT 'Clinical',
  `HeadOfDepartment` VARCHAR(150) NULL,
  `Location` VARCHAR(150) NOT NULL DEFAULT 'Main Building',
  `ContactExtension` VARCHAR(20) NULL,
  `Status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed standard hospital departments
INSERT INTO `departments` (`DepartmentID`, `DepartmentCode`, `DepartmentName`, `DepartmentType`, `HeadOfDepartment`, `Location`, `ContactExtension`, `Status`) VALUES
(1, 'DEP-EMR', 'Emergency Department', 'Clinical', 'Dr. James Wilson, MD', 'Ground Floor, ER Wing', '101', 'Active'),
(2, 'DEP-OPD', 'Outpatient Department', 'Clinical', 'Dr. Kenneth Garcia, MD', 'Ground Floor, East Wing', '102', 'Active'),
(3, 'DEP-INT', 'Internal Medicine', 'Clinical', 'Dr. Daniel Lewis, MD', '2nd Floor, Medical Wing', '201', 'Active'),
(4, 'DEP-PED', 'Pediatrics Department', 'Clinical', 'Dr. Sophia Miller, MD', '2nd Floor, Pediatric Wing', '202', 'Active'),
(5, 'DEP-SUR', 'General Surgery', 'Clinical', 'Dr. James Wilson, MD', '3rd Floor, Surgical Suite', '301', 'Active'),
(6, 'DEP-OBG', 'Obstetrics & Gynecology', 'Clinical', 'Dr. Elena Villanueva, MD', '2nd Floor, Maternal Wing', '203', 'Active'),
(7, 'DEP-LAB', 'Clinical Laboratory', 'Diagnostic', 'Clarisse Mae Santos, RMT', 'Ground Floor, Diagnostic Lab', '105', 'Active'),
(8, 'DEP-PHA', 'Hospital Pharmacy', 'Support', 'Kareen Joy Ramos, RPh', 'Ground Floor, Main Lobby', '104', 'Active'),
(9, 'DEP-BIL', 'Billing & Cashier Division', 'Administrative', 'Maria Santos', 'Ground Floor, Cashier Booth', '106', 'Active'),
(10, 'DEP-REC', 'Medical Records Department (HIRM)', 'Administrative', 'Mark Anthony Valenzuela, RMT', '1st Floor, Records Archive', '108', 'Active'),
(11, 'DEP-NUR', 'Nursing Services Administration', 'Support', 'Elena Gomez, RN', '2nd Floor, Nursing Station', '205', 'Active'),
(12, 'DEP-ADM', 'Hospital Administration & IT', 'Administrative', 'System Administrator', '4th Floor, Executive Suite', '401', 'Active')
ON DUPLICATE KEY UPDATE `DepartmentName`=VALUES(`DepartmentName`);

-- Service Fees Management
CREATE TABLE IF NOT EXISTS `service_fees` (
  `FeeID` INT AUTO_INCREMENT PRIMARY KEY,
  `ServiceCode` VARCHAR(50) NOT NULL UNIQUE,
  `ServiceName` VARCHAR(200) NOT NULL,
  `Category` ENUM('Consultation', 'Laboratory', 'Radiology', 'Pharmacy', 'Nursing/Ward', 'Surgical/Procedure', 'Administrative', 'Emergency') NOT NULL DEFAULT 'Consultation',
  `DepartmentID` INT NULL,
  `StandardRate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `PhilHealthCoveredRate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `DiscountEligible` TINYINT(1) NOT NULL DEFAULT 1,
  `Description` TEXT NULL,
  `Status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`DepartmentID`) REFERENCES `departments`(`DepartmentID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed standard TMH service fees
INSERT INTO `service_fees` (`FeeID`, `ServiceCode`, `ServiceName`, `Category`, `DepartmentID`, `StandardRate`, `PhilHealthCoveredRate`, `DiscountEligible`, `Description`, `Status`) VALUES
(1, 'FEE-CON-GP', 'General Medical Consultation', 'Consultation', 2, 250.00, 250.00, 1, 'Standard primary care consultation', 'Active'),
(2, 'FEE-CON-SP', 'Specialist Consultation', 'Consultation', 3, 500.00, 400.00, 1, 'Consultation with medical specialist (Cardio, Pulmo, Neuro, etc.)', 'Active'),
(3, 'FEE-LAB-CBC', 'Complete Blood Count (CBC) with Platelet', 'Laboratory', 7, 280.00, 280.00, 1, 'Routine hematology screening', 'Active'),
(4, 'FEE-LAB-LIP', 'Lipid Profile Panel', 'Laboratory', 7, 650.00, 500.00, 1, 'Total cholesterol, HDL, LDL, Triglycerides', 'Active'),
(5, 'FEE-LAB-FBS', 'Fasting Blood Sugar (FBS)', 'Laboratory', 7, 180.00, 180.00, 1, 'Glucose monitoring test', 'Active'),
(6, 'FEE-LAB-URN', 'Urinalysis Routine Examination', 'Laboratory', 7, 150.00, 150.00, 1, 'Standard urine microscopy', 'Active'),
(7, 'FEE-RAD-XRAY', 'Chest X-Ray (PA View)', 'Radiology', 7, 450.00, 400.00, 1, 'Digital chest radiography', 'Active'),
(8, 'FEE-RAD-ECG', '12-Lead Electrocardiogram (ECG)', 'Radiology', 3, 350.00, 350.00, 1, 'Cardiac electrical activity recording', 'Active'),
(9, 'FEE-NUR-ADM', 'Ward Nursing & Accommodation (Daily)', 'Nursing/Ward', 11, 800.00, 600.00, 1, 'Routine inpatient nursing care and bed fee', 'Active'),
(10, 'FEE-EMR-FEE', 'Emergency Room Triage & Service Fee', 'Emergency', 1, 450.00, 450.00, 1, 'Immediate ER stabilization and intake fee', 'Active'),
(11, 'FEE-MED-CERT', 'Medical Certificate Issuance Fee', 'Administrative', 10, 150.00, 0.00, 1, 'Official Fit to Work / School certificate', 'Active')
ON DUPLICATE KEY UPDATE `ServiceName`=VALUES(`ServiceName`), `StandardRate`=VALUES(`StandardRate`);

-- Roles Master Table
CREATE TABLE IF NOT EXISTS `roles` (
  `RoleID` INT AUTO_INCREMENT PRIMARY KEY,
  `RoleCode` VARCHAR(50) NOT NULL UNIQUE,
  `RoleName` VARCHAR(100) NOT NULL,
  `Description` TEXT NULL,
  `IsSystemRole` TINYINT(1) NOT NULL DEFAULT 0,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissions Table
CREATE TABLE IF NOT EXISTS `permissions` (
  `PermissionID` INT AUTO_INCREMENT PRIMARY KEY,
  `PermissionCode` VARCHAR(100) NOT NULL UNIQUE,
  `PermissionName` VARCHAR(150) NOT NULL,
  `Module` VARCHAR(100) NOT NULL,
  `Description` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Role Permissions Junction Table
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `RoleID` INT NOT NULL,
  `PermissionID` INT NOT NULL,
  PRIMARY KEY (`RoleID`, `PermissionID`),
  FOREIGN KEY (`RoleID`) REFERENCES `roles`(`RoleID`) ON DELETE CASCADE,
  FOREIGN KEY (`PermissionID`) REFERENCES `permissions`(`PermissionID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed 9 Standard FDD Roles
INSERT INTO `roles` (`RoleID`, `RoleCode`, `RoleName`, `Description`, `IsSystemRole`) VALUES
(1, 'ADMIN', 'System Administrator', 'Full access to system configuration, user accounts, and monitoring', 1),
(2, 'CHIEF', 'Hospital Chief / Medical Director', 'Operational oversight, institutional performance tracking, and reports', 1),
(3, 'RECORDS', 'Medical Records Officer', 'Patient records administration, validation, and historical summaries', 1),
(4, 'REGISTER', 'Admitting / Registration Staff', 'Patient registration, appointment scheduling, and queue coordination', 1),
(5, 'DOCTOR', 'Attending Physician', 'Consultations, SOAP notes, diagnosis, e-prescriptions, and lab requests', 1),
(6, 'NURSE', 'Nurse on Duty', 'Vital signs logging, patient bed monitoring, and task execution', 1),
(7, 'MEDTECH', 'Medical Technologist', 'Lab requests processing, specimen tracking, and test results entry', 1),
(8, 'PHARMACIST', 'Pharmacist / Pharmacy Aide', 'Prescription validation, drug dispensing, and inventory management', 1),
(9, 'BILLING', 'Billing / Cashier Staff', 'Charge computations, discount processing, payments, and receipts', 1)
ON DUPLICATE KEY UPDATE `RoleName`=VALUES(`RoleName`);

-- System Global Audit Log Table
CREATE TABLE IF NOT EXISTS `system_audit_logs` (
  `LogID` INT AUTO_INCREMENT PRIMARY KEY,
  `UserID` INT NULL,
  `UserName` VARCHAR(100) NOT NULL,
  `UserRole` VARCHAR(50) NOT NULL,
  `Action` VARCHAR(100) NOT NULL,
  `Module` VARCHAR(80) NOT NULL,
  `RecordID` VARCHAR(50) NULL,
  `Details` TEXT NULL,
  `IPAddress` VARCHAR(50) NULL,
  `UserAgent` VARCHAR(255) NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_audit_user` (`UserID`),
  INDEX `idx_audit_module` (`Module`),
  INDEX `idx_audit_date` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- System Error Log Table
CREATE TABLE IF NOT EXISTS `system_error_logs` (
  `ErrorID` INT AUTO_INCREMENT PRIMARY KEY,
  `ErrorCode` VARCHAR(50) NULL,
  `ErrorMessage` TEXT NOT NULL,
  `Module` VARCHAR(80) NULL,
  `StackTrace` TEXT NULL,
  `RequestUri` VARCHAR(255) NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Database Backup Operations History
CREATE TABLE IF NOT EXISTS `backup_logs` (
  `BackupID` INT AUTO_INCREMENT PRIMARY KEY,
  `FileName` VARCHAR(255) NOT NULL,
  `FileSize` VARCHAR(50) NOT NULL,
  `BackupType` ENUM('Full Database', 'Schema Only', 'Data Only') NOT NULL DEFAULT 'Full Database',
  `CreatedBy` INT NULL,
  `Status` ENUM('Completed', 'Failed', 'In Progress', 'Restored') NOT NULL DEFAULT 'Completed',
  `Notes` TEXT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure System Administrator User exists in `users` (Password: admin123)
-- Hash: $2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm (password123 / admin123)
INSERT INTO `users` (`UserID`, `FirstName`, `LastName`, `Username`, `PasswordHash`, `Role`, `Email`, `Status`) VALUES
(1, 'System', 'Administrator', 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Admin', 'admin@tupihospital.gov.ph', 'Active'),
(2, 'Maria', 'Santos', 'director', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Staff', 'director@tupihospital.gov.ph', 'Active'),
(3, 'Mark Anthony', 'Valenzuela', 'records', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Staff', 'records@tupihospital.gov.ph', 'Active'),
(4, 'Elena', 'Gomez', 'nurse', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Staff', 'nurse@tupihospital.gov.ph', 'Active'),
(5, 'Clarisse Mae', 'Santos', 'medtech', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Staff', 'medtech@tupihospital.gov.ph', 'Active'),
(6, 'Kareen Joy', 'Ramos', 'pharmacist', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Staff', 'pharmacist@tupihospital.gov.ph', 'Active'),
(7, 'Maria', 'Santos', 'cashier', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Staff', 'cashier@tupihospital.gov.ph', 'Active')
ON DUPLICATE KEY UPDATE `Email`=VALUES(`Email`);


-- =========================================================================
-- 2. NURSE PORTAL TABLES (Role 6: Nurse on Duty)
-- =========================================================================

CREATE TABLE IF NOT EXISTS `patient_vitals` (
  `VitalID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `AppointmentID` INT NULL,
  `BloodPressure` VARCHAR(30) NOT NULL,
  `HeartRate` INT NOT NULL,
  `RespiratoryRate` INT NOT NULL DEFAULT 18,
  `Temperature` DECIMAL(4,1) NOT NULL,
  `OxygenSaturation` INT NOT NULL DEFAULT 98,
  `PainScale` INT NOT NULL DEFAULT 0,
  `WeightKg` DECIMAL(5,2) NULL,
  `HeightCm` DECIMAL(5,2) NULL,
  `BMI` DECIMAL(4,1) NULL,
  `ClinicalNotes` TEXT NULL,
  `RecordedBy` INT NULL,
  `RecordedByName` VARCHAR(100) NOT NULL DEFAULT 'Nurse Elena Gomez, RN',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  INDEX `idx_vit_patient` (`PatientID`, `CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `nurse_tasks` (
  `TaskID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `NurseID` INT NULL,
  `DoctorID` INT NULL,
  `TaskTitle` VARCHAR(200) NOT NULL,
  `Category` ENUM('Medication Admin', 'Vital Check', 'IV Fluid Replacement', 'Wound Dressing', 'Pre-op Prep', 'General Care') NOT NULL DEFAULT 'Vital Check',
  `DueTime` VARCHAR(30) NOT NULL,
  `Priority` ENUM('Normal', 'Urgent', 'STAT') NOT NULL DEFAULT 'Normal',
  `Status` ENUM('Pending', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `Remarks` TEXT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CompletedAt` DATETIME NULL,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed demo vitals
INSERT INTO `patient_vitals` (`VitalID`, `PatientID`, `BloodPressure`, `HeartRate`, `RespiratoryRate`, `Temperature`, `OxygenSaturation`, `PainScale`, `WeightKg`, `HeightCm`, `BMI`, `ClinicalNotes`, `RecordedByName`) VALUES
(1, 1, '120/80', 74, 16, 36.6, 99, 0, 68.5, 172.0, 23.2, 'Patient is resting comfortably, normal sinus rhythm.', 'Elena Gomez, RN'),
(2, 2, '130/85', 82, 18, 36.8, 98, 4, 58.0, 160.0, 22.7, 'Moderate tension headache reported.', 'Elena Gomez, RN'),
(3, 3, '138/88', 88, 20, 36.7, 98, 2, 74.0, 168.0, 26.2, 'Fluttering sensation, monitoring requested by Dr. Lewis.', 'Elena Gomez, RN'),
(4, 4, '118/76', 70, 16, 36.5, 99, 0, 62.0, 165.0, 22.8, 'Routine pre-consultation vitals.', 'Elena Gomez, RN'),
(5, 5, '124/82', 76, 17, 36.6, 99, 1, 70.0, 170.0, 24.2, 'Stable baseline vitals.', 'Elena Gomez, RN')
ON DUPLICATE KEY UPDATE `BloodPressure`=VALUES(`BloodPressure`);


-- =========================================================================
-- 3. MEDICAL TECHNOLOGIST TABLES (Role 7: Medical Technologist)
-- =========================================================================

CREATE TABLE IF NOT EXISTS `test_catalog` (
  `CatalogID` INT AUTO_INCREMENT PRIMARY KEY,
  `TestCode` VARCHAR(50) NOT NULL UNIQUE,
  `TestName` VARCHAR(200) NOT NULL,
  `Category` ENUM('Hematology', 'Clinical Chemistry', 'Urinalysis', 'Microbiology', 'Serology', 'Immunology', 'Blood Banking') NOT NULL DEFAULT 'Clinical Chemistry',
  `SpecimenType` VARCHAR(100) NOT NULL DEFAULT 'Whole Blood / Serum',
  `TurnaroundTime` VARCHAR(50) NOT NULL DEFAULT '2–4 Hours',
  `StandardPrice` DECIMAL(10,2) NOT NULL DEFAULT 350.00,
  `Status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reference_ranges` (
  `RangeID` INT AUTO_INCREMENT PRIMARY KEY,
  `CatalogID` INT NOT NULL,
  `ParameterName` VARCHAR(100) NOT NULL,
  `Unit` VARCHAR(50) NOT NULL,
  `MaleRange` VARCHAR(100) NOT NULL,
  `FemaleRange` VARCHAR(100) NOT NULL,
  `CriticalLow` VARCHAR(50) NULL,
  `CriticalHigh` VARCHAR(50) NULL,
  FOREIGN KEY (`CatalogID`) REFERENCES `test_catalog`(`CatalogID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `laboratory_samples` (
  `SampleID` INT AUTO_INCREMENT PRIMARY KEY,
  `SampleBarcode` VARCHAR(50) NOT NULL UNIQUE,
  `RequestID` INT NOT NULL,
  `PatientID` INT NOT NULL,
  `SpecimenType` VARCHAR(100) NOT NULL,
  `CollectionDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CollectedBy` VARCHAR(100) NOT NULL DEFAULT 'Clarisse Mae Santos, RMT',
  `ProcessingStatus` ENUM('Collected', 'In Lab', 'Processing', 'Analyzed', 'Rejected') NOT NULL DEFAULT 'Collected',
  `StorageLocation` VARCHAR(100) NOT NULL DEFAULT 'Rack A-04 (Centrifuge Bay)',
  `Notes` TEXT NULL,
  FOREIGN KEY (`RequestID`) REFERENCES `laboratory_requests`(`RequestID`) ON DELETE CASCADE,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed standard catalog
INSERT INTO `test_catalog` (`CatalogID`, `TestCode`, `TestName`, `Category`, `SpecimenType`, `TurnaroundTime`, `StandardPrice`) VALUES
(1, 'LAB-CBC', 'Complete Blood Count (CBC)', 'Hematology', 'EDTA Whole Blood', '1–2 Hours', 280.00),
(2, 'LAB-LIP', 'Lipid Profile Panel', 'Clinical Chemistry', 'Serum (Fasting 10-12h)', '3–4 Hours', 650.00),
(3, 'LAB-FBS', 'Fasting Blood Sugar (FBS)', 'Clinical Chemistry', 'Fluoride Oxalate / Serum', '1–2 Hours', 180.00),
(4, 'LAB-URN', 'Routine Urinalysis', 'Urinalysis', 'Midstream Clean Catch Urine', '1 Hour', 150.00),
(5, 'LAB-KID', 'Kidney Function (BUN, Creatinine)', 'Clinical Chemistry', 'Serum', '2–3 Hours', 420.00),
(6, 'LAB-LIV', 'Liver Function Panel (SGOT, SGPT)', 'Clinical Chemistry', 'Serum', '2–3 Hours', 550.00)
ON DUPLICATE KEY UPDATE `TestName`=VALUES(`TestName`);


-- =========================================================================
-- 4. PHARMACY INVENTORY & DISPENSING TABLES (Role 8: Pharmacist)
-- =========================================================================

CREATE TABLE IF NOT EXISTS `pharmacy_inventory` (
  `InventoryID` INT AUTO_INCREMENT PRIMARY KEY,
  `ItemCode` VARCHAR(50) NOT NULL UNIQUE,
  `GenericName` VARCHAR(200) NOT NULL,
  `BrandName` VARCHAR(150) NULL,
  `DosageForm` VARCHAR(80) NOT NULL DEFAULT 'Tablet',
  `Strength` VARCHAR(80) NOT NULL,
  `Category` VARCHAR(100) NOT NULL DEFAULT 'Essential Medicine',
  `UnitCost` DECIMAL(10,2) NOT NULL DEFAULT 5.00,
  `SellingPrice` DECIMAL(10,2) NOT NULL DEFAULT 8.50,
  `CurrentStock` INT NOT NULL DEFAULT 100,
  `ReorderLevel` INT NOT NULL DEFAULT 30,
  `BatchNumber` VARCHAR(80) NOT NULL DEFAULT 'B-2026-001',
  `ExpiryDate` DATE NOT NULL,
  `Supplier` VARCHAR(150) NOT NULL DEFAULT 'DOH Central Depot / Mercury Drug Wholesale',
  `Status` ENUM('In Stock', 'Low Stock', 'Out of Stock', 'Expired') NOT NULL DEFAULT 'In Stock',
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dispensing_records` (
  `DispenseID` INT AUTO_INCREMENT PRIMARY KEY,
  `DispenseCode` VARCHAR(50) NOT NULL UNIQUE,
  `PrescriptionID` INT NOT NULL,
  `PatientID` INT NOT NULL,
  `DispensedBy` INT NULL,
  `DispenserName` VARCHAR(100) NOT NULL DEFAULT 'Kareen Joy Ramos, RPh',
  `QuantityDispensed` VARCHAR(50) NOT NULL,
  `DosageInstructions` TEXT NOT NULL,
  `BatchNumber` VARCHAR(80) NULL,
  `DispenseDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` ENUM('Dispensed', 'Partially Dispensed', 'Cancelled') NOT NULL DEFAULT 'Dispensed',
  `Notes` TEXT NULL,
  FOREIGN KEY (`PrescriptionID`) REFERENCES `prescriptions`(`PrescriptionID`) ON DELETE CASCADE,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pharmacy_stock_movements` (
  `MovementID` INT AUTO_INCREMENT PRIMARY KEY,
  `InventoryID` INT NOT NULL,
  `MovementType` ENUM('Stock In', 'Dispensed', 'Damaged', 'Expired Disposal', 'Adjustment') NOT NULL,
  `Quantity` INT NOT NULL,
  `ReferenceCode` VARCHAR(100) NULL,
  `RecordedBy` VARCHAR(100) NOT NULL DEFAULT 'Pharmacist Staff',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`InventoryID`) REFERENCES `pharmacy_inventory`(`InventoryID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed standard medicine inventory
INSERT INTO `pharmacy_inventory` (`InventoryID`, `ItemCode`, `GenericName`, `BrandName`, `DosageForm`, `Strength`, `Category`, `UnitCost`, `SellingPrice`, `CurrentStock`, `ReorderLevel`, `BatchNumber`, `ExpiryDate`, `Status`) VALUES
(1, 'MED-AMOX-500', 'Amoxicillin Trihydrate', 'Amoxil', 'Capsule', '500mg', 'Antibiotic', 4.50, 7.50, 450, 100, 'B-2026-081', DATE_ADD(CURDATE(), INTERVAL 540 DAY), 'In Stock'),
(2, 'MED-METO-50', 'Metoprolol Tartrate', 'Betaloc', 'Tablet', '50mg', 'Antihypertensive', 5.20, 9.00, 280, 50, 'B-2026-042', DATE_ADD(CURDATE(), INTERVAL 620 DAY), 'In Stock'),
(3, 'MED-AMLO-5', 'Amlodipine Besylate', 'Norvasc', 'Tablet', '5mg', 'Antihypertensive', 3.80, 6.50, 320, 60, 'B-2026-019', DATE_ADD(CURDATE(), INTERVAL 480 DAY), 'In Stock'),
(4, 'MED-ATOR-40', 'Atorvastatin Calcium', 'Lipitor', 'Tablet', '40mg', 'Cardiovascular / Statin', 12.00, 18.50, 190, 40, 'B-2026-092', DATE_ADD(CURDATE(), INTERVAL 400 DAY), 'In Stock'),
(5, 'MED-LOSA-50', 'Losartan Potassium', 'Cozaar', 'Tablet', '50mg', 'Antihypertensive', 6.00, 10.00, 310, 50, 'B-2026-033', DATE_ADD(CURDATE(), INTERVAL 500 DAY), 'In Stock'),
(6, 'MED-METF-500', 'Metformin Hydrochloride', 'Glucophage', 'Tablet', '500mg', 'Antidiabetic', 2.50, 4.50, 500, 100, 'B-2026-055', DATE_ADD(CURDATE(), INTERVAL 700 DAY), 'In Stock'),
(7, 'MED-PARA-500', 'Paracetamol', 'Biogesic', 'Tablet', '500mg', 'Analgesic / Antipyretic', 1.80, 3.50, 40, 50, 'B-2026-012', DATE_ADD(CURDATE(), INTERVAL 360 DAY), 'Low Stock')
ON DUPLICATE KEY UPDATE `GenericName`=VALUES(`GenericName`), `CurrentStock`=VALUES(`CurrentStock`);


-- =========================================================================
-- 5. BILLING & CASHIER TABLES (Role 9: Billing / Cashier Staff)
-- =========================================================================

CREATE TABLE IF NOT EXISTS `billing_charges` (
  `ChargeID` INT AUTO_INCREMENT PRIMARY KEY,
  `PatientID` INT NOT NULL,
  `AppointmentID` INT NULL,
  `FeeID` INT NULL,
  `ChargeCategory` ENUM('Consultation', 'Laboratory', 'Radiology', 'Pharmacy', 'Nursing/Ward', 'Surgical/Procedure', 'Administrative', 'Emergency') NOT NULL DEFAULT 'Consultation',
  `ItemDescription` VARCHAR(255) NOT NULL,
  `Quantity` INT NOT NULL DEFAULT 1,
  `UnitPrice` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `SubTotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `DiscountAmount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `NetAmount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `BillingStatus` ENUM('Unbilled', 'Invoiced', 'Paid', 'Waived') NOT NULL DEFAULT 'Unbilled',
  `CreatedBy` VARCHAR(100) NOT NULL DEFAULT 'Billing Staff',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE,
  FOREIGN KEY (`FeeID`) REFERENCES `service_fees`(`FeeID`) ON DELETE SET NULL,
  INDEX `idx_chg_patient` (`PatientID`, `BillingStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `billing_invoices` (
  `InvoiceID` INT AUTO_INCREMENT PRIMARY KEY,
  `InvoiceNumber` VARCHAR(50) NOT NULL UNIQUE,
  `PatientID` INT NOT NULL,
  `GrossAmount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `DiscountType` ENUM('None', 'Senior Citizen (20%)', 'PWD (20%)', 'Indigent / Barangay (100%)', 'Hospital Employee (50%)') NOT NULL DEFAULT 'None',
  `DiscountAmount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `PhilHealthDeduction` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `TotalPayable` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `AmountPaid` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `BalanceDue` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `PaymentStatus` ENUM('Unpaid', 'Partially Paid', 'Paid In Full', 'Cancelled') NOT NULL DEFAULT 'Unpaid',
  `BilledBy` VARCHAR(100) NOT NULL DEFAULT 'Maria Santos (Cashier)',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DueDate` DATE NOT NULL,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `billing_payments` (
  `PaymentID` INT AUTO_INCREMENT PRIMARY KEY,
  `ReceiptNumber` VARCHAR(50) NOT NULL UNIQUE,
  `InvoiceID` INT NOT NULL,
  `PatientID` INT NOT NULL,
  `AmountPaid` DECIMAL(10,2) NOT NULL,
  `PaymentMethod` ENUM('Cash', 'GCash / Maya', 'Credit/Debit Card', 'Bank Transfer', 'PhilHealth Direct') NOT NULL DEFAULT 'Cash',
  `ReferenceNumber` VARCHAR(100) NULL,
  `AmountInWords` TEXT NOT NULL,
  `CashierName` VARCHAR(100) NOT NULL DEFAULT 'Maria Santos',
  `PaymentDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`InvoiceID`) REFERENCES `billing_invoices`(`InvoiceID`) ON DELETE CASCADE,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed demo initial charges
INSERT INTO `billing_charges` (`ChargeID`, `PatientID`, `ChargeCategory`, `ItemDescription`, `Quantity`, `UnitPrice`, `SubTotal`, `DiscountAmount`, `NetAmount`, `BillingStatus`) VALUES
(1, 3, 'Consultation', 'Specialist Consultation - Cardiology (Dr. Lewis)', 1, 500.00, 500.00, 0.00, 500.00, 'Unbilled'),
(2, 3, 'Laboratory', '12-Lead Electrocardiogram (ECG)', 1, 350.00, 350.00, 0.00, 350.00, 'Unbilled'),
(3, 3, 'Pharmacy', 'Metoprolol Tartrate 50mg (60 Tabs)', 60, 9.00, 540.00, 0.00, 540.00, 'Unbilled'),
(4, 10, 'Consultation', 'Cardiovascular Follow-up Consultation', 1, 500.00, 500.00, 100.00, 400.00, 'Paid'),
(5, 2, 'Consultation', 'Neurology Consultation (Dr. Villanueva)', 1, 500.00, 500.00, 0.00, 500.00, 'Unbilled')
ON DUPLICATE KEY UPDATE `ItemDescription`=VALUES(`ItemDescription`);


-- =========================================================================
-- 6. MEDICAL RECORDS OFFICER TABLES (Role 3: Medical Records Officer)
-- =========================================================================

CREATE TABLE IF NOT EXISTS `record_release_requests` (
  `RequestID` INT AUTO_INCREMENT PRIMARY KEY,
  `RequestNumber` VARCHAR(50) NOT NULL UNIQUE,
  `PatientID` INT NOT NULL,
  `RequestType` ENUM('Complete Medical History', 'Clinical Summary', 'Laboratory Results Only', 'Certificate of Confinement', 'Insurance Abstract') NOT NULL,
  `RequestorName` VARCHAR(150) NOT NULL,
  `RelationshipToPatient` VARCHAR(80) NOT NULL DEFAULT 'Self',
  `PurposeOfRequest` VARCHAR(255) NOT NULL,
  `Status` ENUM('Pending Review', 'Approved / Processing', 'Released', 'Rejected') NOT NULL DEFAULT 'Pending Review',
  `ProcessedBy` VARCHAR(100) NULL,
  `RequestedDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ReleasedDate` DATETIME NULL,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `record_release_requests` (`RequestID`, `RequestNumber`, `PatientID`, `RequestType`, `RequestorName`, `RelationshipToPatient`, `PurposeOfRequest`, `Status`, `ProcessedBy`) VALUES
(1, 'REQ-2026-001', 3, 'Clinical Summary', 'Eduardo Bautista', 'Self', 'PhilHealth Claims & Employer Sickness Benefit', 'Approved / Processing', 'Mark Anthony Valenzuela, RMT'),
(2, 'REQ-2026-002', 10, 'Complete Medical History', 'Angelica Morales', 'Self', 'Personal Health Record & Specialist Transfer', 'Released', 'Mark Anthony Valenzuela, RMT')
ON DUPLICATE KEY UPDATE `RequestNumber`=VALUES(`RequestNumber`);
