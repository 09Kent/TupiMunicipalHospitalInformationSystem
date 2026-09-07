<?php
// Doctor/views/referrals/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Specialty.php';
require_once __DIR__ . '/../../models/Referral.php';

$pageTitle = 'Patient Referrals | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'referrals';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;
$specialtyId = $currentUser['specialty_id'] ?? 2;

$refModel = new Referral();
$patientModel = new Patient();
$specialtyModel = new Specialty();
$doctorModel = new Doctor();

$selectedPatientId = (int)($_GET['patient_id'] ?? 0);
$doctorPatients = $patientModel->getDoctorPatients($doctorId, 50, 0);
$allSpecialties = $specialtyModel->getAll();
$allDoctors = $doctorModel->getAll();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        Session::setFlash('error', 'Invalid security token.');
    } elseif ($action === 'create_referral') {
        $pId = (int)($_POST['patient_id'] ?? 0);
        $targetSpecId = (int)($_POST['target_specialty_id'] ?? 0);
        $targetDocId = !empty($_POST['target_doctor_id']) ? (int)$_POST['target_doctor_id'] : null;
        $reason = trim($_POST['reason'] ?? '');
        $summary = trim($_POST['clinical_summary'] ?? '');
        $priority = $_POST['priority'] ?? 'Routine';

        if ($pId && $targetSpecId && !empty($reason)) {
            if (!$patientModel->hasDoctorAccess($doctorId, $pId)) {
                Session::setFlash('error', 'Access Denied: You are not authorized to create referrals for this patient.');
                header("Location: " . doctor_url("views/referrals/index.php"));
                exit;
            }

            $refId = $refModel->create([
                'patient_id'          => $pId,
                'referring_doctor_id' => $doctorId,
                'target_specialty_id' => $targetSpecId,
                'target_doctor_id'    => $targetDocId,
                'reason'              => $reason,
                'clinical_summary'    => $summary,
                'priority'            => $priority,
                'status'              => 'Pending'
            ]);

            Session::setFlash('success', 'Specialty referral registered and routed successfully.');
            header("Location: " . doctor_url("views/referrals/index.php"));
            exit;
        } else {
            Session::setFlash('error', 'Please fill in all required referral details.');
        }
    } elseif ($action === 'update_status') {
        $refId = (int)($_POST['referral_id'] ?? 0);
        $newStatus = $_POST['status'] ?? 'Accepted';
        $notes = trim($_POST['response_notes'] ?? '');

        if ($refId) {
            $ref = $refModel->findById($refId);
            if ($ref && ((int)$ref['TargetDoctorID'] === $doctorId || ((int)$ref['TargetSpecialtyID'] === $specialtyId && empty($ref['TargetDoctorID'])))) {
                $refModel->updateStatus($refId, $newStatus, $notes);
                Session::setFlash('success', "Referral status updated to $newStatus.");
            } else {
                Session::setFlash('error', 'Access Denied: You can only update referrals directed to you or your specialty.');
            }
            header("Location: " . doctor_url("views/referrals/index.php"));
            exit;
        }
    }
}

$outgoingReferrals = $refModel->getOutgoing($doctorId);
$incomingReferrals = $refModel->getIncoming($doctorId, $specialtyId);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">

    <!-- Header & Action Ribbon -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-aos="fade-down">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">Specialist Patient Referrals</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Cross-departmental collaboration, multi-disciplinary opinions, and referral routing.</p>
      </div>

      <button onclick="document.getElementById('newRefModal').classList.remove('hidden')"
              class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/25 transition flex items-center gap-2">
        <i data-lucide="share-2" class="w-4 h-4"></i>
        <span>Create Specialist Referral</span>
      </button>
    </div>

    <!-- Section 1: Incoming Referrals (Sent to this doctor or specialty) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" data-aos="fade-up">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h2 class="text-sm font-extrabold text-slate-900 font-display flex items-center gap-2">
            <i data-lucide="inbox" class="w-4 h-4 text-purple-600"></i>
            <span>Incoming Referrals to <?= e($currentUser['specialty']) ?> (<?= count($incomingReferrals) ?>)</span>
          </h2>
          <p class="text-xs text-slate-400 font-medium">Patients referred from other medical departments for your evaluation</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
              <th class="py-3 px-5">Referral Code</th>
              <th class="py-3 px-5">Patient Name</th>
              <th class="py-3 px-5">Referring Doctor</th>
              <th class="py-3 px-5">Clinical Reason & Summary</th>
              <th class="py-3 px-5">Priority</th>
              <th class="py-3 px-5">Status</th>
              <th class="py-3 px-5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($incomingReferrals)): ?>
              <tr>
                <td colspan="7" class="py-8 text-center text-slate-400 italic">No incoming referrals at this time.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($incomingReferrals as $inRef): ?>
                <tr class="hover:bg-blue-50/40 transition">
                  <td class="py-4 px-5 font-mono font-bold text-blue-700"><?= e($inRef['ReferralCode']) ?></td>
                  <td class="py-4 px-5">
                    <a href="<?= doctor_url('views/patients/view.php?id=' . $inRef['PatientID']) ?>" 
                       class="font-extrabold text-slate-900 hover:text-blue-600 transition block">
                      <?= e($inRef['PatientFirstName'] . ' ' . $inRef['PatientLastName']) ?>
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono"><?= e($inRef['PatientCode']) ?></span>
                  </td>
                  <td class="py-4 px-5">
                    <div class="font-bold text-slate-800">Dr. <?= e($inRef['RefDocFirstName'] . ' ' . $inRef['RefDocLastName']) ?></div>
                    <span class="text-[11px] text-slate-400"><?= e($inRef['RefDocSpecialty']) ?></span>
                  </td>
                  <td class="py-4 px-5 max-w-sm">
                    <p class="font-bold text-slate-800 line-clamp-1"><?= e($inRef['Reason']) ?></p>
                    <p class="text-[11px] text-slate-500 line-clamp-2"><?= e($inRef['ClinicalSummary'] ?: 'Standard multi-specialty opinion.') ?></p>
                  </td>
                  <td class="py-4 px-5">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= get_priority_badge($inRef['Priority']) ?>">
                      <?= e($inRef['Priority']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 text-[10px] rounded-full border <?= get_status_badge($inRef['Status']) ?>">
                      <?= e($inRef['Status']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5 text-right space-x-1">
                    <?php if ($inRef['Status'] === 'Pending'): ?>
                      <form method="POST" action="" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
                        <input type="hidden" name="action" value="update_status" />
                        <input type="hidden" name="referral_id" value="<?= $inRef['ReferralID'] ?>" />
                        <input type="hidden" name="status" value="Accepted" />
                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[11px] transition">
                          Accept
                        </button>
                      </form>
                    <?php elseif ($inRef['Status'] === 'Accepted'): ?>
                      <form method="POST" action="" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
                        <input type="hidden" name="action" value="update_status" />
                        <input type="hidden" name="referral_id" value="<?= $inRef['ReferralID'] ?>" />
                        <input type="hidden" name="status" value="Completed" />
                        <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-[11px] transition">
                          Complete
                        </button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Section 2: Outgoing Referrals (Sent by this doctor) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" data-aos="fade-up">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h2 class="text-sm font-extrabold text-slate-900 font-display flex items-center gap-2">
            <i data-lucide="send" class="w-4 h-4 text-blue-600"></i>
            <span>Outgoing Referrals (<?= count($outgoingReferrals) ?>)</span>
          </h2>
          <p class="text-xs text-slate-400 font-medium">Referrals you have requested for other specialist evaluations</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
              <th class="py-3 px-5">Referral Code</th>
              <th class="py-3 px-5">Patient Name</th>
              <th class="py-3 px-5">Target Specialty</th>
              <th class="py-3 px-5">Target Doctor</th>
              <th class="py-3 px-5">Reason</th>
              <th class="py-3 px-5">Date</th>
              <th class="py-3 px-5">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($outgoingReferrals)): ?>
              <tr>
                <td colspan="7" class="py-8 text-center text-slate-400 italic">No outgoing referrals recorded yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($outgoingReferrals as $outRef): ?>
                <tr class="hover:bg-slate-50">
                  <td class="py-4 px-5 font-mono font-bold text-blue-700"><?= e($outRef['ReferralCode']) ?></td>
                  <td class="py-4 px-5 font-extrabold text-slate-900">
                    <?= e($outRef['PatientFirstName'] . ' ' . $outRef['PatientLastName']) ?>
                    <span class="block text-[11px] text-slate-400 font-mono font-normal"><?= e($outRef['PatientCode']) ?></span>
                  </td>
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 bg-slate-100 rounded-xl font-bold text-slate-700 text-[11px] border border-slate-200">
                      <?= e($outRef['TargetSpecialtyName']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5 text-slate-700 font-medium">
                    <?= $outRef['TargetDocFirstName'] ? 'Dr. ' . e($outRef['TargetDocFirstName'] . ' ' . $outRef['TargetDocLastName']) : 'Any Specialist' ?>
                  </td>
                  <td class="py-4 px-5 max-w-xs text-slate-700 line-clamp-2"><?= e($outRef['Reason']) ?></td>
                  <td class="py-4 px-5 text-slate-500"><?= format_date($outRef['CreatedAt']) ?></td>
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 text-[10px] rounded-full border <?= get_status_badge($outRef['Status']) ?>">
                      <?= e($outRef['Status']) ?>
                    </span>
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

<!-- Modal: Create Referral -->
<div id="newRefModal" class="<?= isset($_GET['new']) ? '' : 'hidden' ?> fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 animate-in fade-in zoom-in-95">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2.5">
        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
          <i data-lucide="share-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 font-display">Create Patient Referral</h3>
          <p class="text-xs text-slate-400">Refer patient to another clinical specialty</p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('newRefModal').classList.add('hidden')"
              class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form method="POST" action="" class="space-y-4 text-xs">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <input type="hidden" name="action" value="create_referral" />

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

      <!-- Target Specialty -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Target Medical Specialty *</label>
        <select name="target_specialty_id" required 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <?php foreach ($allSpecialties as $spec): ?>
            <?php if ($spec['SpecialtyID'] != $specialtyId): ?>
              <option value="<?= $spec['SpecialtyID'] ?>"><?= e($spec['SpecialtyName']) ?> (<?= e($spec['Description']) ?>)</option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Priority -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Referral Priority *</label>
        <select name="priority" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">
          <option value="Routine" selected>Routine Consultation</option>
          <option value="Urgent">Urgent Specialty Evaluation</option>
          <option value="Emergency">Emergency Transfer / Review</option>
        </select>
      </div>

      <!-- Reason for Referral -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Clinical Reason for Referral *</label>
        <input type="text" name="reason" required placeholder="e.g. Further neurological evaluation for refractory headache..." 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <!-- Clinical Summary -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Clinical Case Summary & Findings</label>
        <textarea name="clinical_summary" rows="3" placeholder="Summarize patient symptoms, current vital signs, and diagnostic questions..." 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="document.getElementById('newRefModal').classList.add('hidden')"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" 
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Dispatch Referral</span>
        </button>
      </div>
    </form>

  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
