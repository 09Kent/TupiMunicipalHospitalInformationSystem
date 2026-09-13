<?php
// views/queue/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Queue.php';

$queueModel = new Queue();

$nowServing = $queueModel->getNowServing();
$todayQueue = $queueModel->getTodayQueue();

$activeNav = 'queue';
$pageTitle = 'Patient Queue Board | Tupi Municipal Hospital Information Management System';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
        Live Triage Queue Board
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Today's Patient Queue</h1>
      <p class="text-xs text-slate-500 mt-0.5">Real-time consultation queue management, caller board, and session tracking.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0 no-print">
      <button type="button" onclick="window.location.reload()" class="px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
        <span>Refresh Board</span>
      </button>

      <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
        <i data-lucide="printer" class="w-4 h-4"></i>
        <span>Print Daily Queue</span>
      </button>
    </div>
  </div>

  <!-- Big Television-Style NOW SERVING Display Board -->
  <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-3xl p-6 sm:p-10 text-white shadow-2xl shadow-blue-600/20 space-y-6 relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex items-center justify-between border-b border-white/20 pb-4">
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
        <span class="text-xs font-extrabold tracking-widest uppercase text-blue-200">ACTIVE CONSULTATION CALLER</span>
      </div>
      <span class="text-xs font-semibold text-blue-200"><?= date('l, d F Y') ?></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
      
      <!-- Left: Big Ticket Number -->
      <div class="flex items-center gap-6">
        <div class="w-32 sm:w-40 aspect-square rounded-3xl bg-white/15 backdrop-blur-md border-2 border-white/30 flex flex-col items-center justify-center text-center shadow-inner">
          <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-blue-200">TICKET</span>
          <span class="text-5xl sm:text-7xl font-black font-mono tracking-tight text-white mt-1">
            #<?= e($nowServing['QueueNumber'] ?? '—') ?>
          </span>
        </div>

        <div class="space-y-1 min-w-0">
          <div class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-extrabold text-white mb-1">
            <?= e($nowServing['QueueStatus'] ?? 'Standby') ?>
          </div>
          <h2 class="text-2xl sm:text-3xl font-black truncate">
            <?= e($nowServing ? ($nowServing['PatFirst'] . ' ' . $nowServing['PatLast']) : 'No Patient Called') ?>
          </h2>
          <div class="text-xs text-blue-100">
            <?= e($nowServing ? ($nowServing['PatientCode'] . ' • ' . $nowServing['Age'] . 'y, ' . $nowServing['Gender']) : 'Waiting for next in line...') ?>
          </div>
        </div>
      </div>

      <!-- Right: Attending Doctor Details -->
      <?php if ($nowServing): ?>
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 flex items-center gap-4">
          <img src="<?= e($nowServing['ProfileImage'] ?? 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150') ?>" 
               class="w-16 h-16 rounded-2xl object-cover border-2 border-white/40 shadow-sm shrink-0" />
          <div class="min-w-0">
            <div class="text-[10px] font-bold text-blue-200 uppercase tracking-wider">Attending Specialist</div>
            <div class="text-base font-extrabold text-white truncate">Dr. <?= e($nowServing['DocFirst'] . ' ' . $nowServing['DocLast']) ?></div>
            <div class="text-xs text-blue-100"><?= e($nowServing['Specialty']) ?></div>
            <div class="text-[11px] text-blue-200 mt-1 flex items-center gap-1">
              <i data-lucide="map-pin" class="w-3 h-3 text-blue-200"></i>
              <?= e($nowServing['ClinicRoom']) ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>

  </div>

  <!-- Queue Management Table -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-6">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <h3 class="text-sm font-extrabold text-slate-900">Today's Queue Roster (<?= count($todayQueue) ?> Patients)</h3>
      <span class="text-xs text-slate-500">Live Status Transitions</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 font-bold">
            <th class="pb-3.5">Ticket #</th>
            <th class="pb-3.5">Patient Name</th>
            <th class="pb-3.5">Assigned Specialist</th>
            <th class="pb-3.5">Queue Priority</th>
            <th class="pb-3.5">Status</th>
            <th class="pb-3.5 text-right no-print">Queue Transition Controls</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($todayQueue)): ?>
            <?php foreach ($todayQueue as $q): ?>
              <tr class="hover:bg-slate-50/70 transition <?= $q['QueueStatus'] === 'In Consultation' ? 'bg-blue-50/40' : '' ?>">
                
                <td class="py-3.5 font-mono font-black text-base text-blue-700">
                  #<?= e($q['QueueNumber']) ?>
                </td>

                <td class="py-3.5">
                  <a href="<?= base_url('views/patients/view.php?id=' . $q['PatientID']) ?>" class="font-bold text-slate-800 hover:text-blue-600 text-sm">
                    <?= e($q['PatFirst'] . ' ' . $q['PatLast']) ?>
                  </a>
                  <div class="text-[10px] text-slate-400"><?= e($q['PatientCode']) ?> • <?= e($q['Age']) ?>y, <?= e($q['Gender']) ?></div>
                </td>

                <td class="py-3.5">
                  <div class="font-bold text-slate-800">Dr. <?= e($q['DocFirst'] . ' ' . $q['DocLast']) ?></div>
                  <div class="text-[10px] text-slate-500"><?= e($q['Specialty']) ?> (<?= e($q['ClinicRoom']) ?>)</div>
                </td>

                <td class="py-3.5">
                  <span class="px-2 py-0.5 rounded-md text-[10px] font-bold <?= $q['Priority'] === 'Priority' || $q['Priority'] === 'Emergency' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-600' ?>">
                    <?= e($q['Priority']) ?>
                  </span>
                </td>

                <td class="py-3.5">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold <?= get_status_badge($q['QueueStatus']) ?>">
                    <?= e($q['QueueStatus']) ?>
                  </span>
                </td>

                <td class="py-3.5 text-right no-print">
                  <div class="flex items-center justify-end gap-1.5">
                    
                    <?php if ($q['QueueStatus'] === 'Waiting'): ?>
                      <button type="button" onclick="updateQueueStatus(<?= $q['QueueID'] ?>, 'call')" 
                              class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg font-bold text-[11px] transition">
                        Call Next
                      </button>
                    <?php elseif ($q['QueueStatus'] === 'Called'): ?>
                      <button type="button" onclick="updateQueueStatus(<?= $q['QueueID'] ?>, 'consult')" 
                              class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-[11px] transition">
                        Start Consult
                      </button>
                    <?php elseif ($q['QueueStatus'] === 'In Consultation'): ?>
                      <button type="button" onclick="updateQueueStatus(<?= $q['QueueID'] ?>, 'complete')" 
                              class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[11px] transition">
                        Complete
                      </button>
                    <?php endif; ?>

                    <?php if ($q['QueueStatus'] !== 'Completed' && $q['QueueStatus'] !== 'Cancelled'): ?>
                      <button type="button" onclick="updateQueueStatus(<?= $q['QueueID'] ?>, 'cancel')" 
                              class="px-2 py-1 bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-700 rounded-lg font-bold text-[11px] transition">
                        ✕
                      </button>
                    <?php endif; ?>

                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400 italic">No patients currently queued for today.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

</main>

<script>
  async function updateQueueStatus(queueId, action) {
    try {
      const res = await fetch('<?= base_url('api/queue/index.php') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ queue_id: queueId, action: action })
      });
      const data = await res.json();
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Error updating queue.');
      }
    } catch (e) {
      alert('Failed to connect to server.');
    }
  }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
