<?php
// Doctor/views/patients/view.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/ConsultationNote.php';
require_once __DIR__ . '/../../models/Diagnosis.php';
require_once __DIR__ . '/../../models/TreatmentPlan.php';
require_once __DIR__ . '/../../models/Prescription.php';
require_once __DIR__ . '/../../models/LaboratoryRequest.php';
require_once __DIR__ . '/../../models/Referral.php';
require_once __DIR__ . '/../../models/MedicalCertificate.php';
require_once __DIR__ . '/../../models/AllergyRecord.php';
require_once __DIR__ . '/../../includes/anatomy_model.php';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$patientId = (int)($_GET['id'] ?? 0);
$patientModel = new Patient();
$profile = $patientModel->getFullProfile($patientId);

if (!$profile) {
    Session::setFlash('error', 'Patient record not found.');
    header('Location: ' . doctor_url('views/patients/index.php'));
    exit;
}

// Doctor-Patient Isolation Check: Verify this doctor is authorized to view this patient's records
if (!$patientModel->hasDoctorAccess($doctorId, $patientId)) {
    Session::setFlash('error', 'Access Denied: You are not authorized to view this patient\'s confidential medical records.');
    header('Location: ' . doctor_url('views/patients/index.php'));
    exit;
}

$noteModel = new ConsultationNote();
$diagnosisModel = new Diagnosis();
$treatmentModel = new TreatmentPlan();
$rxModel = new Prescription();
$labModel = new LaboratoryRequest();
$refModel = new Referral();
$certModel = new MedicalCertificate();
$allergyModel = new AllergyRecord();

$notes = $noteModel->findByPatient($patientId);
$diagnoses = $diagnosisModel->findByPatient($patientId);
$treatmentPlans = $treatmentModel->findByPatient($patientId);
$prescriptions = $rxModel->findByPatient($patientId);
$labRequests = $labModel->findByPatient($patientId);
$allergies = $allergyModel->findByPatient($patientId);
$certificates = $certModel->findByPatient($patientId);

$pageTitle = 'Patient Profile: ' . $profile['FirstName'] . ' ' . $profile['LastName'] . ' • Tupi Municipal Hospital';
$activeMenu = 'patients';
$currentUser = Session::getCurrentUser();

$bodySystemCode = $profile['Analysis']['SystemCode'] ?? ($profile['Analysis']['LocationCode'] ?? 'cardio');

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">

    <!-- Top Action Breadcrumb Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-aos="fade-down">
      <div class="flex items-center gap-3">
        <a href="<?= doctor_url('views/patients/index.php') ?>" 
           class="p-2.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-2xl text-slate-600 transition shadow-xs">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
          <div class="flex items-center gap-2.5">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">
              <?= e($profile['FirstName'] . ' ' . $profile['MiddleName'] . ' ' . $profile['LastName']) ?>
            </h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-100 text-blue-800">
              <?= e($profile['PatientCode']) ?>
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border <?= get_category_badge($profile['PatientCategory']) ?>">
              <?= e($profile['PatientCategory']) ?>
            </span>
          </div>
          <p class="text-xs text-slate-400 font-medium mt-0.5">
            Registered: <?= format_date($profile['CreatedAt']) ?> • Blood Type: <strong class="text-slate-700"><?= e($profile['BloodType']) ?></strong>
          </p>
        </div>
      </div>

      <!-- Clinical Quick Actions Hub -->
      <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= doctor_url('views/diagnosis/index.php?patient_id=' . $patientId . '&app_id=' . ($profile['Appointment']['AppointmentID'] ?? '') . '&start=1') ?>" 
           class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
          <i data-lucide="play" class="w-4 h-4"></i>
          <span>Start Consultation</span>
        </a>
        <a href="<?= doctor_url('views/prescriptions/index.php?patient_id=' . $patientId . '&new=1') ?>" 
           class="px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
          <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
          <span>Write Rx</span>
        </a>
        <a href="<?= doctor_url('views/laboratory/index.php?patient_id=' . $patientId . '&new=1') ?>" 
           class="px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
          <i data-lucide="flask-conical" class="w-4 h-4 text-indigo-600"></i>
          <span>Order Lab</span>
        </a>
        <a href="<?= doctor_url('views/referrals/index.php?patient_id=' . $patientId . '&new=1') ?>" 
           class="px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
          <i data-lucide="share-2" class="w-4 h-4 text-purple-600"></i>
          <span>Refer</span>
        </a>
        <a href="<?= doctor_url('views/certificates/index.php?patient_id=' . $patientId . '&new=1') ?>" 
           class="px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
          <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
          <span>Cert</span>
        </a>
      </div>
    </div>

    <!-- Main Grid Layout (Reference 2 Pattern) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- Left Column: Personal Information & Registrator Intake Data (7 Cols) -->
      <div class="lg:col-span-7 space-y-6" data-aos="fade-right">
        
        <!-- 1. Patient Demographics & Contact Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-5">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide flex items-center gap-2 font-display">
              <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
              <span>Personal Information</span>
            </h3>
            <span class="text-xs text-slate-400 font-medium">Auto-synced from Registrator</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Full Name</span>
              <p class="font-extrabold text-slate-800 text-sm">
                <?= e($profile['FirstName'] . ' ' . $profile['LastName']) ?>
              </p>
            </div>
            <div>
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Age / Gender</span>
              <p class="font-bold text-slate-800 text-sm">
                <?= e($profile['Age']) ?> Years, <?= e($profile['Gender']) ?>
              </p>
            </div>
            <div>
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Date of Birth</span>
              <p class="font-bold text-slate-800 text-sm">
                <?= format_date($profile['DateOfBirth']) ?>
              </p>
            </div>
            <div>
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Contact Phone</span>
              <p class="font-bold text-slate-800"><?= e($profile['ContactNumber']) ?></p>
            </div>
            <div>
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Email Address</span>
              <p class="font-bold text-slate-800 truncate"><?= e($profile['Email'] ?: '—') ?></p>
            </div>
            <div>
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Civil Status</span>
              <p class="font-bold text-slate-800"><?= e($profile['CivilStatus']) ?></p>
            </div>
            <div class="sm:col-span-3">
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Residential Address</span>
              <p class="font-medium text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100"><?= e($profile['Address']) ?></p>
            </div>
          </div>

          <!-- Emergency Contact Sub-section -->
          <?php if (!empty($profile['EmergencyContact'])): ?>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between bg-blue-50/50 p-3.5 rounded-2xl border border-blue-100">
              <div class="flex items-center gap-3 text-xs">
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                  <i data-lucide="phone-call" class="w-4 h-4"></i>
                </div>
                <div>
                  <p class="font-bold text-slate-900">
                    Emergency Contact: <?= e($profile['EmergencyContact']['ContactName']) ?> 
                    <span class="text-slate-500 font-normal">(<?= e($profile['EmergencyContact']['Relationship']) ?>)</span>
                  </p>
                  <p class="text-blue-700 font-mono font-bold"><?= e($profile['EmergencyContact']['ContactNumber']) ?></p>
                </div>
              </div>
            </div>
          <?php endif; ?>

        </div>

        <!-- 2. Chief Complaint & Symptoms Recorded by Registrator -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-5">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide flex items-center gap-2 font-display">
              <i data-lucide="activity" class="w-4 h-4 text-rose-600"></i>
              <span>Intake Chief Complaint</span>
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
              Severity: <?= $profile['Complaint']['Severity'] ?? 3 ?> / 5
            </span>
          </div>

          <!-- Complaint description exactly as entered -->
          <div class="space-y-3">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Patient Statement (Verbatim)</p>
              <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed">
                "<?= e($profile['Complaint']['ComplaintDescription'] ?? 'Patient reported for comprehensive physical evaluation.') ?>"
              </p>
            </div>

            <!-- Aggravating & Relieving Factors -->
            <?php if (!empty($profile['Complaint']['AggravatingFactors']) || !empty($profile['Complaint']['RelievingFactors'])): ?>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <?php if (!empty($profile['Complaint']['AggravatingFactors'])): ?>
                  <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-100 text-amber-900">
                    <span class="font-extrabold block text-[10px] uppercase tracking-wider text-amber-700 mb-0.5">Aggravating Factors</span>
                    <span><?= e($profile['Complaint']['AggravatingFactors']) ?></span>
                  </div>
                <?php endif; ?>
                <?php if (!empty($profile['Complaint']['RelievingFactors'])): ?>
                  <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-100 text-emerald-900">
                    <span class="font-extrabold block text-[10px] uppercase tracking-wider text-emerald-700 mb-0.5">Relieving Factors</span>
                    <span><?= e($profile['Complaint']['RelievingFactors']) ?></span>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <!-- Selected Symptoms -->
            <div>
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-2">Identified Symptoms</span>
              <div class="flex items-center gap-2 flex-wrap">
                <?php if (!empty($profile['Symptoms'])): ?>
                  <?php foreach ($profile['Symptoms'] as $sym): ?>
                    <span class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center gap-1.5 shadow-xs">
                      <i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i>
                      <span><?= e($sym['SymptomName']) ?></span>
                    </span>
                  <?php endforeach; ?>
                <?php else: ?>
                  <span class="text-xs text-slate-400 italic">No isolated symptoms checked</span>
                <?php endif; ?>
              </div>
            </div>

          </div>
        </div>

        <!-- 3. Pre-Consultation Analysis & Suggested Conditions -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide flex items-center gap-2 font-display">
              <i data-lucide="brain" class="w-4 h-4 text-purple-600"></i>
              <span>Pre-Consultation AI/Triage Suggestions</span>
            </h3>
            <span class="text-xs font-bold text-emerald-600">
              Relevance: <?= $profile['Analysis']['RelevanceLevel'] ?? 95 ?>%
            </span>
          </div>

          <!-- Suggested Conditions Tags -->
          <div class="space-y-2.5">
            <p class="text-xs text-slate-500 font-medium">Algorithmic diagnostic hypotheses from Registrator symptom mapping:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <?php if (!empty($profile['PossibleConditions'])): ?>
                <?php foreach ($profile['PossibleConditions'] as $cond): ?>
                  <div class="p-3 bg-purple-50/50 rounded-2xl border border-purple-100 space-y-1">
                    <div class="font-extrabold text-purple-950 flex items-center gap-1.5">
                      <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                      <span><?= e($cond['ConditionName']) ?></span>
                    </div>
                    <p class="text-[11px] text-slate-600"><?= e($cond['Description']) ?></p>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="text-xs text-slate-400 italic">Standard clinical assessment recommended.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- 4. Medical History & Confirmed Allergies -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide flex items-center gap-2 font-display">
              <i data-lucide="shield-alert" class="w-4 h-4 text-amber-600"></i>
              <span>Medical History & Allergies</span>
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Existing Conditions</span>
              <p class="font-bold text-slate-800"><?= e($profile['MedicalHistory']['ExistingConditions'] ?? 'None disclosed') ?></p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Current Medications</span>
              <p class="font-bold text-slate-800"><?= e($profile['MedicalHistory']['CurrentMedications'] ?? 'None currently') ?></p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
              <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Previous Surgeries / Admissions</span>
              <p class="font-bold text-slate-800"><?= e($profile['MedicalHistory']['PreviousHospitalization'] ?? 'None recorded') ?></p>
            </div>
            <div class="p-3 bg-rose-50/60 rounded-xl border border-rose-100">
              <span class="text-rose-700 font-bold uppercase tracking-wider text-[10px] block mb-1">Known Allergies</span>
              <p class="font-bold text-rose-900"><?= e($profile['MedicalHistory']['Allergies'] ?? 'No known allergies') ?></p>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column: Human Body Model Highlighting Affected Area & Assigned Queue Info (5 Cols) -->
      <div class="lg:col-span-5 space-y-6" data-aos="fade-left">
        
        <!-- Human Body Model Highlighting Affected Region -->
        <?php
          render_anatomy_model(
              $bodySystemCode,
              'Affected Anatomical Region',
              'Classified Body System: ' . ($profile['Analysis']['SystemName'] ?? 'General')
          );
        ?>

        <!-- Assigned Doctor & Queue Information Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide flex items-center gap-2 font-display">
              <i data-lucide="ticket" class="w-4 h-4 text-blue-600"></i>
              <span>Queue & Appointment Status</span>
            </h3>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold border <?= get_status_badge($profile['Appointment']['Status'] ?? 'Scheduled') ?>">
              <?= e($profile['Appointment']['Status'] ?? 'Waiting') ?>
            </span>
          </div>

          <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
            <div>
              <span class="text-[10px] font-extrabold uppercase text-blue-600 tracking-wider">Queue Number</span>
              <p class="text-3xl font-black text-blue-950 font-display">#<?= e($profile['Appointment']['QueueNumber'] ?? '01') ?></p>
            </div>
            <div class="text-right">
              <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Consultation Slot</span>
              <p class="text-base font-bold text-slate-900"><?= e($profile['Appointment']['AppointmentTime'] ?? '09:00 AM') ?></p>
              <p class="text-xs text-slate-500"><?= format_date($profile['Appointment']['AppointmentDate'] ?? date('Y-m-d')) ?></p>
            </div>
          </div>

          <div class="text-xs space-y-2 pt-1 text-slate-600">
            <div class="flex items-center justify-between">
              <span class="text-slate-400">Assigned Doctor:</span>
              <strong class="text-slate-800"><?= e($profile['Appointment']['DoctorFirstName'] ? 'Dr. ' . $profile['Appointment']['DoctorFirstName'] . ' ' . $profile['Appointment']['DoctorLastName'] : 'Dr. Daniel Lewis') ?></strong>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-400">Specialty Clinic:</span>
              <strong class="text-slate-800"><?= e($profile['Appointment']['Specialty'] ?? 'Cardiologist') ?> (<?= e($profile['Appointment']['ClinicRoom'] ?? 'Suite 501') ?>)</strong>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-400">Consultation Mode:</span>
              <strong class="text-slate-800"><?= e($profile['Appointment']['ConsultationType'] ?? 'In-Person Consultation') ?></strong>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-100">
            <a href="<?= doctor_url('views/diagnosis/index.php?patient_id=' . $patientId . '&app_id=' . ($profile['Appointment']['AppointmentID'] ?? '') . '&start=1') ?>" 
               class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-xs shadow-lg shadow-blue-600/25 transition flex items-center justify-center gap-2 group">
              <i data-lucide="play" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
              <span>Launch Clinical Exam & Notes</span>
            </a>
          </div>
        </div>

      </div>

    </div>

  </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
