<?php
// Doctor/views/certificates/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/MedicalCertificate.php';
require_once __DIR__ . '/../../models/AllergyRecord.php';

$pageTitle = 'Medical Certificates & Allergies | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'certificates';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$certModel = new MedicalCertificate();
$allergyModel = new AllergyRecord();
$patientModel = new Patient();

$selectedPatientId = (int)($_GET['patient_id'] ?? 0);
$doctorPatients = $patientModel->getDoctorPatients($doctorId, 50, 0);

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        Session::setFlash('error', 'Invalid security token.');
    } elseif ($action === 'create_certificate') {
        $pId = (int)($_POST['patient_id'] ?? 0);
        $type = $_POST['certificate_type'] ?? 'Fit to Work';
        $diag = trim($_POST['diagnosis'] ?? '');
        $start = $_POST['duration_start'] ?? date('Y-m-d');
        $end = $_POST['duration_end'] ?? date('Y-m-d');
        $remarks = trim($_POST['remarks'] ?? '');

        if ($pId && !empty($diag) && !empty($start) && !empty($end)) {
            if (!$patientModel->hasDoctorAccess($doctorId, $pId)) {
                Session::setFlash('error', 'Access Denied: You are not authorized to issue medical certificates for this patient.');
                header("Location: " . doctor_url("views/certificates/index.php"));
                exit;
            }

            $certId = $certModel->create([
                'patient_id'       => $pId,
                'doctor_id'        => $doctorId,
                'certificate_type' => $type,
                'diagnosis'        => $diag,
                'duration_start'   => $start,
                'duration_end'     => $end,
                'remarks'          => $remarks
            ]);

            Session::setFlash('success', 'Official Medical Certificate issued successfully.');
            header("Location: " . doctor_url("views/certificates/print.php?id=$certId"));
            exit;
        } else {
            Session::setFlash('error', 'Please fill in all required certificate fields.');
        }
    } elseif ($action === 'create_allergy') {
        $pId = (int)($_POST['patient_id'] ?? 0);
        $allergen = trim($_POST['allergen'] ?? '');
        $allgType = $_POST['allergy_type'] ?? 'Drug';
        $severity = $_POST['severity'] ?? 'Moderate';
        $reaction = trim($_POST['reaction'] ?? '');

        if ($pId && !empty($allergen) && !empty($reaction)) {
            if (!$patientModel->hasDoctorAccess($doctorId, $pId)) {
                Session::setFlash('error', 'Access Denied: You are not authorized to register allergy records for this patient.');
                header("Location: " . doctor_url("views/certificates/index.php"));
                exit;
            }

            $allergyModel->create([
                'patient_id'   => $pId,
                'doctor_id'    => $doctorId,
                'allergen'     => $allergen,
                'allergy_type' => $allgType,
                'severity'     => $severity,
                'reaction'     => $reaction,
                'status'       => 'Active'
            ]);

            Session::setFlash('success', "Confirmed allergy '$allergen' permanently registered.");
            header("Location: " . doctor_url("views/certificates/index.php"));
            exit;
        } else {
            Session::setFlash('error', 'Please enter allergen and reaction symptoms.');
        }
    } elseif ($action === 'delete_allergy') {
        $allgId = (int)($_POST['allergy_id'] ?? 0);
        if ($allgId) {
            $allergyModel->delete($allgId);
            Session::setFlash('success', 'Allergy record removed.');
            header("Location: " . doctor_url("views/certificates/index.php"));
            exit;
        }
    }
}
require_once __DIR__ . '/../../includes/Paginator.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$allCertificates = $certModel->getAll($doctorId, 200);
$totalCert = count($allCertificates);
$paginator = new Paginator($totalCert, 10, $page);
$certificates = array_slice($allCertificates, $paginator->offset, $paginator->recordsPerPage);

// Fetch patient allergies isolated to doctor's clinical purview
$allDoctorAllergies = [];
$pdo = Database::getConnection();
$bodySysId = $currentUser['body_system_id'] ?? null;
$sqlAllg = "
    SELECT ar.*, p.FirstName AS PatientFirstName, p.LastName AS PatientLastName, p.PatientCode,
           doc.FirstName AS DoctorFirstName, doc.LastName AS DoctorLastName
    FROM allergy_records ar
    JOIN patients p ON ar.PatientID = p.PatientID
    JOIN doctors doc ON ar.DoctorID = doc.DoctorID
    LEFT JOIN appointments a ON p.PatientID = a.PatientID
    LEFT JOIN patient_queue pq ON a.AppointmentID = pq.AppointmentID
    LEFT JOIN complaints c ON p.PatientID = c.PatientID
    LEFT JOIN complaint_analysis ca ON c.ComplaintID = ca.ComplaintID
    WHERE (ar.DoctorID = :docId1 OR a.DoctorID = :docId2 OR pq.DoctorID = :docId3" . ($bodySysId ? " OR ca.BodySystemID = :bsId" : "") . ")
    GROUP BY ar.AllergyID
    ORDER BY ar.CreatedAt DESC LIMIT 50
";
$paramsAllg = [
    ':docId1' => $doctorId,
    ':docId2' => $doctorId,
    ':docId3' => $doctorId
];
if ($bodySysId) {
    $paramsAllg[':bsId'] = $bodySysId;
}
$stmtAllg = $pdo->prepare($sqlAllg);
$stmtAllg->execute($paramsAllg);
$allDoctorAllergies = $stmtAllg->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-aos="fade-down">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">Medical Certificates & Allergy Registry</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Generate official verified medical certificates and permanently register patient allergies.</p>
      </div>

      <div class="flex items-center gap-3">
        <button onclick="document.getElementById('newAllergyModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold rounded-xl text-xs transition flex items-center gap-2">
          <i data-lucide="shield-alert" class="w-4 h-4"></i>
          <span>Record Confirmed Allergy</span>
        </button>

        <button onclick="document.getElementById('newCertModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/25 transition flex items-center gap-2">
          <i data-lucide="award" class="w-4 h-4"></i>
          <span>Issue Medical Certificate</span>
        </button>
      </div>
    </div>

    <!-- Section 1: Issued Medical Certificates -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" data-aos="fade-up">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h2 class="text-sm font-extrabold text-slate-900 font-display flex items-center gap-2">
            <i data-lucide="file-check" class="w-4 h-4 text-blue-600"></i>
            <span>Issued Medical Certificates (<?= count($certificates) ?>)</span>
          </h2>
          <p class="text-xs text-slate-400 font-medium">Fit to work, school excuses, and medical leave documentation</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
              <th class="py-3 px-5">Certificate Code</th>
              <th class="py-3 px-5">Patient Name</th>
              <th class="py-3 px-5">Certificate Type</th>
              <th class="py-3 px-5">Clinical Diagnosis</th>
              <th class="py-3 px-5">Period & Days</th>
              <th class="py-3 px-5">Issued Date</th>
              <th class="py-3 px-5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($certificates)): ?>
              <tr>
                <td colspan="7" class="py-8 text-center text-slate-400 italic">No medical certificates issued yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($certificates as $cert): ?>
                <tr class="hover:bg-blue-50/40 transition group">
                  <td class="py-4 px-5 font-mono font-bold text-blue-700"><?= e($cert['CertificateCode']) ?></td>
                  <td class="py-4 px-5 font-extrabold text-slate-900">
                    <a href="<?= doctor_url('views/patients/view.php?id=' . $cert['PatientID']) ?>" class="hover:text-blue-600 transition">
                      <?= e($cert['PatientFirstName'] . ' ' . $cert['PatientLastName']) ?>
                    </a>
                    <span class="block text-[11px] text-slate-400 font-mono font-normal"><?= e($cert['PatientCode']) ?></span>
                  </td>
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-800 rounded-xl font-bold text-[11px] border border-blue-200">
                      <?= e($cert['CertificateType']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5 font-bold text-slate-800 max-w-xs line-clamp-1"><?= e($cert['Diagnosis']) ?></td>
                  <td class="py-4 px-5 text-slate-600 font-medium">
                    <div><?= format_date($cert['DurationStart']) ?> - <?= format_date($cert['DurationEnd']) ?></div>
                    <span class="text-[10px] text-slate-400 font-bold"><?= $cert['DaysExcused'] ?> Day(s) excused</span>
                  </td>
                  <td class="py-4 px-5 text-slate-500"><?= format_date($cert['IssueDate']) ?></td>
                  <td class="py-4 px-5 text-right">
                    <a href="<?= doctor_url('views/certificates/print.php?id=' . $cert['CertificateID']) ?>" target="_blank"
                       class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold rounded-xl text-[11px] inline-flex items-center gap-1 shadow-xs transition">
                      <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                      <span>Print Certificate</span>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- 10-Record Pagination Bar -->
      <?= $paginator->render('certificates') ?>

    </div>

    <!-- Section 2: Allergy Records Management -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" data-aos="fade-up">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h2 class="text-sm font-extrabold text-slate-900 font-display flex items-center gap-2">
            <i data-lucide="shield-alert" class="w-4 h-4 text-rose-600"></i>
            <span>Doctor-Confirmed Allergy Records (<?= count($allDoctorAllergies) ?>)</span>
          </h2>
          <p class="text-xs text-slate-400 font-medium">Physician-confirmed drug, food, and latex hypersensitivity warnings</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
              <th class="py-3 px-5">Patient Details</th>
              <th class="py-3 px-5">Allergen Substance</th>
              <th class="py-3 px-5">Category</th>
              <th class="py-3 px-5">Severity Level</th>
              <th class="py-3 px-5">Adverse Reaction</th>
              <th class="py-3 px-5">Confirmed Date</th>
              <th class="py-3 px-5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($allDoctorAllergies)): ?>
              <tr>
                <td colspan="7" class="py-8 text-center text-slate-400 italic">No confirmed allergy records logged yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($allDoctorAllergies as $allg): ?>
                <tr class="hover:bg-rose-50/20 transition">
                  <td class="py-4 px-5">
                    <strong class="text-slate-900"><?= e($allg['PatientFirstName'] . ' ' . $allg['PatientLastName']) ?></strong>
                    <span class="block text-[11px] text-slate-400 font-mono"><?= e($allg['PatientCode']) ?></span>
                  </td>
                  <td class="py-4 px-5 font-black text-rose-900 text-sm"><?= e($allg['Allergen']) ?></td>
                  <td class="py-4 px-5">
                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-[11px]">
                      <?= e($allg['AllergyType']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5">
                    <?php
                      $sevBadge = match($allg['Severity']) {
                          'Severe', 'Life-threatening' => 'bg-rose-100 text-rose-800 border-rose-300 font-extrabold',
                          'Moderate' => 'bg-amber-100 text-amber-800 border-amber-300 font-bold',
                          default => 'bg-slate-100 text-slate-700 border-slate-200 font-semibold'
                      };
                    ?>
                    <span class="px-2.5 py-1 rounded-full text-[10px] border <?= $sevBadge ?>">
                      <?= e($allg['Severity']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5 text-slate-700 max-w-xs"><?= e($allg['Reaction']) ?></td>
                  <td class="py-4 px-5 text-slate-500"><?= format_date($allg['ConfirmedDate']) ?></td>
                  <td class="py-4 px-5 text-right">
                    <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this allergy entry?');">
                      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
                      <input type="hidden" name="action" value="delete_allergy" />
                      <input type="hidden" name="allergy_id" value="<?= $allg['AllergyID'] ?>" />
                      <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Remove Allergy">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

</div>

<!-- Modal: Issue Medical Certificate -->
<div id="newCertModal" class="<?= isset($_GET['new']) ? '' : 'hidden' ?> fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 animate-in fade-in zoom-in-95">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2.5">
        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
          <i data-lucide="award" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 font-display">Generate Medical Certificate</h3>
          <p class="text-xs text-slate-400">Official fit to work or medical leave issuance</p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('newCertModal').classList.add('hidden')"
              class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form method="POST" action="" class="space-y-4 text-xs">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <input type="hidden" name="action" value="create_certificate" />

      <!-- Patient Select -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Select Patient *</label>
        <select name="patient_id" required 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- Choose Assigned Patient --</option>
          <?php foreach ($doctorPatients as $dp): ?>
            <option value="<?= $dp['PatientID'] ?>" <?= ($selectedPatientId === (int)$dp['PatientID']) ? 'selected' : '' ?>>
              <?= e($dp['FirstName'] . ' ' . $dp['LastName']) ?> (<?= e($dp['PatientCode']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Certificate Type -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Certificate Classification *</label>
        <select name="certificate_type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">
          <option value="Fit to Work" selected>Fit to Work (Cleared for employment)</option>
          <option value="Medical Leave">Medical Leave (Rest & Excused Absence)</option>
          <option value="Fit to School">Fit to School (Student attendance clearance)</option>
          <option value="General Medical Certificate">General Medical Certificate</option>
        </select>
      </div>

      <!-- Diagnosis -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Certified Clinical Diagnosis *</label>
        <input type="text" name="diagnosis" required placeholder="e.g. Acute Gastroenteritis, Stable Coronary Artery Disease..." 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <!-- Duration Start & End -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Effective Start Date *</label>
          <input type="date" name="duration_start" value="<?= date('Y-m-d') ?>" required 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Effective End Date *</label>
          <input type="date" name="duration_end" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800" />
        </div>
      </div>

      <!-- Remarks -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Physician Advice / Remarks</label>
        <textarea name="remarks" rows="2" placeholder="e.g. Patient advised bed rest and hydration..." 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">Patient is advised strict rest and avoidance of physical stress.</textarea>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="document.getElementById('newCertModal').classList.add('hidden')"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" 
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Generate Certificate</span>
        </button>
      </div>
    </form>

  </div>
</div>

<!-- Modal: Record Allergy -->
<div id="newAllergyModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 animate-in fade-in zoom-in-95">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2.5">
        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
          <i data-lucide="shield-alert" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 font-display">Record Confirmed Allergy</h3>
          <p class="text-xs text-slate-400">Add permanent allergy alert</p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('newAllergyModal').classList.add('hidden')"
              class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form method="POST" action="" class="space-y-4 text-xs">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <input type="hidden" name="action" value="create_allergy" />

      <!-- Patient Select -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Patient *</label>
        <select name="patient_id" required 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">
          <option value="">-- Choose Patient --</option>
          <?php foreach ($doctorPatients as $dp): ?>
            <option value="<?= $dp['PatientID'] ?>" <?= ($selectedPatientId === (int)$dp['PatientID']) ? 'selected' : '' ?>>
              <?= e($dp['FirstName'] . ' ' . $dp['LastName']) ?> (<?= e($dp['PatientCode']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Allergen -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Allergen Substance *</label>
        <input type="text" name="allergen" required placeholder="e.g. Penicillin, Seafood, Dust, Latex..." 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800" />
      </div>

      <!-- Type & Severity -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Category</label>
          <select name="allergy_type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800">
            <option value="Drug" selected>Drug / Medication</option>
            <option value="Food">Food / Nut / Seafood</option>
            <option value="Environmental">Environmental / Dust</option>
            <option value="Latex">Latex</option>
            <option value="Insect">Insect Sting</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Severity</label>
          <select name="severity" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">
            <option value="Mild">Mild</option>
            <option value="Moderate" selected>Moderate</option>
            <option value="Severe">Severe</option>
            <option value="Life-threatening">Life-threatening</option>
          </select>
        </div>
      </div>

      <!-- Reaction -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Reaction Symptoms *</label>
        <input type="text" name="reaction" required placeholder="e.g. Urticaria, swelling, bronchospasm..." 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800" />
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="document.getElementById('newAllergyModal').classList.add('hidden')"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-xs transition">
          Save Allergy Alert
        </button>
      </div>
    </form>

  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
