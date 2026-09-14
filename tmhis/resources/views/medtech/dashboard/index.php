<?php
// Med_Tech/views/dashboard/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/demo_data.php';
require_once __DIR__ . '/../includes/lab_visualizer.php';

$pageTitle = 'Laboratory Dashboard | Medical Technologist • Tupi Municipal Hospital';
$activeMenu = 'dashboard';

$currentUser = Session::getCurrentUser();
$patients = getDemoPatients();
$requests = getDemoLabRequests();
$samples = getDemoSamples();
$results = getDemoLabResults();
$catalog = getDemoTestCatalog();
$referenceRanges = getDemoReferenceRanges();
$activities = getDemoRecentActivity();
$notifications = getDemoNotifications();

// Computed Metrics
$pendingRequestsCount = count(array_filter($requests, fn($r) => $r['status'] === 'Pending'));
$processingSamplesCount = count(array_filter($samples, fn($s) => $s['status'] === 'Processing'));
$forVerificationCount = count(array_filter($results, fn($res) => $res['status'] === 'For Verification'));
$completedTodayCount = 24; // realistic daily completed count

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">

    <!-- ========================================================================= -->
    <!-- TAB 1: MAIN DASHBOARD VIEW                                               -->
    <!-- ========================================================================= -->
    <div id="tab-section-dashboard" class="space-y-8">
      
      <!-- Page Title & Header Bar -->
      <section data-aos="fade-down" class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/80">Role 7</span>
            <span class="text-xs font-bold text-slate-400 font-mono">MEDICAL TECHNOLOGIST</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">LABORATORY DASHBOARD</h1>
          <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Laboratory Requests, Specimen Tracking & Diagnostic Test Results</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
          <button onclick="openCreateSampleModal()" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-2 transition active:scale-95">
            <i data-lucide="scan-barcode" class="w-4 h-4 text-cyan-600"></i>
            <span>Log Specimen</span>
          </button>

          <button onclick="openCreateResultModal()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-2xl shadow-md shadow-indigo-600/20 flex items-center gap-2 transition active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Record Test Result</span>
          </button>
        </div>
      </section>

      <!-- 4 Summary KPI Cards -->
      <section data-aos="fade-up" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Pending Requests -->
        <div onclick="switchMainTab('requests')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
              <i data-lucide="clipboard-list" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200/60">+3 today</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display">12</span>
            <p class="text-xs font-bold text-slate-700 mt-1">Pending Requests</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Laboratory requests waiting for processing</p>
          </div>
        </div>

        <!-- Card 2: Samples in Process -->
        <div onclick="switchMainTab('samples')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 group-hover:scale-110 transition-transform">
              <i data-lucide="flask-conical" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200/60">5 in analyzers</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display">8</span>
            <p class="text-xs font-bold text-slate-700 mt-1">Samples in Process</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Samples currently being processed</p>
          </div>
        </div>

        <!-- Card 3: Results to Verify -->
        <div onclick="switchMainTab('results')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
              <i data-lucide="file-check-2" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200/60">2 STAT Priority</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display">5</span>
            <p class="text-xs font-bold text-slate-700 mt-1">Results to Verify</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Laboratory results awaiting verification</p>
          </div>
        </div>

        <!-- Card 4: Completed Today -->
        <div onclick="switchMainTab('reports')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
              <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">+18% vs avg</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display">24</span>
            <p class="text-xs font-bold text-slate-700 mt-1">Completed Today</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Laboratory tests completed today</p>
          </div>
        </div>
      </section>

      <!-- Centerpiece Laboratory Specimen Visualizer + Section Workload -->
      <section data-aos="fade-up" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Centerpiece Specimen Visualizer (2 Cols) -->
        <div class="lg:col-span-2">
          <?php render_lab_visualizer($samples[0]); ?>
        </div>

        <!-- Section Workload & Analyzer Status (1 Col) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
              <div>
                <h3 class="text-sm font-black text-slate-900 font-display">Laboratory Workload</h3>
                <p class="text-[11px] text-slate-400 font-medium">Active section distribution</p>
              </div>
              <span class="px-2 py-0.5 text-[10px] font-bold bg-indigo-50 text-indigo-700 rounded-md">Real-Time</span>
            </div>

            <!-- Progress distribution bars -->
            <div class="mt-5 space-y-4 text-xs">
              <div>
                <div class="flex justify-between font-bold text-slate-800 mb-1">
                  <span>Hematology</span>
                  <span class="text-indigo-600">42% (14 tests)</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                  <div class="bg-indigo-600 h-full rounded-full" style="width: 42%;"></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between font-bold text-slate-800 mb-1">
                  <span>Clinical Chemistry</span>
                  <span class="text-cyan-600">32% (11 tests)</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                  <div class="bg-cyan-600 h-full rounded-full" style="width: 32%;"></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between font-bold text-slate-800 mb-1">
                  <span>Clinical Microscopy & Urinalysis</span>
                  <span class="text-amber-600">16% (5 tests)</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                  <div class="bg-amber-500 h-full rounded-full" style="width: 16%;"></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between font-bold text-slate-800 mb-1">
                  <span>Serology / Immunology</span>
                  <span class="text-emerald-600">10% (3 tests)</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                  <div class="bg-emerald-500 h-full rounded-full" style="width: 10%;"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Analyzer Health Strip -->
          <div class="pt-4 border-t border-slate-100 mt-6 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
              <span class="font-bold text-slate-700">3 Analyzers Connected</span>
            </div>
            <button onclick="switchMainTab('catalog')" class="text-indigo-600 font-bold hover:underline text-[11px]">View Catalog →</button>
          </div>
        </div>
      </section>

      <!-- LABORATORY REQUEST QUEUE (Major Dashboard Card) -->
      <section data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 sm:p-7">
        
        <!-- Card Header & Filter Chips -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-100">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-lg font-black text-slate-900 tracking-tight font-display">LABORATORY REQUEST QUEUE</h2>
              <span class="px-2 py-0.5 text-xs font-bold bg-slate-100 text-slate-700 rounded-full"><?= count($requests) ?> total</span>
            </div>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Prioritized list of incoming doctor orders and requisition orders</p>
          </div>

          <!-- Filter Pills -->
          <div class="flex items-center gap-1.5 flex-wrap text-xs font-bold" id="queueFilterPills">
            <button onclick="filterRequestTable('all', this)" class="px-3 py-1.5 rounded-xl bg-slate-900 text-white transition active:scale-95">All</button>
            <button onclick="filterRequestTable('Pending', this)" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Pending</button>
            <button onclick="filterRequestTable('Received', this)" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Received</button>
            <button onclick="filterRequestTable('Processing', this)" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Processing</button>
            <button onclick="filterRequestTable('Completed', this)" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Completed</button>
            <button onclick="filterRequestTable('STAT', this)" class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">STAT</button>
          </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto mt-4">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                <th class="py-3 px-3">Request ID</th>
                <th class="py-3 px-3">Patient</th>
                <th class="py-3 px-3">Doctor</th>
                <th class="py-3 px-3">Laboratory Test</th>
                <th class="py-3 px-3">Priority</th>
                <th class="py-3 px-3">Date</th>
                <th class="py-3 px-3">Status</th>
                <th class="py-3 px-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800 font-medium" id="requestsTableBody">
              <?php foreach (array_slice($requests, 0, 8) as $req): ?>
                <tr class="searchable-row hover:bg-slate-50/80 transition-colors group" data-status="<?= e($req['status']) ?>" data-priority="<?= e($req['priority']) ?>">
                  
                  <!-- Request ID -->
                  <td class="py-3.5 px-3">
                    <span class="font-mono font-bold text-indigo-600 group-hover:underline cursor-pointer" onclick="openViewRequestModal('<?= e($req['request_id']) ?>', '<?= e($req['patient_name']) ?>', '<?= e($req['patient_id']) ?>', '<?= e($req['age']) ?> yrs • <?= e($req['gender']) ?>', '<?= e($req['doctor']) ?>', '<?= e($req['test']) ?>', '<?= e($req['priority']) ?>', '<?= format_date($req['date']) ?>', '<?= e($req['status']) ?>', '<?= e($req['sample_id']) ?>', '<?= e($req['clinical_notes']) ?>', '<?= e($req['category']) ?>')">
                      <?= e($req['request_id']) ?>
                    </span>
                  </td>

                  <!-- Patient -->
                  <td class="py-3.5 px-3">
                    <div class="flex items-center gap-2">
                      <span class="font-bold text-slate-900"><?= e($req['patient_name']) ?></span>
                      <span class="text-[10px] text-slate-400 font-mono">(<?= e($req['patient_id']) ?>)</span>
                    </div>
                  </td>

                  <!-- Doctor -->
                  <td class="py-3.5 px-3 text-slate-600">
                    <?= e($req['doctor']) ?>
                  </td>

                  <!-- Laboratory Test -->
                  <td class="py-3.5 px-3">
                    <span class="font-semibold text-slate-900"><?= e($req['test']) ?></span>
                    <span class="block text-[10px] text-slate-400"><?= e($req['category']) ?></span>
                  </td>

                  <!-- Priority -->
                  <td class="py-3.5 px-3">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] <?= get_priority_badge($req['priority']) ?>">
                      <?= e($req['priority']) ?>
                    </span>
                  </td>

                  <!-- Date -->
                  <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">
                    <?= format_date($req['date']) ?>
                  </td>

                  <!-- Status -->
                  <td class="py-3.5 px-3">
                    <span id="req-status-badge-<?= e($req['request_id']) ?>" class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= get_request_status_badge($req['status']) ?>">
                      <?= e($req['status']) ?>
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="py-3.5 px-3 text-right whitespace-nowrap">
                    <div class="flex items-center justify-end gap-1.5">
                      <button onclick="openViewRequestModal('<?= e($req['request_id']) ?>', '<?= e($req['patient_name']) ?>', '<?= e($req['patient_id']) ?>', '<?= e($req['age']) ?> yrs • <?= e($req['gender']) ?>', '<?= e($req['doctor']) ?>', '<?= e($req['test']) ?>', '<?= e($req['priority']) ?>', '<?= format_date($req['date']) ?>', '<?= e($req['status']) ?>', '<?= e($req['sample_id']) ?>', '<?= e($req['clinical_notes']) ?>', '<?= e($req['category']) ?>')" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition active:scale-95">
                        View
                      </button>

                      <button onclick="openCreateResultModal('<?= e($req['request_id']) ?>', '<?= e($req['sample_id']) ?>', '<?= e($req['patient_name']) ?>', '<?= e($req['test']) ?>')" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg transition active:scale-95">
                        Process
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between text-xs">
          <span class="text-slate-400">Showing 8 recent requests</span>
          <button onclick="switchMainTab('requests')" class="text-indigo-600 font-bold hover:underline">View All 22 Requests →</button>
        </div>
      </section>

      <!-- Two-Column Grid: Sample Tracking & Recent Results -->
      <section data-aos="fade-up" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: SAMPLE TRACKING SECTION -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card">
          <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
              <h3 class="text-base font-black text-slate-900 font-display">SAMPLE TRACKING</h3>
              <p class="text-xs text-slate-400 font-medium">Chain of custody & processing timeline</p>
            </div>
            <button onclick="openCreateSampleModal()" class="px-3 py-1.5 bg-cyan-50 text-cyan-700 border border-cyan-200/80 text-xs font-bold rounded-xl hover:bg-cyan-100 transition">
              + New Sample
            </button>
          </div>

          <!-- Sample Item Cards -->
          <div class="mt-5 space-y-4">
            <?php foreach (array_slice($samples, 0, 3) as $s): ?>
              <div class="p-4 rounded-2xl border border-slate-200/70 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition">
                <div class="flex items-center justify-between text-xs">
                  <div>
                    <span class="font-bold text-slate-900"><?= e($s['patient_name']) ?></span>
                    <span class="font-mono text-[11px] text-cyan-700 font-bold ml-1.5">(<?= e($s['sample_id']) ?>)</span>
                  </div>
                  <span class="px-2 py-0.5 rounded-full font-bold text-[10px] <?= get_sample_status_badge($s['status']) ?>">
                    <?= e($s['status']) ?>
                  </span>
                </div>
                
                <p class="text-xs text-slate-500 font-semibold mt-1"><?= e($s['test']) ?> • <span class="text-slate-400"><?= e($s['sample_type']) ?></span></p>

                <!-- Clean Animated Progress Line -->
                <div class="mt-3 pt-3 border-t border-slate-200/60">
                  <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 mb-1">
                    <span class="<?= $s['stage_index'] >= 0 ? 'text-emerald-600' : '' ?>">Collected</span>
                    <span class="<?= $s['stage_index'] >= 1 ? 'text-emerald-600' : '' ?>">Received</span>
                    <span class="<?= $s['stage_index'] >= 2 ? 'text-indigo-600 font-black' : '' ?>">Processing</span>
                    <span class="<?= $s['stage_index'] >= 3 ? 'text-purple-600' : '' ?>">Verify</span>
                    <span class="<?= $s['stage_index'] >= 4 ? 'text-emerald-600' : '' ?>">Done</span>
                  </div>
                  
                  <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 via-indigo-500 to-purple-500 h-full rounded-full transition-all duration-500" style="width: <?= min(100, max(15, ($s['stage_index'] + 1) * 20)) ?>%;"></div>
                  </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400">
                  <span>Collected: <?= format_time($s['collection_time']) ?></span>
                  <div class="flex items-center gap-2">
                    <button onclick="openViewSampleHistoryModal('<?= e($s['sample_id']) ?>')" class="text-slate-600 font-bold hover:underline">History</button>
                    <span>•</span>
                    <button onclick="openUpdateSampleStatusModal('<?= e($s['sample_id']) ?>', '<?= e($s['patient_name']) ?>')" class="text-indigo-600 font-bold hover:underline">Update</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="pt-4 mt-4 border-t border-slate-100 text-center">
            <button onclick="switchMainTab('samples')" class="text-xs font-bold text-cyan-700 hover:underline">View All Specimen Records →</button>
          </div>
        </div>

        <!-- Right: RECENT TEST RESULTS & ACTIVITY -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
              <div>
                <h3 class="text-base font-black text-slate-900 font-display">RECENT TEST RESULTS</h3>
                <p class="text-xs text-slate-400 font-medium">Diagnostic values & auto-flag evaluations</p>
              </div>
              <button onclick="openCreateResultModal()" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-xs font-bold rounded-xl hover:bg-emerald-100 transition">
                + Add Result
              </button>
            </div>

            <!-- Recent Result Cards -->
            <div class="mt-5 space-y-3.5">
              <?php foreach (array_slice($results, 0, 3) as $res): ?>
                <div class="p-4 rounded-2xl border border-slate-200/70 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition text-xs">
                  <div class="flex items-center justify-between">
                    <div>
                      <span class="font-bold text-slate-900 text-sm"><?= e($res['patient_name']) ?></span>
                      <span class="text-slate-400 text-[11px] block mt-0.5"><?= e($res['test']) ?> • <?= format_date($res['date']) ?></span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] <?= $res['status'] === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-purple-50 text-purple-700 border border-purple-200' ?>">
                      <?= e($res['status']) ?>
                    </span>
                  </div>

                  <p class="text-slate-600 font-medium mt-2 text-[11px] bg-white p-2 rounded-xl border border-slate-100">
                    <strong class="text-slate-800">Impression:</strong> <?= e($res['summary']) ?>
                  </p>

                  <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-200/60">
                    <span class="text-[10px] text-slate-400 font-mono"><?= e($res['technologist']) ?></span>
                    <div class="flex items-center gap-2">
                      <button onclick="openViewResultModal('<?= e($res['result_id']) ?>', '<?= e($res['request_id']) ?>', '<?= e($res['patient_name']) ?>', '<?= e($res['patient_id']) ?> (<?= e($res['age']) ?> <?= e($res['gender'][0]) ?>)', '<?= e($res['doctor']) ?>', '<?= e($res['status']) ?>', '<?= e($res['interpretation']) ?>')" class="text-indigo-600 font-bold hover:underline">View</button>
                      <span>•</span>
                      <button onclick="openGenerateReportModal()" class="text-slate-700 font-bold hover:underline">Report</button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="pt-4 mt-4 border-t border-slate-100 text-center">
            <button onclick="switchMainTab('results')" class="text-xs font-bold text-emerald-700 hover:underline">View All Laboratory Results →</button>
          </div>
        </div>
      </section>

      <!-- Bottom Grid: Recent Activity Feed & Live Department Alerts -->
      <section data-aos="fade-up" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Activity Feed -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card">
          <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-sm font-black text-slate-900 font-display">RECENT LAB ACTIVITY</h3>
            <span class="text-[11px] text-slate-400 font-medium">Audit Trail</span>
          </div>

          <div class="mt-4 space-y-3.5 text-xs">
            <?php foreach ($activities as $act): ?>
              <div class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 mt-0.5">
                  <i data-lucide="<?= e($act['icon']) ?>" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900"><?= e($act['title']) ?></span>
                    <span class="text-[10px] text-slate-400"><?= e($act['time']) ?></span>
                  </div>
                  <p class="text-[11px] text-slate-500 mt-0.5"><?= e($act['detail']) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Notifications Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card">
          <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <h3 class="text-sm font-black text-slate-900 font-display">LABORATORY NOTIFICATIONS</h3>
              <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-50 text-rose-700 rounded-full border border-rose-100">Priority Alerts</span>
            </div>
            <button onclick="markAllNotificationsRead()" class="text-xs font-bold text-indigo-600 hover:underline">Clear All</button>
          </div>

          <div class="mt-4 space-y-3 text-xs">
            <?php foreach ($notifications as $n): ?>
              <div onclick="handleNotificationClick('<?= $n['action_link'] ?>')" class="p-3 rounded-2xl border <?= $n['unread'] ? 'bg-indigo-50/30 border-indigo-200/80' : 'bg-slate-50 border-slate-200/70' ?> cursor-pointer hover:border-indigo-300 transition flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5 <?= $n['type'] === 'critical' ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600' ?>">
                  <i data-lucide="<?= $n['type'] === 'critical' ? 'alert-triangle' : 'alert-circle' ?>" class="w-4 h-4"></i>
                </div>
                <div>
                  <h4 class="font-bold text-slate-900"><?= e($n['title']) ?></h4>
                  <p class="text-[11px] text-slate-500 mt-0.5"><?= e($n['message']) ?></p>
                  <span class="text-[10px] text-slate-400 font-mono mt-1 block"><?= e($n['time']) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB 2: LABORATORY REQUESTS QUEUE DEDICATED VIEW                           -->
    <!-- ========================================================================= -->
    <div id="tab-section-requests" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Laboratory Request Queue</h2>
          <p class="text-xs text-slate-500 font-medium">Search, filter, and process all incoming clinical requisitions</p>
        </div>
        <div class="flex items-center gap-2">
          <input type="text" onkeyup="handleGlobalSearch(event)" placeholder="Search requests..." class="px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-500/20">
        </div>
      </div>

      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <div class="overflow-x-auto">
          <table id="medtechRequestsTable" class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                <th class="py-3 px-3">Request ID</th>
                <th class="py-3 px-3">Patient</th>
                <th class="py-3 px-3">Doctor</th>
                <th class="py-3 px-3">Laboratory Test</th>
                <th class="py-3 px-3">Priority</th>
                <th class="py-3 px-3">Date</th>
                <th class="py-3 px-3">Status</th>
                <th class="py-3 px-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
              <?php foreach ($requests as $req): ?>
                <tr class="searchable-row hover:bg-slate-50 transition" data-status="<?= e($req['status']) ?>" data-priority="<?= e($req['priority']) ?>">
                  <td class="py-3 px-3 font-mono font-bold text-indigo-600"><?= e($req['request_id']) ?></td>
                  <td class="py-3 px-3 font-bold text-slate-900"><?= e($req['patient_name']) ?> <span class="text-[10px] text-slate-400 font-mono">(<?= e($req['patient_id']) ?>)</span></td>
                  <td class="py-3 px-3 text-slate-600"><?= e($req['doctor']) ?></td>
                  <td class="py-3 px-3 font-semibold"><?= e($req['test']) ?></td>
                  <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-md text-[10px] <?= get_priority_badge($req['priority']) ?>"><?= e($req['priority']) ?></span></td>
                  <td class="py-3 px-3 text-slate-500"><?= format_date($req['date']) ?></td>
                  <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= get_request_status_badge($req['status']) ?>"><?= e($req['status']) ?></span></td>
                  <td class="py-3 px-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                      <button onclick="openViewRequestModal('<?= e($req['request_id']) ?>', '<?= e($req['patient_name']) ?>', '<?= e($req['patient_id']) ?>', '<?= e($req['age']) ?> yrs • <?= e($req['gender']) ?>', '<?= e($req['doctor']) ?>', '<?= e($req['test']) ?>', '<?= e($req['priority']) ?>', '<?= format_date($req['date']) ?>', '<?= e($req['status']) ?>', '<?= e($req['sample_id']) ?>', '<?= e($req['clinical_notes']) ?>', '<?= e($req['category']) ?>')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition">View</button>
                      <button onclick="openCreateResultModal('<?= e($req['request_id']) ?>', '<?= e($req['sample_id']) ?>', '<?= e($req['patient_name']) ?>', '<?= e($req['test']) ?>')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow-xs">Process</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB 3: SAMPLE TRACKING DEDICATED VIEW                                     -->
    <!-- ========================================================================= -->
    <div id="tab-section-samples" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Specimen & Sample Tracking</h2>
          <p class="text-xs text-slate-500 font-medium">Chain of custody, phlebotomy logging, and analyzer stage progression</p>
        </div>
        <button onclick="openCreateSampleModal()" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
          <i data-lucide="plus" class="w-4 h-4"></i>
          <span>Log New Specimen</span>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($samples as $s): ?>
          <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-5 flex flex-col justify-between searchable-row">
            <div>
              <div class="flex items-center justify-between">
                <span class="font-mono font-bold text-cyan-700 text-sm"><?= e($s['sample_id']) ?></span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= get_sample_status_badge($s['status']) ?>"><?= e($s['status']) ?></span>
              </div>
              <h3 class="font-bold text-slate-900 mt-2 text-sm"><?= e($s['patient_name']) ?></h3>
              <p class="text-xs text-indigo-600 font-semibold mt-0.5"><?= e($s['test']) ?></p>

              <div class="mt-3 p-2.5 bg-slate-50 rounded-xl text-[11px] space-y-1 text-slate-600">
                <div class="flex justify-between">
                  <span class="text-slate-400">Specimen:</span>
                  <span class="font-semibold text-slate-800"><?= e($s['sample_type']) ?></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-400">Rack / Slot:</span>
                  <span class="font-mono font-bold text-slate-800"><?= e($s['rack_location']) ?></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-400">Condition:</span>
                  <span class="font-semibold text-slate-800"><?= e($s['condition']) ?></span>
                </div>
              </div>

              <!-- Animated Progress Timeline -->
              <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-[9px] font-bold text-slate-400 mb-1">
                  <span>Collected</span>
                  <span>Received</span>
                  <span>Processing</span>
                  <span>Done</span>
                </div>
                <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                  <div class="bg-gradient-to-r from-cyan-500 to-indigo-600 h-full rounded-full" style="width: <?= min(100, max(15, ($s['stage_index'] + 1) * 20)) ?>%;"></div>
                </div>
              </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
              <button onclick="openViewSampleHistoryModal('<?= e($s['sample_id']) ?>')" class="text-slate-500 font-bold hover:text-slate-900">History Log</button>
              <button onclick="openUpdateSampleStatusModal('<?= e($s['sample_id']) ?>', '<?= e($s['patient_name']) ?>')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition">Update Stage</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB 4: TEST RESULTS DEDICATED VIEW                                        -->
    <!-- ========================================================================= -->
    <div id="tab-section-results" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Laboratory Test Results</h2>
          <p class="text-xs text-slate-500 font-medium">Multi-parameter diagnostic recordings, reference ranges, and auto-flagging</p>
        </div>
        <button onclick="openCreateResultModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
          <i data-lucide="plus" class="w-4 h-4"></i>
          <span>Record New Test Result</span>
        </button>
      </div>

      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <div class="overflow-x-auto">
          <table id="medtechResultsTable" class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                <th class="py-3 px-3">Result ID</th>
                <th class="py-3 px-3">Patient</th>
                <th class="py-3 px-3">Test</th>
                <th class="py-3 px-3">Status</th>
                <th class="py-3 px-3">Result Summary</th>
                <th class="py-3 px-3">Date</th>
                <th class="py-3 px-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
              <?php foreach ($results as $res): ?>
                <tr class="searchable-row hover:bg-slate-50 transition">
                  <td class="py-3.5 px-3 font-mono font-bold text-indigo-600"><?= e($res['result_id']) ?></td>
                  <td class="py-3.5 px-3 font-bold text-slate-900"><?= e($res['patient_name']) ?></td>
                  <td class="py-3.5 px-3 font-semibold"><?= e($res['test']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= $res['status'] === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-purple-50 text-purple-700 border border-purple-200' ?>"><?= e($res['status']) ?></span></td>
                  <td class="py-3.5 px-3 text-slate-600"><?= e($res['summary']) ?></td>
                  <td class="py-3.5 px-3 text-slate-500"><?= format_date($res['date']) ?></td>
                  <td class="py-3.5 px-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                      <button onclick="openViewResultModal('<?= e($res['result_id']) ?>', '<?= e($res['request_id']) ?>', '<?= e($res['patient_name']) ?>', '<?= e($res['patient_id']) ?> (<?= e($res['age']) ?> <?= e($res['gender'][0]) ?>)', '<?= e($res['doctor']) ?>', '<?= e($res['status']) ?>', '<?= e($res['interpretation']) ?>')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition">View</button>
                      <button onclick="openGenerateReportModal()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow-xs">Report</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB 5: TEST CATALOG DEDICATED VIEW                                        -->
    <!-- ========================================================================= -->
    <div id="tab-section-catalog" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Laboratory Test Catalog</h2>
          <p class="text-xs text-slate-500 font-medium">Manage hospital test items, required specimen types, and standard turnaround times</p>
        </div>
        <button onclick="openModal('catalogModal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
          <i data-lucide="plus" class="w-4 h-4"></i>
          <span>Create Test Catalog Entry</span>
        </button>
      </div>

      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <div class="overflow-x-auto">
          <table id="medtechCatalogTable" class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                <th class="py-3 px-3">Test ID</th>
                <th class="py-3 px-3">Test Name</th>
                <th class="py-3 px-3">Category / Section</th>
                <th class="py-3 px-3">Specimen Type</th>
                <th class="py-3 px-3">Volume</th>
                <th class="py-3 px-3">Turnaround (TAT)</th>
                <th class="py-3 px-3">Status</th>
                <th class="py-3 px-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
              <?php foreach ($catalog as $cat): ?>
                <tr class="searchable-row hover:bg-slate-50 transition">
                  <td class="py-3.5 px-3 font-mono font-bold text-slate-900"><?= e($cat['test_id']) ?></td>
                  <td class="py-3.5 px-3 font-bold text-slate-900"><?= e($cat['test_name']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100"><?= e($cat['category']) ?></span></td>
                  <td class="py-3.5 px-3 text-slate-600"><?= e($cat['specimen']) ?></td>
                  <td class="py-3.5 px-3 font-mono text-slate-500"><?= e($cat['volume']) ?></td>
                  <td class="py-3.5 px-3 text-slate-600 font-semibold"><?= e($cat['tat']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><?= e($cat['status']) ?></span></td>
                  <td class="py-3.5 px-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                      <button onclick="openModal('catalogModal')" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Edit"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></button>
                      <button onclick="showToast('Cannot delete standard laboratory test', 'warning')" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="Delete"><i data-lucide="trash" class="w-3.5 h-3.5"></i></button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB 6: LABORATORY REPORTS VIEW                                            -->
    <!-- ========================================================================= -->
    <div id="tab-section-reports" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Diagnostic Laboratory Reports</h2>
          <p class="text-xs text-slate-500 font-medium">Generate, review, and print official hospital pathology reports</p>
        </div>
        <button onclick="openGenerateReportModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span>Generate Laboratory Report</span>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($results as $res): ?>
          <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="font-mono text-xs font-bold text-slate-400"><?= e($res['result_id']) ?></span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">RELEASE READY</span>
              </div>
              <h3 class="font-bold text-slate-900 text-base mt-3"><?= e($res['patient_name']) ?></h3>
              <p class="text-xs text-indigo-600 font-semibold"><?= e($res['test']) ?></p>

              <p class="text-xs text-slate-500 mt-2 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <?= e($res['summary']) ?>
              </p>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
              <span class="text-slate-400 font-mono text-[11px]"><?= format_date($res['date']) ?></span>
              <button onclick="openGenerateReportModal()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Print Report</span>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB 7: REFERENCE RANGES DEDICATED VIEW                                    -->
    <!-- ========================================================================= -->
    <div id="tab-section-ranges" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Reference Range Configuration</h2>
          <p class="text-xs text-slate-500 font-medium">Biological intervals and critical threshold values applied during result evaluation</p>
        </div>
      </div>

      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <div class="overflow-x-auto">
          <table id="medtechRangesTable" class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                <th class="py-3 px-3">Test</th>
                <th class="py-3 px-3">Parameter</th>
                <th class="py-3 px-3">Gender</th>
                <th class="py-3 px-3">Age Range</th>
                <th class="py-3 px-3">Normal Range</th>
                <th class="py-3 px-3">Critical Limits</th>
                <th class="py-3 px-3">Unit</th>
                <th class="py-3 px-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
              <?php foreach ($referenceRanges as $ref): ?>
                <tr class="searchable-row hover:bg-slate-50 transition">
                  <td class="py-3.5 px-3 font-semibold text-slate-900"><?= e($ref['test']) ?></td>
                  <td class="py-3.5 px-3 font-bold text-indigo-700"><?= e($ref['parameter']) ?></td>
                  <td class="py-3.5 px-3 text-slate-600"><?= e($ref['gender']) ?></td>
                  <td class="py-3.5 px-3 text-slate-500"><?= e($ref['age_range']) ?></td>
                  <td class="py-3.5 px-3 font-mono font-bold text-emerald-700"><?= e($ref['min']) ?> – <?= e($ref['max']) ?></td>
                  <td class="py-3.5 px-3 font-mono text-rose-600 font-semibold">< <?= e($ref['crit_low']) ?> / > <?= e($ref['crit_high']) ?></td>
                  <td class="py-3.5 px-3 text-slate-500 font-medium"><?= e($ref['unit']) ?></td>
                  <td class="py-3.5 px-3 text-right">
                    <button onclick="showToast('Reference range verified with Pathologist standards', 'info')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-[11px]">Edit</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB 8: NOTIFICATIONS DEDICATED VIEW                                       -->
    <!-- ========================================================================= -->
    <div id="tab-section-notifications" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Department Notifications</h2>
          <p class="text-xs text-slate-500 font-medium">STAT order requisitions, quality control alerts, and recollection reminders</p>
        </div>
        <button onclick="markAllNotificationsRead()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition">
          Mark All Read
        </button>
      </div>

      <div class="space-y-3">
        <?php foreach ($notifications as $notif): ?>
          <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-5 flex items-start gap-4 hover:border-slate-300 transition">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 <?= $notif['type'] === 'critical' ? 'bg-rose-100 text-rose-600' : ($notif['type'] === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600') ?>">
              <i data-lucide="<?= $notif['type'] === 'critical' ? 'alert-triangle' : ($notif['type'] === 'warning' ? 'alert-circle' : 'info') ?>" class="w-5 h-5"></i>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <h4 class="font-bold text-slate-900 text-sm"><?= e($notif['title']) ?></h4>
                <span class="text-xs text-slate-400 font-mono"><?= e($notif['time']) ?></span>
              </div>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= e($notif['message']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </main>
</div>

<?php 
require_once __DIR__ . '/../includes/modals.php';
require_once __DIR__ . '/../includes/footer.php'; 
?>
