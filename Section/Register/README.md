# Tupi Municipal Hospital Information Management System

A modern, responsive, multi-step **Medical Patient Intake & Pre-Consultation Form** frontend application built for clinics, specialty health centers, and digital hospital portals.

---

## 🌟 Key Features & Workflow

The application guides patients through a structured, calm, and trustworthy 8-step clinical intake flow:

1. **Step 1 — Patient Registration**
   * Two-column responsive card layout.
   * **Personal Information**: First Name, Middle Name, Last Name, Date of Birth (with **instant automatic Age calculation**), Gender cards (Male, Female, Other), Civil Status, Phone, Email, and Address.
   * **Emergency Contact**: Full Name, Relationship (Parent, Spouse, Sibling, etc.), Emergency Hotline.
   * **Medical Background**: Known Allergies, Existing Conditions, Current Medications, Prior Hospitalization, Blood Type dropdown.
   * **1-Click Demo Presets**: Test instant scenarios (e.g. Gastritis, Migraine, Palpitations, Knee Sprain).

2. **Step 2 — Verify Patient Information**
   * Clean verification dashboard with formatted demographics and identity badges.
   * "Edit Information" action to update fields without losing data.
   * "Confirm & Continue" action with animated verification checkmark.

3. **Step 3 — Chief Complaint & Symptom Evaluation**
   * Large intuitive textarea with character counter.
   * **Live NLP-like Suggestion Bar**: Dynamically analyzes clinical keywords as the patient types and previews relevant body systems.
   * **Structured Location Cards**: Head, Neck, Chest, Abdomen, Back, Arms, Hands, Pelvis, Legs, Feet, Whole Body.
   * **Visual Severity Scale (1–5)**: Color-coded severity tiles (Very Mild → Very Severe).
   * **Onset & Duration**: Today, 1–3 days, <1 week, 1–4 weeks, >1 month.
   * **Aggravating & Relieving Factors**: Text inputs for triggers and remedies.
   * **Multi-Select Symptom Grid**: Fever, Headache, Dizziness, Nausea, Vomiting, Fatigue, Cough, Shortness of Breath, etc.
   * **Urgent Red-Flag Detection**: Automatically reveals clinical emergency advisory if urgent keywords are detected.

4. **Step 4 — Medical Classification & Suggested Body System**
   * Simulated clinical keyword parsing animation.
   * Highlighted **Primary Associated System** (e.g., 🩺 Digestive System — 96% Relevance) with clinical rationale.
   * **9 Interactive Body Systems Grid** allowing patients to explore or manually switch systems:
     * 🩺 Digestive System
     * 🫀 Cardiovascular System
     * 🧠 Nervous System
     * 🫁 Respiratory System
     * 🦴 Musculoskeletal System
     * 🧴 Integumentary (Skin) System
     * 🧪 Urinary & Renal System
     * 🧬 Endocrine & Metabolic System
     * 🏥 General / Multisystem
   * Related Symptom Categories & Possible Discussion Topics.
   * **Safety Disclaimers**: Explicitly clarifies that suggestions are for consultation reference only and do not constitute a diagnosis.

5. **Step 5 — Interactive Human Body Location (Anterior & Posterior Views)**
   * High-detail vector SVG anatomical body map.
   * **Front (Anterior) & Back (Posterior)** view toggle tabs.
   * Interactive body zones with hover highlights and glowing blue locator pin with pulsing radar rings.
   * Auto-highlights the region matching the patient's complaint (e.g., Abdomen/Epigastric area).
   * Detailed anatomical metrics card (Detected Area, Sub-region, Related System, Confidence score, Clinical Notes).

6. **Step 6 — Pre-Consultation Summary Review**
   * Comprehensive patient intake dashboard.
   * Printable summary with Intake Reference ID (`#AH-XXXXXX`).
   * Chief complaint recap, symptoms breakdown, severity, duration, and body localization.
   * Printable / Exportable intake summary with dedicated print styles.

7. **Step 7 — Smart Doctor Recommendations**
   * Frontend sorting algorithm that automatically prioritizes medical specialists matching the patient's affected body system (e.g., Gastroenterologists for Digestive concerns, Cardiologists for chest concerns).
   * Filter controls: All Specialists, Matched System, Available Today.
   * Doctor Cards featuring avatar, credentials, experience, rating, clinic location, availability status, and consultation fee.

8. **Step 8 — Doctor Detail Modal & Final Confirmation**
   * In-depth modal with doctor's biography, education, hospital credentials, languages spoken, accepted insurances, and clinical expertise.
   * In-Person vs. Telehealth consultation toggle and time slot picker.
   * Doctor assignment confirmation animation.
   * **Final Success Screen** with official Intake Pass, scheduled time, clinic room, print button, and option to start a new intake.

---

## 🎨 Design System

* **Primary Theme**: Modern Healthcare Blue (`#2563EB`, `#1D4ED8`, `#3B82F6`, `#EFF6FF`) replacing generic orange accents.
* **Surface**: Pristine white cards, soft ice-blue accents, and subtle slate borders (`#E2E8F0`).
* **Typography**: Plus Jakarta Sans & Inter for crisp, modern legibility.
* **Micro-Animations**: Smooth step transitions, pulsing radar scan lines, animated SVG checkmarks, and hover micro-interactions.
* **Local Persistence**: State automatically synchronizes with browser `localStorage`.

---

## 🚀 How to Run

1. Simply double-click or open `index.html` in any web browser (Chrome, Edge, Firefox, Safari):
   ```text
   file:///c:/Users/Administrator/Downloads/process form/index.html
   ```
2. Or use any local static server (e.g., Live Server extension in VS Code).
