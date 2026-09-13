<?php
// Nurse/views/queue/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle = 'Patient Queue Assistance | Nurse Portal • Tupi Municipal Hospital';
$activeMenu = 'queue';

$currentUser = Session::getCurrentUser();
$patients = getDemoPatients();

// Separate into queue buckets
$waitingList = array_filter($patients, fn($p) => $p['queue_status'] === 'Waiting');
$calledList = array_filter($patients, fn($p) => in_array($p['queue_status'], ['Called', 'Ready for Consultation']));
$inProgressList = array_filter($patients, fn($p) => in_array($p['queue_status'], ['In Progress', 'Under Observation']));
$completedList = array_filter($patients, fn($p) => in_array($p['queue_status'], ['Completed', 'For Discharge']));

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">

    <!-- Page Title & Header Actions -->
    <section data-aos="fade-down" class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">Patient Flow</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">Queue Assistance & Triage</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Coordinate patient sequence, call rooms, and update triage statuses</p>
      </div>
      
      <div class="flex items-center gap-3">
        <button onclick="callNextPatient()" 
                class="px-4 py-2.5 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-teal-600/20 flex items-center gap-2">
          <i data-lucide="bell-ring" class="w-4 h-4 animate-bounce"></i>
          <span>Call Next Patient</span>
        </button>
      </div>
    </section>

    <!-- Queue Status Kanban Columns -->
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6" data-aos="fade-up">

      <!-- Column 1: Waiting -->
      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card flex flex-col h-[640px]">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            <h3 class="font-bold text-sm text-slate-900 font-display">Waiting In Queue</h3>
          </div>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200"><?= count($waitingList) ?></span>
        </div>

        <div class="flex-1 overflow-y-auto space-y-3 pr-1" id="waitingCol">
          <?php foreach ($waitingList as $idx => $p): ?>
          <div class="p-4 bg-amber-50/40 hover:bg-amber-50 border border-amber-100 rounded-2xl transition shadow-xs group space-y-3">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-amber-200/60 text-amber-900 font-black text-xs flex items-center justify-center">#<?= $p['queue_number'] ?></span>
                <div>
                  <h4 class="font-bold text-xs text-slate-900"><?= e($p['name']) ?></h4>
                  <p class="text-[10px] text-slate-400"><?= e($p['room']) ?></p>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Waiting</span>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-amber-200/40 text-xs">
              <a href="<?= nurse_url('views/patients/view.php?id=' . $idx) ?>" class="text-[11px] font-semibold text-slate-500 hover:text-teal-700">View Record</a>
              <button onclick="changeStatus(<?= $idx ?>, 'Called')" class="px-2.5 py-1 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg text-[10px] transition shadow-xs">
                Call Patient
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Column 2: Called / Ready -->
      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card flex flex-col h-[640px]">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
            <h3 class="font-bold text-sm text-slate-900 font-display">Called / Ready</h3>
          </div>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200"><?= count($calledList) ?></span>
        </div>

        <div class="flex-1 overflow-y-auto space-y-3 pr-1" id="calledCol">
          <?php foreach ($calledList as $idx => $p): ?>
          <div class="p-4 bg-blue-50/40 hover:bg-blue-50 border border-blue-100 rounded-2xl transition shadow-xs group space-y-3">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-blue-200/60 text-blue-900 font-black text-xs flex items-center justify-center">#<?= $p['queue_number'] ?></span>
                <div>
                  <h4 class="font-bold text-xs text-slate-900"><?= e($p['name']) ?></h4>
                  <p class="text-[10px] text-slate-400"><?= e($p['room']) ?></p>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-800"><?= e($p['queue_status']) ?></span>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-blue-200/40 text-xs">
              <a href="<?= nurse_url('views/patients/view.php?id=' . $idx) ?>" class="text-[11px] font-semibold text-slate-500 hover:text-teal-700">View Record</a>
              <button onclick="changeStatus(<?= $idx ?>, 'In Progress')" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-[10px] transition shadow-xs">
                Start Triage
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Column 3: In Progress / Observation -->
      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card flex flex-col h-[640px]">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
            <h3 class="font-bold text-sm text-slate-900 font-display">In Progress</h3>
          </div>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200"><?= count($inProgressList) ?></span>
        </div>

        <div class="flex-1 overflow-y-auto space-y-3 pr-1" id="progressCol">
          <?php foreach ($inProgressList as $idx => $p): ?>
          <div class="p-4 bg-indigo-50/40 hover:bg-indigo-50 border border-indigo-100 rounded-2xl transition shadow-xs group space-y-3">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-indigo-200/60 text-indigo-900 font-black text-xs flex items-center justify-center">#<?= $p['queue_number'] ?></span>
                <div>
                  <h4 class="font-bold text-xs text-slate-900"><?= e($p['name']) ?></h4>
                  <p class="text-[10px] text-slate-400"><?= e($p['room']) ?> • BP <?= e($p['bp']) ?></p>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800">In Progress</span>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-indigo-200/40 text-xs">
              <a href="<?= nurse_url('views/patients/view.php?id=' . $idx) ?>" class="text-[11px] font-semibold text-slate-500 hover:text-teal-700">View Record</a>
              <button onclick="changeStatus(<?= $idx ?>, 'Completed')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition shadow-xs">
                Mark Complete
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Column 4: Completed / Discharged -->
      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card flex flex-col h-[640px]">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <h3 class="font-bold text-sm text-slate-900 font-display">Completed</h3>
          </div>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"><?= count($completedList) ?></span>
        </div>

        <div class="flex-1 overflow-y-auto space-y-3 pr-1" id="completedCol">
          <?php foreach ($completedList as $idx => $p): ?>
          <div class="p-4 bg-emerald-50/30 hover:bg-emerald-50 border border-emerald-100 rounded-2xl transition shadow-xs group space-y-3">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-200/60 text-emerald-900 font-black text-xs flex items-center justify-center">#<?= $p['queue_number'] ?></span>
                <div>
                  <h4 class="font-bold text-xs text-slate-900"><?= e($p['name']) ?></h4>
                  <p class="text-[10px] text-slate-400"><?= e($p['room']) ?></p>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Done</span>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-emerald-200/40 text-xs">
              <a href="<?= nurse_url('views/patients/view.php?id=' . $idx) ?>" class="text-[11px] font-semibold text-slate-500 hover:text-teal-700">View Record</a>
              <span class="text-[10px] text-slate-400">Assisted</span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </section>

  </main>
</div>

<script>
function changeStatus(index, newStatus) {
  alert(`Queue status updated to "${newStatus}" for selected patient.`);
  location.reload();
}

function callNextPatient() {
  alert('Broadcast Announcement: Calling Next Inpatient for Routine Vitals & Triage!');
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
