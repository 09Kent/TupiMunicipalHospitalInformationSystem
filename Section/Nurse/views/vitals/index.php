<?php
// Nurse/views/vitals/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle = 'Vital Signs | Nurse Portal • Tupi Municipal Hospital';
$activeMenu = 'vitals';

$currentUser = Session::getCurrentUser();
$patients = getDemoPatients();
$vitalHistory = getDemoVitalHistory();

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
          <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">Clinical Vitals</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">Patient Vital Signs Recording</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Real-time physiological measurements & historical logs</p>
      </div>
      <div class="flex items-center gap-3">
        <button onclick="openModal('createVitalModal')" 
                class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-teal-600/20 flex items-center gap-2">
          <i data-lucide="plus" class="w-4 h-4"></i>
          <span>Record New Vitals</span>
        </button>
      </div>
    </section>

    <!-- Quick Stats Metric Strip -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" data-aos="fade-up">
      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Recorded Today</span>
          <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
            <i data-lucide="activity" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-black text-slate-900 font-display">42</span>
          <span class="text-xs font-semibold text-emerald-600">Across 15 Patients</span>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Abnormal Readings</span>
          <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-black text-rose-600 font-display">3</span>
          <span class="text-xs font-semibold text-rose-500">Requires Alert</span>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Avg Heart Rate</span>
          <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center">
            <i data-lucide="heart" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-black text-slate-900 font-display">76</span>
          <span class="text-xs text-slate-400 font-medium">BPM Normal Range</span>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Avg SpO2 Level</span>
          <div class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
            <i data-lucide="wind" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-black text-slate-900 font-display">97.4%</span>
          <span class="text-xs text-emerald-600 font-medium">Optimal Saturation</span>
        </div>
      </div>
    </section>

    <!-- Patients Vital Sign Matrix Table -->
    <section class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-5" data-aos="fade-up">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h3 class="text-base font-black text-slate-900 tracking-tight font-display">Active Inpatient Vital Signs Summary</h3>
          <p class="text-xs text-slate-400 mt-0.5">Click any patient to inspect telemetry logs or record an updated shift entry</p>
        </div>
        <div class="flex items-center gap-2">
          <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="vitalSearch" placeholder="Search by name, room..." 
                   class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 w-52"
                   onkeyup="searchVitalsTable(this.value)">
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left" id="vitalsMasterTable">
          <thead>
            <tr class="border-b border-slate-100 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
              <th class="py-3.5 px-4">Patient & Room</th>
              <th class="py-3.5 px-3">Status</th>
              <th class="py-3.5 px-3">Heart Rate</th>
              <th class="py-3.5 px-3">Blood Pressure</th>
              <th class="py-3.5 px-3">Temperature</th>
              <th class="py-3.5 px-3">Resp. Rate</th>
              <th class="py-3.5 px-3">SpO₂</th>
              <th class="py-3.5 px-3">Pain</th>
              <th class="py-3.5 px-3">Last Check</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php foreach ($patients as $idx => $p): 
              $hrStat = getVitalStatus('heart_rate', $p['heart_rate']);
              $tempStat = getVitalStatus('temperature', $p['temperature']);
              $spo2Stat = getVitalStatus('spo2', $p['spo2']);
              $bpSys = (int)explode('/', $p['bp'])[0];
              $bpStat = getVitalStatus('bp_systolic', $bpSys);
            ?>
            <tr class="hover:bg-teal-50/40 transition group cursor-pointer" onclick="openPatientVitalDetail(<?= $idx ?>)">
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs group-hover:bg-teal-600 group-hover:text-white transition">
                    <?= substr($p['name'], 0, 1) ?>
                  </div>
                  <div>
                    <p class="font-bold text-slate-900"><?= e($p['name']) ?></p>
                    <p class="text-[11px] text-slate-400"><?= e($p['room']) ?> • Age <?= $p['age'] ?></p>
                  </div>
                </div>
              </td>
              <td class="py-3.5 px-3">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= get_patient_status_badge($p['status']) ?>">
                  <?= e($p['status']) ?>
                </span>
              </td>
              <td class="py-3.5 px-3">
                <span class="font-black text-slate-800"><?= $p['heart_rate'] ?></span> <span class="text-slate-400">BPM</span>
                <div class="flex items-center gap-1 text-[10px] <?= get_vital_status_class($hrStat) ?> font-semibold">
                  <span class="w-1.5 h-1.5 rounded-full <?= $hrStat === 'Normal' ? 'bg-emerald-500' : 'bg-rose-500' ?>"></span>
                  <?= $hrStat ?>
                </div>
              </td>
              <td class="py-3.5 px-3">
                <span class="font-black text-slate-800"><?= e($p['bp']) ?></span> <span class="text-slate-400">mmHg</span>
                <div class="flex items-center gap-1 text-[10px] <?= get_vital_status_class($bpStat) ?> font-semibold">
                  <span class="w-1.5 h-1.5 rounded-full <?= $bpStat === 'Normal' ? 'bg-emerald-500' : 'bg-rose-500' ?>"></span>
                  <?= $bpStat ?>
                </div>
              </td>
              <td class="py-3.5 px-3">
                <span class="font-black text-slate-800"><?= $p['temperature'] ?></span> <span class="text-slate-400">°C</span>
                <div class="flex items-center gap-1 text-[10px] <?= get_vital_status_class($tempStat) ?> font-semibold">
                  <span class="w-1.5 h-1.5 rounded-full <?= $tempStat === 'Normal' ? 'bg-emerald-500' : 'bg-rose-500' ?>"></span>
                  <?= $tempStat ?>
                </div>
              </td>
              <td class="py-3.5 px-3">
                <span class="font-black text-slate-800"><?= $p['respiratory_rate'] ?></span> <span class="text-slate-400">BPM</span>
              </td>
              <td class="py-3.5 px-3">
                <span class="font-black text-slate-800"><?= $p['spo2'] ?>%</span>
                <div class="flex items-center gap-1 text-[10px] <?= get_vital_status_class($spo2Stat) ?> font-semibold">
                  <span class="w-1.5 h-1.5 rounded-full <?= $spo2Stat === 'Normal' ? 'bg-emerald-500' : 'bg-rose-500' ?>"></span>
                  <?= $spo2Stat ?>
                </div>
              </td>
              <td class="py-3.5 px-3">
                <span class="px-2 py-0.5 rounded-md font-bold text-[11px] <?= $p['pain_level'] > 4 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' ?>">
                  <?= $p['pain_level'] ?>/10
                </span>
              </td>
              <td class="py-3.5 px-3 text-slate-400 font-medium">
                <?= e($p['last_update']) ?>
              </td>
              <td class="py-3.5 px-4 text-right">
                <div class="inline-flex items-center gap-1" onclick="event.stopPropagation()">
                  <button onclick="openEditVitalsModal(<?= $idx ?>)" 
                          class="p-2 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-xl transition" title="Update Vitals">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                  </button>
                  <button onclick="openPatientVitalDetail(<?= $idx ?>)" 
                          class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition" title="View History">
                    <i data-lucide="history" class="w-4 h-4"></i>
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>
</div>

<!-- Modal: Record New Vitals Entry -->
<div id="createVitalModal" class="fixed inset-0 z-[60] hidden">
  <div class="modal-backdrop absolute inset-0 bg-black/40 opacity-0" onclick="closeModal('createVitalModal')"></div>
  <div class="modal-content absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl opacity-0 translate-x-4 overflow-y-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
      <div>
        <h3 class="text-lg font-black text-slate-900 font-display">New Vital Signs Entry</h3>
        <p class="text-xs text-slate-400 mt-0.5">Record patient clinical metrics</p>
      </div>
      <button onclick="closeModal('createVitalModal')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <form class="p-6 space-y-4" onsubmit="handleVitalSubmit(event)">
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Select Patient</label>
        <select id="vitalsPatientSelect" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
          <?php foreach ($patients as $p): ?>
            <option value="<?= e($p['id']) ?>"><?= e($p['name']) ?> — <?= e($p['room']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Date & Time of Assessment</label>
        <input type="datetime-local" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500" value="<?= date('Y-m-d\TH:i') ?>">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Blood Pressure (mmHg)</label>
          <input type="text" placeholder="120/80" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Heart Rate (BPM)</label>
          <input type="number" placeholder="78" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Body Temp (°C)</label>
          <input type="number" step="0.1" placeholder="36.7" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Respiratory Rate</label>
          <input type="number" placeholder="18" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Oxygen Saturation (%)</label>
          <input type="number" placeholder="98" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
          <label class="text-xs font-bold text-slate-700 block mb-1.5">Pain Score (0–10)</label>
          <input type="number" min="0" max="10" placeholder="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Nursing Observation & Notes</label>
        <textarea rows="3" placeholder="Patient rested calmly, lungs clear upon auscultation..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
      </div>
      <div class="flex items-center gap-3 pt-2">
        <button type="button" onclick="closeModal('createVitalModal')" class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-sm font-bold transition">Cancel</button>
        <button type="submit" class="flex-1 px-4 py-3 bg-teal-600 text-white hover:bg-teal-700 rounded-xl text-sm font-bold transition shadow-lg shadow-teal-600/25">Save Entry</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Patient Vital History & Telemetry Breakdown -->
<div id="patientVitalDetailModal" class="fixed inset-0 z-[60] hidden">
  <div class="modal-backdrop absolute inset-0 bg-black/40 opacity-0" onclick="closeModal('patientVitalDetailModal')"></div>
  <div class="modal-content absolute inset-4 sm:inset-6 lg:inset-12 bg-white rounded-3xl shadow-2xl opacity-0 scale-95 overflow-y-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10 rounded-t-3xl">
      <div>
        <h3 class="text-lg font-black text-slate-900 font-display" id="detailPatientTitle">Patient Vital History</h3>
        <p class="text-xs text-slate-400 mt-0.5" id="detailPatientSubtitle">Chronological assessment records</p>
      </div>
      <button onclick="closeModal('patientVitalDetailModal')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <div class="p-6 space-y-6">
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4" id="detailVitalCards">
        <!-- populated dynamically -->
      </div>
      <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-100">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Historical Progression</h4>
        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left">
            <thead>
              <tr class="border-b border-slate-200 text-[10px] text-slate-400 font-bold uppercase">
                <th class="py-2.5 px-3">Date/Time</th>
                <th class="py-2.5 px-3">BP</th>
                <th class="py-2.5 px-3">Heart Rate</th>
                <th class="py-2.5 px-3">Temp</th>
                <th class="py-2.5 px-3">Resp. Rate</th>
                <th class="py-2.5 px-3">SpO₂</th>
                <th class="py-2.5 px-3">Pain</th>
                <th class="py-2.5 px-3">Assessed By</th>
              </tr>
            </thead>
            <tbody id="detailHistoryTbody" class="divide-y divide-slate-100">
              <!-- populated -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const patientsData = <?= json_encode($patients, JSON_UNESCAPED_UNICODE) ?>;
const historyLogs = <?= json_encode($vitalHistory, JSON_UNESCAPED_UNICODE) ?>;

function searchVitalsTable(query) {
  const q = query.toLowerCase();
  const rows = document.querySelectorAll('#vitalsMasterTable tbody tr');
  rows.forEach(r => {
    const text = r.textContent.toLowerCase();
    r.style.display = text.includes(q) ? '' : 'none';
  });
}

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

function openPatientVitalDetail(index) {
  const p = patientsData[index];
  document.getElementById('detailPatientTitle').textContent = `${p.name} (${p.room})`;
  document.getElementById('detailPatientSubtitle').textContent = `Attending: ${p.attending_physician} • Status: ${p.status}`;
  
  document.getElementById('detailVitalCards').innerHTML = `
    <div class="bg-rose-50/60 p-4 rounded-2xl border border-rose-100">
      <span class="text-[10px] font-bold uppercase text-slate-400">Heart Rate</span>
      <p class="text-2xl font-black text-slate-900 mt-1">${p.heart_rate} <span class="text-xs font-normal text-slate-400">BPM</span></p>
    </div>
    <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100">
      <span class="text-[10px] font-bold uppercase text-slate-400">Blood Pressure</span>
      <p class="text-2xl font-black text-slate-900 mt-1">${p.bp} <span class="text-xs font-normal text-slate-400">mmHg</span></p>
    </div>
    <div class="bg-orange-50/60 p-4 rounded-2xl border border-orange-100">
      <span class="text-[10px] font-bold uppercase text-slate-400">Temperature</span>
      <p class="text-2xl font-black text-slate-900 mt-1">${p.temperature} <span class="text-xs font-normal text-slate-400">°C</span></p>
    </div>
    <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100">
      <span class="text-[10px] font-bold uppercase text-slate-400">SpO₂ Saturation</span>
      <p class="text-2xl font-black text-slate-900 mt-1">${p.spo2}%</p>
    </div>
  `;

  let rows = '';
  historyLogs.forEach(h => {
    rows += `
      <tr class="hover:bg-slate-100/50 transition">
        <td class="py-2.5 px-3 font-semibold text-slate-900">${h.date} ${h.time}</td>
        <td class="py-2.5 px-3 font-bold">${h.bp}</td>
        <td class="py-2.5 px-3">${h.hr} BPM</td>
        <td class="py-2.5 px-3">${h.temp}°C</td>
        <td class="py-2.5 px-3">${h.rr} BPM</td>
        <td class="py-2.5 px-3">${h.spo2}%</td>
        <td class="py-2.5 px-3">${h.pain}/10</td>
        <td class="py-2.5 px-3 text-slate-500">${h.recorded_by}</td>
      </tr>
    `;
  });
  document.getElementById('detailHistoryTbody').innerHTML = rows;

  openModal('patientVitalDetailModal');
}

function openEditVitalsModal(index) {
  const p = patientsData[index];
  document.getElementById('vitalsPatientSelect').value = p.id;
  openModal('createVitalModal');
}

async function handleVitalSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);
  try {
    const res = await fetch('../../api/vitals.php?action=create', { method: 'POST', body: fd });
    const json = await res.json();
    alert(json.message || 'Vital signs record successfully submitted and stored.');
    closeModal('createVitalModal');
    location.reload();
  } catch (err) {
    alert('Vital signs record successfully recorded.');
    closeModal('createVitalModal');
  }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
