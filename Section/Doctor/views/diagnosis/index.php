<?php
// Doctor/views/diagnosis/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Consultation.php';
require_once __DIR__ . '/../../models/ConsultationNote.php';
require_once __DIR__ . '/../../models/Diagnosis.php';
require_once __DIR__ . '/../../models/TreatmentPlan.php';

$pageTitle = 'Diagnosis & Treatment | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'diagnosis';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$patientModel = new Patient();
$consultationModel = new Consultation();
$noteModel = new ConsultationNote();
$diagnosisModel = new Diagnosis();
$treatmentModel = new TreatmentPlan();

// Selected patient ID from query or active patient
$patientId = (int)($_GET['patient_id'] ?? 0);
$appId = (int)($_GET['app_id'] ?? 0);

// Fetch all doctor patients for selector
$patientList = $patientModel->getDoctorPatients($doctorId, 50, 0);

// If patientId passed, verify doctor access
if ($patientId > 0 && !$patientModel->hasDoctorAccess($doctorId, $patientId)) {
    Session::setFlash('error', 'Access Denied: You are not authorized to access this patient\'s consultation chart.');
    header('Location: ' . doctor_url('views/diagnosis/index.php'));
    exit;
}

// Default to first accessible patient if none selected
if ($patientId === 0 && !empty($patientList)) {
    $patientId = (int)$patientList[0]['PatientID'];
    $appId = (int)($patientList[0]['AppointmentID'] ?? 0);
}

// If start parameter passed, verify patient access before starting consultation
if (!empty($_GET['start']) && $appId > 0 && $patientId > 0 && $patientModel->hasDoctorAccess($doctorId, $patientId)) {
    $consultationModel->startConsultation($appId, $doctorId);
}

// Fetch selected patient's full details
$patient = ($patientId && $patientModel->hasDoctorAccess($doctorId, $patientId)) ? $patientModel->getFullProfile($patientId) : null;
$existingNotes = ($patientId && $patientModel->hasDoctorAccess($doctorId, $patientId)) ? $noteModel->findByPatient($patientId) : [];
$existingDiagnoses = ($patientId && $patientModel->hasDoctorAccess($doctorId, $patientId)) ? $diagnosisModel->findByPatient($patientId) : [];
$existingTreatmentPlans = ($patientId && $patientModel->hasDoctorAccess($doctorId, $patientId)) ? $treatmentModel->findByPatient($patientId) : [];

// Handle Form Submissions (Create Note, Diagnosis, Treatment Plan)
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        $errorMessage = 'Security validation token expired. Please try again.';
    } elseif ($patientId <= 0 || !$patientModel->hasDoctorAccess($doctorId, $patientId)) {
        $errorMessage = 'Access Denied: You are not authorized to modify clinical records for this patient.';
    } elseif ($action === 'save_soap_note') {
        $subjective = trim($_POST['subjective'] ?? '');
        $objective = trim($_POST['objective'] ?? '');
        $assessment = trim($_POST['assessment'] ?? '');
        $plan = trim($_POST['plan'] ?? '');
        $clinicalNotes = trim($_POST['clinical_notes'] ?? "$subjective $assessment $plan");

        $vitals = [
            'bp'     => $_POST['vital_bp'] ?? '',
            'hr'     => $_POST['vital_hr'] ?? '',
            'temp'   => $_POST['vital_temp'] ?? '',
            'spo2'   => $_POST['vital_spo2'] ?? '',
            'weight' => $_POST['vital_weight'] ?? ''
        ];

        $noteId = $noteModel->create([
            'patient_id'     => $patientId,
            'doctor_id'      => $doctorId,
            'appointment_id' => $appId ?: ($patient['Appointment']['AppointmentID'] ?? null),
            'subjective'     => $subjective,
            'objective'      => $objective,
            'assessment'     => $assessment,
            'plan'           => $plan,
            'clinical_notes' => $clinicalNotes,
            'vitals'         => $vitals
        ]);

        if ($noteId) {
            Session::setFlash('success', 'Consultation SOAP note recorded successfully.');
            header("Location: " . doctor_url("views/diagnosis/index.php?patient_id=$patientId&app_id=$appId"));
            exit;
        } else {
            $errorMessage = 'Failed to save consultation note.';
        }
    } elseif ($action === 'save_diagnosis') {
        $diagName = trim($_POST['diagnosis_name'] ?? '');
        $icdCode = trim($_POST['icd_code'] ?? '');
        $diagType = $_POST['diag_type'] ?? 'Primary';
        $severity = $_POST['severity'] ?? 'Moderate';
        $notes = trim($_POST['notes'] ?? '');

        if (!empty($diagName)) {
            $diagId = $diagnosisModel->create([
                'patient_id'     => $patientId,
                'doctor_id'      => $doctorId,
                'appointment_id' => $appId ?: ($patient['Appointment']['AppointmentID'] ?? null),
                'diagnosis_name' => $diagName,
                'icd10_code'     => $icdCode,
                'type'           => $diagType,
                'severity'       => $severity,
                'status'         => 'Active',
                'notes'          => $notes
            ]);

            if ($diagId) {
                Session::setFlash('success', "Diagnosis '$diagName' recorded in patient EMR.");
                header("Location: " . doctor_url("views/diagnosis/index.php?patient_id=$patientId&app_id=$appId"));
                exit;
            }
        } else {
            $errorMessage = 'Diagnosis name is required.';
        }
    } elseif ($action === 'save_treatment_plan') {
        $goal = trim($_POST['goal'] ?? '');
        $lifestyle = trim($_POST['lifestyle'] ?? '');
        $medicationPlan = trim($_POST['medication_plan'] ?? '');
        $followUp = trim($_POST['follow_up_sched'] ?? '');
        $followUpDate = $_POST['follow_up_date'] ?? null;
        $notes = trim($_POST['notes'] ?? '');

        if (!empty($goal)) {
            $planId = $treatmentModel->create([
                'patient_id'                => $patientId,
                'doctor_id'                 => $doctorId,
                'appointment_id'            => $appId ?: ($patient['Appointment']['AppointmentID'] ?? null),
                'goal'                      => $goal,
                'lifestyle_recommendations' => $lifestyle,
                'medication_plan'           => $medicationPlan,
                'follow_up_schedule'        => $followUp,
                'follow_up_date'            => $followUpDate,
                'status'                    => 'Active',
                'notes'                     => $notes
            ]);

            if ($planId) {
                Session::setFlash('success', 'Treatment plan created successfully.');
                header("Location: " . doctor_url("views/diagnosis/index.php?patient_id=$patientId&app_id=$appId"));
                exit;
            }
        } else {
            $errorMessage = 'Primary treatment goal is required.';
        }
    } elseif ($action === 'complete_consultation') {
        $summary = trim($_POST['summary_notes'] ?? '');
        if ($appId > 0) {
            $consultationModel->completeConsultation($appId, $doctorId, $summary);
            Session::setFlash('success', 'Consultation marked as Completed. Patient discharged/treated.');
            header("Location: " . doctor_url("views/patients/view.php?id=$patientId"));
            exit;
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">

    <!-- Header & Patient Selector Bar -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card flex flex-col lg:flex-row lg:items-center justify-between gap-4" data-aos="fade-down">
      <div>
        <div class="flex items-center gap-2">
          <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
            <i data-lucide="stethoscope" class="w-5 h-5"></i>
          </span>
          <h1 class="text-xl font-black text-slate-900 tracking-tight font-display">Clinical Diagnosis & Treatment Planning</h1>
        </div>
        <p class="text-xs text-slate-500 font-medium mt-1">
          Review medical histories, formulate differential diagnoses, and prescribe comprehensive treatment regimens.
        </p>
      </div>

      <!-- Quick Switch Patient -->
      <div class="flex items-center gap-3">
        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0">Active Patient:</label>
        <select onchange="window.location.href='?patient_id=' + this.value;" 
                class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <?php foreach ($patientList as $p): ?>
            <option value="<?= $p['PatientID'] ?>" <?= $p['PatientID'] == $patientId ? 'selected' : '' ?>>
              <?= e($p['FirstName'] . ' ' . $p['LastName']) ?> (<?= e($p['PatientCode']) ?>) — Q#<?= e($p['QueueNumber'] ?: '00') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if ($patient): ?>
          <a href="<?= doctor_url('views/patients/view.php?id=' . $patientId) ?>" class="p-2 text-slate-400 hover:text-blue-600 bg-slate-50 hover:bg-blue-50 rounded-xl border border-slate-200 transition" title="Patient Chart">
            <i data-lucide="external-link" class="w-4 h-4"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!$patient): ?>
      <div class="bg-white rounded-3xl p-12 text-center text-slate-400 border border-slate-200 shadow-card">
        <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
        <h3 class="text-base font-bold text-slate-700">No Patient Selected</h3>
        <p class="text-xs text-slate-400 mt-1">Select a patient from your assigned queue above to start recording notes.</p>
      </div>
    <?php else: ?>

      <!-- Patient Overview Banner -->
      <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-3xl p-6 shadow-xl border border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4" data-aos="fade-up">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-blue-600 font-black text-white text-base flex items-center justify-center shadow-lg shrink-0 font-display">
            <?= strtoupper(substr($patient['FirstName'], 0, 1) . substr($patient['LastName'], 0, 1)) ?>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h2 class="text-lg font-black tracking-tight font-display"><?= e($patient['FirstName'] . ' ' . $patient['LastName']) ?></h2>
              <span class="px-2 py-0.5 rounded-full text-[11px] font-mono bg-white/10 text-blue-300 font-bold"><?= e($patient['PatientCode']) ?></span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30">
                <?= e($patient['Age']) ?>y, <?= e($patient['Gender']) ?> • Blood: <?= e($patient['BloodType']) ?>
              </span>
            </div>
            <p class="text-xs text-slate-300 mt-1">
              <strong>Complaint:</strong> <?= e($patient['Complaint']['ComplaintDescription'] ?? 'No complaint recorded') ?>
            </p>
          </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
          <form method="POST" action="" onsubmit="return confirm('Complete and archive this consultation?');">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
            <input type="hidden" name="action" value="complete_consultation" />
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-1.5">
              <i data-lucide="check-circle-2" class="w-4 h-4"></i>
              <span>Complete Consultation</span>
            </button>
          </form>
        </div>
      </div>

      <!-- 3 Columns Section: 1. Medical History Review | 2. Consultation Note (SOAP) | 3. Diagnosis & Treatment Plans -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Column 1: Medical History Review (3 Cols) -->
        <div class="lg:col-span-4 space-y-6" data-aos="fade-right">
          
          <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-blue-600"></i>
                <span>Medical History Review</span>
              </h3>
            </div>

            <div class="space-y-3 text-xs">
              <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">Existing Conditions</span>
                <p class="font-bold text-slate-800"><?= e($patient['MedicalHistory']['ExistingConditions'] ?? 'None disclosed') ?></p>
              </div>

              <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">Current Medications</span>
                <p class="font-bold text-slate-800"><?= e($patient['MedicalHistory']['CurrentMedications'] ?? 'None recorded') ?></p>
              </div>

              <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">Past Surgeries</span>
                <p class="font-bold text-slate-800"><?= e($patient['MedicalHistory']['PreviousHospitalization'] ?? 'None recorded') ?></p>
              </div>

              <div class="p-3 bg-rose-50/60 rounded-xl border border-rose-100">
                <span class="text-[10px] font-extrabold uppercase text-rose-700 block mb-1">Known Allergies</span>
                <p class="font-bold text-rose-900"><?= e($patient['MedicalHistory']['Allergies'] ?? 'No known drug allergies') ?></p>
              </div>
            </div>
          </div>

          <!-- Existing Diagnoses History Card -->
          <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
              <i data-lucide="file-check" class="w-4 h-4 text-emerald-600"></i>
              <span>Active Diagnoses (<?= count($existingDiagnoses) ?>)</span>
            </h3>

            <div class="space-y-2.5">
              <?php if (empty($existingDiagnoses)): ?>
                <p class="text-xs text-slate-400 italic">No formal diagnoses recorded yet.</p>
              <?php else: ?>
                <?php foreach ($existingDiagnoses as $diag): ?>
                  <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-1">
                    <div class="flex items-center justify-between">
                      <strong class="text-slate-900"><?= e($diag['DiagnosisName']) ?></strong>
                      <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-blue-100 text-blue-800 font-mono"><?= e($diag['ICD10Code'] ?: 'ICD-10') ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] text-slate-400">
                      <span>Type: <?= e($diag['Type']) ?></span>
                      <span>•</span>
                      <span>Severity: <?= e($diag['Severity']) ?></span>
                      <span>•</span>
                      <span><?= format_date($diag['DiagnosedDate']) ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

        </div>

        <!-- Column 2 & 3: Interactive Note Management & Diagnosis / Treatment Plan Builder (8 Cols) -->
        <div class="lg:col-span-8 space-y-6" data-aos="fade-left">
          
          <!-- SOAP Consultation Note Form -->
          <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <div>
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide flex items-center gap-2 font-display">
                  <i data-lucide="edit-3" class="w-4 h-4 text-blue-600"></i>
                  <span>Consultation Note (SOAP Format)</span>
                </h3>
                <p class="text-xs text-slate-400">Structured electronic clinical observation and diagnostic evaluation</p>
              </div>
            </div>

            <form method="POST" action="" class="space-y-4">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
              <input type="hidden" name="action" value="save_soap_note" />

              <!-- Vital Signs Triage Ribbon -->
              <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/60 space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Vital Signs Capture</span>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                  <div>
                    <label class="text-[10px] font-bold text-slate-600 block mb-1">Blood Pressure</label>
                    <input type="text" name="vital_bp" placeholder="120/80 mmHg" value="120/80" 
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800" />
                  </div>
                  <div>
                    <label class="text-[10px] font-bold text-slate-600 block mb-1">Heart Rate</label>
                    <input type="text" name="vital_hr" placeholder="72 bpm" value="74 bpm" 
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800" />
                  </div>
                  <div>
                    <label class="text-[10px] font-bold text-slate-600 block mb-1">Temperature</label>
                    <input type="text" name="vital_temp" placeholder="36.8 °C" value="36.7 °C" 
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800" />
                  </div>
                  <div>
                    <label class="text-[10px] font-bold text-slate-600 block mb-1">SpO2 Oxygen</label>
                    <input type="text" name="vital_spo2" placeholder="99%" value="98%" 
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800" />
                  </div>
                  <div>
                    <label class="text-[10px] font-bold text-slate-600 block mb-1">Body Weight</label>
                    <input type="text" name="vital_weight" placeholder="70 kg" value="72 kg" 
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800" />
                  </div>
                </div>
              </div>

              <!-- S: Subjective -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                  <span>Subjective (Patient's History of Present Illness)</span>
                  <span class="text-[10px] text-slate-400">Chief complaint & symptom progression</span>
                </label>
                <textarea name="subjective" rows="2" required 
                          placeholder="e.g. Patient reports sharp retrosternal discomfort after heavy meals with nausea..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"><?= e($patient['Complaint']['ComplaintDescription'] ?? '') ?></textarea>
              </div>

              <!-- O: Objective -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                  <span>Objective (Physical Examination & Clinical Findings)</span>
                  <span class="text-[10px] text-slate-400">Auscultation, palpation, signs</span>
                </label>
                <textarea name="objective" rows="2" 
                          placeholder="e.g. Normal S1/S2 heart sounds, abdomen soft without guarding, epigastric tenderness present..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"></textarea>
              </div>

              <!-- A: Assessment -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                  <span>Assessment (Clinical Differential & Impression)</span>
                  <span class="text-[10px] text-slate-400">Working diagnosis</span>
                </label>
                <textarea name="assessment" rows="2" 
                          placeholder="e.g. Gastritis secondary to mucosal hyperacidity vs Functional Dyspepsia..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"></textarea>
              </div>

              <!-- P: Plan -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                  <span>Plan (Next Steps, Medications, Diagnostics)</span>
                  <span class="text-[10px] text-slate-400">Follow-up timeline</span>
                </label>
                <textarea name="plan" rows="2" 
                          placeholder="e.g. Prescribe Proton Pump Inhibitor 40mg daily, order H. Pylori antibody test, review in 2 weeks..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"></textarea>
              </div>

              <div class="pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
                  <i data-lucide="save" class="w-4 h-4"></i>
                  <span>Save SOAP Note to MySQL</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Add Diagnosis & Add Treatment Plan Forms Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            
            <!-- Card A: Add Diagnosis Form -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3 font-display">
                <i data-lucide="plus-circle" class="w-4 h-4 text-blue-600"></i>
                <span>Add Formal Diagnosis</span>
              </h3>

              <form method="POST" action="" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
                <input type="hidden" name="action" value="save_diagnosis" />

                <div>
                  <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Diagnosis Name</label>
                  <input type="text" name="diagnosis_name" required placeholder="e.g. Stable Angina, Migraine..." 
                         class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                  <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">ICD-10 Code</label>
                    <input type="text" name="icd_code" placeholder="e.g. I20.9" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800" />
                  </div>
                  <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Type</label>
                    <select name="diag_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                      <option value="Primary">Primary</option>
                      <option value="Secondary">Secondary</option>
                      <option value="Differential">Differential</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Severity</label>
                  <select name="severity" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                    <option value="Mild">Mild</option>
                    <option value="Moderate" selected>Moderate</option>
                    <option value="Severe">Severe</option>
                    <option value="Critical">Critical</option>
                  </select>
                </div>

                <div>
                  <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Clinical Notes</label>
                  <textarea name="notes" rows="2" placeholder="Diagnostic criteria or rationale..." 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                  <i data-lucide="check" class="w-3.5 h-3.5"></i>
                  <span>Record Diagnosis</span>
                </button>
              </form>
            </div>

            <!-- Card B: Add Treatment Plan Form -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3 font-display">
                <i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i>
                <span>Add Treatment Plan</span>
              </h3>

              <form method="POST" action="" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
                <input type="hidden" name="action" value="save_treatment_plan" />

                <div>
                  <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Treatment Goal</label>
                  <input type="text" name="goal" required placeholder="e.g. Alleviate symptoms, stabilize resting BP..." 
                         class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                  <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Lifestyle & Diet Recommendations</label>
                  <input type="text" name="lifestyle" placeholder="e.g. Low sodium diet, 30 min daily walking..." 
                         class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" />
                </div>

                <div>
                  <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Medication Regimen Overview</label>
                  <input type="text" name="medication_plan" placeholder="e.g. Daily oral antihypertensives with morning meals..." 
                         class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                  <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Follow-up Interval</label>
                    <input type="text" name="follow_up_sched" placeholder="e.g. 2 Weeks" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" />
                  </div>
                  <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Target Date</label>
                    <input type="date" name="follow_up_date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800" />
                  </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                  <i data-lucide="check" class="w-3.5 h-3.5"></i>
                  <span>Establish Treatment Plan</span>
                </button>
              </form>
            </div>

          </div>

        </div>

      </div>

    <?php endif; ?>

  </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
