<?php
// Nurse/views/patients/view.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle = 'Patient Record Chart | Nurse Portal • Tupi Municipal Hospital';
$activeMenu = 'patients';

$currentUser = Session::getCurrentUser();
$patients = getDemoPatients();
$instructions = getDemoInstructions();
$vitalHistory = getDemoVitalHistory();

$patientId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!isset($patients[$patientId])) {
    $patientId = 0;
}
$patient = $patients[$patientId];

// Filter doctor instructions for this patient
$patientInstructions = array_filter($instructions, function($item) use ($patient) {
    return stripos($item['patient'], explode(' ', $patient['name'])[0]) !== false;
});
if (empty($patientInstructions)) {
    $patientInstructions = array_slice($instructions, 0, 2);
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">

    <!-- Header Navigation & Actions -->
    <section data-aos="fade-down" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <a href="<?= nurse_url('views/patients/index.php') ?>" 
           class="p-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl transition shadow-xs">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display"><?= e($patient['name']) ?></h1>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= get_patient_status_badge($patient['status']) ?>">
              ● <?= e($patient['status']) ?>
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5">Patient Record Chart • View-Only (Clinical Telemetry & Nursing Care)</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5">
          <i data-lucide="printer" class="w-3.5 h-3.5"></i>
          <span>Print Chart</span>
        </button>
        <button onclick="openModal('recordVitalsQuickModal')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-teal-600/25 flex items-center gap-2">
          <i data-lucide="plus" class="w-4 h-4"></i>
          <span>Add Vital Signs</span>
        </button>
      </div>
    </section>

    <!-- Main Patient Profile Banner -->
    <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card" data-aos="fade-up">
      <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        
        <div class="flex items-center gap-5">
          <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-teal-500 to-cyan-600 text-white font-black text-3xl flex items-center justify-center shadow-xl shadow-teal-500/25 shrink-0">
            <?= substr($patient['name'], 0, 1) ?>
          </div>
          <div>
            <div class="flex items-center gap-3">
              <h2 class="text-xl font-bold text-slate-900"><?= e($patient['name']) ?></h2>
              <span class="px-2 py-0.5 rounded-md font-mono text-xs font-bold bg-slate-100 text-slate-600"><?= e($patient['id']) ?></span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
              Age: <strong class="text-slate-800"><?= $patient['age'] ?> yrs</strong> • 
              Gender: <strong class="text-slate-800"><?= e($patient['gender']) ?></strong> • 
              Room: <strong class="text-teal-700 font-bold"><?= e($patient['room']) ?></strong>
            </p>
            <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
              <span>Attending Physician: <strong class="text-slate-700"><?= e($patient['attending_physician']) ?></strong></span>
              <span>•</span>
              <span>Admitted: <strong class="text-slate-700"><?= e($patient['admission_date']) ?></strong></span>
            </p>
          </div>
        </div>

        <!-- Contact & Emergency Info -->
        <div class="flex flex-wrap items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
          <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Contact Phone</span>
            <span class="font-bold text-slate-800 font-mono"><?= e($patient['contact']) ?></span>
          </div>
          <div class="h-8 w-px bg-slate-200"></div>
          <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Known Allergies</span>
            <span class="font-bold <?= $patient['allergies'] !== 'None' ? 'text-rose-600 font-black' : 'text-emerald-700' ?>"><?= e($patient['allergies']) ?></span>
          </div>
        </div>

      </div>
    </section>

    <!-- Grid Layout: Vitals Telemetry + Doctor Orders -->
    <section class="grid grid-cols-1 xl:grid-cols-12 gap-6">

      <!-- LEFT: Latest Vital Signs (7 Cols) -->
      <div class="xl:col-span-7 space-y-6" data-aos="fade-right">
        
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-5">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-black text-slate-900 tracking-tight font-display">Latest Physiological Vitals</h3>
              <p class="text-xs text-slate-400 mt-0.5">Recorded <?= e($patient['last_update']) ?></p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">Shift Monitored</span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="bg-rose-50/60 p-4 rounded-2xl border border-rose-100">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Heart Rate</span>
              <p class="text-2xl font-black text-slate-900 mt-1"><?= $patient['heart_rate'] ?> <span class="text-xs font-normal text-slate-400">BPM</span></p>
              <span class="text-[10px] text-emerald-600 font-bold mt-1 block">● Normal Rhythm</span>
            </div>

            <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Blood Pressure</span>
              <p class="text-2xl font-black text-slate-900 mt-1"><?= e($patient['bp']) ?> <span class="text-xs font-normal text-slate-400">mmHg</span></p>
              <span class="text-[10px] text-blue-600 font-bold mt-1 block">● Monitored</span>
            </div>

            <div class="bg-orange-50/60 p-4 rounded-2xl border border-orange-100">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Body Temp</span>
              <p class="text-2xl font-black text-slate-900 mt-1"><?= $patient['temperature'] ?> <span class="text-xs font-normal text-slate-400">°C</span></p>
              <span class="text-[10px] text-emerald-600 font-bold mt-1 block">● Afebrile</span>
            </div>

            <div class="bg-cyan-50/60 p-4 rounded-2xl border border-cyan-100">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Resp. Rate</span>
              <p class="text-2xl font-black text-slate-900 mt-1"><?= $patient['respiratory_rate'] ?> <span class="text-xs font-normal text-slate-400">BPM</span></p>
              <span class="text-[10px] text-emerald-600 font-bold mt-1 block">● Normal</span>
            </div>

            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">SpO₂ Level</span>
              <p class="text-2xl font-black text-slate-900 mt-1"><?= $patient['spo2'] ?>%</p>
              <span class="text-[10px] text-emerald-600 font-bold mt-1 block">● Adequate</span>
            </div>

            <div class="bg-purple-50/60 p-4 rounded-2xl border border-purple-100">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pain Score</span>
              <p class="text-2xl font-black text-slate-900 mt-1"><?= $patient['pain_level'] ?>/10</p>
              <span class="text-[10px] text-purple-600 font-bold mt-1 block">● Mild / Managed</span>
            </div>
          </div>
        </div>

        <!-- Vital Signs History Table -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900 tracking-tight font-display">Vital Signs Historical Log</h3>
            <span class="text-xs text-slate-400 font-semibold">Past 24 Hours</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
              <thead>
                <tr class="border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold">
                  <th class="py-2.5 px-3">Date & Time</th>
                  <th class="py-2.5 px-2">BP</th>
                  <th class="py-2.5 px-2">HR</th>
                  <th class="py-2.5 px-2">Temp</th>
                  <th class="py-2.5 px-2">SpO₂</th>
                  <th class="py-2.5 px-2">Pain</th>
                  <th class="py-2.5 px-3">Recorded By</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <?php foreach ($vitalHistory as $vh): ?>
                <tr class="hover:bg-slate-50 transition">
                  <td class="py-2.5 px-3 font-semibold text-slate-900"><?= e($vh['date']) ?> <?= e($vh['time']) ?></td>
                  <td class="py-2.5 px-2 font-bold"><?= e($vh['bp']) ?></td>
                  <td class="py-2.5 px-2"><?= $vh['hr'] ?> BPM</td>
                  <td class="py-2.5 px-2"><?= $vh['temp'] ?>°C</td>
                  <td class="py-2.5 px-2"><?= $vh['spo2'] ?>%</td>
                  <td class="py-2.5 px-2"><?= $vh['pain'] ?>/10</td>
                  <td class="py-2.5 px-3 text-slate-500 font-medium"><?= e($vh['recorded_by']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- RIGHT: Medical History & Doctor Instructions (5 Cols) -->
      <div class="xl:col-span-5 space-y-6" data-aos="fade-left">
        
        <!-- Medical History -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
          <h3 class="text-base font-black text-slate-900 tracking-tight font-display">Medical Background & Diagnoses</h3>
          
          <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Documented History</span>
            <p class="text-xs text-slate-800 font-medium leading-relaxed"><?= e($patient['medical_history']) ?></p>
          </div>

          <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-100 flex items-start gap-3">
            <i data-lucide="shield-alert" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
            <div class="text-xs">
              <span class="font-bold text-amber-900">Clinical Precaution</span>
              <p class="text-amber-800 mt-0.5 leading-relaxed">Observe strict vital monitoring protocol as ordered. Ensure bed rails raised and call button within patient reach.</p>
            </div>
          </div>
        </div>

        <!-- Attending Doctor Instructions (Read-Only) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900 tracking-tight font-display">Attending Physician Orders</h3>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
              <i data-lucide="lock" class="w-3 h-3 inline-block mr-0.5"></i>
              View Only
            </span>
          </div>

          <div class="space-y-3">
            <?php foreach ($patientInstructions as $ins): ?>
            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-2">
              <div class="flex items-center justify-between">
                <span class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                  <i data-lucide="stethoscope" class="w-3.5 h-3.5 text-blue-600"></i>
                  <?= e($ins['doctor']) ?>
                </span>
                <span class="px-2 py-0.5 rounded text-[9px] font-bold <?= get_priority_badge($ins['priority']) ?>"><?= e($ins['priority']) ?></span>
              </div>
              <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-100 italic leading-relaxed">
                "<?= e($ins['instruction']) ?>"
              </p>
              <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1">
                <span>Ordered: <?= format_date($ins['date'], 'd M Y h:i A') ?></span>
                <span class="text-emerald-600 font-bold">● Active Protocol</span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>

    </section>

  </main>
</div>

<!-- Modal: Quick Vitals Add -->
<div id="recordVitalsQuickModal" class="fixed inset-0 z-[60] hidden">
  <div class="modal-backdrop absolute inset-0 bg-black/40 opacity-0" onclick="closeModal('recordVitalsQuickModal')"></div>
  <div class="modal-content absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl opacity-0 translate-x-4 overflow-y-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
      <div>
        <h3 class="text-lg font-black text-slate-900 font-display">Add Vital Signs Entry</h3>
        <p class="text-xs text-slate-400 mt-0.5">Recording for <?= e($patient['name']) ?></p>
      </div>
      <button onclick="closeModal('recordVitalsQuickModal')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <form class="p-6 space-y-4" onsubmit="event.preventDefault(); alert('Vitals updated for patient chart!'); closeModal('recordVitalsQuickModal');">
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Date & Time</label>
        <input type="datetime-local" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500" value="<?= date('Y-m-d\TH:i') ?>">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Blood Pressure</label>
          <input type="text" placeholder="120/80" value="<?= e($patient['bp']) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Heart Rate (BPM)</label>
          <input type="number" placeholder="78" value="<?= $patient['heart_rate'] ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Temperature (°C)</label>
          <input type="number" step="0.1" value="<?= $patient['temperature'] ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">SpO₂ (%)</label>
          <input type="number" value="<?= $patient['spo2'] ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Nurse's Clinical Notes</label>
        <textarea rows="3" placeholder="Notes on patient condition during vitals capture..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
      </div>
      <div class="flex items-center gap-3 pt-2">
        <button type="button" onclick="closeModal('recordVitalsQuickModal')" class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-sm font-bold transition">Cancel</button>
        <button type="submit" class="flex-1 px-4 py-3 bg-teal-600 text-white hover:bg-teal-700 rounded-xl text-sm font-bold transition shadow-lg shadow-teal-600/25">Save to Record</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  modal.classList.remove('hidden');
  requestAnimationFrame(() => {
    const backdrop = modal.querySelector('.modal-backdrop');
    const content = modal.querySelector('.modal-content');
    if (backdrop) { backdrop.style.opacity = '1'; backdrop.style.backdropFilter = 'blur(8px)'; }
    if (content) { content.style.opacity = '1'; content.style.transform = 'scale(1) translateX(0) translateY(0)'; }
  });
  lucide.createIcons();
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  const backdrop = modal.querySelector('.modal-backdrop');
  const content = modal.querySelector('.modal-content');
  if (backdrop) { backdrop.style.opacity = '0'; backdrop.style.backdropFilter = 'none'; }
  if (content) { content.style.opacity = '0'; content.style.transform = 'scale(0.95) translateX(16px)'; }
  setTimeout(() => modal.classList.add('hidden'), 300);
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
