<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Database Seeder for Role 2: Hospital Chief / Medical Director (MySQL & SQLite Compatible)
 */

declare(strict_types=1);

function seedHospitalData(PDO $db): void {
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $isMysql = ($driver === 'mysql');

    // Check if data already exists
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM departments");
    if ($stmt && (int)$stmt->fetchColumn() > 0) {
        return; // already seeded
    }

    $db->beginTransaction();

    try {
        // 1. Departments (10 Realistic Departments)
        $departments = [
            ['Medical Records', 'HOSP-REC', 'Maria Clara Santos, RMT', 18, 92.5, 186, 12, 94.0],
            ['Nursing', 'HOSP-NUR', 'Chief Nurse Patricia Lopez, RN', 42, 96.0, 420, 18, 95.5],
            ['Laboratory', 'HOSP-LAB', 'Dr. Roberto Cruz, MD, FPSP', 22, 94.0, 248, 16, 96.0],
            ['Pharmacy', 'HOSP-PHAR', 'Pharmacist Lead Ana Garcia, RPh', 16, 91.0, 310, 14, 93.0],
            ['Billing & Cashier', 'HOSP-BIL', 'Ferdinand Ramos, CPA', 14, 89.5, 175, 8, 91.5],
            ['Outpatient (OPD)', 'HOSP-OPD', 'Dr. Carlos Mendoza, MD', 28, 93.0, 290, 22, 92.0],
            ['Emergency (ER)', 'HOSP-ER', 'Dr. Juan Reyes, MD, FPCS', 26, 97.5, 195, 6, 96.8],
            ['Surgery / Operating Room', 'HOSP-OR', 'Dr. Manuel Roxas, MD, FPCS', 20, 95.0, 140, 5, 95.0],
            ['Internal Medicine', 'HOSP-IM', 'Dr. Teresa Villanueva, MD, FPCP', 24, 94.5, 230, 15, 94.0],
            ['Hospital Administration', 'HOSP-ADM', 'Dr. Maria Santos, MD, MHA', 12, 98.0, 110, 2, 98.0],
        ];

        $deptInsert = $db->prepare("
            INSERT INTO departments (name, code, head, total_staff, active_rate, completed_tasks, pending_tasks, performance_score)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($departments as $dept) {
            $deptInsert->execute($dept);
        }

        // 2. Staff Members (32 Staff Members with realistic Philippine names & roles)
        $staff = [
            ['STF-001', 'Maria Clara Santos', 'Medical Records Officer', 'Medical Records', 'Day Shift (7 AM - 3 PM)', 'm.santos@hospity.ph', '0917-111-2233'],
            ['STF-002', 'Patricia Lopez', 'Chief Nurse', 'Nursing', 'Day Shift (7 AM - 3 PM)', 'p.lopez@hospity.ph', '0917-222-3344'],
            ['STF-003', 'John Michael Cruz', 'Medical Technologist', 'Laboratory', 'Day Shift (7 AM - 3 PM)', 'jm.cruz@hospity.ph', '0917-333-4455'],
            ['STF-004', 'Ana Garcia', 'Senior Pharmacist', 'Pharmacy', 'Day Shift (7 AM - 3 PM)', 'a.garcia@hospity.ph', '0917-444-5566'],
            ['STF-005', 'Ferdinand Ramos', 'Billing Supervisor', 'Billing & Cashier', 'Day Shift (7 AM - 3 PM)', 'f.ramos@hospity.ph', '0917-555-6677'],
            ['STF-006', 'Dr. Juan Reyes', 'Attending ER Physician', 'Emergency (ER)', 'Night Shift (11 PM - 7 AM)', 'j.reyes@hospity.ph', '0917-666-7788'],
            ['STF-007', 'Dr. Carlos Mendoza', 'OPD Specialist', 'Outpatient (OPD)', 'Day Shift (7 AM - 3 PM)', 'c.mendoza@hospity.ph', '0917-777-8899'],
            ['STF-008', 'Dr. Teresa Villanueva', 'Consultant Physician', 'Internal Medicine', 'Day Shift (7 AM - 3 PM)', 't.villanueva@hospity.ph', '0917-888-9900'],
            ['STF-009', 'Dr. Manuel Roxas', 'General Surgeon', 'Surgery / Operating Room', 'On-Call', 'm.roxas@hospity.ph', '0917-999-0011'],
            ['STF-010', 'Gabriel Alonzo', 'Junior Medical Technologist', 'Laboratory', 'Evening Shift (3 PM - 11 PM)', 'g.alonzo@hospity.ph', '0917-101-0202'],
            ['STF-011', 'Kathryn Bernardo', 'Staff Nurse II', 'Nursing', 'Day Shift (7 AM - 3 PM)', 'k.bernardo@hospity.ph', '0917-102-0303'],
            ['STF-012', 'Daniel Padilla', 'Radiologic Technologist', 'Laboratory', 'Day Shift (7 AM - 3 PM)', 'd.padilla@hospity.ph', '0917-103-0404'],
            ['STF-013', 'Bea Alonzo', 'Dispensing Pharmacist', 'Pharmacy', 'Evening Shift (3 PM - 11 PM)', 'b.alonzo@hospity.ph', '0917-104-0505'],
            ['STF-014', 'Alden Richards', 'Patient Billing Officer', 'Billing & Cashier', 'Day Shift (7 AM - 3 PM)', 'a.richards@hospity.ph', '0917-105-0606'],
            ['STF-015', 'Nadine Lustre', 'Medical Records Clerk', 'Medical Records', 'Day Shift (7 AM - 3 PM)', 'n.lustre@hospity.ph', '0917-106-0707'],
            ['STF-016', 'James Reid', 'Triage Nurse', 'Emergency (ER)', 'Night Shift (11 PM - 7 AM)', 'j.reid@hospity.ph', '0917-107-0808'],
            ['STF-017', 'Liza Soberano', 'ICU Head Nurse', 'Nursing', 'Day Shift (7 AM - 3 PM)', 'l.soberano@hospity.ph', '0917-108-0909'],
            ['STF-018', 'Enrique Gil', 'Clinical Pharmacist', 'Pharmacy', 'Day Shift (7 AM - 3 PM)', 'e.gil@hospity.ph', '0917-109-1010'],
            ['STF-019', 'Joshua Garcia', 'Staff Nurse I', 'Outpatient (OPD)', 'Day Shift (7 AM - 3 PM)', 'j.garcia@hospity.ph', '0917-110-1111'],
            ['STF-020', 'Julia Barretto', 'Quality Assurance Officer', 'Hospital Administration', 'Day Shift (7 AM - 3 PM)', 'j.barretto@hospity.ph', '0917-111-1212'],
            ['STF-021', 'Dr. Eduardo Gomez', 'Pediatrician', 'Outpatient (OPD)', 'Day Shift (7 AM - 3 PM)', 'e.gomez@hospity.ph', '0917-112-1313'],
            ['STF-022', 'Dr. Corazon Aquino', 'OB-GYN Consultant', 'Outpatient (OPD)', 'Day Shift (7 AM - 3 PM)', 'c.aquino@hospity.ph', '0917-113-1414'],
            ['STF-023', 'Dr. Benjamin Cruz', 'Cardiologist', 'Internal Medicine', 'Day Shift (7 AM - 3 PM)', 'b.cruz@hospity.ph', '0917-114-1515'],
            ['STF-024', 'Dr. Angela David', 'Pulmonologist', 'Internal Medicine', 'Day Shift (7 AM - 3 PM)', 'a.david@hospity.ph', '0917-115-1616'],
            ['STF-025', 'Dr. Francis Soriano', 'Orthopedic Surgeon', 'Surgery / Operating Room', 'On-Call', 'f.soriano@hospity.ph', '0917-116-1717'],
            ['STF-026', 'Dr. Rowena Tan', 'Neurologist', 'Internal Medicine', 'Day Shift (7 AM - 3 PM)', 'r.tan@hospity.ph', '0917-117-1818'],
            ['STF-027', 'Dr. Ramon Castillo', 'Anesthesiologist', 'Surgery / Operating Room', 'Day Shift (7 AM - 3 PM)', 'r.castillo@hospity.ph', '0917-118-1919'],
            ['STF-028', 'Dr. Helena Villar', 'Dermatologist', 'Outpatient (OPD)', 'Day Shift (7 AM - 3 PM)', 'h.villar@hospity.ph', '0917-119-2020'],
            ['STF-029', 'Dr. Victor Laurel', 'Ophthalmologist', 'Outpatient (OPD)', 'Day Shift (7 AM - 3 PM)', 'v.laurel@hospity.ph', '0917-120-2121'],
            ['STF-030', 'Dr. Grace Poe-Lim', 'ENT Specialist', 'Outpatient (OPD)', 'Day Shift (7 AM - 3 PM)', 'g.lim@hospity.ph', '0917-121-2222'],
            ['STF-031', 'Marco Flores', 'Billing Clerk', 'Billing & Cashier', 'Day Shift (7 AM - 3 PM)', 'm.flores@hospity.ph', '0917-122-2323'],
            ['STF-032', 'Grace Bautista', 'Laboratory Clerk', 'Laboratory', 'Day Shift (7 AM - 3 PM)', 'g.bautista@hospity.ph', '0917-123-2424'],
        ];

        $staffInsert = $db->prepare("
            INSERT INTO staff_members (staff_code, name, role, department_name, shift, email, contact, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')
        ");
        foreach ($staff as $s) {
            $staffInsert->execute($s);
        }

        // 3. Staff Activity Logs
        $activities = [
            ['Maria Clara Santos', 'Medical Records Officer', 'Medical Records', 'Record Update', 'Updated patient electronic health record for Inpatient #IP-2026-889', 'Completed', 5],
            ['John Michael Cruz', 'Medical Technologist', 'Laboratory', 'Lab Entry', 'Laboratory Result Recorded for Automated CBC Batch #LB-992', 'Completed', 15],
            ['Ana Garcia', 'Senior Pharmacist', 'Pharmacy', 'Dispensing Log', 'Medicine Dispensing Record Created for Ward 3 Patient Rx', 'Completed', 25],
            ['Patricia Lopez', 'Chief Nurse', 'Nursing', 'Shift Handover', 'Completed Shift End Ward Endorsement for Inpatient Wing 4', 'Completed', 35],
            ['Ferdinand Ramos', 'Billing Supervisor', 'Billing & Cashier', 'Billing Audit', 'Reconciled PhilHealth Claims Batch for July Discharges', 'Completed', 45],
            ['Dr. Juan Reyes', 'Attending ER Physician', 'Emergency (ER)', 'Consultation', 'Recorded ER Triage Assessment for Trauma Case #ER-402', 'Completed', 55],
            ['Kathryn Bernardo', 'Staff Nurse II', 'Nursing', 'Vitals Entry', 'Recorded Vital Signs Monitoring Chart for ICU Bed 2', 'Completed', 65],
            ['Daniel Padilla', 'Radiologic Technologist', 'Laboratory', 'Imaging Log', 'Uploaded Chest X-Ray Digital DICOM Scan for Patient #OPD-3044', 'Completed', 75],
            ['Bea Alonzo', 'Dispensing Pharmacist', 'Pharmacy', 'Inventory Check', 'Logged Pharmacy Daily Controlled Substance Physical Inventory', 'Completed', 85],
            ['Alden Richards', 'Patient Billing Officer', 'Billing & Cashier', 'Invoice Entry', 'Generated Final Clearance Invoice for Discharged Inpatient #IP-2026-870', 'Completed', 95],
            ['Nadine Lustre', 'Medical Records Clerk', 'Medical Records', 'Archival', 'Archived Physical Health Folder to Digital Storage Vault', 'Completed', 105],
            ['James Reid', 'Triage Nurse', 'Emergency (ER)', 'Triage Log', 'Logged Acute Respiratory Distress Patient Intake in ER Bay 1', 'Completed', 115],
            ['Liza Soberano', 'ICU Head Nurse', 'Nursing', 'Care Plan', 'Updated Ventilator Weaning Care Protocol for ICU Bed 4', 'Completed', 125],
            ['Enrique Gil', 'Clinical Pharmacist', 'Pharmacy', 'Medication Review', 'Performed Drug Interaction Cross-Check for Polypharmacy Regimen', 'Completed', 135],
            ['Joshua Garcia', 'Staff Nurse I', 'Outpatient (OPD)', 'Consult Queue', 'Updated OPD General Consultation Patient Calling System', 'Completed', 145],
            ['Julia Barretto', 'Quality Assurance Officer', 'Hospital Administration', 'Audit Check', 'Logged Quality Infection Control Checklist for Surgical Suite 2', 'Completed', 155],
            ['Gabriel Alonzo', 'Junior Medical Technologist', 'Laboratory', 'Lab Entry', 'Completed Blood Chemistry Profile Verification for Lipid Panels', 'Completed', 165],
            ['Marco Flores', 'Billing Clerk', 'Billing & Cashier', 'Payment Posting', 'Posted HMO Guarantee Letter Authorization Verification', 'Completed', 175],
            ['Grace Bautista', 'Laboratory Clerk', 'Laboratory', 'Sample Intake', 'Received and barcoded 45 blood specimen tubes from OPD Phlebotomy', 'Completed', 185],
            ['Dr. Carlos Mendoza', 'OPD Specialist', 'Outpatient (OPD)', 'Consultation', 'Signed OPD Clinical Consultation Summary for Diabetic Clinic', 'Completed', 195],
            ['Dr. Teresa Villanueva', 'Consultant Physician', 'Internal Medicine', 'Ward Round', 'Documented Morning Rounds Evaluation for 14 IM Patients', 'Completed', 205],
            ['Dr. Manuel Roxas', 'General Surgeon', 'Surgery / Operating Room', 'Post-Op Log', 'Submitted Laparoscopic Appendectomy Operative Technique Sheet', 'Completed', 215],
            ['Dr. Eduardo Gomez', 'Pediatrician', 'Outpatient (OPD)', 'Vaccine Log', 'Logged Pediatric Immunization Series (DTP-HepB-Hib Booster)', 'Completed', 225],
            ['Dr. Corazon Aquino', 'OB-GYN Consultant', 'Outpatient (OPD)', 'Ultrasound Entry', 'Recorded Obstetric Biometry Ultrasound Measurement Report', 'Completed', 235],
            ['Dr. Benjamin Cruz', 'Cardiologist', 'Internal Medicine', 'ECG Review', 'Interpreted 12-Lead Diagnostic Electrocardiograms Batch', 'Completed', 245],
            ['Dr. Angela David', 'Pulmonologist', 'Internal Medicine', 'Spirometry', 'Uploaded Pulmonary Function Test Spirometry Analysis', 'Completed', 255],
            ['Dr. Francis Soriano', 'Orthopedic Surgeon', 'Surgery / Operating Room', 'Cast Log', 'Completed Closed Reduction and Splinting for Radius Fracture', 'Completed', 265],
            ['Dr. Rowena Tan', 'Neurologist', 'Internal Medicine', 'EEG Review', 'Evaluated 24-Hour Ambulatory Electroencephalogram Results', 'Completed', 275],
            ['Dr. Ramon Castillo', 'Anesthesiologist', 'Surgery / Operating Room', 'Pre-Op Eval', 'Completed Pre-Anesthetic Risk Clearance for Elective Cholecystectomy', 'Completed', 285],
            ['Dr. Helena Villar', 'Dermatologist', 'Outpatient (OPD)', 'Biopsy Log', 'Recorded Punch Biopsy Pathology Requisition Slip', 'Completed', 295],
            ['Dr. Victor Laurel', 'Ophthalmologist', 'Outpatient (OPD)', 'Fundus Exam', 'Documented Dilated Eye Examination for Hypertensive Retinopathy', 'Completed', 305],
            ['Dr. Grace Poe-Lim', 'ENT Specialist', 'Outpatient (OPD)', 'Endoscopy Log', 'Completed Diagnostic Nasopharyngoscopy Video Archive', 'Completed', 315],
            ['Maria Clara Santos', 'Medical Records Officer', 'Medical Records', 'ICD-10 Coding', 'Coded 25 Inpatient Discharge Summaries with ICD-10/RVS Codes', 'Completed', 325],
            ['John Michael Cruz', 'Medical Technologist', 'Laboratory', 'Urinalysis', 'Validated 30 Routine Urinalysis Automated Microscopy Tests', 'Completed', 335],
            ['Ana Garcia', 'Senior Pharmacist', 'Pharmacy', 'Restock Verification', 'Verified Temperature Storage Logs for Inpatient Vaccine Refrigerators', 'Completed', 345],
            ['Patricia Lopez', 'Chief Nurse', 'Nursing', 'Nurse Staffing', 'Adjusted Night Shift Nurse-to-Patient Ratio Allocations for Ward 2', 'Completed', 355],
            ['Ferdinand Ramos', 'Billing Supervisor', 'Billing & Cashier', 'End of Day', 'Closed Daily Cashier Register & Consolidated POS Collections', 'Completed', 365],
            ['James Reid', 'Triage Nurse', 'Emergency (ER)', 'Emergency Shift', 'Handed over ER Fast-Track Queue to Night Duty Supervisor', 'Completed', 375],
            ['Kathryn Bernardo', 'Staff Nurse II', 'Nursing', 'Med Administration', 'Recorded 8 PM Scheduled IV Antibiotic Administration in Wing A', 'Completed', 385],
            ['Gabriel Alonzo', 'Junior Medical Technologist', 'Laboratory', 'Crossmatching', 'Completed Blood Crossmatching for Emergency Packed RBC Transfusion', 'Completed', 395],
            ['Bea Alonzo', 'Dispensing Pharmacist', 'Pharmacy', 'Stat Rx', 'Dispensed Emergency STAT Intravenous Infusions to ICU Ward', 'Completed', 405],
            ['Liza Soberano', 'ICU Head Nurse', 'Nursing', 'Crash Cart', 'Verified Emergency Crash Cart Defibrillator Checklist', 'Completed', 415],
            ['Marco Flores', 'Billing Clerk', 'Billing & Cashier', 'Night Deposit', 'Archived Electronic Settlement Slips for Credit Card Payments', 'Completed', 425],
            ['Dr. Juan Reyes', 'Attending ER Physician', 'Emergency (ER)', 'ER Admissions', 'Admitted 3 Acute Febrile Illness Cases to Observation Holding Unit', 'Completed', 435],
            ['Nadine Lustre', 'Medical Records Clerk', 'Medical Records', 'EHR Indexing', 'Scanned and indexed outside clinical referrals into patient chart', 'Completed', 445],
            ['Daniel Padilla', 'Radiologic Technologist', 'Laboratory', 'Night X-Ray', 'Performed Portable Bedside Chest Radiograph in ICU Room 3', 'Completed', 455],
            ['Alden Richards', 'Patient Billing Officer', 'Billing & Cashier', 'Insurance Claim', 'Submitted Electronic Claim Notification to PhilHealth Portal', 'Completed', 465],
            ['Joshua Garcia', 'Staff Nurse I', 'Outpatient (OPD)', 'Clinic Sanitization', 'Documented End-of-Day Terminal Sanitization Log for OPD Exam Rooms', 'Completed', 475],
            ['Julia Barretto', 'Quality Assurance Officer', 'Hospital Administration', 'Report Gen', 'Compiled Monthly Sentinel Event Statistical Summary', 'Completed', 1440],
            ['Enrique Gil', 'Clinical Pharmacist', 'Pharmacy', 'Recall Check', 'Checked FDA Drug Advisory Safety Warning for Antihypertensive Batches', 'Completed', 1500],
            ['Dr. Carlos Mendoza', 'OPD Specialist', 'Outpatient (OPD)', 'Teleconsult', 'Conducted Follow-up Teleconsultation for Chronic Kidney Disease Pt', 'Completed', 1560],
            ['Patricia Lopez', 'Chief Nurse', 'Nursing', 'Equipment Audit', 'Inspected 15 Infusion Pumps for Annual Preventive Calibration', 'Completed', 1620],
            ['Maria Clara Santos', 'Medical Records Officer', 'Medical Records', 'Death Certificate', 'Processed Certified True Copy of Medical Certificate for SSS Claim', 'Completed', 1680],
            ['John Michael Cruz', 'Medical Technologist', 'Laboratory', 'Calibration', 'Ran 3-Level Quality Control Calibrators on Chemistry Analyzer', 'Completed', 1740],
            ['Ana Garcia', 'Senior Pharmacist', 'Pharmacy', 'Supplier PO', 'Reviewed Purchase Requisition for Critical Antibiotics Resupply', 'Completed', 1800],
        ];

        $logInsert = $db->prepare("
            INSERT INTO staff_activity_logs (staff_name, role_title, department_name, activity_type, activity_description, status, logged_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($activities as $act) {
            $timestamp = date('Y-m-d H:i:s', time() - ($act[6] * 60));
            $logInsert->execute([$act[0], $act[1], $act[2], $act[3], $act[4], $act[5], $timestamp]);
        }

        // 4. Doctor Consultation Statistics (30 Doctor Records)
        $doctors = [
            ['Dr. Juan Reyes', 'Emergency (ER)', 'Emergency Medicine / Trauma', 195, 188, 7, 9.8, 97.2],
            ['Dr. Carlos Mendoza', 'Outpatient (OPD)', 'Internal Medicine / Family Med', 186, 178, 8, 8.5, 96.5],
            ['Dr. Teresa Villanueva', 'Internal Medicine', 'Adult Pulmonology & Critical Care', 164, 158, 6, 7.8, 95.8],
            ['Dr. Manuel Roxas', 'Surgery / Operating Room', 'General & Laparoscopic Surgery', 142, 138, 4, 6.5, 98.1],
            ['Dr. Eduardo Gomez', 'Outpatient (OPD)', 'Pediatrics & Adolescent Medicine', 178, 172, 6, 8.2, 98.4],
            ['Dr. Corazon Aquino', 'Outpatient (OPD)', 'Obstetrics & Gynecology', 156, 150, 6, 7.5, 96.9],
            ['Dr. Benjamin Cruz', 'Internal Medicine', 'Cardiology & Interventional Care', 148, 142, 6, 7.1, 97.5],
            ['Dr. Angela David', 'Internal Medicine', 'Pulmonology & Sleep Medicine', 134, 128, 6, 6.4, 95.2],
            ['Dr. Francis Soriano', 'Surgery / Operating Room', 'Orthopedic & Spine Surgery', 125, 120, 5, 6.0, 96.7],
            ['Dr. Rowena Tan', 'Internal Medicine', 'Neurology & Stroke Care', 118, 114, 4, 5.6, 96.0],
            ['Dr. Ramon Castillo', 'Surgery / Operating Room', 'Anesthesiology & Pain Medicine', 152, 149, 3, 7.2, 99.0],
            ['Dr. Helena Villar', 'Outpatient (OPD)', 'Dermatology & Venereology', 140, 134, 6, 6.8, 97.0],
            ['Dr. Victor Laurel', 'Outpatient (OPD)', 'Ophthalmology & Refractive Care', 132, 126, 6, 6.3, 96.4],
            ['Dr. Grace Poe-Lim', 'Outpatient (OPD)', 'Otorhinolaryngology (ENT)', 128, 122, 6, 6.1, 95.9],
            ['Dr. Fernando Poe Jr.', 'Emergency (ER)', 'Emergency Medicine Specialist', 168, 160, 8, 8.0, 95.5],
            ['Dr. Rodrigo Duterte', 'Surgery / Operating Room', 'Urology & Renal Transplant', 110, 106, 4, 5.2, 94.8],
            ['Dr. Miriam Santiago', 'Internal Medicine', 'Rheumatology & Autoimmune Care', 115, 110, 5, 5.5, 98.5],
            ['Dr. Jose Rizal', 'Outpatient (OPD)', 'Ophthalmology & Cornea Specialist', 145, 140, 5, 6.9, 99.5],
            ['Dr. Andres Bonifacio', 'Emergency (ER)', 'Trauma Surgery / Acute Care', 172, 165, 7, 8.2, 96.1],
            ['Dr. Apolinario Mabini', 'Internal Medicine', 'Physical Medicine & Rehab', 105, 101, 4, 5.0, 97.8],
            ['Dr. Gabriela Silang', 'Nursing / Clinical Care', 'Infectious Diseases Consultant', 130, 125, 5, 6.2, 96.3],
            ['Dr. Antonio Luna', 'Laboratory / Pathology', 'Clinical Pathology & Hematology', 160, 156, 4, 7.6, 98.7],
            ['Dr. Melchora Aquino', 'Outpatient (OPD)', 'Geriatric Medicine & Palliative Care', 112, 108, 4, 5.3, 98.0],
            ['Dr. Emilio Aguinaldo', 'Internal Medicine', 'Gastroenterology & Hepatology', 124, 118, 6, 5.9, 95.0],
            ['Dr. Gregorio del Pilar', 'Surgery / Operating Room', 'Pediatric Surgery', 98, 95, 3, 4.7, 97.4],
            ['Dr. Marcelo del Pilar', 'Outpatient (OPD)', 'Psychiatry & Behavioral Health', 116, 110, 6, 5.5, 96.2],
            ['Dr. Juan Luna', 'Internal Medicine', 'Nephrology & Dialysis', 138, 132, 6, 6.6, 96.8],
            ['Dr. Francisco Balagtas', 'Outpatient (OPD)', 'Allergy & Immunology', 104, 100, 4, 5.0, 97.1],
            ['Dr. Vicente Sotto', 'Emergency (ER)', 'Critical Care Medicine', 158, 151, 7, 7.5, 95.7],
            ['Dr. Lope K. Santos', 'Internal Medicine', 'Endocrinology & Diabetology', 142, 137, 5, 6.8, 97.6],
        ];

        $docInsert = $db->prepare("
            INSERT INTO doctor_consultation_stats (doctor_name, department_name, specialty, total_consultations, completed_consultations, cancelled_consultations, avg_daily_consultations, satisfaction_score)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($doctors as $d) {
            $docInsert->execute($d);
        }

        // 5. Patient Census Historical Records (30 Days)
        $censusInsert = $db->prepare("
            INSERT INTO patient_census (census_date, total_patients, inpatients, outpatients, emergency, discharged, new_admissions, occupancy_rate)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $in = 48 + rand(-4, 6);
            $out = 62 + rand(-8, 10);
            $er = 16 + rand(-3, 5);
            $dis = 34 + rand(-5, 5);
            $new = 28 + rand(-4, 6);
            $tot = $in + $out + $er;
            $occ = round(($in / 60) * 100, 1);
            $censusInsert->execute([$date, $tot, $in, $out, $er, $dis, $new, $occ]);
        }

        // 6. Appointment Summaries (30 Days)
        $appInsert = $db->prepare("
            INSERT INTO appointment_summaries (summary_date, total_appointments, completed, pending, cancelled, no_show, avg_wait_time_minutes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $tot = 82 + rand(-8, 12);
            $comp = round($tot * 0.78);
            $pend = rand(8, 14);
            $canc = rand(3, 7);
            $noshow = $tot - ($comp + $pend + $canc);
            if ($noshow < 0) $noshow = 2;
            $wait = rand(14, 22);
            $appInsert->execute([$date, $tot, $comp, $pend, $canc, $noshow, $wait]);
        }

        // 7. Core Operational Reports (7 Core Reports + 23 Archive Reports = 30 total)
        $reports = [
            [
                'RPT-CENSUS-001', 'Daily Patient Census Report', 'Census & Inpatient', 'Daily', 'Today (Aug 30, 2026)',
                'Finalized',
                json_encode([
                    'total_patients' => 126,
                    'inpatients' => 48,
                    'outpatients' => 62,
                    'emergency' => 16,
                    'discharged' => 34,
                    'bed_occupancy' => '80.0%',
                    'avg_length_of_stay' => '3.4 Days',
                    'new_admissions' => 28
                ]),
                json_encode([
                    'summary' => 'Overall patient census remains within optimal capacity thresholds with balanced bed turnover.',
                    'findings' => 'Bed occupancy in Medical Ward stands at 80%. ER triage to admission time is running smoothly at an average of 42 minutes. Discharges are timely.',
                    'breakdown' => [
                        ['Category' => 'Inpatient Medical/Surgical', 'Count' => 32, 'Capacity' => 40, 'Occupancy' => '80.0%'],
                        ['Category' => 'Intensive Care Unit (ICU)', 'Count' => 8, 'Capacity' => 10, 'Occupancy' => '80.0%'],
                        ['Category' => 'Pediatric & Neonatal Ward', 'Count' => 8, 'Capacity' => 10, 'Occupancy' => '80.0%'],
                        ['Category' => 'Outpatient General & Specialty', 'Count' => 62, 'Capacity' => 100, 'Occupancy' => '62.0%'],
                        ['Category' => 'Emergency Department', 'Count' => 16, 'Capacity' => 20, 'Occupancy' => '80.0%'],
                    ]
                ])
            ],
            [
                'RPT-PERF-001', 'Monthly Operational Performance Report', 'Hospital Performance', 'Monthly', 'August 2026 (Month-to-Date)',
                'Finalized',
                json_encode([
                    'overall_performance' => '92.0%',
                    'patient_volume_rate' => '94.0%',
                    'appointment_completion' => '89.0%',
                    'discharge_efficiency' => '93.0%',
                    'department_efficiency' => '87.0%',
                    'total_consultations' => 3840,
                    'total_admissions' => 742,
                    'avg_daily_census' => 124
                ]),
                json_encode([
                    'summary' => 'Comprehensive monthly operational review showing 92% combined performance rating across clinical and administrative workflows.',
                    'findings' => 'Patient discharge efficiency improved by 4.2% following the implementation of streamlined PhilHealth electronic clearance protocols.',
                    'indicators' => [
                        ['Metric' => 'Patient Volume Capacity', 'Target' => '90%', 'Actual' => '94%', 'Status' => 'Exceeded'],
                        ['Metric' => 'Appointment Completion Rate', 'Target' => '85%', 'Actual' => '89%', 'Status' => 'Exceeded'],
                        ['Metric' => 'Patient Discharge Efficiency', 'Target' => '90%', 'Actual' => '93%', 'Status' => 'Exceeded'],
                        ['Metric' => 'Department Inter-Workflow Efficiency', 'Target' => '85%', 'Actual' => '87%', 'Status' => 'Met'],
                    ]
                ])
            ],
            [
                'RPT-REV-001', 'Billing and Revenue Summary Report', 'Financial Oversight', 'Monthly', 'August 2026',
                'Finalized',
                json_encode([
                    'total_revenue' => 1284500.00,
                    'paid_amount' => 1092000.00,
                    'outstanding_balance' => 192500.00,
                    'monthly_growth' => '+8.4%',
                    'total_services_rendered' => 4520,
                    'consultation_rev' => 320000.00,
                    'lab_rev' => 285000.00,
                    'pharmacy_rev' => 410000.00,
                    'other_rev' => 269500.00
                ]),
                json_encode([
                    'summary' => 'High-level financial summary of hospital revenue generated across all medical and ancillary departments. (View-Only Executive Digest)',
                    'findings' => 'Pharmacy and Outpatient Consultations continue to drive strong gross receipts with 8.4% month-over-month growth. Outstanding PhilHealth receivables are within allowable 45-day aging.',
                    'revenue_streams' => [
                        ['Stream' => 'Pharmacy Dispensing Services', 'Gross' => '₱410,000.00', 'Share' => '31.9%'],
                        ['Stream' => 'Physician & Specialist Consultations', 'Gross' => '₱320,000.00', 'Share' => '24.9%'],
                        ['Stream' => 'Laboratory & Diagnostic Imaging', 'Gross' => '₱285,000.00', 'Share' => '22.2%'],
                        ['Stream' => 'Operating Room & Other Ancillary Fees', 'Gross' => '₱269,500.00', 'Share' => '21.0%'],
                    ]
                ])
            ],
            [
                'RPT-LAB-001', 'Laboratory Utilization Report', 'Clinical Diagnostics', 'Monthly', 'August 2026',
                'Finalized',
                json_encode([
                    'total_requests' => 1480,
                    'completed_tests' => 1395,
                    'pending_tests' => 62,
                    'cancelled_tests' => 23,
                    'utilization_rate' => '94.2%',
                    'avg_turnaround_time' => '1.4 Hours'
                ]),
                json_encode([
                    'summary' => 'Operational diagnostic workload analysis demonstrating rapid turnaround time and high reagent efficiency.',
                    'findings' => 'Complete Blood Count (CBC) and Blood Chemistry Panels form 62% of diagnostic volume with zero critical equipment downtime recorded this cycle.',
                    'popular_tests' => [
                        ['Test' => 'Complete Blood Count (CBC)', 'Requests' => 248, 'Completed' => 232, 'Pending' => 16, 'Rate' => '93.5%'],
                        ['Test' => 'Routine Urinalysis', 'Requests' => 195, 'Completed' => 188, 'Pending' => 7, 'Rate' => '96.4%'],
                        ['Test' => 'Blood Chemistry (FBS, Lipid, Crea)', 'Requests' => 210, 'Completed' => 198, 'Pending' => 12, 'Rate' => '94.3%'],
                        ['Test' => 'Chest X-Ray Digital Imaging', 'Requests' => 180, 'Completed' => 174, 'Pending' => 6, 'Rate' => '96.7%'],
                        ['Test' => 'Other Diagnostic & Serology Services', 'Requests' => 647, 'Completed' => 603, 'Pending' => 21, 'Rate' => '93.2%'],
                    ]
                ])
            ],
            [
                'RPT-PHAR-001', 'Pharmacy Stock Report', 'Pharmacy & Supply', 'Weekly', 'Week 4 - August 2026',
                'Finalized',
                json_encode([
                    'total_medicines' => 450,
                    'available_stock' => 418,
                    'low_stock_items' => 24,
                    'critical_items' => 6,
                    'expired_items' => 2,
                    'stock_health_score' => '95.1%'
                ]),
                json_encode([
                    'summary' => 'Executive pharmacy inventory status monitoring supply sufficiency, minimum reorder thresholds, and expiration alerts. (View-Only)',
                    'findings' => 'Essential antibiotics, emergency inotropes, and IV fluids maintain healthy buffer stocks. 2 expired batches isolated for safe disposal protocol.',
                    'sample_inventory' => [
                        ['Medicine' => 'Amoxicillin 500mg Capsule', 'Stock' => '1,450 Caps', 'MinLevel' => '500', 'Status' => 'Normal', 'Expiry' => '2027-04-15'],
                        ['Medicine' => 'Paracetamol 500mg Tablet', 'Stock' => '2,800 Tabs', 'MinLevel' => '1,000', 'Status' => 'Normal', 'Expiry' => '2027-08-30'],
                        ['Medicine' => 'Ceftriaxone 1g IV Vial', 'Stock' => '180 Vials', 'MinLevel' => '200', 'Status' => 'Low Stock', 'Expiry' => '2026-12-10'],
                        ['Medicine' => 'Salbutamol Nebule 2.5mg', 'Stock' => '420 Nebs', 'MinLevel' => '300', 'Status' => 'Normal', 'Expiry' => '2027-02-28'],
                        ['Medicine' => 'Insulin Regular 100IU/ml', 'Stock' => '35 Vials', 'MinLevel' => '50', 'Status' => 'Critical', 'Expiry' => '2026-10-15'],
                    ]
                ])
            ],
            [
                'RPT-APP-001', 'Appointment Summary Report', 'Outpatient Flow', 'Daily', 'Today (Aug 30, 2026)',
                'Finalized',
                json_encode([
                    'total_appointments' => 82,
                    'completed' => 64,
                    'pending' => 10,
                    'cancelled' => 5,
                    'no_show' => 3,
                    'completion_rate' => '78.0%',
                    'avg_doctor_wait' => '16 mins'
                ]),
                json_encode([
                    'summary' => 'Daily clinic appointment scheduling flow and doctor consultation queue performance overview.',
                    'findings' => 'Morning consultation clinics recorded 91% attendance. Cancellation rate is low at 6.1%. SMS automated reminder system reduced no-shows.',
                    'breakdown_by_dept' => [
                        ['Department' => 'Internal Medicine Clinic', 'Total' => 24, 'Completed' => 20, 'Pending' => 2, 'Cancelled' => 2],
                        ['Department' => 'Pediatrics Clinic', 'Total' => 20, 'Completed' => 16, 'Pending' => 3, 'Cancelled' => 1],
                        ['Department' => 'OB-GYN Clinic', 'Total' => 18, 'Completed' => 14, 'Pending' => 2, 'Cancelled' => 1],
                        ['Department' => 'Surgery Clinic', 'Total' => 12, 'Completed' => 10, 'Pending' => 2, 'Cancelled' => 0],
                        ['Department' => 'Specialty ENT & Ophthalmology', 'Total' => 8, 'Completed' => 4, 'Pending' => 1, 'Cancelled' => 1],
                    ]
                ])
            ],
            [
                'RPT-DOH-001', 'DOH Compliance Report', 'Regulatory & Accreditation', 'Quarterly', 'Q3 - August 2026',
                'Finalized',
                json_encode([
                    'overall_compliance' => '96.0%',
                    'patient_records_score' => '98.0%',
                    'staffing_requirements' => '94.0%',
                    'laboratory_compliance' => '97.0%',
                    'pharmacy_compliance' => '95.0%',
                    'facility_compliance' => '96.0%',
                    'reporting_compliance' => '98.0%',
                    'last_inspection' => 'Aug 15, 2026',
                    'next_review' => 'Nov 15, 2026'
                ]),
                json_encode([
                    'summary' => 'Department of Health (DOH) Administrative Order and Licensing Compliance Audit for Level 2 General Hospital Operation.',
                    'findings' => 'The facility achieved a 96% overall rating. All clinical documentation and statutory infectious disease reporting meet national standards.',
                    'domains' => [
                        ['Domain' => 'Patient Medical Records & Confidentiality', 'Score' => '98.0%', 'Status' => 'Compliant', 'Note' => 'Data privacy protocols certified.'],
                        ['Domain' => 'Licensed Staffing & Specialist Ratios', 'Score' => '94.0%', 'Status' => 'Compliant', 'Note' => 'Nurse-patient ratios maintained in all wards.'],
                        ['Domain' => 'Laboratory & Biosafety Standards', 'Score' => '97.0%', 'Status' => 'Compliant', 'Note' => 'RIHT-certified external quality assessment passed.'],
                        ['Domain' => 'Pharmacy Licensing & Cold-Chain', 'Score' => '95.0%', 'Status' => 'Compliant', 'Note' => 'Daily automated temperature logging active.'],
                        ['Domain' => 'Facility Safety, Sanitation & Waste', 'Score' => '96.0%', 'Status' => 'Compliant', 'Note' => 'Annual BFP fire clearance valid.'],
                        ['Domain' => 'Mandatory Health System Reporting', 'Score' => '98.0%', 'Status' => 'Compliant', 'Note' => 'Weekly DOH FHSIS reports submitted on time.'],
                    ]
                ])
            ]
        ];

        // Add 23 archive reports to total 30 reports
        for ($k = 1; $k <= 23; $k++) {
            $repTypes = [
                ['Census', 'Weekly Patient Census Archive', 'Census & Inpatient', 'Weekly'],
                ['Perf', 'Monthly Hospital Performance Summary', 'Hospital Performance', 'Monthly'],
                ['Rev', 'Quarterly Revenue & Billing Consolidation', 'Financial Oversight', 'Quarterly'],
                ['Lab', 'Diagnostic Laboratory Volume Breakdown', 'Clinical Diagnostics', 'Monthly'],
                ['Phar', 'Pharmacy Formulary & Stock Health Audit', 'Pharmacy & Supply', 'Monthly'],
                ['DOH', 'DOH Continuous Quality Improvement Report', 'Regulatory & Accreditation', 'Quarterly'],
            ];
            $pick = $repTypes[$k % count($repTypes)];
            $code = "RPT-ARCH-{$k}";
            $name = "{$pick[1]} - Period Archive #{$k}";
            $cat = $pick[2];
            $ptype = $pick[3];
            $label = "Archive Period Q" . (($k % 4) + 1) . " 202" . (6 - floor($k / 10));

            $reports[] = [
                $code, $name, $cat, $ptype, $label, 'Archived',
                json_encode(['status' => 'Archived', 'compliance_index' => '95.' . ($k % 9) . '%', 'records_processed' => 1200 + ($k * 35)]),
                json_encode(['summary' => "Historical archive report dataset for {$name}.", 'findings' => 'All historical metrics logged according to executive governance standards.'])
            ];
        }

        $rptInsert = $db->prepare("
            INSERT INTO operational_reports (report_code, report_name, category, period_type, period_label, status, summary_metrics_json, detailed_payload_json)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($reports as $r) {
            $rptInsert->execute($r);
        }

        // 8. Billing & Revenue Summary Record
        $db->exec("
            INSERT INTO billing_revenue_summary (report_period, total_revenue, paid_amount, outstanding_balance, total_services_count, consultation_revenue, lab_revenue, pharmacy_revenue, other_revenue, growth_rate)
            VALUES ('August 2026', 1284500.00, 1092000.00, 192500.00, 4520, 320000.00, 285000.00, 410000.00, 269500.00, 8.4)
        ");

        // 9. Laboratory Utilization Tests
        $labTests = [
            ['Complete Blood Count (CBC)', 'Hematology', 248, 232, 16, 0, 93.5, 0.75],
            ['Routine Urinalysis', 'Clinical Microscopy', 195, 188, 7, 0, 96.4, 0.50],
            ['Blood Chemistry Panel (FBS/Lipid/Crea)', 'Clinical Chemistry', 210, 198, 12, 0, 94.3, 1.25],
            ['Chest X-Ray Digital View (PA/Lateral)', 'Radiology / Imaging', 180, 174, 6, 0, 96.7, 0.45],
            ['Fecalysis Routine Exam', 'Clinical Microscopy', 115, 110, 5, 0, 95.6, 0.50],
            ['Serum Electrolytes (Na/K/Cl)', 'Clinical Chemistry', 140, 134, 6, 0, 95.7, 1.00],
            ['12-Lead Electrocardiogram (ECG)', 'Cardiology Diagnostic', 165, 160, 5, 0, 97.0, 0.30],
            ['Dengue Duo NS1/IgG/IgM Rapid Test', 'Serology & Immunology', 88, 85, 3, 0, 96.6, 0.60],
            ['Blood Typing & Crossmatching', 'Blood Bank', 75, 74, 1, 0, 98.7, 1.50],
            ['HbA1c Glycated Hemoglobin', 'Clinical Chemistry', 92, 88, 4, 0, 95.6, 1.10],
        ];

        $labInsert = $db->prepare("
            INSERT INTO laboratory_utilization (test_name, category, requests_count, completed_count, pending_count, cancelled_count, utilization_rate, avg_turnaround_hours)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($labTests as $lt) {
            $labInsert->execute($lt);
        }

        // 10. Pharmacy Stocks (Realistic Medicines)
        $pharmacy = [
            ['Amoxicillin 500mg', 'Amoxicillin Trihydrate', '500mg Capsule', 1450, 500, 'Box', 'Normal', '2027-04-15', 'LOT-AMX-2024'],
            ['Paracetamol 500mg', 'Acetaminophen', '500mg Tablet', 2800, 1000, 'Box', 'Normal', '2027-08-30', 'LOT-PCM-8821'],
            ['Ceftriaxone 1g IV', 'Ceftriaxone Sodium', '1g Vial with Diluent', 180, 200, 'Vial', 'Low Stock', '2026-12-10', 'LOT-CTX-9901'],
            ['Salbutamol 2.5mg', 'Salbutamol Sulfate', '2.5mg/2.5ml Nebule', 420, 300, 'Box', 'Normal', '2027-02-28', 'LOT-SAL-3341'],
            ['Insulin Regular 100IU', 'Human Insulin (rDNA)', '100 IU/ml 10ml Vial', 35, 50, 'Vial', 'Critical', '2026-10-15', 'LOT-INS-1120'],
            ['Losartan Potassium 50mg', 'Losartan Potassium', '50mg Film Tablet', 1600, 600, 'Box', 'Normal', '2027-06-20', 'LOT-LST-5561'],
            ['Amlodipine 5mg', 'Amlodipine Besylate', '5mg Tablet', 1900, 700, 'Box', 'Normal', '2027-09-15', 'LOT-AML-7721'],
            ['Omeprazole 40mg IV', 'Omeprazole Sodium', '40mg Lyophilized Powder', 95, 120, 'Vial', 'Low Stock', '2026-11-30', 'LOT-OMP-4401'],
            ['Tramadol 50mg/ml Amp', 'Tramadol HCl', '50mg/ml 2ml Ampule', 210, 150, 'Ampule', 'Normal', '2027-01-18', 'LOT-TRM-8890'],
            ['Normal Saline (0.9% NaCl)', 'Sodium Chloride 0.9%', '1,000ml Infusion Bottle', 650, 400, 'Bottle', 'Normal', '2027-12-31', 'LOT-NS-9090'],
            ['Dextrose 5% in Water', 'D5W IV Infusion', '1,000ml Infusion Bottle', 520, 350, 'Bottle', 'Normal', '2027-11-20', 'LOT-D5W-6651'],
            ['Expired Batch Sample Doxy', 'Doxycycline Hyclate', '100mg Capsule', 15, 50, 'Box', 'Expired', '2026-06-30', 'LOT-DOX-OLD'],
        ];

        $pharInsert = $db->prepare("
            INSERT INTO pharmacy_stocks (medicine_name, generic_name, dosage, current_stock, min_level, unit, status, expiry_date, lot_number)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($pharmacy as $ph) {
            $pharInsert->execute($ph);
        }

        // 11. DOH Compliance Items
        $compliance = [
            ['Patient Medical Records & Confidentiality', 98.0, 'Compliant', 30, 29, '2026-08-15', '2026-11-15', 'Data privacy protocols certified. Electronic backup verified.'],
            ['Licensed Staffing & Specialist Ratios', 94.0, 'Compliant', 25, 24, '2026-08-15', '2026-11-15', 'Nurse-patient ratios maintained in all wards.'],
            ['Laboratory & Biosafety Standards', 97.0, 'Compliant', 20, 19, '2026-08-15', '2026-11-15', 'RIHT-certified external quality assessment passed.'],
            ['Pharmacy Licensing & Cold-Chain Protocols', 95.0, 'Compliant', 20, 19, '2026-08-15', '2026-11-15', 'Daily automated temperature logging active.'],
            ['Facility Safety, Sanitation & Waste Management', 96.0, 'Compliant', 25, 24, '2026-08-15', '2026-11-15', 'Hazardous medical waste certified disposal via DENR.'],
            ['Mandatory Health System & Statutory Reporting', 98.0, 'Compliant', 20, 20, '2026-08-15', '2026-11-15', 'Weekly DOH FHSIS and NOTIS reports submitted.'],
        ];

        $compInsert = $db->prepare("
            INSERT INTO doh_compliance_items (domain_name, compliance_score, status, total_indicators, compliant_indicators, last_inspection_date, next_review_date, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($compliance as $c) {
            $compInsert->execute($c);
        }

        // 12. Administrative Notifications (6 items)
        $notifs = [
            ['DOH Compliance Review', 'Compliance', 'Quarterly DOH Compliance audit report is finalized and ready for executive sign-off.', 0, 'high', 'reports'],
            ['Monthly Operational Report', 'Reports', 'August 2026 Hospital Operational Performance Report has been generated.', 0, 'normal', 'reports'],
            ['Department Performance Update', 'Performance', 'Nursing & Emergency Department monthly efficiency index updated.', 0, 'normal', 'department-performance'],
            ['Staff Activity Sync', 'Staff', '55 recent staff activity records synced from clinical and ancillary modules.', 0, 'normal', 'staff-activity'],
            ['Pharmacy Low Stock Alert', 'Pharmacy', 'Ceftriaxone 1g IV and Insulin Regular approaching reorder threshold.', 1, 'high', 'reports'],
            ['Doctor Consultation Milestone', 'Doctors', 'Monthly consultation volume exceeded target by +4.8% across OPD and ER.', 1, 'normal', 'doctor-stats'],
        ];

        $notifInsert = $db->prepare("
            INSERT INTO administrative_notifications (title, category, description, is_read, urgency, target_section)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        foreach ($notifs as $n) {
            $notifInsert->execute($n);
        }

        // 13. Audit Trail (Initial Executive Logs)
        $auditLogs = [
            ['Dr. Maria Santos', 'Hospital Chief / Medical Director', 'Viewed DOH Compliance Report', 'DOH Compliance Report (Q3 2026)', 'Accessed executive compliance matrix and inspection notes.'],
            ['Dr. Maria Santos', 'Hospital Chief / Medical Director', 'Exported Operational Report', 'Monthly Operational Performance Report', 'Exported formatted report as PDF executive briefing.'],
            ['Dr. Maria Santos', 'Hospital Chief / Medical Director', 'Viewed Doctor Consultation Statistics', 'Doctor Statistics', 'Reviewed departmental consultation quotas and patient satisfaction.'],
            ['Dr. Maria Santos', 'Hospital Chief / Medical Director', 'Viewed Daily Patient Census Report', 'Daily Patient Census Report', 'Monitored inpatient bed occupancy and ER throughput.'],
            ['Dr. Maria Santos', 'Hospital Chief / Medical Director', 'Inspected Department Performance Scorecards', 'Department Performance', 'Reviewed task completion rates across 10 hospital departments.'],
        ];

        $auditInsert = $db->prepare("
            INSERT INTO audit_trail (user_name, role_title, action_performed, report_affected, details, logged_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        foreach ($auditLogs as $idx => $a) {
            $timestamp = date('Y-m-d H:i:s', time() - (($idx + 1) * 3600));
            $auditInsert->execute([$a[0], $a[1], $a[2], $a[3], $a[4], $timestamp]);
        }

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        die("Seed failed: " . $e->getMessage());
    }
}
