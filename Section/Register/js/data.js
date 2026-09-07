// Medical Knowledge Base & Mock Data for Frontend Intake System

const BODY_SYSTEMS_DATA = {
  digestive: {
    id: "digestive",
    name: "Digestive System",
    icon: "🩺",
    lucideIcon: "activity",
    color: "blue",
    description: "Involves the esophagus, stomach, intestines, liver, pancreas, and gallbladder.",
    defaultLocation: "abdomen",
    defaultSubRegion: "Upper Abdominal / Epigastric Region",
    keywords: ["stomach", "abdomen", "abdominal", "belly", "nausea", "vomiting", "diarrhea", "constipation", "acid", "reflux", "heartburn", "bloating", "indigestion", "cramps", "bowel", "gastric", "ulcer", "appetite", "stool", "eating", "food"],
    symptomCategories: ["Abdominal Discomfort", "Digestive Disturbance", "Upper GI Irritation", "Bowel Irregularity"],
    possibleConditions: [
      { name: "Gastritis", note: "Irritation or inflammation of the stomach lining" },
      { name: "Gastroesophageal Reflux (GERD)", note: "Acid backup into the esophagus causing heartburn" },
      { name: "Gastroenteritis", note: "Temporary inflammation of digestive tract (stomach flu)" },
      { name: "Peptic Ulcer Disease", note: "Sores in the lining of the stomach or small intestine" },
      { name: "Functional Dyspepsia", note: "Persistent indigestion without apparent structural cause" }
    ],
    recommendedSpecialties: ["Gastroenterologist", "Internal Medicine", "General Practitioner"]
  },
  cardiovascular: {
    id: "cardiovascular",
    name: "Cardiovascular System",
    icon: "🫀",
    lucideIcon: "heart-pulse",
    color: "rose",
    description: "Involves the heart, blood vessels, circulation, and blood pressure regulation.",
    defaultLocation: "chest",
    defaultSubRegion: "Anterior Chest / Precordial Area",
    keywords: ["chest", "heart", "palpitation", "palpitations", "racing heart", "heartbeat", "pulse", "tightness", "pressure in chest", "angina", "cardiac", "circulation", "flutters"],
    symptomCategories: ["Precordial Discomfort", "Rhythm Sensation", "Circulatory Symptoms", "Exertional Fatigue"],
    possibleConditions: [
      { name: "Cardiac Palpitations", note: "Sensations of racing, pounding, or fluttering heartbeat" },
      { name: "Chest Wall Discomfort", note: "Musculoskeletal or non-cardiac thoracic wall sensation" },
      { name: "Hypertensive Response", note: "Blood pressure elevation requiring clinical screening" },
      { name: "Cardiovascular Evaluation Needed", note: "Symptoms requiring baseline ECG and physician review" }
    ],
    recommendedSpecialties: ["Cardiologist", "Internal Medicine", "General Practitioner"]
  },
  nervous: {
    id: "nervous",
    name: "Nervous System",
    icon: "🧠",
    lucideIcon: "zap",
    color: "indigo",
    description: "Involves the brain, spinal cord, nerves, sensation, and coordination.",
    defaultLocation: "head",
    defaultSubRegion: "Cranial / Frontal & Temporal Region",
    keywords: ["head", "headache", "migraine", "dizzy", "dizziness", "vertigo", "lightheaded", "numbness", "tingling", "seizure", "neuralgia", "brain", "faint", "confusion", "balance", "tremor", "memory"],
    symptomCategories: ["Cephalalgia (Headache)", "Sensory Disturbance", "Equilibrium Impairment", "Neuropathic Sensations"],
    possibleConditions: [
      { name: "Tension Headache", note: "Common mild to moderate band-like head pressure" },
      { name: "Migraine with/without Aura", note: "Throbbing unilateral headache often with light/sound sensitivity" },
      { name: "Benign Positional Vertigo", note: "Spinning sensation triggered by changes in head position" },
      { name: "Peripheral Nerve Irritation", note: "Local nerve compression causing tingling or numbness" }
    ],
    recommendedSpecialties: ["Neurologist", "Internal Medicine", "General Practitioner"]
  },
  respiratory: {
    id: "respiratory",
    name: "Respiratory System",
    icon: "🫁",
    lucideIcon: "wind",
    color: "cyan",
    description: "Involves the airways, lungs, trachea, and gas exchange.",
    defaultLocation: "chest",
    defaultSubRegion: "Thoracic / Bronchial & Lung Fields",
    keywords: ["cough", "coughing", "breathing", "breath", "shortness of breath", "wheezing", "lungs", "throat", "phlegm", "mucus", "asthma", "congestion", "respiratory", "inhale", "exhale"],
    symptomCategories: ["Airway Irritation", "Bronchial Reactivity", "Dyspneic Sensation", "Upper Respiratory Symptoms"],
    possibleConditions: [
      { name: "Upper Respiratory Tract Infection (URTI)", note: "Common viral infection affecting nose and throat" },
      { name: "Acute Bronchitis", note: "Inflammation of bronchial airways commonly following a cold" },
      { name: "Reactive Airway / Asthma Symptoms", note: "Bronchospasm causing wheezing or shortness of breath" },
      { name: "Allergic Rhinitis / Pharyngitis", note: "Allergen-induced upper airway mucosal inflammation" }
    ],
    recommendedSpecialties: ["Pulmonologist", "ENT Specialist", "Internal Medicine", "General Practitioner"]
  },
  musculoskeletal: {
    id: "musculoskeletal",
    name: "Musculoskeletal System",
    icon: "🦴",
    lucideIcon: "bone",
    color: "amber",
    description: "Involves bones, joints, muscles, tendons, ligaments, and spine.",
    defaultLocation: "back",
    defaultSubRegion: "Lumbar & Joint Articulations",
    keywords: ["back", "joint", "muscle", "bone", "knee", "shoulder", "spine", "neck", "hip", "ankle", "wrist", "sprain", "strain", "stiffness", "arthritis", "swelling", "sciatica", "leg pain", "arm pain"],
    symptomCategories: ["Myofascial Strain", "Articular Discomfort", "Axial Spinal Tension", "Localized Joint Pain"],
    possibleConditions: [
      { name: "Mechanical Lumbar Strain", note: "Muscle or ligament tension in the lower back region" },
      { name: "Osteoarthritis / Joint Inflammation", note: "Degenerative wear of protective joint cartilage" },
      { name: "Tendonitis / Bursitis", note: "Inflammation of tendon or lubricating fluid sac from overuse" },
      { name: "Myofascial Pain Syndrome", note: "Chronic pain disorder in trigger points of muscular tissue" }
    ],
    recommendedSpecialties: ["Orthopedic Specialist", "Physical Medicine Specialist", "Rheumatologist", "General Practitioner"]
  },
  integumentary: {
    id: "integumentary",
    name: "Integumentary (Skin) System",
    icon: "🧴",
    lucideIcon: "shield",
    color: "emerald",
    description: "Involves skin, hair, nails, and cutaneous nerve receptors.",
    defaultLocation: "arms",
    defaultSubRegion: "Dermal / Cutaneous Tissue",
    keywords: ["skin", "rash", "itching", "itch", "redness", "lesion", "eczema", "hives", "acne", "bumps", "burn", "scaling", "blister", "mole", "dermatitis"],
    symptomCategories: ["Cutaneous Eruption", "Pruritic Symptoms", "Dermal Inflammation", "Allergic Skin Reaction"],
    possibleConditions: [
      { name: "Contact Dermatitis", note: "Skin reaction from direct contact with irritating substance" },
      { name: "Urticaria (Hives)", note: "Transient itchy red welts typically triggered by allergic reaction" },
      { name: "Eczema (Atopic Dermatitis)", note: "Dry, red, itchy inflammatory skin condition" },
      { name: "Superficial Fungal / Bacterial Dermatosis", note: "Localized topical infection requiring evaluation" }
    ],
    recommendedSpecialties: ["Dermatologist", "Allergist", "General Practitioner"]
  },
  urinary: {
    id: "urinary",
    name: "Urinary & Renal System",
    icon: "🧪",
    lucideIcon: "droplets",
    color: "teal",
    description: "Involves kidneys, ureters, bladder, and urinary tract function.",
    defaultLocation: "pelvis",
    defaultSubRegion: "Suprapubic & Flank / Renal Region",
    keywords: ["urine", "urination", "bladder", "kidney", "peeing", "burning urine", "urinary", "flank pain", "frequent urination", "bloody urine", "pelvic"],
    symptomCategories: ["Dysuria (Discomfort on Urination)", "Urinary Frequency", "Flank Discomfort", "Lower Urinary Tract Symptoms"],
    possibleConditions: [
      { name: "Urinary Tract Infection (UTI)", note: "Bacterial colonization of bladder or urethra" },
      { name: "Renal Colic / Nephrolithiasis Concern", note: "Crystalline stones in kidney or urinary tract" },
      { name: "Overactive Bladder Syndrome", note: "Frequent, sudden involuntary bladder contractions" },
      { name: "Benign Lower Urinary Irritation", note: "Temporary inflammation from hydration or dietary factors" }
    ],
    recommendedSpecialties: ["Urologist", "Nephrologist", "Internal Medicine", "General Practitioner"]
  },
  endocrine: {
    id: "endocrine",
    name: "Endocrine & Metabolic System",
    icon: "🧬",
    lucideIcon: "sparkles",
    color: "purple",
    description: "Involves hormones, thyroid, pituitary, adrenal, and metabolism.",
    defaultLocation: "neck",
    defaultSubRegion: "Thyroid & Systemic Metabolic Axis",
    keywords: ["thyroid", "fatigue", "weight", "hormone", "sugar", "glucose", "sweating", "cold intolerance", "heat intolerance", "thirst", "metabolism"],
    symptomCategories: ["Metabolic Imbalance", "Endocrine Dysregulation", "Thermoregulatory Changes", "Systemic Energy Fatigue"],
    possibleConditions: [
      { name: "Thyroid Function Imbalance", note: "Hypothyroidism or hyperthyroidism requiring hormone panel" },
      { name: "Glycemic Dysregulation Concern", note: "Blood sugar fluctuation requiring fasting panel" },
      { name: "Adrenal Fatigue / Hormonal Stress", note: "Cortisol axis dysregulation during chronic stress" },
      { name: "Metabolic Syndrome Manifestation", note: "Cluster of metabolic markers needing physician review" }
    ],
    recommendedSpecialties: ["Endocrinologist", "Internal Medicine", "General Practitioner"]
  },
  general: {
    id: "general",
    name: "General / Multisystem",
    icon: "🏥",
    lucideIcon: "cross",
    color: "slate",
    description: "Involves whole body, generalized immune response, or non-localized symptoms.",
    defaultLocation: "whole_body",
    defaultSubRegion: "Systemic / Whole Body",
    keywords: ["fever", "chills", "weakness", "malaise", "tired", "exhaustion", "sweats", "whole body", "unwell", "sick", "virus"],
    symptomCategories: ["Constitutional Symptoms", "Systemic Immune Activation", "General Malaise", "Vital Sign Variability"],
    possibleConditions: [
      { name: "Viral Syndrome / Malaise", note: "General viral reaction with systemic immune response" },
      { name: "Physical Exhaustion / Sleep Deficit", note: "Fatigue secondary to physiological stress or lack of rest" },
      { name: "Post-Viral Convalescence", note: "Gradual recovery period after recent infection" },
      { name: "Multisystem Evaluation Needed", note: "Broad symptom presentation requiring comprehensive physical" }
    ],
    recommendedSpecialties: ["Internal Medicine", "General Practitioner", "Family Medicine"]
  }
};

// Body Region to Anatomy Map
const BODY_REGIONS_MAP = {
  head: {
    name: "Head & Cranium",
    defaultSystem: "nervous",
    subRegion: "Cranial, Temporal & Facial Area",
    confidence: "High (95%)",
    clinicalNotes: "Covers cerebrum, cranial nerves, sinuses, scalp, and facial sensation pathways.",
    view: "front"
  },
  neck: {
    name: "Neck & Cervical Spine",
    defaultSystem: "musculoskeletal",
    subRegion: "Anterior / Posterior Cervical Region",
    confidence: "High (90%)",
    clinicalNotes: "Covers cervical vertebrae, thyroid gland, pharynx, and neck musculature.",
    view: "front"
  },
  chest: {
    name: "Chest & Thorax",
    defaultSystem: "cardiovascular",
    subRegion: "Anterior Chest / Precordial & Sternal Area",
    confidence: "High (92%)",
    clinicalNotes: "Houses the heart, pericardium, lungs, pleura, thoracic cage, and esophagus.",
    view: "front"
  },
  abdomen: {
    name: "Abdomen (Stomach & GI)",
    defaultSystem: "digestive",
    subRegion: "Epigastric & Umbilical Abdominal Quadrants",
    confidence: "High (96%)",
    clinicalNotes: "Houses stomach, intestines, liver, gallbladder, pancreas, and abdominal wall.",
    view: "front"
  },
  back: {
    name: "Back & Lumbar Spine",
    defaultSystem: "musculoskeletal",
    subRegion: "Thoracic & Lumbar Spinal Column",
    confidence: "High (94%)",
    clinicalNotes: "Includes paraspinal musculature, intervertebral discs, and dorsal nerve roots.",
    view: "back"
  },
  arms: {
    name: "Upper Limbs & Arms",
    defaultSystem: "musculoskeletal",
    subRegion: "Biceps, Forearms & Elbow Articulations",
    confidence: "High (90%)",
    clinicalNotes: "Includes brachial plexus nerves, humerus, radius, ulna, and arm musculature.",
    view: "front"
  },
  hands: {
    name: "Hands & Wrists",
    defaultSystem: "musculoskeletal",
    subRegion: "Carpal, Metacarpal & Digital Extremities",
    confidence: "High (88%)",
    clinicalNotes: "Covers carpal tunnel, digital joints, median/radial nerves, and flexor tendons.",
    view: "front"
  },
  pelvis: {
    name: "Pelvis & Groin Area",
    defaultSystem: "urinary",
    subRegion: "Suprapubic, Inguinal & Pelvic Floor",
    confidence: "High (89%)",
    clinicalNotes: "Encompasses bladder, reproductive organs, pelvic girdle, and inguinal canals.",
    view: "front"
  },
  legs: {
    name: "Lower Limbs & Legs",
    defaultSystem: "musculoskeletal",
    subRegion: "Femoral, Patellar (Knee) & Tibial Region",
    confidence: "High (93%)",
    clinicalNotes: "Includes quadriceps, hamstrings, knee cruciate ligaments, calves, and sciatic path.",
    view: "front"
  },
  feet: {
    name: "Feet & Ankles",
    defaultSystem: "musculoskeletal",
    subRegion: "Tarsal, Plantar & Metatarsal Complex",
    confidence: "High (91%)",
    clinicalNotes: "Includes Achilles tendon, plantar fascia, ankle talocrural joint, and foot arches.",
    view: "front"
  },
  whole_body: {
    name: "Whole Body / Systemic",
    defaultSystem: "general",
    subRegion: "Generalized Systemic Distribution",
    confidence: "High (85%)",
    clinicalNotes: "Non-localized presentation involving systemic physiological or metabolic factors.",
    view: "front"
  },
  other: {
    name: "Other Specific Location",
    defaultSystem: "general",
    subRegion: "Non-standard Anatomical Distribution",
    confidence: "Medium (75%)",
    clinicalNotes: "To be clarified and examined in detail during physician clinical consultation.",
    view: "front"
  }
};

// Mock Doctors Directory with specialties, badges, ratings, and schedules
const MOCK_DOCTORS = [
  {
    id: "dr-santos",
    name: "Dr. Maria Santos",
    title: "MD, FPCP, FPSG",
    gender: "female",
    specialty: "Gastroenterologist",
    system: "Digestive System",
    systemId: "digestive",
    experience: "14 years experience",
    rating: 4.9,
    reviewsCount: 142,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Suite 405, East Wing",
    availability: "Available Today",
    availableTime: "Today at 2:30 PM",
    badge: "Top Rated Specialist",
    avatar: "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Board-certified specialist in digestive wellness, endoscopy, acid reflux management, gastritis, and inflammatory bowel disorders.",
    education: "Johns Hopkins Medicine (Fellowship), UST Faculty of Medicine (MD)",
    languages: ["English", "Tagalog", "Spanish"],
    consultationFee: "$85.00",
    insuranceAccepted: ["BlueCross", "Aetna", "UnitedHealth", "Cigna", "Medicare"],
    expertise: ["Acid Reflux & GERD", "Gastritis & Ulcers", "Irritable Bowel Syndrome", "Liver & Gallbladder Care", "Endoscopy Screening"]
  },
  {
    id: "dr-reyes",
    name: "Dr. John Reyes",
    title: "MD, FPCP",
    gender: "male",
    specialty: "Internal Medicine Specialist",
    system: "Digestive System",
    systemId: "digestive",
    experience: "10 years experience",
    rating: 4.8,
    reviewsCount: 98,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Room 210, Main Tower",
    availability: "Available Today",
    availableTime: "Today at 4:15 PM",
    badge: "Rapid Consultation",
    avatar: "https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Comprehensive adult disease management, digestive health screening, metabolic evaluation, and preventative primary healthcare.",
    education: "Philippine General Hospital (Residency), UP College of Medicine (MD)",
    languages: ["English", "Tagalog"],
    consultationFee: "$65.00",
    insuranceAccepted: ["BlueCross", "Kaiser", "Aetna", "Humana"],
    expertise: ["Adult General Health", "GI Symptom Screening", "Hypertension & Diabetes", "Preventative Health Audits"]
  },
  {
    id: "dr-chen",
    name: "Dr. Alexander Chen",
    title: "MD, FACC, FSCAI",
    gender: "male",
    specialty: "Cardiologist",
    system: "Cardiovascular System",
    systemId: "cardiovascular",
    experience: "16 years experience",
    rating: 4.95,
    reviewsCount: 210,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Cardiology Center, 5th Floor",
    availability: "Available Today",
    availableTime: "Today at 3:00 PM",
    badge: "Cardiology Chief",
    avatar: "https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Expert in cardiovascular evaluations, arrhythmias, chest discomfort triage, hypertension, and advanced heart health.",
    education: "Stanford Medicine (Cardiology Fellowship), Harvard Medical School (MD)",
    languages: ["English", "Mandarin"],
    consultationFee: "$110.00",
    insuranceAccepted: ["BlueCross", "UnitedHealth", "Aetna", "Cigna", "Medicare"],
    expertise: ["Heart Palpitations & Arrhythmia", "Chest Pain Triage", "Echocardiography", "Preventative Cardiology", "Hypertension Control"]
  },
  {
    id: "dr-villanueva",
    name: "Dr. Elena Villanueva",
    title: "MD, FCN",
    gender: "female",
    specialty: "Neurologist",
    system: "Nervous System",
    systemId: "nervous",
    experience: "12 years experience",
    rating: 4.9,
    reviewsCount: 165,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Suite 302, North Wing",
    availability: "Available Today",
    availableTime: "Today at 1:45 PM",
    badge: "Headache & Migraine Specialist",
    avatar: "https://images.unsplash.com/photo-1594824813570-5b12852b7a4b?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Dedicated to comprehensive headache diagnostics, migraine therapeutics, peripheral neuropathy, and vertigo management.",
    education: "Columbia University Medical Center (Fellowship), St. Luke's College of Medicine (MD)",
    languages: ["English", "Tagalog"],
    consultationFee: "$95.00",
    insuranceAccepted: ["BlueCross", "Aetna", "UnitedHealth", "Kaiser"],
    expertise: ["Migraines & Chronic Headaches", "Vertigo & Dizziness", "Nerve Pain & Neuropathy", "Cognitive Health", "Sleep & Nerve Disorders"]
  },
  {
    id: "dr-tan",
    name: "Dr. Marcus Tan",
    title: "MD, FPCCP",
    gender: "male",
    specialty: "Pulmonologist",
    system: "Respiratory System",
    systemId: "respiratory",
    experience: "15 years experience",
    rating: 4.88,
    reviewsCount: 130,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Room 108, Pavilion B",
    availability: "Available Tomorrow",
    availableTime: "Tomorrow at 10:00 AM",
    badge: "Pulmonary Care Specialist",
    avatar: "https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Specializing in bronchial asthma, acute and persistent cough evaluation, post-viral respiratory care, and breathing disorders.",
    education: "Mayo Clinic College of Medicine (Pulmonary Fellowship), UP-PGH (MD)",
    languages: ["English", "Hokkien", "Tagalog"],
    consultationFee: "$90.00",
    insuranceAccepted: ["BlueCross", "Aetna", "Cigna", "Medicare"],
    expertise: ["Chronic Cough Diagnostics", "Asthma & Bronchitis", "Shortness of Breath Evaluation", "Pulmonary Function Tests"]
  },
  {
    id: "dr-navarro",
    name: "Dr. Gabriel Navarro",
    title: "MD, FPOA",
    gender: "male",
    specialty: "Orthopedic Specialist",
    system: "Musculoskeletal System",
    systemId: "musculoskeletal",
    experience: "13 years experience",
    rating: 4.92,
    reviewsCount: 175,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Suite 101, Ortho Pavilion",
    availability: "Available Today",
    availableTime: "Today at 5:00 PM",
    badge: "Sports & Joint Care",
    avatar: "https://images.unsplash.com/photo-1582750433449-648ed127bb54?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Expert in back pain rehabilitation, spine ergonomics, knee and shoulder joint injuries, arthritis, and sports-related strains.",
    education: "Singapore General Hospital (Fellowship), UST Medicine (MD)",
    languages: ["English", "Tagalog"],
    consultationFee: "$85.00",
    insuranceAccepted: ["BlueCross", "UnitedHealth", "Aetna", "Humana"],
    expertise: ["Lower Back Pain & Sciatica", "Knee & Shoulder Joint Pain", "Sprain & Muscle Strain Rehab", "Arthritis Management"]
  },
  {
    id: "dr-lim",
    name: "Dr. Patricia Lim",
    title: "MD, FPDS",
    gender: "female",
    specialty: "Dermatologist",
    system: "Integumentary (Skin) System",
    systemId: "integumentary",
    experience: "11 years experience",
    rating: 4.91,
    reviewsCount: 154,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Room 304, Wellness Wing",
    availability: "Available Tomorrow",
    availableTime: "Tomorrow at 11:30 AM",
    badge: "Board Certified Dermatologist",
    avatar: "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Specializing in acute rashes, contact dermatitis, allergic eczema, skin lesions, and preventative dermal health.",
    education: "Mount Sinai Hospital (Fellowship), Ateneo School of Medicine (MD)",
    languages: ["English", "Tagalog"],
    consultationFee: "$80.00",
    insuranceAccepted: ["BlueCross", "Aetna", "UnitedHealth", "Cigna"],
    expertise: ["Allergic Rashes & Hives", "Eczema & Dermatitis", "Skin Barrier Restoration", "Cutaneous Infections"]
  },
  {
    id: "dr-alvarez",
    name: "Dr. Roberto Alvarez",
    title: "MD, FPUA",
    gender: "male",
    specialty: "Urologist",
    system: "Urinary & Renal System",
    systemId: "urinary",
    experience: "15 years experience",
    rating: 4.87,
    reviewsCount: 112,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Suite 220, South Tower",
    availability: "Available Today",
    availableTime: "Today at 3:45 PM",
    badge: "Urinary Care Expert",
    avatar: "https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Focused on urinary tract diagnostics, kidney stones, bladder health, dysuria management, and renal screening.",
    education: "UCSF Health (Fellowship), UP-PGH (MD)",
    languages: ["English", "Tagalog", "Spanish"],
    consultationFee: "$95.00",
    insuranceAccepted: ["BlueCross", "Medicare", "Aetna", "UnitedHealth"],
    expertise: ["Urinary Tract Infections", "Kidney Stone Triage", "Bladder Discomfort", "Renal Function Assessments"]
  },
  {
    id: "dr-gonzales",
    name: "Dr. Sofia Gonzales",
    title: "MD, FPCP, FPSEDM",
    gender: "female",
    specialty: "Endocrinologist",
    system: "Endocrine & Metabolic System",
    systemId: "endocrine",
    experience: "13 years experience",
    rating: 4.93,
    reviewsCount: 168,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Suite 412, Medical Plaza",
    availability: "Available Tomorrow",
    availableTime: "Tomorrow at 2:00 PM",
    badge: "Hormone & Metabolism Lead",
    avatar: "https://images.unsplash.com/photo-1594824813570-5b12852b7a4b?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Expertise in thyroid disorders, metabolic fatigue, blood glucose regulation, hormonal imbalances, and weight physiology.",
    education: "Cleveland Clinic (Endocrine Fellowship), UST Faculty of Medicine (MD)",
    languages: ["English", "Tagalog"],
    consultationFee: "$90.00",
    insuranceAccepted: ["BlueCross", "UnitedHealth", "Aetna", "Cigna"],
    expertise: ["Thyroid Health & Nodules", "Metabolic Dysregulation", "Hormonal Fatigue Evaluation", "Diabetes Care"]
  },
  {
    id: "dr-garcia",
    name: "Dr. Kenneth Garcia",
    title: "MD, FAFP",
    gender: "male",
    specialty: "Family & General Medicine",
    system: "General / Multisystem",
    systemId: "general",
    experience: "8 years experience",
    rating: 4.85,
    reviewsCount: 89,
    hospital: "Tupi Municipal Hospital",
    clinicRoom: "Room 102, Ground Floor",
    availability: "Available Today",
    availableTime: "Today at 1:15 PM",
    badge: "Immediate Walk-In",
    avatar: "https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=300&h=300",
    description: "Comprehensive family medicine, holistic health checks, initial symptom evaluation, and multi-specialty coordination.",
    education: "St. Luke's Medical Center (Residency), Ateneo SOM (MD)",
    languages: ["English", "Tagalog"],
    consultationFee: "$55.00",
    insuranceAccepted: ["BlueCross", "Kaiser", "Aetna", "Humana", "Medicare"],
    expertise: ["Primary Care Consultation", "Multi-symptom Workup", "Preventative Screenings", "Routine Health Clearances"]
  }
];

// Demo Presets for 1-Click Instant Testing of any symptom pathway
const DEMO_PRESETS = {
  gastritis: {
    label: "Stomach Pain & Nausea (Gastrointestinal)",
    personal: {
      firstName: "Kent Carl",
      middleName: "Dela",
      lastName: "Amit",
      dob: "2000-05-18",
      gender: "Male",
      civilStatus: "Single",
      phone: "+1 (555) 349-8291",
      email: "kent.amit@example.com",
      address: "742 Evergreen Terrace, Springfield, OR 97477"
    },
    emergency: {
      name: "Carmen Amit",
      relationship: "Parent",
      phone: "+1 (555) 892-1145"
    },
    medical: {
      allergies: "Penicillin (Mild rash)",
      conditions: "Occasional Acid Reflux",
      medications: "Antacids as needed (Omeprazole 20mg)",
      hospitalization: "No previous hospitalizations",
      bloodType: "O+"
    },
    complaint: "I have been experiencing sharp burning stomach pain right below my ribs since yesterday afternoon. The pain noticeably worsens after eating spicy meals and I have felt nauseous with frequent bloating.",
    bodyLocation: "abdomen",
    severity: 3,
    duration: "1-3 days ago",
    aggravating: "Eating heavy or spicy meals, lying down flat",
    relieving: "Drinking warm water, taking over-the-counter antacids",
    symptoms: ["Nausea", "Fatigue", "Constipation", "Indigestion / Bloating"]
  },
  migraine: {
    label: "Severe Throbbing Headache (Neurological)",
    personal: {
      firstName: "Sophia",
      middleName: "Marie",
      lastName: "Reyes",
      dob: "1996-11-24",
      gender: "Female",
      civilStatus: "Single",
      phone: "+1 (555) 782-4412",
      email: "sophia.reyes@example.com",
      address: "128 Beacon St, Boston, MA 02116"
    },
    emergency: {
      name: "Marcus Reyes",
      relationship: "Sibling",
      phone: "+1 (555) 782-9901"
    },
    medical: {
      allergies: "None known",
      conditions: "History of light sensitivity",
      medications: "Ibuprofen 400mg occasionally",
      hospitalization: "None",
      bloodType: "A+"
    },
    complaint: "Intense throbbing pain on the left side of my head that began this morning. Bright lights and computer screens make it much worse, accompanied by mild dizziness and nausea.",
    bodyLocation: "head",
    severity: 4,
    duration: "Today",
    aggravating: "Screen glare, loud sounds, bright sunlight",
    relieving: "Resting in a quiet dark room, cold compress on forehead",
    symptoms: ["Headache", "Dizziness", "Nausea", "Fatigue"]
  },
  chest_palp: {
    label: "Chest Palpitations & Flutters (Cardiovascular)",
    personal: {
      firstName: "David",
      middleName: "Paul",
      lastName: "Miller",
      dob: "1988-03-12",
      gender: "Male",
      civilStatus: "Married",
      phone: "+1 (555) 234-9988",
      email: "david.miller@example.com",
      address: "450 Ocean Drive, Miami, FL 33139"
    },
    emergency: {
      name: "Sarah Miller",
      relationship: "Spouse",
      phone: "+1 (555) 234-9989"
    },
    medical: {
      allergies: "Sulfa drugs",
      conditions: "Mild Hypertension",
      medications: "Amlodipine 5mg daily",
      hospitalization: "Appendectomy (2018)",
      bloodType: "B+"
    },
    complaint: "I felt rapid heartbeats and fluttering sensations in my chest during work earlier today. It felt like my heart skipped a beat, with mild tightness in the upper chest area.",
    bodyLocation: "chest",
    severity: 3,
    duration: "Today",
    aggravating: "Caffeine intake, work stress, rapid walking",
    relieving: "Sitting down, deep breathing exercises",
    symptoms: ["Chest Pain / Pressure", "Dizziness", "Fatigue"]
  },
  knee_sprain: {
    label: "Right Knee Pain & Stiffness (Musculoskeletal)",
    personal: {
      firstName: "Lucas",
      middleName: "James",
      lastName: "Vance",
      dob: "1994-08-05",
      gender: "Male",
      civilStatus: "Single",
      phone: "+1 (555) 671-8823",
      email: "lucas.vance@example.com",
      address: "910 Pine Crest Rd, Seattle, WA 98101"
    },
    emergency: {
      name: "Robert Vance",
      relationship: "Parent",
      phone: "+1 (555) 671-9900"
    },
    medical: {
      allergies: "Latex",
      conditions: "None",
      medications: "None currently",
      hospitalization: "None",
      bloodType: "O-"
    },
    complaint: "Twisted my right knee during a casual weekend run 3 days ago. The joint is swollen, stiff in the mornings, and painful when bending or walking up stairs.",
    bodyLocation: "legs",
    severity: 3,
    duration: "1-3 days ago",
    aggravating: "Climbing stairs, prolonged standing, bending the knee",
    relieving: "Ice packs, elevating leg on pillows, resting",
    symptoms: ["Joint Pain", "Muscle Pain", "Fatigue"]
  }
};
