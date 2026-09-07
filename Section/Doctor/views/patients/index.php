<?php
// Doctor/views/patients/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Patient.php';

$pageTitle = 'Patients Directory | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'patients';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;
$specialty = $currentUser['specialty'] ?? 'Cardiologist';

$patientModel = new Patient();

// Search & Filter parameters
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$date = trim($_GET['date'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$filters = [
    'search' => $search,
    'status' => $status,
    'date'   => $date
];

$patients = $patientModel->getDoctorPatients($doctorId, $limit, $offset, $filters);
$totalPatients = $patientModel->countDoctorPatients($doctorId, $filters);
$totalPages = max(1, ceil($totalPatients / $limit));

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">

    <!-- Page Header & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-aos="fade-down">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">Specialty Patient Roster</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">
          Showing patients assigned to <strong class="text-blue-600 font-bold"><?= e($specialty) ?></strong> via intake triage
        </p>
      </div>
      <div class="flex items-center gap-3">
        <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-xs font-extrabold border border-blue-200 shadow-xs">
          <?= $totalPatients ?> Total Case<?= $totalPatients === 1 ? '' : 's' ?>
        </span>
      </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card" data-aos="fade-up">
      <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
        
        <!-- Search input -->
        <div class="lg:col-span-5 relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <i data-lucide="search" class="w-4 h-4"></i>
          </div>
          <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search by name, patient code, complaint..." 
                 class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
        </div>

        <!-- Status Filter -->
        <div class="lg:col-span-3">
          <select name="status" 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            <option value="">All Consultation Statuses</option>
            <option value="Waiting" <?= $status === 'Waiting' ? 'selected' : '' ?>>Waiting</option>
            <option value="In Consultation" <?= $status === 'In Consultation' ? 'selected' : '' ?>>In Consultation</option>
            <option value="Scheduled" <?= $status === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
            <option value="Confirmed" <?= $status === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
          </select>
        </div>

        <!-- Date Filter -->
        <div class="lg:col-span-2">
          <input type="date" name="date" value="<?= e($date) ?>" 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
        </div>

        <!-- Submit & Reset -->
        <div class="lg:col-span-2 flex items-center gap-2">
          <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-1.5">
            <i data-lucide="filter" class="w-3.5 h-3.5"></i>
            <span>Filter</span>
          </button>
          <?php if (!empty($search) || !empty($status) || !empty($date)): ?>
            <a href="<?= doctor_url('views/patients/index.php') ?>" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Clear Filters">
              <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
            </a>
          <?php endif; ?>
        </div>

      </form>
    </div>

    <!-- Patients Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" data-aos="fade-up">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
              <th class="py-4 px-5">Queue</th>
              <th class="py-4 px-5">Patient Details</th>
              <th class="py-4 px-5">Chief Complaint</th>
              <th class="py-4 px-5">Body System</th>
              <th class="py-4 px-5">Appointment</th>
              <th class="py-4 px-5">Status</th>
              <th class="py-4 px-5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            <?php if (empty($patients)): ?>
              <tr>
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                  <p class="font-bold text-slate-600 text-sm">No patients found matching your criteria</p>
                  <p class="text-xs text-slate-400">Try clearing filters or search query.</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($patients as $p): ?>
                <tr class="hover:bg-blue-50/40 transition group">
                  
                  <!-- Queue # -->
                  <td class="py-4 px-5">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl font-black text-xs font-display <?= !empty($p['QueueNumber']) ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' ?>">
                      <?= e($p['QueueNumber'] ?: '—') ?>
                    </span>
                  </td>

                  <!-- Patient Name & Code -->
                  <td class="py-4 px-5">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                        <?= strtoupper(substr($p['FirstName'], 0, 1) . substr($p['LastName'], 0, 1)) ?>
                      </div>
                      <div>
                        <a href="<?= doctor_url('views/patients/view.php?id=' . $p['PatientID']) ?>" 
                           class="font-extrabold text-slate-900 group-hover:text-blue-600 transition">
                          <?= e($p['FirstName'] . ' ' . $p['LastName']) ?>
                        </a>
                        <p class="text-[11px] text-slate-400 font-mono mt-0.5"><?= e($p['PatientCode']) ?> • <?= e($p['Age']) ?>y, <?= e($p['Gender']) ?></p>
                      </div>
                    </div>
                  </td>

                  <!-- Complaint -->
                  <td class="py-4 px-5 max-w-xs">
                    <p class="text-slate-700 font-medium line-clamp-2">
                      <?= e($p['ComplaintDescription'] ?: 'General health evaluation') ?>
                    </p>
                    <?php if (!empty($p['LocationName'])): ?>
                      <span class="text-[10px] text-slate-400 font-medium">Region: <?= e($p['LocationName']) ?></span>
                    <?php endif; ?>
                  </td>

                  <!-- Body System -->
                  <td class="py-4 px-5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-semibold text-[11px] border border-slate-200">
                      <span><?= e($p['SystemEmoji'] ?? '🩺') ?></span>
                      <span><?= e($p['SystemName'] ?: $specialty) ?></span>
                    </span>
                  </td>

                  <!-- Appointment Time / Date -->
                  <td class="py-4 px-5 text-slate-600 font-medium">
                    <div class="font-bold text-slate-800"><?= format_date($p['AppointmentDate'] ?? $p['RegisteredDate']) ?></div>
                    <div class="text-[11px] text-slate-400"><?= e($p['AppointmentTime'] ?: '09:00 AM') ?></div>
                  </td>

                  <!-- Status -->
                  <td class="py-4 px-5">
                    <span class="px-2.5 py-1 text-[10px] rounded-full border <?= get_status_badge($p['AppointmentStatus'] ?? ($p['QueueStatus'] ?? $p['Status'])) ?>">
                      <?= e($p['AppointmentStatus'] ?? ($p['QueueStatus'] ?? $p['Status'])) ?>
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="py-4 px-5 text-right space-x-1.5">
                    <a href="<?= doctor_url('views/patients/view.php?id=' . $p['PatientID']) ?>" 
                       class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 font-bold rounded-xl transition text-[11px] inline-flex items-center gap-1">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                      <span>View</span>
                    </a>
                    <a href="<?= doctor_url('views/diagnosis/index.php?patient_id=' . $p['PatientID'] . '&app_id=' . ($p['AppointmentID'] ?? '') . '&start=1') ?>" 
                       class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition text-[11px] inline-flex items-center gap-1 shadow-xs">
                      <i data-lucide="play" class="w-3.5 h-3.5"></i>
                      <span>Consult</span>
                    </a>
                  </td>

                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer (10 patients per page) -->
      <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs">
          <p class="text-slate-500 font-medium">
            Showing <strong class="text-slate-800"><?= $offset + 1 ?></strong> to <strong class="text-slate-800"><?= min($offset + $limit, $totalPatients) ?></strong> of <strong class="text-slate-800"><?= $totalPatients ?></strong> patients
          </p>
          <div class="flex items-center gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&date=<?= urlencode($date) ?>" 
                 class="w-8 h-8 rounded-xl font-bold flex items-center justify-center transition <?= $i === $page ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>

  </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
