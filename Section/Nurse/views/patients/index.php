<?php
// Nurse/views/patients/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle = 'Patients Directory | Nurse Portal • Tupi Municipal Hospital';
$activeMenu = 'patients';

$currentUser = Session::getCurrentUser();
$patients = getDemoPatients();

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
          <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">Patient Directory</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">Inpatient Roster</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Assigned ward occupants & medical telemetry oversight</p>
      </div>
      <div class="flex items-center gap-3">
        <div class="relative">
          <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
          <input type="text" id="patientSearch" placeholder="Search patients..." 
                 class="pl-9 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs w-64"
                 onkeyup="filterPatientGrid(this.value)">
        </div>
      </div>
    </section>

    <!-- Patient Grid -->
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="patientCardsGrid" data-aos="fade-up">
      <?php foreach ($patients as $idx => $p): ?>
      <div class="patient-card bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:shadow-card-hover hover:-translate-y-1 transition-all duration-200 space-y-5 flex flex-col justify-between"
           data-name="<?= strtolower($p['name']) ?>" data-room="<?= strtolower($p['room']) ?>" data-status="<?= strtolower($p['status']) ?>">
        
        <div>
          <!-- Header -->
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3.5">
              <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-700 text-white font-black text-base flex items-center justify-center shadow-md shadow-teal-500/20">
                <?= substr($p['name'], 0, 1) ?>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base font-display"><?= e($p['name']) ?></h3>
                <p class="text-xs text-slate-400 mt-0.5"><?= e($p['id']) ?> • Age <?= $p['age'] ?> • <?= e($p['gender']) ?></p>
              </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= get_patient_status_badge($p['status']) ?>">
              <?= e($p['status']) ?>
            </span>
          </div>

          <!-- Room & Physician -->
          <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-slate-100 text-xs">
            <div class="bg-slate-50/80 p-2.5 rounded-xl border border-slate-100">
              <span class="text-[10px] font-bold uppercase text-slate-400">Room</span>
              <p class="font-bold text-slate-800 mt-0.5"><?= e($p['room']) ?></p>
            </div>
            <div class="bg-slate-50/80 p-2.5 rounded-xl border border-slate-100">
              <span class="text-[10px] font-bold uppercase text-slate-400">Attending Doctor</span>
              <p class="font-bold text-slate-800 mt-0.5 truncate"><?= e($p['attending_physician']) ?></p>
            </div>
          </div>

          <!-- Vitals Snapshot -->
          <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">
            <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
              <span class="text-[9px] font-bold uppercase text-slate-400 block">BP</span>
              <span class="font-black text-slate-900"><?= e($p['bp']) ?></span>
            </div>
            <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
              <span class="text-[9px] font-bold uppercase text-slate-400 block">Heart Rate</span>
              <span class="font-black text-slate-900"><?= $p['heart_rate'] ?> <span class="text-[9px] font-normal text-slate-400">BPM</span></span>
            </div>
            <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
              <span class="text-[9px] font-bold uppercase text-slate-400 block">SpO₂</span>
              <span class="font-black text-slate-900"><?= $p['spo2'] ?>%</span>
            </div>
          </div>
        </div>

        <!-- Card Footer -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
          <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
            <span><?= e($p['last_update']) ?></span>
          </div>
          <a href="<?= nurse_url('views/patients/view.php?id=' . $idx) ?>" 
             class="px-3.5 py-1.5 bg-slate-50 hover:bg-teal-50 text-slate-700 hover:text-teal-700 border border-slate-200 hover:border-teal-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <span>View Record</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
          </a>
        </div>

      </div>
      <?php endforeach; ?>
    </section>

  </main>
</div>

<script>
function filterPatientGrid(q) {
  const query = q.toLowerCase();
  document.querySelectorAll('.patient-card').forEach(card => {
    const name = card.dataset.name;
    const room = card.dataset.room;
    const status = card.dataset.status;
    const match = name.includes(query) || room.includes(query) || status.includes(query);
    card.style.display = match ? '' : 'none';
  });
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
