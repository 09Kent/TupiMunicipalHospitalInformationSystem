<?php
// Doctor/views/prescriptions/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Prescription.php';

$pageTitle = 'Electronic Prescriptions | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'prescriptions';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$rxModel = new Prescription();
$patientModel = new Patient();

// Patient filter & Status filter
$filterStatus = $_GET['status'] ?? '';
$selectedPatientId = (int)($_GET['patient_id'] ?? 0);

// Doctor's patients for selector
$doctorPatients = $patientModel->getDoctorPatients($doctorId, 50, 0);

// Handle POST actions (Create Rx, Cancel Rx)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        Session::setFlash('error', 'Invalid security token.');
    } elseif ($action === 'create_prescription') {
        $pId = (int)($_POST['patient_id'] ?? 0);
        $medicine = trim($_POST['medicine_name'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $frequency = trim($_POST['frequency'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '1 Box');
        $refills = (int)($_POST['refills'] ?? 0);

        if ($pId && !empty($medicine) && !empty($dosage)) {
            if (!$patientModel->hasDoctorAccess($doctorId, $pId)) {
                Session::setFlash('error', 'Access Denied: You are not authorized to issue prescriptions for this patient.');
                header("Location: " . doctor_url("views/prescriptions/index.php"));
                exit;
            }

            $rxId = $rxModel->create([
                'patient_id'    => $pId,
                'doctor_id'     => $doctorId,
                'medicine_name' => $medicine,
                'dosage'        => $dosage,
                'frequency'     => $frequency,
                'duration'      => $duration,
                'instructions'  => $instructions,
                'quantity'      => $quantity,
                'refills'       => $refills,
                'status'        => 'Active'
            ]);

            Session::setFlash('success', "Electronic Prescription ($medicine $dosage) generated successfully.");
            header("Location: " . doctor_url("views/prescriptions/print.php?id=$rxId"));
            exit;
        } else {
            Session::setFlash('error', 'Please fill in all required prescription fields.');
        }
    } elseif ($action === 'cancel_prescription') {
        $cancelId = (int)($_POST['prescription_id'] ?? 0);
        if ($cancelId) {
            $targetRx = $rxModel->findById($cancelId);
            if ($targetRx && (int)$targetRx['DoctorID'] === $doctorId) {
                $rxModel->cancel($cancelId);
                Session::setFlash('success', 'Prescription has been marked as Cancelled.');
            } else {
                Session::setFlash('error', 'Access Denied: You can only cancel prescriptions issued under your license.');
            }
            header("Location: " . doctor_url("views/prescriptions/index.php"));
            exit;
        }
    }
}

require_once __DIR__ . '/../../includes/Paginator.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$allPrescriptions = $rxModel->getAll($doctorId, 200, $filterStatus);
$totalRx = count($allPrescriptions);
$paginator = new Paginator($totalRx, 10, $page, ['status' => $filterStatus]);
$prescriptions = array_slice($allPrescriptions, $paginator->offset, $paginator->recordsPerPage);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">

    <!-- Header & Action Ribbon -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-aos="fade-down">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">Electronic Prescriptions (e-Rx)</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Issue, track, and generate official printable hospital prescriptions with verified licensing.</p>
      </div>

      <!-- Trigger New Rx Modal -->
      <button onclick="document.getElementById('newRxModal').classList.remove('hidden')"
              class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/25 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Create Electronic Prescription</span>
      </button>
    </div>

    <!-- Prescriptions Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" data-aos="fade-up">
      
      <!-- Filter Tabs -->
      <div class="p-4 bg-slate-50/60 border-b border-slate-100 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-2">
          <a href="?status=" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= empty($filterStatus) ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            All Records
          </a>
          <a href="?status=Active" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $filterStatus === 'Active' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            Active
          </a>
          <a href="?status=Completed" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $filterStatus === 'Completed' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            Completed
          </a>
          <a href="?status=Cancelled" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $filterStatus === 'Cancelled' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            Cancelled
          </a>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
              <th class="py-4 px-5">Rx Code</th>
              <th class="py-4 px-5">Patient Name</th>
              <th class="py-4 px-5">Medication & Dosage</th>
              <th class="py-4 px-5">Regimen / Frequency</th>
              <th class="py-4 px-5">Duration</th>
              <th class="py-4 px-5">Issued Date</th>
              <th class="py-4 px-5">Status</th>
              <th class="py-4 px-5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            <?php if (empty($prescriptions)): ?>
              <tr>
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <i data-lucide="file-text" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                  <p class="font-bold text-slate-600 text-sm">No prescriptions recorded yet</p>
                  <p class="text-xs text-slate-400">Click "Create Electronic Prescription" to issue medication orders.</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($prescriptions as $rx): ?>
                <tr class="hover:bg-blue-50/40 transition group">
                  <td class="py-4 px-5 font-mono font-bold text-blue-700">
                    <?= e($rx['PrescriptionCode']) ?>
                  </td>
                  <td class="py-4 px-5">
                    <a href="<?= doctor_url('views/patients/view.php?id=' . $rx['PatientID']) ?>" 
                       class="font-extrabold text-slate-900 group-hover:text-blue-600 transition block">
                      <?= e($rx['PatientFirstName'] . ' ' . $rx['PatientLastName']) ?>
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono"><?= e($rx['PatientCode']) ?></span>
                  </td>
                  <td class="py-4 px-5">
                    <div class="font-extrabold text-slate-800 text-sm"><?= e($rx['MedicineName']) ?></div>
                    <div class="text-slate-500 font-medium text-[11px]"><?= e($rx['Dosage']) ?> • Qty: <?= e($rx['Quantity']) ?></div>
                  </td>
                  <td class="py-4 px-5 text-slate-700 font-medium max-w-xs">
                    <div><?= e($rx['Frequency']) ?></div>
                    <span class="text-[11px] text-slate-400 italic"><?= e($rx['Instructions']) ?></span>
                  </td>
                  <td class="py-4 px-5 font-bold text-slate-700">
                    <?= e($rx['Duration']) ?>
                  </td>
                  <td class="py-4 px-5 text-slate-600 font-medium">
                    <?= format_date($rx['IssuedDate']) ?>
                  </td>
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 text-[10px] rounded-full border <?= get_status_badge($rx['Status']) ?>">
                      <?= e($rx['Status']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5 text-right space-x-1.5">
                    <a href="<?= doctor_url('views/prescriptions/print.php?id=' . $rx['PrescriptionID']) ?>" target="_blank"
                       class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold rounded-xl text-[11px] inline-flex items-center gap-1 shadow-xs transition">
                      <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                      <span>Print Rx</span>
                    </a>
                    <?php if ($rx['Status'] === 'Active'): ?>
                      <form method="POST" action="" class="inline" onsubmit="return confirm('Cancel this active prescription?');">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
                        <input type="hidden" name="action" value="cancel_prescription" />
                        <input type="hidden" name="prescription_id" value="<?= $rx['PrescriptionID'] ?>" />
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Cancel Rx">
                          <i data-lucide="x-circle" class="w-4 h-4"></i>
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

      <!-- 10-Record Pagination Bar -->
      <?= $paginator->render('prescriptions') ?>

    </div>

  </main>

</div>

<!-- Modal: Create New Electronic Prescription -->
<div id="newRxModal" class="<?= isset($_GET['new']) ? '' : 'hidden' ?> fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 animate-in fade-in zoom-in-95">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2.5">
        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
          <i data-lucide="file-plus-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 font-display">New Electronic Prescription (e-Rx)</h3>
          <p class="text-xs text-slate-400">Generate prescription with official hospital metadata</p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('newRxModal').classList.add('hidden')"
              class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form method="POST" action="" class="space-y-4 text-xs">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <input type="hidden" name="action" value="create_prescription" />

      <!-- Patient Select -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Select Patient *</label>
        <select name="patient_id" required 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- Choose Assigned Patient --</option>
          <?php foreach ($doctorPatients as $dp): ?>
            <option value="<?= $dp['PatientID'] ?>" <?= ($selectedPatientId === (int)$dp['PatientID']) ? 'selected' : '' ?>>
              <?= e($dp['FirstName'] . ' ' . $dp['LastName']) ?> (<?= e($dp['PatientCode']) ?>) — <?= e($dp['Age']) ?>y, <?= e($dp['Gender']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Medicine Name & Dosage -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Medication Name *</label>
          <input type="text" name="medicine_name" required placeholder="e.g. Paracetamol, Metoprolol..." 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Dosage / Strength *</label>
          <input type="text" name="dosage" required placeholder="e.g. 500mg, 25mg, 10ml..." 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <!-- Frequency & Duration -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Frequency *</label>
          <input type="text" name="frequency" required placeholder="e.g. Every 6 hours, Twice daily (BID)..." 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Duration *</label>
          <input type="text" name="duration" required placeholder="e.g. 3 Days, 30 Days, PRN..." 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <!-- Quantity & Refills -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Dispensing Quantity</label>
          <input type="text" name="quantity" value="1 Box (20 Tablets)" 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Authorized Refills</label>
          <input type="number" name="refills" value="0" min="0" max="5" 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <!-- Instructions / Patient Advisory -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Instructions / Special Directions</label>
        <textarea name="instructions" rows="2" placeholder="e.g. Take with a full glass of water after meals. Avoid driving." 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">Take after meals as prescribed.</textarea>
      </div>

      <!-- Buttons -->
      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="document.getElementById('newRxModal').classList.add('hidden')"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" 
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Generate e-Prescription</span>
        </button>
      </div>
    </form>

  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
