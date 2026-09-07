-- Seed Data for MedicalRegistrationDB
USE `MedicalRegistrationDB`;

-- 1. Users (Registrator & Admin)
-- Password for all default accounts is: password123 (Hash generated with BCRYPT)
INSERT INTO `users` (`UserID`, `FirstName`, `LastName`, `Username`, `PasswordHash`, `Role`, `Email`, `Status`) VALUES
(1, 'Sarah', 'Jenkins', 'registrator', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Registrator', 'sarah.jenkins@tupimunicipal.gov.ph', 'Active'),
(2, 'Alex', 'Vaughn', 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Admin', 'admin@tupimunicipal.gov.ph', 'Active'),
(3, 'Michael', 'Chang', 'registrator2', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Registrator', 'm.chang@tupimunicipal.gov.ph', 'Active')
ON DUPLICATE KEY UPDATE `Username`=`Username`;

-- 2. Body Systems Master
INSERT INTO `body_systems` (`BodySystemID`, `SystemName`, `SystemCode`, `Icon`, `Emoji`, `Description`, `Status`) VALUES
(1, 'Digestive System', 'digestive', 'activity', '🩺', 'Involves the esophagus, stomach, intestines, liver, pancreas, and gallbladder.', 'Active'),
(2, 'Cardiovascular System', 'cardiovascular', 'heart-pulse', '🫀', 'Involves the heart, blood vessels, circulation, and blood pressure regulation.', 'Active'),
(3, 'Nervous System', 'nervous', 'zap', '🧠', 'Involves the brain, spinal cord, nerves, sensation, and neuromuscular coordination.', 'Active'),
(4, 'Respiratory System', 'respiratory', 'wind', '🫁', 'Involves airways, lungs, trachea, bronchi, and respiratory gas exchange.', 'Active'),
(5, 'Musculoskeletal System', 'musculoskeletal', 'bone', '🦴', 'Involves bones, joints, muscles, tendons, ligaments, and vertebral column.', 'Active'),
(6, 'Integumentary System', 'integumentary', 'shield', '🧴', 'Involves skin, dermis, cutaneous tissue, hair, nails, and surface barrier.', 'Active'),
(7, 'Urinary System', 'urinary', 'droplets', '🧪', 'Involves kidneys, ureters, urinary bladder, urethra, and fluid homeostasis.', 'Active'),
(8, 'Endocrine System', 'endocrine', 'sparkles', '🧬', 'Involves thyroid, adrenal glands, pancreas islet cells, and hormonal balance.', 'Active'),
(9, 'General / Multisystem', 'general', 'cross', '🏥', 'Involves generalized constitutional symptoms, systemic immune, or metabolic factors.', 'Active')
ON DUPLICATE KEY UPDATE `SystemCode`=`SystemCode`;

-- 3. Body Locations
INSERT INTO `body_locations` (`BodyLocationID`, `BodySystemID`, `LocationName`, `LocationCode`, `FrontBack`, `SubRegion`, `Description`) VALUES
(1, 3, 'Head & Cranium', 'head', 'Both', 'Cranial, Frontal & Temporal Area', 'Brain, cranial nerves, eyes, ENT, and scalp.'),
(2, 4, 'Neck & Throat', 'neck', 'Both', 'Anterior & Posterior Cervical Region', 'Cervical vertebrae, pharynx, thyroid, and carotid path.'),
(3, 2, 'Chest & Thorax', 'chest', 'Front', 'Anterior Chest / Precordial Area', 'Heart, pericardium, lungs, pleura, and sternal wall.'),
(4, 1, 'Abdomen', 'abdomen', 'Front', 'Epigastric & Umbilical Quadrants', 'Stomach, intestines, liver, gallbladder, and abdominal wall.'),
(5, 5, 'Back & Spine', 'back', 'Back', 'Thoracic & Lumbar Column', 'Paraspinal musculature, vertebrae, and dorsal roots.'),
(6, 5, 'Arms & Shoulders', 'arms', 'Both', 'Brachial & Forearm Segments', 'Humerus, biceps, triceps, elbow, and radial nerve.'),
(7, 5, 'Hands & Wrists', 'hands', 'Both', 'Carpal & Digital Extremities', 'Carpal tunnel, metacarpal joints, and digital tendons.'),
(8, 7, 'Pelvis & Groin', 'pelvis', 'Both', 'Suprapubic & Inguinal Floor', 'Urinary bladder, pelvic floor, and inguinal canal.'),
(9, 5, 'Legs & Knees', 'legs', 'Both', 'Femoral & Patellar Articulations', 'Quadriceps, hamstrings, knee joints, and calves.'),
(10, 5, 'Feet & Ankles', 'feet', 'Both', 'Plantar & Tarsal Complex', 'Ankles, Achilles tendon, and plantar fascia.'),
(11, 9, 'Whole Body', 'whole_body', 'Both', 'Generalized Systemic Distribution', 'Diffuse, constitutional, or multi-organ presentation.'),
(12, 9, 'Other Area', 'other', 'Both', 'Non-standard Anatomical Area', 'Specific region to be clarified during clinical exam.')
ON DUPLICATE KEY UPDATE `LocationCode`=`LocationCode`;

-- 4. Symptoms Master
INSERT INTO `symptoms` (`SymptomID`, `SymptomName`, `Description`, `Icon`, `Status`) VALUES
(1, 'Fever', 'Elevated core body temperature above 37.8°C', 'thermometer', 'Active'),
(2, 'Headache', 'Pain or throbbing pressure in the cranial region', 'brain', 'Active'),
(3, 'Dizziness', 'Sensation of lightheadedness, unsteadiness, or vertigo', 'rotate-cw', 'Active'),
(4, 'Nausea', 'Sensation of unease in the stomach with urge to vomit', 'frown', 'Active'),
(5, 'Vomiting', 'Forceful expulsion of stomach contents', 'alert-circle', 'Active'),
(6, 'Fatigue', 'Overwhelming feeling of tiredness and low energy', 'battery-low', 'Active'),
(7, 'Cough', 'Sudden, repetitive reflex to clear the airways of mucus/irritants', 'wind', 'Active'),
(8, 'Difficulty Breathing', 'Shortness of breath or dyspneic sensation during exertion/rest', 'activity', 'Active'),
(9, 'Diarrhea', 'Frequent, loose, or watery bowel movements', 'droplets', 'Active'),
(10, 'Constipation', 'Infrequent bowel movements or difficulty passing stool', 'lock', 'Active'),
(11, 'Chest Pain / Pressure', 'Discomfort, squeezing, or pain localized in the thoracic cage', 'heart', 'Active'),
(12, 'Muscle Pain', 'Aching, soreness, or cramping in muscular tissue', 'dumbbell', 'Active'),
(13, 'Joint Pain', 'Stiffness, swelling, or pain in articular joints', 'bone', 'Active'),
(14, 'Skin Rash / Itching', 'Cutaneous erythema, itchy hives, bumps, or dry scaling', 'shield', 'Active'),
(15, 'Sore Throat', 'Pain, scratchiness, or irritation of the pharynx', 'message-circle', 'Active'),
(16, 'Indigestion / Bloating', 'Fullness, epigastric burning, or excessive abdominal gas', 'flame', 'Active')
ON DUPLICATE KEY UPDATE `SymptomName`=`SymptomName`;

-- 5. Possible Conditions Master
INSERT INTO `possible_conditions` (`ConditionID`, `ConditionName`, `BodySystemID`, `Description`, `Status`) VALUES
(1, 'Gastritis', 1, 'Irritation, erosion, or inflammation of the protective stomach mucosal lining.', 'Active'),
(2, 'Gastroesophageal Reflux (GERD)', 1, 'Acidic stomach contents backing up into the esophagus causing pyrosis.', 'Active'),
(3, 'Gastroenteritis', 1, 'Short-term inflammation of the gastrointestinal tract commonly from food or viral sources.', 'Active'),
(4, 'Peptic Ulcer Disease', 1, 'Discrete sores developing on the inner lining of stomach or duodenum.', 'Active'),
(5, 'Functional Dyspepsia', 1, 'Chronic recurrent indigestion without identifiable organic structural pathology.', 'Active'),
(6, 'Cardiac Palpitations', 2, 'Perception of rapid, fluttering, or irregular heartbeats requiring ECG audit.', 'Active'),
(7, 'Chest Wall Discomfort', 2, 'Musculoskeletal or costochondral thoracic sensitivity.', 'Active'),
(8, 'Hypertensive Response', 2, 'Systemic blood pressure elevation requiring clinical screening and observation.', 'Active'),
(9, 'Tension-Type Headache', 3, 'Bilateral band-like pressure around the forehead and occipital region.', 'Active'),
(10, 'Migraine with/without Aura', 3, 'Moderate to severe throbbing unilateral headache with photophobia.', 'Active'),
(11, 'Benign Positional Vertigo', 3, 'Transient spinning sensation triggered by head positional alterations.', 'Active'),
(12, 'Upper Respiratory Infection', 4, 'Viral inflammation of the nasal, pharyngeal, and laryngeal mucosa.', 'Active'),
(13, 'Acute Bronchitis', 4, 'Inflammation of tracheobronchial tree with productive or dry cough.', 'Active'),
(14, 'Reactive Airway / Asthma', 4, 'Bronchospasm causing wheezing, chest tightness, or dyspnea.', 'Active'),
(15, 'Mechanical Lumbar Strain', 5, 'Acute muscular or ligamentous strain in the lumbosacral spine region.', 'Active'),
(16, 'Osteoarthritis / Joint Wear', 5, 'Degenerative joint disease with cartilage erosion and morning stiffness.', 'Active'),
(17, 'Contact Dermatitis', 6, 'Eczematous skin reaction provoked by allergen or irritant contact.', 'Active'),
(18, 'Urinary Tract Infection', 7, 'Bacterial proliferation in the bladder or urethra causing dysuria.', 'Active'),
(19, 'Thyroid Axis Imbalance', 8, 'Hypothyroidism or hyperthyroidism affecting systemic metabolic rate.', 'Active'),
(20, 'Viral Syndrome / Malaise', 9, 'Constitutional viral illness with body aches, fatigue, and low-grade pyrexia.', 'Active')
ON DUPLICATE KEY UPDATE `ConditionName`=`ConditionName`;

-- 6. Doctors Master Directory
INSERT INTO `doctors` (`DoctorID`, `FirstName`, `LastName`, `Title`, `Specialty`, `LicenseNumber`, `ContactNumber`, `Email`, `ExperienceYears`, `Clinic`, `ClinicRoom`, `ProfileImage`, `Rating`, `ReviewsCount`, `ConsultationFee`, `Bio`, `Education`, `Languages`, `Status`) VALUES
(1, 'Maria', 'Santos', 'MD, FPCP, FPSG', 'Gastroenterologist', 'LIC-MED-84910', '+1 (555) 201-9081', 'dr.santos@tupimunicipal.gov.ph', 14, 'Tupi Municipal Hospital', 'Suite 405, East Wing', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300&h=300', 4.95, 142, '$85.00', 'Board-certified specialist in digestive wellness, endoscopy, acid reflux management, gastritis, and inflammatory bowel disorders.', 'Johns Hopkins Medicine (Fellowship), UST Faculty of Medicine (MD)', 'English, Tagalog, Spanish', 'Available'),
(2, 'John', 'Reyes', 'MD, FPCP', 'Internal Medicine Specialist', 'LIC-MED-77412', '+1 (555) 201-9082', 'dr.reyes@tupimunicipal.gov.ph', 10, 'Tupi Municipal Hospital', 'Room 210, Main Tower', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300', 4.88, 98, '$65.00', 'Comprehensive adult disease management, digestive health screening, metabolic evaluation, and preventative primary healthcare.', 'Philippine General Hospital (Residency), UP College of Medicine (MD)', 'English, Tagalog', 'Available'),
(3, 'Alexander', 'Chen', 'MD, FACC, FSCAI', 'Cardiologist', 'LIC-MED-99301', '+1 (555) 201-9083', 'dr.chen@tupimunicipal.gov.ph', 16, 'Tupi Municipal Hospital', 'Cardiology Center, 5th Floor', 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=300&h=300', 4.96, 210, '$110.00', 'Expert in cardiovascular evaluations, arrhythmias, chest discomfort triage, hypertension, and advanced heart health.', 'Stanford Medicine (Cardiology Fellowship), Harvard Medical School (MD)', 'English, Mandarin', 'Available'),
(4, 'Elena', 'Villanueva', 'MD, FCN', 'Neurologist', 'LIC-MED-66281', '+1 (555) 201-9084', 'dr.villanueva@tupimunicipal.gov.ph', 12, 'Tupi Municipal Hospital', 'Suite 302, North Wing', 'https://images.unsplash.com/photo-1594824813570-5b12852b7a4b?auto=format&fit=crop&q=80&w=300&h=300', 4.92, 165, '$95.00', 'Dedicated to comprehensive headache diagnostics, migraine therapeutics, peripheral neuropathy, and vertigo management.', 'Columbia University Medical Center (Fellowship), St. Luke\'s College of Medicine (MD)', 'English, Tagalog', 'Available'),
(5, 'Marcus', 'Tan', 'MD, FPCCP', 'Pulmonologist', 'LIC-MED-55194', '+1 (555) 201-9085', 'dr.tan@tupimunicipal.gov.ph', 15, 'Tupi Municipal Hospital', 'Room 108, Pavilion B', 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300&h=300', 4.89, 130, '$90.00', 'Specializing in bronchial asthma, acute and persistent cough evaluation, post-viral respiratory care, and breathing disorders.', 'Mayo Clinic College of Medicine (Pulmonary Fellowship), UP-PGH (MD)', 'English, Hokkien, Tagalog', 'Available'),
(6, 'Gabriel', 'Navarro', 'MD, FPOA', 'Orthopedic Specialist', 'LIC-MED-44029', '+1 (555) 201-9086', 'dr.navarro@tupimunicipal.gov.ph', 13, 'Tupi Municipal Hospital', 'Suite 101, Ortho Pavilion', 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?auto=format&fit=crop&q=80&w=300&h=300', 4.93, 175, '$85.00', 'Expert in back pain rehabilitation, spine ergonomics, knee and shoulder joint injuries, arthritis, and sports-related strains.', 'Singapore General Hospital (Fellowship), UST Medicine (MD)', 'English, Tagalog', 'Available'),
(7, 'Patricia', 'Lim', 'MD, FPDS', 'Dermatologist', 'LIC-MED-33918', '+1 (555) 201-9087', 'dr.lim@tupimunicipal.gov.ph', 11, 'Tupi Municipal Hospital', 'Room 304, Wellness Wing', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300&h=300', 4.91, 154, '$80.00', 'Specializing in acute rashes, contact dermatitis, allergic eczema, skin lesions, and preventative dermal health.', 'Mount Sinai Hospital (Fellowship), Ateneo SOM (MD)', 'English, Tagalog', 'Available'),
(8, 'Roberto', 'Alvarez', 'MD, FPUA', 'Urologist', 'LIC-MED-22874', '+1 (555) 201-9088', 'dr.alvarez@tupimunicipal.gov.ph', 15, 'Tupi Municipal Hospital', 'Suite 220, South Tower', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300', 4.87, 112, '$95.00', 'Focused on urinary tract diagnostics, kidney stones, bladder health, dysuria management, and renal screening.', 'UCSF Health (Fellowship), UP-PGH (MD)', 'English, Tagalog, Spanish', 'Available'),
(9, 'Sofia', 'Gonzales', 'MD, FPCP, FPSEDM', 'Endocrinologist', 'LIC-MED-11763', '+1 (555) 201-9089', 'dr.gonzales@tupimunicipal.gov.ph', 13, 'Tupi Municipal Hospital', 'Suite 412, Medical Plaza', 'https://images.unsplash.com/photo-1594824813570-5b12852b7a4b?auto=format&fit=crop&q=80&w=300&h=300', 4.94, 168, '$90.00', 'Expertise in thyroid disorders, metabolic fatigue, blood glucose regulation, hormonal imbalances, and weight physiology.', 'Cleveland Clinic (Fellowship), UST Faculty of Medicine (MD)', 'English, Tagalog', 'Available'),
(10, 'Kenneth', 'Garcia', 'MD, FAFP', 'Family & General Medicine', 'LIC-MED-10045', '+1 (555) 201-9090', 'dr.garcia@tupimunicipal.gov.ph', 8, 'Tupi Municipal Hospital', 'Room 102, Ground Floor', 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=300&h=300', 4.85, 89, '$55.00', 'Comprehensive family medicine, holistic health checks, initial symptom evaluation, and multi-specialty coordination.', 'St. Luke\'s Medical Center (Residency), Ateneo SOM (MD)', 'English, Tagalog', 'Available')
ON DUPLICATE KEY UPDATE `LicenseNumber`=`LicenseNumber`;

-- 7. Doctor-Body System Junction Linkages
INSERT INTO `doctor_body_systems` (`DoctorID`, `BodySystemID`) VALUES
(1, 1), -- Dr. Santos -> Digestive
(2, 1), -- Dr. Reyes -> Digestive
(2, 9), -- Dr. Reyes -> General
(3, 2), -- Dr. Chen -> Cardiovascular
(4, 3), -- Dr. Villanueva -> Nervous
(5, 4), -- Dr. Tan -> Respiratory
(6, 5), -- Dr. Navarro -> Musculoskeletal
(7, 6), -- Dr. Lim -> Integumentary
(8, 7), -- Dr. Alvarez -> Urinary
(9, 8), -- Dr. Gonzales -> Endocrine
(10, 9), -- Dr. Garcia -> General
(10, 1)  -- Dr. Garcia -> Digestive
ON DUPLICATE KEY UPDATE `DoctorID`=`DoctorID`;

-- 8. Seed Sample Patients
INSERT INTO `patients` (`PatientID`, `PatientCode`, `FirstName`, `MiddleName`, `LastName`, `DateOfBirth`, `Age`, `Gender`, `CivilStatus`, `ContactNumber`, `Email`, `Address`, `BloodType`, `PatientCategory`, `Status`, `RegisteredBy`, `CreatedAt`) VALUES
(1, 'PAT-2026-0001', 'Kent Carl', 'Dela', 'Amit', '2000-05-18', 26, 'Male', 'Single', '+1 (555) 349-8291', 'kent.amit@example.com', '742 Evergreen Terrace, Springfield, OR 97477', 'O+', 'Outpatient', 'Active', 1, '2026-08-15 08:30:00'),
(2, 'PAT-2026-0002', 'Sophia', 'Marie', 'Reyes', '1996-11-24', 29, 'Female', 'Single', '+1 (555) 782-4412', 'sophia.reyes@example.com', '128 Beacon St, Boston, MA 02116', 'A+', 'Consultation', 'Active', 1, '2026-08-15 09:15:00'),
(3, 'PAT-2026-0003', 'David', 'Paul', 'Miller', '1988-03-12', 38, 'Male', 'Married', '+1 (555) 234-9988', 'david.miller@example.com', '450 Ocean Drive, Miami, FL 33139', 'B+', 'Outpatient', 'Active', 1, '2026-08-15 10:00:00'),
(4, 'PAT-2026-0004', 'Lucas', 'James', 'Vance', '1994-08-05', 32, 'Male', 'Single', '+1 (555) 671-8823', 'lucas.vance@example.com', '910 Pine Crest Rd, Seattle, WA 98101', 'O-', 'Consultation', 'Active', 1, '2026-08-15 11:30:00'),
(5, 'PAT-2026-0005', 'Emma', 'Grace', 'Johnson', '1992-04-19', 34, 'Female', 'Married', '+1 (555) 431-7700', 'emma.j@example.com', '55 Lincoln Way, San Francisco, CA 94122', 'AB+', 'Admitted', 'Active', 1, '2026-08-15 13:00:00'),
(6, 'PAT-2026-0006', 'Robert', 'Alan', 'King', '1980-01-15', 46, 'Male', 'Married', '+1 (555) 542-8811', 'robert.king@example.com', '320 Highland Ave, Austin, TX 78701', 'A-', 'Outpatient', 'Active', 1, '2026-08-14 14:20:00'),
(7, 'PAT-2026-0007', 'Clara', 'Isabel', 'Diaz', '2001-09-30', 24, 'Female', 'Single', '+1 (555) 902-3344', 'clara.diaz@example.com', '18 Oakridge Lane, Denver, CO 80203', 'O+', 'Outpatient', 'Active', 1, '2026-08-14 15:45:00'),
(8, 'PAT-2026-0008', 'Ethan', 'Blake', 'Taylor', '1975-06-22', 51, 'Male', 'Married', '+1 (555) 887-1290', 'ethan.taylor@example.com', '804 Valley View Dr, Chicago, IL 60611', 'B-', 'Consultation', 'Active', 1, '2026-08-13 09:10:00'),
(9, 'PAT-2026-0009', 'Olivia', 'Rose', 'Wilson', '1999-02-14', 27, 'Female', 'Single', '+1 (555) 673-9912', 'olivia.w@example.com', '210 Sunset Blvd, Los Angeles, CA 90028', 'A+', 'Outpatient', 'Active', 1, '2026-08-13 11:25:00'),
(10, 'PAT-2026-0010', 'Noah', 'Daniel', 'Anderson', '1985-12-03', 40, 'Male', 'Divorced', '+1 (555) 321-4478', 'noah.a@example.com', '405 River Road, Minneapolis, MN 55401', 'O+', 'Admitted', 'Active', 1, '2026-08-12 16:30:00'),
(11, 'PAT-2026-0011', 'Isabella', 'Mae', 'Martinez', '1993-07-28', 33, 'Female', 'Married', '+1 (555) 765-8899', 'isabella.m@example.com', '612 Cedar Street, San Diego, CA 92101', 'B+', 'Outpatient', 'Active', 1, '2026-08-11 10:45:00'),
(12, 'PAT-2026-0012', 'William', 'Henry', 'Clark', '1968-10-10', 57, 'Male', 'Widowed', '+1 (555) 998-3321', 'william.clark@example.com', '170 Elm St, Atlanta, GA 30301', 'AB-', 'Consultation', 'Active', 1, '2026-08-10 14:00:00')
ON DUPLICATE KEY UPDATE `PatientCode`=`PatientCode`;

-- 9. Emergency Contacts
INSERT INTO `emergency_contacts` (`EmergencyContactID`, `PatientID`, `ContactName`, `Relationship`, `ContactNumber`) VALUES
(1, 1, 'Carmen Amit', 'Parent', '+1 (555) 892-1145'),
(2, 2, 'Marcus Reyes', 'Sibling', '+1 (555) 782-9901'),
(3, 3, 'Sarah Miller', 'Spouse', '+1 (555) 234-9989'),
(4, 4, 'Robert Vance', 'Parent', '+1 (555) 671-9900'),
(5, 5, 'Michael Johnson', 'Spouse', '+1 (555) 431-7701')
ON DUPLICATE KEY UPDATE `ContactName`=`ContactName`;

-- 10. Medical Histories
INSERT INTO `medical_histories` (`MedicalHistoryID`, `PatientID`, `Allergies`, `ExistingConditions`, `CurrentMedications`, `PreviousHospitalization`) VALUES
(1, 1, 'Penicillin (Mild rash)', 'Occasional Acid Reflux', 'Omeprazole 20mg PRN', 'None'),
(2, 2, 'None known', 'History of photophobia', 'Ibuprofen 400mg occasionally', 'None'),
(3, 3, 'Sulfa drugs', 'Mild Hypertension', 'Amlodipine 5mg daily', 'Appendectomy (2018)'),
(4, 4, 'Latex', 'None', 'None currently', 'None'),
(5, 5, 'Aspirin', 'Gastric Ulcer history', 'Antacids', 'Gallbladder removal (2021)')
ON DUPLICATE KEY UPDATE `PatientID`=`PatientID`;

-- 11. Complaints
INSERT INTO `complaints` (`ComplaintID`, `PatientID`, `ComplaintDescription`, `Severity`, `Duration`, `AggravatingFactors`, `RelievingFactors`, `CreatedAt`) VALUES
(1, 1, 'Sharp burning stomach pain right below the ribs since yesterday. The pain noticeably worsens after eating spicy meals, accompanied by frequent nausea and bloating.', 3, '1–3 days ago', 'Eating spicy/heavy meals, lying flat', 'Drinking warm water, over-the-counter antacids', '2026-08-15 08:32:00'),
(2, 2, 'Intense throbbing pain on the left side of my head that began this morning. Bright lights and computer screens make it much worse, accompanied by dizziness and mild nausea.', 4, 'Today', 'Screen glare, loud sounds, bright sunlight', 'Resting in a quiet dark room, cold compress', '2026-08-15 09:18:00'),
(3, 3, 'Rapid heartbeats and fluttering sensations in my chest during work earlier today. Mild tightness in the upper anterior chest area.', 3, 'Today', 'Caffeine intake, work stress, rapid walking', 'Sitting down, slow deep breathing', '2026-08-15 10:05:00'),
(4, 4, 'Twisted right knee during a weekend run 3 days ago. The joint is swollen, stiff in the mornings, and painful when climbing stairs or bending.', 3, '1–3 days ago', 'Climbing stairs, prolonged standing', 'Ice packs, leg elevation, resting', '2026-08-15 11:35:00'),
(5, 5, 'Severe upper abdominal cramps and heartburn after lunch.', 4, 'Today', 'Eating foods with high fat content', 'Sitting upright, drinking milk', '2026-08-15 13:05:00')
ON DUPLICATE KEY UPDATE `ComplaintDescription`=`ComplaintDescription`;

-- 12. Patient Symptoms
INSERT INTO `patient_symptoms` (`ComplaintID`, `SymptomID`) VALUES
(1, 4), -- Nausea
(1, 6), -- Fatigue
(1, 10), -- Constipation
(1, 16), -- Indigestion / Bloating
(2, 2), -- Headache
(2, 3), -- Dizziness
(2, 4), -- Nausea
(3, 11), -- Chest Pain / Pressure
(3, 3), -- Dizziness
(4, 13), -- Joint Pain
(4, 12), -- Muscle Pain
(5, 4), -- Nausea
(5, 16)  -- Indigestion
ON DUPLICATE KEY UPDATE `ComplaintID`=`ComplaintID`;

-- 13. Complaint Analysis
INSERT INTO `complaint_analysis` (`ComplaintID`, `BodySystemID`, `BodyLocationID`, `RelevanceLevel`, `ConfidenceLevel`, `ClinicalNotes`) VALUES
(1, 1, 4, 96, 'High (96%)', 'Epigastric gastric mucosal irritation with secondary dyspeptic presentation.'),
(2, 3, 1, 94, 'High (94%)', 'Unilateral cephalalgia with sensory photophobia indicative of migraine pathway.'),
(3, 2, 3, 92, 'High (92%)', 'Precordial rhythm flutter requiring baseline 12-lead ECG and cardiovascular evaluation.'),
(4, 5, 9, 93, 'High (93%)', 'Patellofemoral joint strain with mechanical inflammatory edema.'),
(5, 1, 4, 95, 'High (95%)', 'Upper gastrointestinal hyperacidity and acute gastric irritation.')
ON DUPLICATE KEY UPDATE `ComplaintID`=`ComplaintID`;

-- 14. Complaint Conditions
INSERT INTO `complaint_conditions` (`ComplaintID`, `ConditionID`) VALUES
(1, 1), -- Gastritis
(1, 2), -- GERD
(1, 3), -- Gastroenteritis
(2, 10), -- Migraine
(2, 9),  -- Tension Headache
(3, 6),  -- Cardiac Palpitations
(3, 7),  -- Chest Wall Discomfort
(4, 15), -- Lumbar / Joint Strain
(4, 16), -- Osteoarthritis
(5, 1),  -- Gastritis
(5, 2)   -- GERD
ON DUPLICATE KEY UPDATE `ComplaintID`=`ComplaintID`;

-- 15. Appointments
INSERT INTO `appointments` (`AppointmentID`, `PatientID`, `DoctorID`, `AppointmentDate`, `AppointmentTime`, `ConsultationType`, `Reason`, `Priority`, `Notes`, `Status`, `CreatedBy`, `CreatedAt`) VALUES
(1, 1, 1, CURDATE(), '02:30 PM', 'In-Person Consultation', 'Stomach pain and nausea after meals', 'Normal', 'Scheduled following digital pre-consultation intake.', 'Waiting', 1, '2026-08-15 08:35:00'),
(2, 2, 4, CURDATE(), '01:45 PM', 'In-Person Consultation', 'Severe throbbing migraine with photophobia', 'Urgent', 'Patient requested quiet examination room.', 'In Consultation', 1, '2026-08-15 09:20:00'),
(3, 3, 3, CURDATE(), '03:00 PM', 'In-Person Consultation', 'Chest flutter and palpitations', 'High Priority', 'Baseline ECG ordered upon arrival.', 'Scheduled', 1, '2026-08-15 10:10:00'),
(4, 4, 6, CURDATE(), '05:00 PM', 'In-Person Consultation', 'Right knee sprain and joint swelling', 'Normal', 'Advised to bring previous knee imaging if any.', 'Scheduled', 1, '2026-08-15 11:40:00'),
(5, 5, 1, CURDATE(), '04:15 PM', 'In-Person Consultation', 'Acute gastric cramps and pyrosis', 'Urgent', 'Inpatient pre-consultation review.', 'Confirmed', 1, '2026-08-15 13:10:00')
ON DUPLICATE KEY UPDATE `AppointmentID`=`AppointmentID`;

-- 16. Patient Queue
INSERT INTO `patient_queue` (`QueueID`, `AppointmentID`, `PatientID`, `DoctorID`, `QueueNumber`, `QueueDate`, `QueueStatus`, `Priority`, `CalledAt`, `ConsultationStartedAt`, `CreatedAt`) VALUES
(1, 2, 2, 4, '01', CURDATE(), 'In Consultation', 'Priority', '2026-08-15 13:40:00', '2026-08-15 13:45:00', '2026-08-15 09:20:00'),
(2, 1, 1, 1, '02', CURDATE(), 'Waiting', 'Normal', NULL, NULL, '2026-08-15 08:35:00'),
(3, 3, 3, 3, '03', CURDATE(), 'Waiting', 'Normal', NULL, NULL, '2026-08-15 10:10:00'),
(4, 5, 5, 1, '04', CURDATE(), 'Waiting', 'Priority', NULL, NULL, '2026-08-15 13:10:00'),
(5, 4, 4, 6, '05', CURDATE(), 'Waiting', 'Normal', NULL, NULL, '2026-08-15 11:40:00')
ON DUPLICATE KEY UPDATE `QueueID`=`QueueID`;

-- 17. Registration History Timeline
INSERT INTO `patient_registration_history` (`HistoryID`, `PatientID`, `UserID`, `Action`, `Description`, `CreatedAt`) VALUES
(1, 1, 1, 'Patient Registered', 'Patient Kent Carl Amit registered with digital code PAT-2026-0001.', '2026-08-15 08:30:00'),
(2, 1, 1, 'Complaint Added', 'Chief complaint recorded: Sharp burning stomach pain and nausea.', '2026-08-15 08:32:00'),
(3, 1, 1, 'Symptom Classification', 'Automated symptom correlation classified primary system as Digestive System (96% relevance).', '2026-08-15 08:33:00'),
(4, 1, 1, 'Doctor Assigned', 'Consultation booked with Dr. Maria Santos (Gastroenterology).', '2026-08-15 08:35:00'),
(5, 1, 1, 'Queue Generated', 'Assigned Queue Ticket #02 for Today.', '2026-08-15 08:35:00'),
(6, 2, 1, 'Patient Registered', 'Patient Sophia Marie Reyes registered with digital code PAT-2026-0002.', '2026-08-15 09:15:00'),
(7, 2, 1, 'Doctor Assigned', 'Consultation booked with Dr. Elena Villanueva (Neurology). Queue #01.', '2026-08-15 09:20:00')
ON DUPLICATE KEY UPDATE `HistoryID`=`HistoryID`;
