<?php
// Pharmacy/views/dashboard/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle   = 'Pharmacy Dashboard | Pharmacist • Tupi Municipal Hospital';
$activeMenu  = 'dashboard';
$currentUser = Session::getCurrentUser();

// ── Load Demo Data ────────────────────────────────────────
$patients      = getDemoPatients();
$prescriptions = getdemoPrescriptions();
$catalog       = getDemoCatalog();
$inventory     = getDemoInventory();
$dispensing    = getDemoDispensingRecords();
$stockMovements= getDemoStockMovements();
$lowStockAlerts= getDemoLowStockAlerts();
$expiryAlerts  = getDemoExpiryAlerts();
$activities    = getDemoRecentActivity();
$notifications = getDemoNotifications();

// ── Computed KPIs ─────────────────────────────────────────
$pendingRx       = count(array_filter($prescriptions, fn($r) => $r['status'] === 'Pending'));
$dispensedToday  = count(array_filter($dispensing, fn($d) => $d['disp_date'] === date('Y-m-d') && $d['status'] === 'Dispensed'));
$lowStockCount   = count(array_filter($inventory, fn($i) => $i['status'] === 'Low Stock' || $i['status'] === 'Out of Stock'));
$expirySoonCount = count(array_filter($expiryAlerts, fn($e) => $e['status'] === 'Expiring Soon'));
$readyToDispense = count(array_filter($prescriptions, fn($r) => $r['status'] === 'Ready for Dispensing'));
$totalInventoryUnits = array_sum(array_column($inventory, 'current_stock'));

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MAIN CONTENT WRAPPER                                    -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">

  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-5 sm:p-7 space-y-7 flex-1">


    <!-- ═══════════════════════════════════════════════════ -->
    <!-- TAB 1: PHARMACY DASHBOARD                           -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div id="tab-section-dashboard" class="space-y-7">

      <!-- Page Header ──────────────────────────────────── -->
      <section data-aos="fade-down" class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">Role 8</span>
            <span class="text-xs font-bold text-slate-400 font-mono">PHARMACIST / PHARMACY</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">PHARMACY DASHBOARD</h1>
          <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Prescription Processing, Medicine Dispensing & Inventory Management</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <button onclick="switchMainTab('prescriptions')"
                  class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-2 transition active:scale-95">
            <i data-lucide="clipboard-list" class="w-4 h-4 text-indigo-500"></i>
            <span>Prescription Queue</span>
          </button>
          <button onclick="openStockInModal()"
                  class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-2xl shadow-md shadow-sky-600/20 flex items-center gap-2 transition active:scale-95">
            <i data-lucide="package-plus" class="w-4 h-4"></i>
            <span>Stock In</span>
          </button>
        </div>
      </section>

      <!-- 4 KPI Summary Cards ──────────────────────────── -->
      <section data-aos="fade-up" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Card 1: Pending Rx -->
        <div onclick="switchMainTab('prescriptions')"
             class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
              <i data-lucide="clipboard-list" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200/60">+3 new</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $pendingRx ?></span>
            <p class="text-xs font-bold text-slate-700 mt-1">Pending Prescriptions</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Prescriptions waiting for verification</p>
          </div>
        </div>

        <!-- Card 2: Dispensed Today -->
        <div onclick="switchMainTab('dispensing')"
             class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
              <i data-lucide="pill" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">+5 vs yesterday</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $dispensedToday ?></span>
            <p class="text-xs font-bold text-slate-700 mt-1">Dispensed Today</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Medicines dispensed today</p>
          </div>
        </div>

        <!-- Card 3: Low Stock -->
        <div onclick="switchMainTab('alerts')"
             class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform animate-alert-throb">
              <i data-lucide="triangle-alert" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200/60">Needs reorder</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $lowStockCount ?></span>
            <p class="text-xs font-bold text-slate-700 mt-1">Low Stock Alerts</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Medicines below minimum stock</p>
          </div>
        </div>

        <!-- Card 4: Expiring Soon -->
        <div onclick="switchMainTab('alerts')"
             class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
              <i data-lucide="calendar-x" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200/60">Action needed</span>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $expirySoonCount ?></span>
            <p class="text-xs font-bold text-slate-700 mt-1">Expiring Soon</p>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Medicines approaching expiry</p>
          </div>
        </div>
      </section>

      <!-- Centerpiece Visualizer + Section Workload ──────── -->
      <section data-aos="fade-up" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Pharmacy Visualizer (Centerpiece) -->
        <div class="lg:col-span-1 bg-gradient-to-br from-slate-900 via-sky-950 to-blue-900 rounded-3xl p-6 relative overflow-hidden flex flex-col justify-between min-h-[320px]">
          <!-- Ambient Glow -->
          <div id="vizGlow" class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-20 blur-3xl transition-all duration-1000" style="background: #0ea5e9;"></div>
          <div class="absolute -bottom-16 -left-16 w-48 h-48 rounded-full bg-sky-400/10 blur-2xl"></div>

          <!-- Header Badge -->
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
              <div>
                <span class="text-[10px] font-black uppercase tracking-widest text-sky-400">PHARMACY STOCK</span>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Live Inventory Monitor</p>
              </div>
              <button onclick="cycleVisualizer()" class="p-2 bg-white/10 hover:bg-white/20 rounded-xl transition border border-white/10">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-white"></i>
              </button>
            </div>

            <!-- Medicine Bottle Visual -->
            <div class="flex items-center justify-center py-4">
              <div class="relative">
                <!-- Bottle SVG -->
                <svg width="80" height="110" viewBox="0 0 80 110" class="animate-float-bottle drop-shadow-2xl">
                  <!-- Bottle neck -->
                  <rect x="28" y="4" width="24" height="18" rx="4" fill="#0ea5e9" opacity="0.9"/>
                  <!-- Bottle cap -->
                  <rect x="24" y="0" width="32" height="10" rx="4" fill="#0284c7"/>
                  <!-- Bottle body -->
                  <rect x="10" y="20" width="60" height="82" rx="14" fill="#0ea5e9" opacity="0.85"/>
                  <!-- Label -->
                  <rect x="18" y="42" width="44" height="40" rx="6" fill="white" opacity="0.15"/>
                  <!-- Rx symbol -->
                  <text x="40" y="66" text-anchor="middle" font-size="18" font-weight="900" fill="white" font-family="Outfit">Rx</text>
                  <!-- Shine -->
                  <rect x="16" y="26" width="8" height="50" rx="4" fill="white" opacity="0.15"/>
                </svg>
                <!-- Floating pill -->
                <div class="absolute -top-2 -right-8 animate-float-pill">
                  <svg width="36" height="14" viewBox="0 0 36 14">
                    <rect width="36" height="14" rx="7" fill="#8b5cf6" opacity="0.9"/>
                    <line x1="18" y1="0" x2="18" y2="14" stroke="white" stroke-width="1.5" opacity="0.5"/>
                  </svg>
                </div>
                <!-- Scan line -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden rounded-full" style="width:80px;height:110px;">
                  <div class="absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-sky-400 to-transparent opacity-60 animate-scan-line"></div>
                </div>
              </div>
            </div>

            <!-- Medicine Info -->
            <div class="text-center mt-3">
              <h3 id="vizMedName" class="text-lg font-black text-white font-display leading-tight">Amoxicillin 500mg</h3>
              <div class="mt-1 flex items-center justify-center gap-3">
                <span id="vizStock" class="text-2xl font-black font-display" style="color:#0ea5e9">120 Units</span>
              </div>
              <div class="mt-2 flex items-center justify-center gap-2 flex-wrap">
                <span class="text-[10px] font-bold text-white/60 font-mono" id="vizBatch">B-2026-011</span>
                <span class="text-[10px] font-black uppercase tracking-wider" id="vizStatus" style="color:#0ea5e9">IN STOCK</span>
              </div>
            </div>
          </div>

          <!-- Inventory mini-stats -->
          <div class="relative z-10 mt-5 grid grid-cols-3 gap-2">
            <div class="text-center bg-white/8 rounded-xl py-2 px-1 border border-white/10">
              <p class="text-lg font-black text-white font-display"><?= count($inventory) ?></p>
              <p class="text-[9px] text-white/50 font-medium uppercase tracking-wider">Medicines</p>
            </div>
            <div class="text-center bg-white/8 rounded-xl py-2 px-1 border border-white/10">
              <p class="text-lg font-black text-white font-display"><?= number_format($totalInventoryUnits) ?></p>
              <p class="text-[9px] text-white/50 font-medium uppercase tracking-wider">Total Units</p>
            </div>
            <div class="text-center bg-white/8 rounded-xl py-2 px-1 border border-white/10">
              <p class="text-lg font-black text-amber-400 font-display"><?= $lowStockCount ?></p>
              <p class="text-[9px] text-white/50 font-medium uppercase tracking-wider">Low Stock</p>
            </div>
          </div>
        </div>

        <!-- Quick Stats: Prescription Workload + Inventory Breakdown -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">

          <!-- Prescription Pipeline -->
          <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-5">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-sm font-black text-slate-900 font-display">Prescription Pipeline</h3>
                <p class="text-[11px] text-slate-400 font-medium">Today's workflow</p>
              </div>
              <button onclick="switchMainTab('prescriptions')" class="text-xs font-bold text-sky-600 hover:underline">View All</button>
            </div>
            <div class="space-y-3">
              <?php
              $rxPipeline = [
                ['label'=>'Pending',               'count'=>$pendingRx,    'color'=>'bg-amber-400',  'text'=>'text-amber-700',  'pct'=>round($pendingRx/30*100)],
                ['label'=>'Under Verification',    'count'=>count(array_filter($prescriptions,fn($r)=>$r['status']==='Under Verification')),    'color'=>'bg-blue-400',   'text'=>'text-blue-700',   'pct'=>12],
                ['label'=>'Verified',              'count'=>count(array_filter($prescriptions,fn($r)=>$r['status']==='Verified')),              'color'=>'bg-indigo-400', 'text'=>'text-indigo-700', 'pct'=>15],
                ['label'=>'Ready for Dispensing',  'count'=>$readyToDispense, 'color'=>'bg-emerald-400','text'=>'text-emerald-700','pct'=>round($readyToDispense/30*100)],
                ['label'=>'Dispensed Today',       'count'=>$dispensedToday,  'color'=>'bg-teal-400',   'text'=>'text-teal-700',   'pct'=>round($dispensedToday/30*100)],
              ];
              foreach ($rxPipeline as $p): ?>
              <div>
                <div class="flex items-center justify-between text-xs mb-1">
                  <span class="font-semibold text-slate-700"><?= $p['label'] ?></span>
                  <span class="font-black <?= $p['text'] ?>"><?= $p['count'] ?></span>
                </div>
                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                  <div class="h-full <?= $p['color'] ?> rounded-full stock-bar" style="width:<?= $p['pct'] ?>%"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Inventory Status Doughnut -->
          <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-5">
            <div class="flex items-center justify-between mb-3">
              <div>
                <h3 class="text-sm font-black text-slate-900 font-display">Inventory Status</h3>
                <p class="text-[11px] text-slate-400 font-medium">Stock distribution</p>
              </div>
              <button onclick="switchMainTab('inventory')" class="text-xs font-bold text-sky-600 hover:underline">View All</button>
            </div>
            <div class="h-44">
              <canvas id="inventoryStatusChart"></canvas>
            </div>
          </div>

          <!-- Stock by Category -->
          <div class="sm:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-card p-5">
            <div class="flex items-center justify-between mb-3">
              <div>
                <h3 class="text-sm font-black text-slate-900 font-display">Stock by Category</h3>
                <p class="text-[11px] text-slate-400 font-medium">Total units in inventory</p>
              </div>
            </div>
            <div class="h-40">
              <canvas id="categoryStockChart"></canvas>
            </div>
          </div>
        </div>
      </section>

      <!-- Prescription Queue (Dashboard Preview) + Activity ─ -->
      <section data-aos="fade-up" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Prescription Queue Preview -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <i data-lucide="clipboard-list" class="w-4 h-4 text-indigo-500"></i>
              <h3 class="text-sm font-black text-slate-900 font-display">Prescription Queue</h3>
              <span class="px-2 py-0.5 text-[10px] font-bold bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100"><?= count(array_filter($prescriptions,fn($r)=>in_array($r['status'],['Pending','Under Verification']))) ?> active</span>
            </div>
            <button onclick="switchMainTab('prescriptions')" class="text-xs font-bold text-sky-600 hover:underline flex items-center gap-1">
              View all <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </button>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 bg-slate-50/50">
                  <th class="py-3 px-4">Rx ID</th>
                  <th class="py-3 px-4">Patient</th>
                  <th class="py-3 px-4">Medicine</th>
                  <th class="py-3 px-4">Priority</th>
                  <th class="py-3 px-4">Status</th>
                  <th class="py-3 px-4">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <?php foreach (array_slice($prescriptions, 0, 6) as $rx): ?>
                <tr class="searchable-row hover:bg-slate-50 transition">
                  <td class="py-3 px-4 font-mono font-bold text-slate-500 text-[11px]"><?= e($rx['rx_id']) ?></td>
                  <td class="py-3 px-4 font-semibold text-slate-900"><?= e($rx['patient_name']) ?></td>
                  <td class="py-3 px-4 text-slate-600 max-w-[140px] truncate"><?= e($rx['medicine']) ?></td>
                  <td class="py-3 px-4">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= get_priority_badge($rx['priority']) ?>">
                      <?= e($rx['priority']) ?>
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= get_rx_status_badge($rx['status']) ?>">
                      <?= e($rx['status']) ?>
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <button onclick="openViewRxModal(<?= htmlspecialchars(json_encode($rx), ENT_QUOTES) ?>)"
                            class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold rounded-lg transition text-[10px] flex items-center gap-1">
                      <i data-lucide="eye" class="w-3 h-3"></i> View
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <i data-lucide="activity" class="w-4 h-4 text-sky-500"></i>
              <h3 class="text-sm font-black text-slate-900 font-display">Recent Activity</h3>
            </div>
            <button onclick="switchMainTab('activity')" class="text-xs font-bold text-sky-600 hover:underline">View all</button>
          </div>
          <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            <?php foreach (array_slice($activities, 0, 6) as $act):
              $bgColor = match($act['color']) {
                'emerald'=>'bg-emerald-50 text-emerald-600',
                'blue'=>'bg-blue-50 text-blue-600',
                'amber'=>'bg-amber-50 text-amber-600',
                'teal'=>'bg-teal-50 text-teal-600',
                'rose'=>'bg-rose-50 text-rose-600',
                'orange'=>'bg-orange-50 text-orange-600',
                default=>'bg-slate-50 text-slate-600'
              };
            ?>
            <div class="px-4 py-3 hover:bg-slate-50/60 transition">
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl <?= $bgColor ?> flex items-center justify-center shrink-0 mt-0.5">
                  <i data-lucide="<?= e($act['icon']) ?>" class="w-3.5 h-3.5"></i>
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-bold text-slate-900"><?= e($act['title']) ?></p>
                  <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5"><?= e($act['message']) ?></p>
                  <p class="text-[10px] text-slate-400 font-medium mt-0.5"><?= e($act['time']) ?></p>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <!-- Low-Stock & Expiry Alert Summary ──────────────── -->
      <section data-aos="fade-up" class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Low-Stock Alerts -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <i data-lucide="triangle-alert" class="w-4 h-4 text-amber-500"></i>
              <h3 class="text-sm font-black text-slate-900 font-display">Low-Stock Alerts</h3>
              <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-700 rounded-full border border-amber-200"><?= count($lowStockAlerts) ?></span>
            </div>
            <button onclick="switchMainTab('alerts')" class="text-xs font-bold text-amber-600 hover:underline">View all</button>
          </div>
          <div class="divide-y divide-slate-100">
            <?php foreach (array_slice($lowStockAlerts, 0, 5) as $alert): 
              $pct = $alert['current_stock'] > 0 ? min(100, round($alert['current_stock']/$alert['min_stock']*100)) : 0;
              $barColor = $alert['current_stock'] === 0 ? 'bg-rose-500' : ($pct < 50 ? 'bg-amber-500' : 'bg-yellow-400');
            ?>
            <div class="px-5 py-3.5 hover:bg-slate-50/60 transition">
              <div class="flex items-center justify-between mb-1.5">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="w-6 h-6 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                    <i data-lucide="pill" class="w-3 h-3 text-amber-600"></i>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate"><?= e($alert['medicine']) ?></p>
                    <p class="text-[10px] text-slate-400 font-mono"><?= e($alert['batch']) ?></p>
                  </div>
                </div>
                <div class="text-right shrink-0 ml-2">
                  <span class="text-xs font-black <?= $alert['current_stock'] === 0 ? 'text-rose-600' : 'text-amber-700' ?>"><?= $alert['current_stock'] ?></span>
                  <span class="text-[10px] text-slate-400 font-medium"> / <?= $alert['min_stock'] ?> min</span>
                </div>
              </div>
              <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full <?= $barColor ?> rounded-full stock-bar" style="width:<?= $pct ?>%"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Expiry Alerts -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <i data-lucide="calendar-x" class="w-4 h-4 text-orange-500"></i>
              <h3 class="text-sm font-black text-slate-900 font-display">Expiry Alerts</h3>
              <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-50 text-orange-700 rounded-full border border-orange-200"><?= count($expiryAlerts) ?></span>
            </div>
            <button onclick="switchMainTab('alerts')" class="text-xs font-bold text-orange-600 hover:underline">View all</button>
          </div>
          <div class="divide-y divide-slate-100">
            <?php foreach (array_slice($expiryAlerts, 0, 5) as $e): 
              $isExpired = $e['days_left'] <= 0;
              $isUrgent  = $e['days_left'] > 0 && $e['days_left'] <= 15;
            ?>
            <div class="px-5 py-3.5 hover:bg-slate-50/60 transition">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="w-6 h-6 rounded-lg <?= $isExpired ? 'bg-rose-50' : ($isUrgent ? 'bg-orange-50' : 'bg-amber-50') ?> flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-3 h-3 <?= $isExpired ? 'text-rose-600' : ($isUrgent ? 'text-orange-600' : 'text-amber-600') ?>"></i>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate"><?= e($e['medicine']) ?></p>
                    <p class="text-[10px] text-slate-400 font-mono"><?= e($e['batch']) ?></p>
                  </div>
                </div>
                <div class="shrink-0 ml-2 text-right">
                  <?php if ($isExpired): ?>
                    <span class="px-2 py-0.5 text-[10px] font-black bg-rose-100 text-rose-700 rounded-full border border-rose-200">EXPIRED</span>
                  <?php elseif ($isUrgent): ?>
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full border border-orange-200"><?= $e['days_left'] ?> days</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-amber-50 text-amber-700 rounded-full border border-amber-200"><?= $e['days_left'] ?> days</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </div><!-- /TAB 1 -->


    <!-- ═══════════════════════════════════════════════════ -->
    <!-- TAB 2: PRESCRIPTION QUEUE                          -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div id="tab-section-prescriptions" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Prescription Queue</h2>
          <p class="text-xs text-slate-500 font-medium">Verify, process and track electronic prescriptions</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <div id="rxFilterPills" class="flex gap-1.5 flex-wrap">
            <?php
            $statuses = ['all'=>'All', 'Pending'=>'Pending', 'Under Verification'=>'Verifying', 'Verified'=>'Verified', 'Ready for Dispensing'=>'Ready', 'Dispensed'=>'Dispensed', 'Rejected'=>'Rejected'];
            $first = true;
            foreach ($statuses as $val=>$label):
            ?>
            <button onclick="filterRxTable('<?= $val ?>', this)"
                    class="rx-filter-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $first ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
              <?= $label ?>
            </button>
            <?php $first=false; endforeach; ?>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
          <table id="pharmacyRxTable" class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 bg-slate-50/50">
                <th class="py-3.5 px-4">Rx ID</th>
                <th class="py-3.5 px-4">Patient</th>
                <th class="py-3.5 px-4">Doctor</th>
                <th class="py-3.5 px-4">Medicine</th>
                <th class="py-3.5 px-4">Qty</th>
                <th class="py-3.5 px-4">Date</th>
                <th class="py-3.5 px-4">Priority</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody id="rxTableBody" class="divide-y divide-slate-100 text-slate-800 font-medium">
              <?php foreach ($prescriptions as $rx): ?>
              <tr class="searchable-row rx-table-row transition cursor-pointer" data-status="<?= e($rx['status']) ?>">
                <td class="py-3.5 px-4 font-mono font-bold text-slate-400 text-[11px]"><?= e($rx['rx_id']) ?></td>
                <td class="py-3.5 px-4 font-semibold text-slate-900"><?= e($rx['patient_name']) ?></td>
                <td class="py-3.5 px-4 text-slate-600"><?= e(explode(',', $rx['doctor'])[0]) ?></td>
                <td class="py-3.5 px-4 text-slate-700 max-w-[160px] truncate"><?= e($rx['medicine']) ?></td>
                <td class="py-3.5 px-4 text-slate-600"><?= e($rx['quantity']) ?></td>
                <td class="py-3.5 px-4 text-slate-500 font-mono"><?= e($rx['rx_date']) ?></td>
                <td class="py-3.5 px-4">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= get_priority_badge($rx['priority']) ?>">
                    <?= e($rx['priority']) ?>
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= get_rx_status_badge($rx['status']) ?>">
                    <?= e($rx['status']) ?>
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <button onclick="openViewRxModal(<?= htmlspecialchars(json_encode($rx),ENT_QUOTES) ?>)"
                            class="p-1.5 bg-slate-50 hover:bg-sky-50 text-slate-600 hover:text-sky-600 rounded-lg transition" title="View">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </button>
                    <button onclick="openVerifyRxModal(<?= htmlspecialchars(json_encode($rx),ENT_QUOTES) ?>)"
                            class="p-1.5 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 rounded-lg transition" title="Verify">
                      <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    </button>
                    <?php if ($rx['status'] === 'Ready for Dispensing' || $rx['status'] === 'Verified'): ?>
                    <button onclick="openDispensingModal(<?= htmlspecialchars(json_encode($rx),ENT_QUOTES) ?>)"
                            class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg transition" title="Dispense">
                      <i data-lucide="pill" class="w-3.5 h-3.5"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- /TAB 2 -->


    <!-- ═══════════════════════════════════════════════════ -->
    <!-- TAB 3: MEDICINE DISPENSING                          -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div id="tab-section-dispensing" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Medicine Dispensing</h2>
          <p class="text-xs text-slate-500 font-medium">Dispense medicines and manage patient dispensing history</p>
        </div>
      </div>

      <!-- Ready to Dispense -->
      <div>
        <h3 class="text-sm font-black text-slate-700 mb-3 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Ready for Dispensing
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach (array_filter($prescriptions, fn($r) => $r['status'] === 'Ready for Dispensing' || ($r['status'] === 'Verified' && in_array($r['rx_id'],['RX-2026-009','RX-2026-013','RX-2026-023']))) as $rx): ?>
          <div class="bg-white rounded-3xl border border-emerald-200/60 shadow-card p-5 flex flex-col justify-between group hover:shadow-card-hover transition">
            <div>
              <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] font-bold text-slate-400"><?= e($rx['rx_id']) ?></span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= get_priority_badge($rx['priority']) ?>"><?= e($rx['priority']) ?></span>
              </div>
              <h4 class="font-black text-slate-900 text-base"><?= e($rx['patient_name']) ?></h4>
              <p class="text-xs text-sky-700 font-bold mt-1 flex items-center gap-1.5">
                <i data-lucide="pill" class="w-3.5 h-3.5"></i>
                <?= e($rx['medicine']) ?>
              </p>
              <p class="text-xs text-slate-500 mt-1"><?= e($rx['quantity']) ?> • <?= e($rx['frequency']) ?> • <?= e($rx['route']) ?></p>
              <p class="text-[11px] text-slate-400 mt-1">Prescribed by <?= e(explode(',', $rx['doctor'])[0]) ?></p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex gap-2">
              <button onclick="openDispensingModal(<?= htmlspecialchars(json_encode($rx),ENT_QUOTES) ?>)"
                      class="flex-1 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5">
                <i data-lucide="pill" class="w-3.5 h-3.5"></i>
                Dispense Medicine
              </button>
              <button onclick="openViewRxModal(<?= htmlspecialchars(json_encode($rx),ENT_QUOTES) ?>)"
                      class="px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold rounded-xl transition border border-slate-200">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Patient Dispensing History -->
      <div>
        <h3 class="text-sm font-black text-slate-700 mb-3 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-slate-400"></span> Patient Dispensing History
        </h3>
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="overflow-x-auto">
            <table id="pharmacyDispensingTable" class="w-full text-left text-xs">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 bg-slate-50/50">
                  <th class="py-3.5 px-4">Disp ID</th>
                  <th class="py-3.5 px-4">Date</th>
                  <th class="py-3.5 px-4">Patient</th>
                  <th class="py-3.5 px-4">Medicine</th>
                  <th class="py-3.5 px-4">Qty Dispensed</th>
                  <th class="py-3.5 px-4">Rx ID</th>
                  <th class="py-3.5 px-4">Dispensed By</th>
                  <th class="py-3.5 px-4">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php foreach ($dispensing as $d): ?>
                <tr class="searchable-row hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 font-mono font-bold text-slate-400 text-[11px]"><?= e($d['disp_id']) ?></td>
                  <td class="py-3.5 px-4 text-slate-500 font-mono"><?= e($d['disp_date']) ?></td>
                  <td class="py-3.5 px-4 font-semibold text-slate-900"><?= e($d['patient_name']) ?></td>
                  <td class="py-3.5 px-4 text-slate-700 max-w-[160px] truncate"><?= e($d['medicine']) ?></td>
                  <td class="py-3.5 px-4 font-bold text-sky-700"><?= e($d['qty_dispensed']) ?></td>
                  <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]"><?= e($d['rx_id']) ?></td>
                  <td class="py-3.5 px-4 text-slate-600"><?= e($d['dispensed_by']) ?></td>
                  <td class="py-3.5 px-4">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $d['status']==='Dispensed' ? 'bg-teal-50 text-teal-700 border-teal-200' : 'bg-amber-50 text-amber-700 border-amber-200' ?>">
                      <?= e($d['status']) ?>
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div><!-- /TAB 3 -->


    <!-- ═══════════════════════════════════════════════════ -->
    <!-- TAB 4: PHARMACY INVENTORY                          -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div id="tab-section-inventory" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Pharmacy Inventory</h2>
          <p class="text-xs text-slate-500 font-medium">Manage medicine stock levels, batches and expiry dates</p>
        </div>
        <div class="flex items-center gap-2">
          <button onclick="openStockInModal()"
                  class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
            <i data-lucide="package-plus" class="w-4 h-4"></i> Stock In
          </button>
          <button onclick="openDisposalModal()"
                  class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition border border-rose-200 flex items-center gap-1.5">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Dispose
          </button>
        </div>
      </div>

      <!-- Inventory KPIs -->
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <?php
        $inStock   = count(array_filter($inventory, fn($i)=>$i['status']==='In Stock'));
        $lowStock  = count(array_filter($inventory, fn($i)=>$i['status']==='Low Stock'));
        $outStock  = count(array_filter($inventory, fn($i)=>$i['status']==='Out of Stock'));
        $expiring  = count(array_filter($inventory, fn($i)=>$i['status']==='Expiring Soon'));
        $expired   = count(array_filter($inventory, fn($i)=>$i['status']==='Expired'));
        $kpiData   = [
          ['Total Medicines','catalog', count($catalog), 'bg-sky-50 text-sky-600 border-sky-100', 'box'],
          ['Total Units', 'inventory', number_format($totalInventoryUnits), 'bg-indigo-50 text-indigo-600 border-indigo-100', 'layers'],
          ['Low Stock',   'alerts',   $lowStock,   'bg-amber-50 text-amber-600 border-amber-100',  'triangle-alert'],
          ['Out of Stock','alerts',   $outStock,   'bg-rose-50 text-rose-600 border-rose-100',     'package-x'],
          ['Expiring Soon','alerts',  $expiring,   'bg-orange-50 text-orange-600 border-orange-100','calendar-x'],
        ];
        foreach ($kpiData as [$label, $tab, $val, $classes, $icon]): ?>
        <div onclick="switchMainTab('<?= $tab ?>')"
             class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-4 text-center cursor-pointer hover:shadow-card-hover transition group">
          <div class="w-9 h-9 rounded-xl <?= $classes ?> border flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
            <i data-lucide="<?= $icon ?>" class="w-4 h-4"></i>
          </div>
          <p class="text-xl font-black text-slate-900 font-display"><?= $val ?></p>
          <p class="text-[10px] text-slate-500 font-medium mt-0.5"><?= $label ?></p>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Filter Pills -->
      <div class="flex gap-1.5 flex-wrap">
        <?php
        $invStatuses = ['all'=>'All', 'In Stock'=>'In Stock', 'Low Stock'=>'Low Stock', 'Out of Stock'=>'Out of Stock', 'Expiring Soon'=>'Expiring Soon', 'Expired'=>'Expired'];
        $first = true;
        foreach ($invStatuses as $val=>$label): ?>
        <button onclick="filterInventory('<?= $val ?>', this)"
                class="inv-filter-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $first ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
          <?= $label ?>
        </button>
        <?php $first=false; endforeach; ?>
      </div>

      <!-- Inventory Table -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
          <table id="pharmacyInventoryTable" class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 bg-slate-50/50">
                <th class="py-3.5 px-4">Medicine</th>
                <th class="py-3.5 px-4">Category</th>
                <th class="py-3.5 px-4">Batch</th>
                <th class="py-3.5 px-4">Supplier</th>
                <th class="py-3.5 px-4">Current</th>
                <th class="py-3.5 px-4">Minimum</th>
                <th class="py-3.5 px-4">Expiry</th>
                <th class="py-3.5 px-4">Unit</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody id="inventoryTableBody" class="divide-y divide-slate-100 text-slate-800 font-medium">
              <?php foreach ($inventory as $inv): ?>
              <tr class="searchable-row hover:bg-slate-50 transition" data-status="<?= e($inv['status']) ?>">
                <td class="py-3.5 px-4 font-semibold text-slate-900"><?= e($inv['medicine']) ?></td>
                <td class="py-3.5 px-4 text-slate-500"><?= e($inv['category']) ?></td>
                <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]"><?= e($inv['batch']) ?></td>
                <td class="py-3.5 px-4 text-slate-500 text-[11px] max-w-[120px] truncate"><?= e($inv['supplier']) ?></td>
                <td class="py-3.5 px-4 font-black <?= $inv['current_stock'] === 0 ? 'text-rose-600' : ($inv['current_stock'] < $inv['min_stock'] ? 'text-amber-600' : 'text-emerald-700') ?>">
                  <?= number_format($inv['current_stock']) ?>
                </td>
                <td class="py-3.5 px-4 text-slate-500"><?= number_format($inv['min_stock']) ?></td>
                <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]"><?= e(date('M Y', strtotime($inv['expiry_date']))) ?></td>
                <td class="py-3.5 px-4 text-slate-500"><?= e($inv['unit']) ?></td>
                <td class="py-3.5 px-4">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= get_inventory_status_badge($inv['status']) ?>">
                    <?= e($inv['status']) ?>
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openStockInModal()"
                            class="p-1.5 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-lg transition" title="Add Stock">
                      <i data-lucide="plus" class="w-3 h-3"></i>
                    </button>
                    <button onclick="openDisposalModal()"
                            class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="Dispose">
                      <i data-lucide="trash-2" class="w-3 h-3"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Stock Movement History -->
      <div>
        <h3 class="text-sm font-black text-slate-700 mb-3 flex items-center gap-2">
          <i data-lucide="arrow-left-right" class="w-4 h-4 text-sky-600"></i> Stock Movement History
        </h3>
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 bg-slate-50/50">
                  <th class="py-3.5 px-4">Date</th>
                  <th class="py-3.5 px-4">Medicine</th>
                  <th class="py-3.5 px-4">Batch</th>
                  <th class="py-3.5 px-4">Type</th>
                  <th class="py-3.5 px-4">Qty</th>
                  <th class="py-3.5 px-4">Prev Stock</th>
                  <th class="py-3.5 px-4">New Stock</th>
                  <th class="py-3.5 px-4">Recorded By</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php foreach ($stockMovements as $mov):
                  $movColor = match($mov['movement_type']) {
                    'Stock-In'   => 'bg-teal-50 text-teal-700 border-teal-200',
                    'Dispensed'  => 'bg-sky-50 text-sky-700 border-sky-200',
                    'Disposed'   => 'bg-rose-50 text-rose-700 border-rose-200',
                    'Adjustment' => 'bg-slate-100 text-slate-700 border-slate-200',
                    default      => 'bg-slate-100 text-slate-600 border-slate-200',
                  };
                ?>
                <tr class="searchable-row hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]"><?= e($mov['date']) ?></td>
                  <td class="py-3.5 px-4 font-semibold text-slate-900"><?= e($mov['medicine']) ?></td>
                  <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]"><?= e($mov['batch']) ?></td>
                  <td class="py-3.5 px-4">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $movColor ?>"><?= e($mov['movement_type']) ?></span>
                  </td>
                  <td class="py-3.5 px-4 font-black <?= $mov['quantity'] < 0 ? 'text-rose-600' : 'text-emerald-700' ?>">
                    <?= $mov['quantity'] > 0 ? '+' : '' ?><?= e($mov['quantity']) ?>
                  </td>
                  <td class="py-3.5 px-4 text-slate-500"><?= number_format($mov['prev_stock']) ?></td>
                  <td class="py-3.5 px-4 font-bold text-slate-800"><?= number_format($mov['new_stock']) ?></td>
                  <td class="py-3.5 px-4 text-slate-500 text-[11px]"><?= e($mov['recorded_by']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div><!-- /TAB 4 -->


    <!-- ═══════════════════════════════════════════════════ -->
    <!-- TAB 5: MEDICINE CATALOG                             -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div id="tab-section-catalog" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Medicine Catalog</h2>
          <p class="text-xs text-slate-500 font-medium">Browse, search and manage the hospital medicine formulary</p>
        </div>
        <button onclick="showToast('Add medicine catalog entry form opening...','info')"
                class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
          <i data-lucide="plus" class="w-4 h-4"></i> Add Medicine
        </button>
      </div>

      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
          <table id="pharmacyCatalogTable" class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 bg-slate-50/50">
                <th class="py-3.5 px-4">Med ID</th>
                <th class="py-3.5 px-4">Generic Name</th>
                <th class="py-3.5 px-4">Brand Name</th>
                <th class="py-3.5 px-4">Category</th>
                <th class="py-3.5 px-4">Dosage Form</th>
                <th class="py-3.5 px-4">Strength</th>
                <th class="py-3.5 px-4">Route</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
              <?php foreach ($catalog as $med): ?>
              <tr class="searchable-row hover:bg-slate-50 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-slate-400 text-[11px]"><?= e($med['med_id']) ?></td>
                <td class="py-3.5 px-4 font-semibold text-slate-900"><?= e($med['generic_name']) ?></td>
                <td class="py-3.5 px-4 text-slate-600"><?= e($med['brand_name']) ?></td>
                <td class="py-3.5 px-4">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-100"><?= e($med['category']) ?></span>
                </td>
                <td class="py-3.5 px-4 text-slate-600"><?= e($med['dosage_form']) ?></td>
                <td class="py-3.5 px-4 font-bold text-indigo-700"><?= e($med['strength']) ?></td>
                <td class="py-3.5 px-4 text-slate-500"><?= e($med['route']) ?></td>
                <td class="py-3.5 px-4">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $med['status']==='Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                    <?= e($med['status']) ?>
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="showToast('Viewing medicine: <?= e(addslashes($med['generic_name'])) ?>','info')"
                            class="p-1.5 bg-slate-50 hover:bg-sky-50 text-slate-600 hover:text-sky-600 rounded-lg transition" title="View">
                      <i data-lucide="eye" class="w-3 h-3"></i>
                    </button>
                    <button onclick="showToast('Editing medicine catalog entry','info')"
                            class="p-1.5 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 rounded-lg transition" title="Edit">
                      <i data-lucide="pencil" class="w-3 h-3"></i>
                    </button>
                    <button onclick="showToast('Cannot delete standard formulary entry','warning')"
                            class="p-1.5 bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-rose-600 rounded-lg transition" title="Delete">
                      <i data-lucide="trash" class="w-3 h-3"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- /TAB 5 -->


    <!-- ═══════════════════════════════════════════════════ -->
    <!-- TAB 6: PHARMACY ALERTS                             -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div id="tab-section-alerts" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Pharmacy Alerts</h2>
          <p class="text-xs text-slate-500 font-medium">Low-stock warnings, expiry alerts and disposal records</p>
        </div>
        <div class="flex items-center gap-2">
          <button onclick="openStockInModal()"
                  class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
            <i data-lucide="package-plus" class="w-4 h-4"></i> Restock
          </button>
          <button onclick="openDisposalModal()"
                  class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition border border-rose-200 flex items-center gap-1.5">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Dispose
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Low-Stock Alerts Panel -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 bg-amber-50/40 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
              <i data-lucide="triangle-alert" class="w-4 h-4"></i>
            </div>
            <div>
              <h3 class="text-sm font-black text-slate-900 font-display">Low-Stock Alerts</h3>
              <p class="text-[10px] text-slate-500"><?= count($lowStockAlerts) ?> medicines require restocking</p>
            </div>
          </div>
          <div class="divide-y divide-slate-100">
            <?php foreach ($lowStockAlerts as $alert):
              $pct = $alert['current_stock'] > 0 ? min(100, round($alert['current_stock']/$alert['min_stock']*100)) : 0;
              $barColor = $alert['current_stock'] === 0 ? 'bg-rose-500' : ($pct < 40 ? 'bg-amber-500' : 'bg-yellow-400');
              $isOut = $alert['current_stock'] === 0;
            ?>
            <div class="px-5 py-4 hover:bg-slate-50/60 transition">
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl <?= $isOut ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' ?> flex items-center justify-center shrink-0 mt-0.5 animate-alert-throb">
                  <i data-lucide="<?= $isOut ? 'package-x' : 'package-minus' ?>" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs font-bold text-slate-900"><?= e($alert['medicine']) ?></p>
                      <p class="text-[10px] text-slate-400 font-mono mt-0.5"><?= e($alert['batch']) ?> • <?= e($alert['category']) ?></p>
                    </div>
                    <?php if ($isOut): ?>
                      <span class="px-2 py-0.5 text-[10px] font-black bg-rose-100 text-rose-700 rounded-full border border-rose-200 shrink-0">OUT OF STOCK</span>
                    <?php else: ?>
                      <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700 rounded-full border border-amber-200 shrink-0">LOW STOCK</span>
                    <?php endif; ?>
                  </div>
                  <div class="mt-2.5">
                    <div class="flex items-center justify-between text-[10px] mb-1">
                      <span class="text-slate-500 font-medium">Current: <strong class="<?= $isOut ? 'text-rose-600' : 'text-amber-700' ?>"><?= $alert['current_stock'] ?> <?= e($alert['unit']) ?>s</strong></span>
                      <span class="text-slate-400">Min: <?= $alert['min_stock'] ?> <?= e($alert['unit']) ?>s</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                      <div class="h-full <?= $barColor ?> rounded-full stock-bar" style="width:<?= $pct ?>%"></div>
                    </div>
                  </div>
                  <div class="mt-2 flex gap-1.5">
                    <button onclick="openStockInModal(); showToast('Preparing stock-in for <?= e(addslashes($alert['medicine'])) ?>','info')"
                            class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 text-[10px] font-bold rounded-lg transition flex items-center gap-1">
                      <i data-lucide="package-plus" class="w-3 h-3"></i> Restock
                    </button>
                    <button onclick="switchMainTab('inventory')"
                            class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-lg transition">
                      View Inventory
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Expiry Alerts Panel -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 bg-orange-50/40 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
              <i data-lucide="calendar-x" class="w-4 h-4"></i>
            </div>
            <div>
              <h3 class="text-sm font-black text-slate-900 font-display">Expiry Alerts</h3>
              <p class="text-[10px] text-slate-500"><?= count($expiryAlerts) ?> medicines expiring or already expired</p>
            </div>
          </div>
          <div class="divide-y divide-slate-100">
            <?php foreach ($expiryAlerts as $e):
              $isExpired = $e['days_left'] <= 0;
              $isUrgent  = $e['days_left'] > 0 && $e['days_left'] <= 15;
            ?>
            <div class="px-5 py-4 hover:bg-slate-50/60 transition">
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl <?= $isExpired ? 'bg-rose-50 text-rose-600' : ($isUrgent ? 'bg-orange-50 text-orange-600' : 'bg-amber-50 text-amber-600') ?> flex items-center justify-center shrink-0 mt-0.5">
                  <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs font-bold text-slate-900"><?= e($e['medicine']) ?></p>
                      <p class="text-[10px] text-slate-400 font-mono mt-0.5"><?= e($e['batch']) ?></p>
                    </div>
                    <?php if ($isExpired): ?>
                      <span class="px-2 py-0.5 text-[10px] font-black bg-rose-100 text-rose-700 rounded-full border border-rose-200">EXPIRED</span>
                    <?php elseif ($isUrgent): ?>
                      <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full border border-orange-200"><?= $e['days_left'] ?> days left</span>
                    <?php else: ?>
                      <span class="px-2 py-0.5 text-[10px] font-semibold bg-amber-50 text-amber-700 rounded-full border border-amber-200"><?= $e['days_left'] ?> days left</span>
                    <?php endif; ?>
                  </div>
                  <div class="mt-2 flex items-center gap-3 text-[10px] text-slate-500">
                    <span>Expires: <strong class="<?= $isExpired ? 'text-rose-700' : 'text-slate-700' ?>"><?= e(date('d M Y', strtotime($e['expiry_date']))) ?></strong></span>
                    <span>Stock: <?= $e['current_stock'] ?> units</span>
                  </div>
                  <?php if ($isExpired): ?>
                  <div class="mt-2">
                    <button onclick="openDisposalModal(); showToast('Preparing disposal for expired <?= e(addslashes($e['medicine'])) ?>','warning')"
                            class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-bold rounded-lg transition flex items-center gap-1">
                      <i data-lucide="trash-2" class="w-3 h-3"></i> Dispose Expired
                    </button>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div><!-- /TAB 6 -->


    <!-- ═══════════════════════════════════════════════════ -->
    <!-- TAB 7: REPORTS & ACTIVITY LOG                       -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div id="tab-section-activity" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 font-display">Reports & Activity Log</h2>
          <p class="text-xs text-slate-500 font-medium">Recent pharmacy activity, audit trail and notifications</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activity Timeline -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-5">
          <h3 class="text-sm font-black text-slate-900 font-display mb-4 flex items-center gap-2">
            <i data-lucide="activity" class="w-4 h-4 text-sky-500"></i> Recent Pharmacy Activity
          </h3>
          <div class="relative space-y-4">
            <?php foreach ($activities as $idx => $act):
              $bgColor = match($act['color']) {
                'emerald'=>'bg-emerald-100 text-emerald-700',
                'blue'=>'bg-blue-100 text-blue-700',
                'amber'=>'bg-amber-100 text-amber-700',
                'teal'=>'bg-teal-100 text-teal-700',
                'rose'=>'bg-rose-100 text-rose-700',
                'orange'=>'bg-orange-100 text-orange-700',
                default=>'bg-slate-100 text-slate-700'
              };
            ?>
            <div class="flex gap-3">
              <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-xl <?= $bgColor ?> flex items-center justify-center shrink-0">
                  <i data-lucide="<?= e($act['icon']) ?>" class="w-3.5 h-3.5"></i>
                </div>
                <?php if ($idx < count($activities) - 1): ?>
                <div class="w-px flex-1 bg-slate-100 mt-1"></div>
                <?php endif; ?>
              </div>
              <div class="pb-4 min-w-0 flex-1">
                <div class="flex items-center justify-between">
                  <p class="text-xs font-bold text-slate-900"><?= e($act['title']) ?></p>
                  <span class="text-[10px] text-slate-400 font-mono shrink-0 ml-2"><?= e($act['time']) ?></span>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5"><?= e($act['message']) ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Notifications Log -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-black text-slate-900 font-display flex items-center gap-2">
              <i data-lucide="bell" class="w-4 h-4 text-amber-500"></i> Pharmacy Notifications
            </h3>
            <button onclick="markAllRead()" class="text-xs font-bold text-sky-600 hover:underline">Mark all read</button>
          </div>
          <div class="space-y-3">
            <?php foreach ($notifications as $notif): ?>
            <div class="flex gap-3 p-3 rounded-2xl border <?= $notif['unread'] ? 'bg-sky-50/30 border-sky-100' : 'bg-white border-slate-100' ?> transition hover:bg-slate-50">
              <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 <?= $notif['type']==='critical' ? 'bg-rose-100 text-rose-600' : ($notif['type']==='warning' ? 'bg-amber-100 text-amber-600' : 'bg-sky-100 text-sky-600') ?>">
                <i data-lucide="<?= $notif['type']==='critical' ? 'alert-triangle' : ($notif['type']==='warning' ? 'alert-circle' : 'info') ?>" class="w-3.5 h-3.5"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <h5 class="text-xs font-bold text-slate-900"><?= e($notif['title']) ?></h5>
                  <span class="text-[10px] text-slate-400 shrink-0 ml-2"><?= e($notif['time']) ?></span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed"><?= e($notif['message']) ?></p>
              </div>
              <?php if ($notif['unread']): ?>
              <div class="w-2 h-2 rounded-full bg-sky-500 shrink-0 mt-1"></div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div><!-- /TAB 7 -->


  </main>
</div><!-- /MAIN CONTENT WRAPPER -->

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
