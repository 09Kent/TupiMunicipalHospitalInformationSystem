<?php
// Nurse/views/tasks/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle = 'Task Coordination | Nurse Portal • Tupi Municipal Hospital';
$activeMenu = 'tasks';

$currentUser = Session::getCurrentUser();
$patients = getDemoPatients();

// Tasks list
$allTasks = [];
foreach ($patients as $idx => $p) {
    $allTasks[] = [
        'patient_index' => $idx,
        'patient_name' => $p['name'],
        'room' => $p['room'],
        'task' => $p['assigned_task'],
        'status' => $p['task_status'],
        'priority' => $p['task_priority'],
        'due' => $p['task_due'],
        'physician' => $p['attending_physician'],
        'status_note' => $p['last_update']
    ];
}

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
          <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">Clinical Workflow</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">Assigned Nursing Tasks</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Shift duties, medication administration, and patient care coordination</p>
      </div>

      <div class="flex items-center gap-3">
        <button onclick="openModal('createStatusUpdateModal')" 
                class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-teal-600/20 flex items-center gap-2">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
          <span>Create Status Update</span>
        </button>
      </div>
    </section>

    <!-- Task Coordination Matrix -->
    <section class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-5" data-aos="fade-up">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h3 class="text-base font-black text-slate-900 tracking-tight font-display">Shift Duty Schedule</h3>
          <p class="text-xs text-slate-400 mt-0.5">Assigned to Nurse Maria Santos (Day Shift)</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="taskCardsContainer">
        <?php foreach ($allTasks as $t): ?>
        <div class="p-5 bg-slate-50/80 hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-200 shadow-xs flex flex-col justify-between space-y-4 group">
          <div>
            <div class="flex items-start justify-between gap-3">
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= get_priority_badge($t['priority']) ?>">
                <?= e($t['priority']) ?> Priority
              </span>
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= get_task_badge($t['status']) ?>">
                <?= e($t['status']) ?>
              </span>
            </div>

            <div class="mt-3">
              <h4 class="font-bold text-sm text-slate-900 leading-snug"><?= e($t['task']) ?></h4>
              <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                <i data-lucide="user" class="w-3.5 h-3.5 text-teal-600"></i>
                <strong class="text-slate-700"><?= e($t['patient_name']) ?></strong> (<?= e($t['room']) ?>)
              </p>
              <p class="text-[11px] text-slate-400 mt-0.5">Ordered by: <?= e($t['physician']) ?></p>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
            <span class="text-[11px] text-slate-500 font-semibold flex items-center gap-1">
              <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
              Due: <?= e($t['due']) ?>
            </span>
            <div class="flex items-center gap-1.5">
              <button onclick="updateTaskPrompt(<?= $t['patient_index'] ?>, '<?= e($t['task']) ?>')" 
                      class="px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold rounded-xl text-xs transition">
                Update Status
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

  </main>
</div>

<!-- Modal: Create Patient Status Update -->
<div id="createStatusUpdateModal" class="fixed inset-0 z-[60] hidden">
  <div class="modal-backdrop absolute inset-0 bg-black/40 opacity-0" onclick="closeModal('createStatusUpdateModal')"></div>
  <div class="modal-content absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl opacity-0 translate-x-4 overflow-y-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
      <div>
        <h3 class="text-lg font-black text-slate-900 font-display">Create Patient Status Update</h3>
        <p class="text-xs text-slate-400 mt-0.5">Log condition progress & clinical intervention</p>
      </div>
      <button onclick="closeModal('createStatusUpdateModal')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <form class="p-6 space-y-4" onsubmit="event.preventDefault(); alert('Patient status update saved successfully!'); closeModal('createStatusUpdateModal');">
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Patient</label>
        <select class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
          <?php foreach ($patients as $p): ?>
            <option value="<?= e($p['id']) ?>"><?= e($p['name']) ?> — <?= e($p['room']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Current Condition Status</label>
        <select class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
          <option>Stable</option>
          <option>Under Observation</option>
          <option>Needs Attention</option>
          <option>Critical</option>
          <option>For Discharge</option>
        </select>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Vital Sign Concern</label>
        <input type="text" placeholder="e.g. Blood pressure slightly elevated" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Observation Details</label>
        <textarea rows="2" placeholder="Describe physical signs, complaints..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Action Taken</label>
        <textarea rows="2" placeholder="Medication given, physician notified, repositioned..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Additional Notes</label>
        <textarea rows="2" placeholder="Continue monitoring schedule..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
      </div>
      <div class="flex items-center gap-3 pt-2">
        <button type="button" onclick="closeModal('createStatusUpdateModal')" class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-sm font-bold transition">Cancel</button>
        <button type="submit" class="flex-1 px-4 py-3 bg-teal-600 text-white hover:bg-teal-700 rounded-xl text-sm font-bold transition shadow-lg shadow-teal-600/25">Save Status Update</button>
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

function updateTaskPrompt(patientIndex, taskName) {
  const newStatus = prompt(`Update status for "${taskName}":\nType: Pending, In Progress, or Completed`, "Completed");
  if (newStatus) {
    alert(`Task status updated to ${newStatus}.`);
    location.reload();
  }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
