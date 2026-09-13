@extends('layouts.register')

@section('title', 'Dashboard | Tupi Municipal Hospital Information Management System')

@section('content')
<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">
  
  <!-- Welcome Banner & Quick Action Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-600/15 relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    
    <div class="relative z-10 space-y-1">
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-white/15 backdrop-blur-xs border border-white/20 mb-1">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        EHR Clinical Intake System Active
      </div>
      <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Good day, <?= e($user['name'] ?? 'Registrator') ?></h1>
      <p class="text-xs sm:text-sm text-blue-100 max-w-xl">
        Tupi Municipal Hospital registration portal. Manage patient registrations, triage symptoms, monitor doctor consultation queues, and schedule appointments in real time.
      </p>
    </div>

    <div class="relative z-10 flex flex-wrap items-center gap-3 shrink-0">
      <a href="<?= route('register.registration.index') ?>" 
         class="px-5 py-3 bg-white hover:bg-blue-50 text-blue-700 text-xs font-black rounded-2xl shadow-lg shadow-black/10 transition-all flex items-center gap-2 group">
        <i data-lucide="user-plus" class="w-4 h-4 text-blue-600"></i>
        <span>+ Register Patient</span>
        <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
      </a>

      <a href="<?= route('register.queue.index') ?>" 
         class="px-4 py-3 bg-white/15 hover:bg-white/25 text-white border border-white/25 rounded-2xl text-xs font-bold transition flex items-center gap-2">
        <i data-lucide="list-ordered" class="w-4 h-4"></i>
        <span>Queue Board</span>
      </a>
    </div>
  </div>

  <!-- Real-time MySQL Statistics KPI Cards Grid -->
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-5">
    
    <!-- KPI 1: Total Patients -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-2 hover:border-blue-300 transition">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Patients</span>
        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
          <i data-lucide="users" class="w-4 h-4"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black text-slate-900"><?= number_format($kpis['total_patients']) ?></div>
      <div class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
        <i data-lucide="database" class="w-3 h-3"></i> MySQL Verified
      </div>
    </div>

    <!-- KPI 2: Today's Registrations -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-2 hover:border-blue-300 transition">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Today's Registrations</span>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
          <i data-lucide="user-check" class="w-4 h-4"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black text-slate-900"><?= number_format($kpis['today_registrations']) ?></div>
      <div class="text-[11px] text-slate-500 font-medium">New intakes today</div>
    </div>

    <!-- KPI 3: Today's Consultations -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-2 hover:border-blue-300 transition">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Today's Consultations</span>
        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
          <i data-lucide="calendar" class="w-4 h-4"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black text-slate-900"><?= number_format($kpis['today_consultations']) ?></div>
      <div class="text-[11px] text-indigo-600 font-semibold">Scheduled sessions</div>
    </div>

    <!-- KPI 4: Waiting Patients -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-2 hover:border-blue-300 transition">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Waiting in Queue</span>
        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
          <i data-lucide="clock" class="w-4 h-4"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black text-slate-900"><?= number_format($kpis['waiting_patients']) ?></div>
      <div class="text-[11px] text-amber-600 font-semibold">Triage queue active</div>
    </div>

    <!-- KPI 5: Admitted Patients -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-2 hover:border-blue-300 transition col-span-2 lg:col-span-1">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Admitted Inpatients</span>
        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
          <i data-lucide="bed" class="w-4 h-4"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black text-slate-900"><?= number_format($kpis['admitted_patients']) ?></div>
      <div class="text-[11px] text-purple-600 font-semibold">Ward census</div>
    </div>

  </div>

  <!-- Middle Grid: Live Queue Board + Today's Schedule -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Column 1: Live Patient Queue Monitor (Now Serving Widget) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-6 flex flex-col justify-between">
      
      <div class="space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></div>
            <h3 class="text-sm font-extrabold text-slate-900">Live Consultation Queue</h3>
          </div>
          <a href="<?= route('register.queue.index') ?>" class="text-xs text-blue-600 font-bold hover:underline">Manage Queue</a>
        </div>

        <!-- NOW SERVING Highlight Box -->
        <?php if ($nowServing): ?>
          <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-md shadow-blue-600/20 space-y-3">
            <div class="flex items-center justify-between text-[11px] font-bold tracking-wider uppercase text-blue-200">
              <span>NOW SERVING</span>
              <span class="px-2 py-0.5 rounded-full bg-white/20 backdrop-blur-xs text-white">In Consultation</span>
            </div>

            <div class="flex items-center gap-4">
              <div class="text-4xl font-black font-mono tracking-tight text-white">
                #<?= e($nowServing['QueueNumber']) ?>
              </div>
              <div class="border-l border-white/20 pl-4 min-w-0">
                <div class="text-base font-bold truncate"><?= e($nowServing['PatFirst'] . ' ' . $nowServing['PatLast']) ?></div>
                <div class="text-xs text-blue-100">Attending: Dr. <?= e($nowServing['DocLast']) ?> (<?= e($nowServing['Specialty']) ?>)</div>
                <div class="text-[11px] text-blue-200 mt-0.5"><?= e($nowServing['ClinicRoom']) ?></div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-5 text-center space-y-2">
            <div class="text-2xl">🩺</div>
            <div class="text-xs font-bold text-blue-900">No Patient Currently Called</div>
            <p class="text-[11px] text-slate-500">Next patient in queue is ready to be called.</p>
          </div>
        <?php endif; ?>

        <!-- Next in Line List -->
        <div>
          <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Next in Queue</div>
          <div class="space-y-2">
            <?php 
            $waitingQueue = array_filter($todayQueue, fn($q) => $q['QueueStatus'] === 'Waiting');
            $nextList = array_slice($waitingQueue, 0, 3);
            if (!empty($nextList)): 
              foreach ($nextList as $item): ?>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                  <div class="flex items-center gap-3">
                    <span class="font-mono font-black text-blue-700 bg-blue-100 px-2 py-0.5 rounded-lg text-xs">
                      #<?= e($item['QueueNumber']) ?>
                    </span>
                    <div>
                      <div class="font-bold text-slate-800"><?= e($item['PatFirst'] . ' ' . $item['PatLast']) ?></div>
                      <div class="text-[10px] text-slate-400">Dr. <?= e($item['DocLast']) ?> • <?= e($item['Specialty']) ?></div>
                    </div>
                  </div>
                  <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                    Waiting
                  </span>
                </div>
              <?php endforeach; 
            else: ?>
              <div class="text-xs text-slate-400 italic py-2 text-center">Queue is currently clear.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100">
        <a href="<?= route('register.queue.index') ?>" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
          <i data-lucide="tv" class="w-4 h-4"></i>
          <span>Open Fullscreen Queue Board</span>
        </a>
      </div>

    </div>

    <!-- Column 2 & 3: Today's Consultation Appointments -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-4 lg:col-span-2">
      
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
            <i data-lucide="calendar" class="w-4 h-4"></i>
          </div>
          <h3 class="text-sm font-extrabold text-slate-900">Today's Consultation Schedule (<?= count($todayAppointments) ?> Sessions)</h3>
        </div>
        <a href="<?= route('register.appointments.index') ?>" class="text-xs text-blue-600 font-bold hover:underline">View All Schedule</a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="pb-3 font-bold">Time</th>
              <th class="pb-3 font-bold">Patient</th>
              <th class="pb-3 font-bold">Assigned Specialist</th>
              <th class="pb-3 font-bold">Reason</th>
              <th class="pb-3 font-bold">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (!empty($todayAppointments)): ?>
              <?php foreach ($todayAppointments as $app): ?>
                <tr class="hover:bg-slate-50/70 transition">
                  <td class="py-3 font-bold text-blue-700 font-mono"><?= e($app['AppointmentTime']) ?></td>
                  <td class="py-3">
                    <div class="font-bold text-slate-800"><?= e($app['PatFirst'] . ' ' . $app['PatLast']) ?></div>
                    <div class="text-[10px] text-slate-400"><?= e($app['PatientCode']) ?> • <?= e($app['Age']) ?>y, <?= e($app['Gender']) ?></div>
                  </td>
                  <td class="py-3">
                    <div class="font-bold text-slate-800">Dr. <?= e($app['DocLast']) ?></div>
                    <div class="text-[10px] text-slate-500"><?= e($app['Specialty']) ?> (<?= e($app['ClinicRoom']) ?>)</div>
                  </td>
                  <td class="py-3 text-slate-600 max-w-[180px] truncate" title="<?= e($app['Reason']) ?>">
                    <?= e($app['Reason']) ?>
                  </td>
                  <td class="py-3">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold <?= get_status_badge($app['Status']) ?>">
                      <?= e($app['Status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="py-6 text-center text-slate-400 italic">No consultations scheduled for today yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>

  </div>

  <!-- Bottom Section: Recent Patient Registrations Master Table -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-5">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Recent Patient Registrations</h3>
        <p class="text-xs text-slate-500">Database-driven patient directory synchronized with pre-consultation intake.</p>
      </div>

      <div class="flex items-center gap-3">
        <a href="<?= route('register.patients.index') ?>" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
          <span>View All Patients</span>
          <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
      </div>
    </div>

    <!-- Responsive Patients Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100">
            <th class="pb-3 font-bold">Patient Code</th>
            <th class="pb-3 font-bold">Patient Name</th>
            <th class="pb-3 font-bold">Category</th>
            <th class="pb-3 font-bold">Registered</th>
            <th class="pb-3 font-bold">Chief Complaint</th>
            <th class="pb-3 font-bold">Classified System</th>
            <th class="pb-3 font-bold">Status</th>
            <th class="pb-3 font-bold text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($recentPatients as $pat): ?>
            <tr class="hover:bg-slate-50/70 transition">
              <td class="py-3.5 font-mono font-bold text-blue-700">
                <a href="<?= route('register.patients.view', $pat['PatientID']) ?>" class="hover:underline">
                  <?= e($pat['PatientCode']) ?>
                </a>
              </td>
              <td class="py-3.5">
                <div class="font-bold text-slate-800"><?= e($pat['FirstName'] . ' ' . $pat['LastName']) ?></div>
                <div class="text-[10px] text-slate-400"><?= e($pat['Gender']) ?> • <?= e($pat['Age']) ?> yrs • <?= e($pat['ContactNumber']) ?></div>
              </td>
              <td class="py-3.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?= get_category_badge($pat['PatientCategory']) ?>">
                  <?= e($pat['PatientCategory']) ?>
                </span>
              </td>
              <td class="py-3.5 text-slate-500 font-medium">
                <?= format_date($pat['CreatedAt']) ?>
              </td>
              <td class="py-3.5 text-slate-600 max-w-[180px] truncate" title="<?= e($pat['ComplaintDescription']) ?>">
                <?= e($pat['ComplaintDescription'] ?: 'None reported') ?>
              </td>
              <td class="py-3.5">
                <?php if (!empty($pat['RelatedSystem'])): ?>
                  <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded-lg text-[10px]">
                    <?= e($pat['SystemEmoji'] ?? '🩺') ?> <?= e($pat['RelatedSystem']) ?>
                  </span>
                <?php else: ?>
                  <span class="text-slate-400 text-[10px]">General</span>
                <?php endif; ?>
              </td>
              <td class="py-3.5">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $pat['Status'] === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                  <?= e($pat['Status']) ?>
                </span>
              </td>
              <td class="py-3.5 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <a href="<?= route('register.patients.view', $pat['PatientID']) ?>" 
                     class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="View Patient Profile">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </a>
                  <a href="<?= route('register.patients.edit', $pat['PatientID']) ?>" 
                     class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Patient Information">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>

</main>
@endsection
