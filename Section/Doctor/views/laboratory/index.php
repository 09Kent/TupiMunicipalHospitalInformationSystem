<?php
// Doctor/views/laboratory/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/LaboratoryRequest.php';
require_once __DIR__ . '/../../models/LaboratoryResult.php';

$pageTitle = 'Laboratory Requests & Diagnostics | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'laboratory';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$labModel = new LaboratoryRequest();
$resultModel = new LaboratoryResult();
$patientModel = new Patient();

$filterStatus = $_GET['status'] ?? '';
$selectedPatientId = (int)($_GET['patient_id'] ?? 0);
$viewRequestId = (int)($_GET['view_req'] ?? 0);

$doctorPatients = $patientModel->getDoctorPatients($doctorId, 50, 0);

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        Session::setFlash('error', 'Invalid security token.');
    } elseif ($action === 'create_lab_request') {
        $pId = (int)($_POST['patient_id'] ?? 0);
        $testType = trim($_POST['test_type'] ?? '');
        $priority = $_POST['priority'] ?? 'Routine';
        $notes = trim($_POST['clinical_notes'] ?? '');

        if ($pId && !empty($testType)) {
            if (!$patientModel->hasDoctorAccess($doctorId, $pId)) {
                Session::setFlash('error', 'Access Denied: You are not authorized to order laboratory tests for this patient.');
                header("Location: " . doctor_url("views/laboratory/index.php"));
                exit;
            }

            $reqId = $labModel->create([
                'patient_id'     => $pId,
                'doctor_id'      => $doctorId,
                'test_type'      => $testType,
                'priority'       => $priority,
                'clinical_notes' => $notes,
                'status'         => 'Pending'
            ]);

            Session::setFlash('success', "Laboratory order for $testType registered successfully.");
            header("Location: " . doctor_url("views/laboratory/index.php"));
            exit;
        } else {
            Session::setFlash('error', 'Please select a patient and diagnostic test type.');
        }
    } elseif ($action === 'add_lab_result') {
        $reqId = (int)($_POST['request_id'] ?? 0);
        $pId = (int)($_POST['patient_id'] ?? 0);
        $testName = trim($_POST['test_name'] ?? '');
        $resultVal = trim($_POST['result_value'] ?? '');
        $normalRange = trim($_POST['normal_range'] ?? '');
        $units = trim($_POST['units'] ?? '');
        $interpretation = $_POST['interpretation'] ?? 'Normal';
        $notes = trim($_POST['notes'] ?? '');

        if ($reqId && $pId && !empty($testName) && !empty($resultVal)) {
            $req = $labModel->findById($reqId);
            if (!$req || ((int)$req['DoctorID'] !== $doctorId && !$patientModel->hasDoctorAccess($doctorId, $pId))) {
                Session::setFlash('error', 'Access Denied: You are not authorized to record results for this laboratory request.');
                header("Location: " . doctor_url("views/laboratory/index.php"));
                exit;
            }

            $resultModel->create([
                'request_id'     => $reqId,
                'patient_id'     => $pId,
                'doctor_id'      => $doctorId,
                'test_name'      => $testName,
                'result_value'   => $resultVal,
                'normal_range'   => $normalRange,
                'units'          => $units,
                'interpretation' => $interpretation,
                'notes'          => $notes
            ]);

            Session::setFlash('success', "Diagnostic result recorded for $testName.");
            header("Location: " . doctor_url("views/laboratory/index.php?view_req=$reqId"));
            exit;
        }
    } elseif ($action === 'update_status') {
        $reqId = (int)($_POST['request_id'] ?? 0);
        $newStatus = $_POST['status'] ?? 'Pending';
        if ($reqId) {
            $req = $labModel->findById($reqId);
            if ($req && (int)$req['DoctorID'] === $doctorId) {
                $labModel->updateStatus($reqId, $newStatus);
                Session::setFlash('success', "Laboratory request status updated to $newStatus.");
            } else {
                Session::setFlash('error', 'Access Denied: You can only update status for your own laboratory orders.');
            }
            header("Location: " . doctor_url("views/laboratory/index.php"));
            exit;
        }
    }
}
require_once __DIR__ . '/../../includes/Paginator.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$allRequests = $labModel->getAll($doctorId, 200, $filterStatus);
$totalReq = count($allRequests);
$paginator = new Paginator($totalReq, 10, $page, ['status' => $filterStatus]);
$requests = array_slice($allRequests, $paginator->offset, $paginator->recordsPerPage);

// If viewing a specific request's results
$activeRequest = $viewRequestId ? $labModel->findById($viewRequestId) : null;
if ($activeRequest && (int)$activeRequest['DoctorID'] !== $doctorId && !$patientModel->hasDoctorAccess($doctorId, (int)$activeRequest['PatientID'])) {
    $activeRequest = null;
    $viewRequestId = 0;
}
$activeResults = ($viewRequestId && $activeRequest) ? $resultModel->findByRequest($viewRequestId) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">

    <!-- Page Title & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-aos="fade-down">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">Laboratory & Diagnostic Requests</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Order diagnostic investigations, monitor lab specimens, and evaluate clinical result ranges.</p>
      </div>

      <button onclick="document.getElementById('newLabModal').classList.remove('hidden')"
              class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/25 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Create Laboratory Request</span>
      </button>
    </div>

    <!-- Active Results Viewer Box (If viewing request) -->
    <?php if ($activeRequest): ?>
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-200 shadow-xl space-y-6 animate-in fade-in" data-aos="fade-up">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
              <i data-lucide="flask-conical" class="w-6 h-6"></i>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base font-extrabold text-slate-900 font-display"><?= e($activeRequest['TestType']) ?></h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-100 text-blue-800"><?= e($activeRequest['RequestCode']) ?></span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold border <?= get_priority_badge($activeRequest['Priority']) ?>">
                  <?= e($activeRequest['Priority']) ?>
                </span>
              </div>
              <p class="text-xs text-slate-400 mt-0.5">
                Patient: <strong class="text-slate-700"><?= e($activeRequest['PatientFirstName'] . ' ' . $activeRequest['PatientLastName']) ?></strong> (<?= e($activeRequest['PatientCode']) ?>) • Ordered: <?= format_date($activeRequest['RequestedDate']) ?>
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button onclick="document.getElementById('addResultModal').classList.remove('hidden')" 
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
              <span>Add Result Value</span>
            </button>
            <a href="<?= doctor_url('views/laboratory/index.php') ?>" class="p-2 text-slate-400 hover:text-slate-600 bg-slate-100 rounded-xl transition">
              <i data-lucide="x" class="w-4 h-4"></i>
            </a>
          </div>
        </div>

        <!-- Results Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                <th class="py-3 px-4">Test Parameter</th>
                <th class="py-3 px-4">Recorded Value</th>
                <th class="py-3 px-4">Normal Reference Range</th>
                <th class="py-3 px-4">Units</th>
                <th class="py-3 px-4">Interpretation</th>
                <th class="py-3 px-4">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php if (empty($activeResults)): ?>
                <tr>
                  <td colspan="6" class="py-6 text-center text-slate-400 italic">
                    No diagnostic values uploaded for this request yet. Click "Add Result Value" to log specimen metrics.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($activeResults as $res): ?>
                  <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4 font-bold text-slate-900"><?= e($res['TestName']) ?></td>
                    <td class="py-3 px-4 font-extrabold font-mono text-slate-800 text-sm"><?= e($res['ResultValue']) ?></td>
                    <td class="py-3 px-4 text-slate-500 font-mono"><?= e($res['NormalRange']) ?></td>
                    <td class="py-3 px-4 text-slate-500"><?= e($res['Units'] ?: '—') ?></td>
                    <td class="py-3 px-4">
                      <?php
                        $interpClass = match($res['Interpretation']) {
                            'High', 'Critical' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'Low' => 'bg-amber-100 text-amber-800 border-amber-200',
                            default => 'bg-emerald-100 text-emerald-800 border-emerald-200'
                        };
                      ?>
                      <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $interpClass ?>">
                        <?= e($res['Interpretation']) ?>
                      </span>
                    </td>
                    <td class="py-3 px-4 text-slate-500"><?= format_date($res['ResultDate']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <!-- Lab Requests Directory Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" data-aos="fade-up">
      
      <!-- Filter Tabs -->
      <div class="p-4 bg-slate-50/60 border-b border-slate-100 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-2">
          <a href="?status=" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= empty($filterStatus) ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            All Orders
          </a>
          <a href="?status=Pending" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $filterStatus === 'Pending' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            Pending
          </a>
          <a href="?status=In Progress" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $filterStatus === 'In Progress' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            In Progress
          </a>
          <a href="?status=Completed" 
             class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $filterStatus === 'Completed' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
            Completed
          </a>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
              <th class="py-4 px-5">Order Code</th>
              <th class="py-4 px-5">Patient Details</th>
              <th class="py-4 px-5">Diagnostic Test Type</th>
              <th class="py-4 px-5">Priority</th>
              <th class="py-4 px-5">Order Date</th>
              <th class="py-4 px-5">Status</th>
              <th class="py-4 px-5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            <?php if (empty($requests)): ?>
              <tr>
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <i data-lucide="flask-conical" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                  <p class="font-bold text-slate-600 text-sm">No laboratory requests found</p>
                  <p class="text-xs text-slate-400">Click "Create Laboratory Request" to order tests for assigned patients.</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($requests as $req): ?>
                <tr class="hover:bg-blue-50/40 transition group">
                  <td class="py-4 px-5 font-mono font-bold text-blue-700">
                    <?= e($req['RequestCode']) ?>
                  </td>
                  <td class="py-4 px-5">
                    <a href="<?= doctor_url('views/patients/view.php?id=' . $req['PatientID']) ?>" 
                       class="font-extrabold text-slate-900 group-hover:text-blue-600 transition block">
                      <?= e($req['PatientFirstName'] . ' ' . $req['PatientLastName']) ?>
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono"><?= e($req['PatientCode']) ?> • <?= e($req['Age']) ?>y, <?= e($req['Gender']) ?></span>
                  </td>
                  <td class="py-4 px-5">
                    <div class="font-extrabold text-slate-800 text-sm"><?= e($req['TestType']) ?></div>
                    <span class="text-[11px] text-slate-400 line-clamp-1"><?= e($req['ClinicalNotes'] ?: 'Routine diagnostic audit') ?></span>
                  </td>
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 text-[10px] rounded-full border <?= get_priority_badge($req['Priority']) ?>">
                      <?= e($req['Priority']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5 text-slate-600 font-medium">
                    <?= format_date($req['RequestedDate']) ?>
                  </td>
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 text-[10px] rounded-full border <?= get_status_badge($req['Status']) ?>">
                      <?= e($req['Status']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-5 text-right space-x-1.5">
                    <a href="?view_req=<?= $req['RequestID'] ?>" 
                       class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold rounded-xl text-[11px] inline-flex items-center gap-1 shadow-xs transition">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                      <span>Results (<?= $req['ResultsCount'] ?>)</span>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- 10-Record Pagination Bar -->
      <?= $paginator->render('orders') ?>

    </div>

  </main>

</div>

<!-- Modal: Create Lab Request -->
<div id="newLabModal" class="<?= isset($_GET['new']) ? '' : 'hidden' ?> fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 animate-in fade-in zoom-in-95">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2.5">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
          <i data-lucide="flask-conical" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 font-display">New Laboratory Investigation</h3>
          <p class="text-xs text-slate-400">Order blood chemistry, imaging, and diagnostic panels</p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('newLabModal').classList.add('hidden')"
              class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form method="POST" action="" class="space-y-4 text-xs">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <input type="hidden" name="action" value="create_lab_request" />

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

      <!-- Test Type Quick Selector -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Diagnostic Test Type *</label>
        <select name="test_type" required 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="12-Lead Electrocardiogram (ECG)">12-Lead Electrocardiogram (ECG)</option>
          <option value="Complete Blood Count (CBC)">Complete Blood Count (CBC)</option>
          <option value="Blood Chemistry & Electrolytes">Blood Chemistry & Electrolytes</option>
          <option value="Fasting Lipid Profile">Fasting Lipid Profile</option>
          <option value="Brain MRI (without contrast)">Brain MRI (without contrast)</option>
          <option value="Chest X-Ray (PA View)">Chest X-Ray (PA View)</option>
          <option value="Abdominal Ultrasound">Abdominal Ultrasound</option>
          <option value="Routine Urinalysis">Routine Urinalysis</option>
          <option value="Echocardiogram (2D Echo)">Echocardiogram (2D Echo)</option>
          <option value="Thyroid Function Panel (TSH, FT3, FT4)">Thyroid Function Panel (TSH, FT3, FT4)</option>
          <option value="CT Scan (Head/Torso)">CT Scan (Head/Torso)</option>
        </select>
      </div>

      <!-- Priority -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Priority / Urgency *</label>
        <select name="priority" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">
          <option value="Routine" selected>Routine (Standard 24-48h)</option>
          <option value="Urgent">Urgent (Within 4-6h)</option>
          <option value="STAT">STAT (Immediate Emergency Triage)</option>
        </select>
      </div>

      <!-- Clinical Notes -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Clinical Indication & Specimen Notes</label>
        <textarea name="clinical_notes" rows="2" placeholder="e.g. Rule out ischemic ST changes, evaluate sinus tachycardia..." 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="document.getElementById('newLabModal').classList.add('hidden')"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" 
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Issue Lab Request</span>
        </button>
      </div>
    </form>

  </div>
</div>

<!-- Modal: Add Diagnostic Result Value -->
<?php if ($activeRequest): ?>
<div id="addResultModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 animate-in fade-in zoom-in-95">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 font-display">Record Diagnostic Result</h3>
          <p class="text-xs text-slate-400"><?= e($activeRequest['TestType']) ?></p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('addResultModal').classList.add('hidden')"
              class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form method="POST" action="" class="space-y-3 text-xs">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <input type="hidden" name="action" value="add_lab_result" />
      <input type="hidden" name="request_id" value="<?= $activeRequest['RequestID'] ?>" />
      <input type="hidden" name="patient_id" value="<?= $activeRequest['PatientID'] ?>" />

      <div>
        <label class="block font-bold text-slate-700 mb-1">Parameter / Test Name *</label>
        <input type="text" name="test_name" value="<?= e($activeRequest['TestType']) ?>" required 
               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800" />
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Result Value *</label>
          <input type="text" name="result_value" required placeholder="e.g. 14.8, Normal Sinus..." 
                 class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Units</label>
          <input type="text" name="units" placeholder="e.g. g/dL, mg/dL, bpm..." 
                 class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Normal Reference Range</label>
          <input type="text" name="normal_range" placeholder="e.g. 13.5 - 17.5" value="Within normal limits" 
                 class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Interpretation *</label>
          <select name="interpretation" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">
            <option value="Normal" selected>Normal</option>
            <option value="High">High</option>
            <option value="Low">Low</option>
            <option value="Critical">Critical</option>
            <option value="Abnormal">Abnormal</option>
          </select>
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="document.getElementById('addResultModal').classList.add('hidden')"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition">
          Save Result
        </button>
      </div>
    </form>

  </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
