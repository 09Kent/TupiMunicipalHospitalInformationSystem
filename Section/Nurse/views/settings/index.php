<?php
// Nurse/views/settings/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle = 'Settings & Role Permissions | Nurse Portal • Tupi Municipal Hospital';
$activeMenu = 'settings';

$currentUser = Session::getCurrentUser();

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
          <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">Role 6 Specification</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">Nurse Profile & System Privileges</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Credentials, duty shift schedule, and access control policies</p>
      </div>
    </section>

    <!-- Profile & Permissions Grid -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6" data-aos="fade-up">

      <!-- LEFT: Profile & Shift Card (5 Cols) -->
      <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card space-y-6 text-center sm:text-left">
          
          <div class="flex flex-col sm:flex-row items-center gap-5">
            <img src="<?= e($currentUser['avatar']) ?>" alt="Nurse Avatar" 
                 class="w-20 h-20 rounded-3xl object-cover ring-4 ring-teal-500/20 shadow-lg shrink-0">
            <div>
              <h3 class="text-lg font-black text-slate-900 font-display"><?= e($currentUser['name']) ?></h3>
              <p class="text-xs text-teal-600 font-bold mt-0.5">Nurse on Duty • General Ward</p>
              <p class="text-[11px] text-slate-400 font-mono mt-1"><?= e($currentUser['license']) ?></p>
            </div>
          </div>

          <div class="space-y-3 pt-4 border-t border-slate-100 text-xs">
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
              <span class="text-slate-400 font-medium">Assigned Shift</span>
              <span class="font-bold text-slate-800"><?= e($currentUser['shift']) ?></span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
              <span class="text-slate-400 font-medium">Staff Email</span>
              <span class="font-bold text-slate-800"><?= e($currentUser['email']) ?></span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
              <span class="text-slate-400 font-medium">Clinical Station</span>
              <span class="font-bold text-slate-800">Station 3 (North Wing)</span>
            </div>
          </div>

        </div>
      </div>

      <!-- RIGHT: Role Permissions Matrix (7 Cols) -->
      <div class="lg:col-span-7 space-y-6">
        
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card space-y-6">
          <div>
            <h3 class="text-base font-black text-slate-900 tracking-tight font-display">Role 6 Access Control Matrix</h3>
            <p class="text-xs text-slate-400 mt-0.5">Explicit authorization boundaries for Nurse On Duty</p>
          </div>

          <!-- Granted Capabilities -->
          <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
              <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
              <span>Authorized Nursing Capabilities</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
              <div class="p-2.5 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-center gap-2 text-emerald-950 font-medium">
                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                <span>Record & Update Vital Signs</span>
              </div>
              <div class="p-2.5 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-center gap-2 text-emerald-950 font-medium">
                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                <span>View Patient Records & Histories</span>
              </div>
              <div class="p-2.5 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-center gap-2 text-emerald-950 font-medium">
                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                <span>Update Inpatient Condition Status</span>
              </div>
              <div class="p-2.5 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-center gap-2 text-emerald-950 font-medium">
                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                <span>View Doctor Clinical Instructions</span>
              </div>
              <div class="p-2.5 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-center gap-2 text-emerald-950 font-medium">
                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                <span>Assist & Triage Patient Queues</span>
              </div>
              <div class="p-2.5 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-center gap-2 text-emerald-950 font-medium">
                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                <span>Execute & Update Assigned Tasks</span>
              </div>
            </div>
          </div>

          <!-- Restricted Capabilities -->
          <div class="space-y-3 pt-4 border-t border-slate-100">
            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-700 flex items-center gap-1.5">
              <i data-lucide="shield-x" class="w-4 h-4 text-rose-600"></i>
              <span>Restricted System Actions</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
              <div class="p-2.5 bg-rose-50/50 rounded-xl border border-rose-100 flex items-center gap-2 text-rose-950 font-medium">
                <i data-lucide="x" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                <span>Delete Patient Medical Records</span>
              </div>
              <div class="p-2.5 bg-rose-50/50 rounded-xl border border-rose-100 flex items-center gap-2 text-rose-950 font-medium">
                <i data-lucide="x" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                <span>Modify Physician Instructions</span>
              </div>
              <div class="p-2.5 bg-rose-50/50 rounded-xl border border-rose-100 flex items-center gap-2 text-rose-950 font-medium">
                <i data-lucide="x" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                <span>Alter Medication Prescriptions</span>
              </div>
              <div class="p-2.5 bg-rose-50/50 rounded-xl border border-rose-100 flex items-center gap-2 text-rose-950 font-medium">
                <i data-lucide="x" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                <span>Manage Users & System Roles</span>
              </div>
            </div>
          </div>

        </div>

      </div>

    </section>

  </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
