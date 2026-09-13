<?php
// Nurse/includes/sidebar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$currentUser = Session::getCurrentUser();
$activeMenu = $activeMenu ?? 'dashboard';
?>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="w-[260px] bg-white border-r border-slate-200/80 flex flex-col fixed lg:sticky top-0 h-screen z-50 shadow-xl lg:shadow-none transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shrink-0">

  <!-- Logo / Brand -->
  <div class="h-20 border-b border-slate-100 flex items-center px-5">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center shadow-lg shadow-teal-500/30">
        <i data-lucide="heart-pulse" class="w-5 h-5 text-white"></i>
      </div>
      <div>
        <h1 class="text-sm font-black text-slate-900 tracking-tight font-display flex items-center gap-1.5">
          Tupi Municipal Hospital
          <span class="px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 rounded-md">EMR</span>
        </h1>
        <p class="text-[11px] font-semibold text-slate-400">Nurse On Duty Portal</p>
      </div>
    </div>
  </div>

  <!-- Shift Info -->
  <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100">
    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5 flex items-center justify-between">
      <span>Current Shift</span>
      <span class="text-[9px] text-teal-600 font-extrabold lowercase flex items-center gap-1">
        <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
        Active
      </span>
    </label>
    <div class="bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs">
      <div class="flex items-center gap-2">
        <i data-lucide="clock" class="w-3.5 h-3.5 text-teal-600"></i>
        <span><?= e($currentUser['shift'] ?? 'Day Shift') ?></span>
      </div>
    </div>
  </div>

  <!-- Navigation Menu -->
  <div class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
    
    <!-- Dashboard -->
    <a href="<?= nurse_url('views/dashboard/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'dashboard' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/25' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/60' ?>">
      <i data-lucide="layout-dashboard" class="w-4 h-4 <?= $activeMenu === 'dashboard' ? 'text-white' : 'text-slate-400 group-hover:text-teal-600' ?>"></i>
      <span>Dashboard</span>
      <?php if ($activeMenu === 'dashboard'): ?>
        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
      <?php endif; ?>
    </a>

    <!-- Vital Signs -->
    <a href="<?= nurse_url('views/vitals/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'vitals' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/25' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/60' ?>">
      <i data-lucide="heart-pulse" class="w-4 h-4 <?= $activeMenu === 'vitals' ? 'text-white' : 'text-slate-400 group-hover:text-teal-600' ?>"></i>
      <span>Vital Signs</span>
    </a>

    <!-- Patients -->
    <a href="<?= nurse_url('views/patients/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'patients' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/25' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/60' ?>">
      <i data-lucide="users" class="w-4 h-4 <?= $activeMenu === 'patients' ? 'text-white' : 'text-slate-400 group-hover:text-teal-600' ?>"></i>
      <span>Patients</span>
    </a>

    <!-- Patient Queue -->
    <a href="<?= nurse_url('views/queue/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'queue' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/25' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/60' ?>">
      <i data-lucide="list-ordered" class="w-4 h-4 <?= $activeMenu === 'queue' ? 'text-white' : 'text-slate-400 group-hover:text-teal-600' ?>"></i>
      <span>Patient Queue</span>
      <span class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">5</span>
    </a>

    <!-- Tasks -->
    <a href="<?= nurse_url('views/tasks/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'tasks' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/25' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/60' ?>">
      <i data-lucide="clipboard-check" class="w-4 h-4 <?= $activeMenu === 'tasks' ? 'text-white' : 'text-slate-400 group-hover:text-teal-600' ?>"></i>
      <span>Tasks</span>
      <span class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">3</span>
    </a>

    <!-- Notifications -->
    <a href="<?= nurse_url('views/notifications/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'notifications' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/25' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/60' ?>">
      <i data-lucide="bell" class="w-4 h-4 <?= $activeMenu === 'notifications' ? 'text-white' : 'text-slate-400 group-hover:text-teal-600' ?>"></i>
      <span>Notifications</span>
      <span class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">4</span>
    </a>

    <div class="pt-3 pb-1">
      <div class="border-t border-slate-100 my-1"></div>
      <span class="px-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">System</span>
    </div>

    <!-- Settings (View Only) -->
    <a href="<?= nurse_url('views/settings/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'settings' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/25' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/60' ?>">
      <i data-lucide="settings" class="w-4 h-4 <?= $activeMenu === 'settings' ? 'text-white' : 'text-slate-400 group-hover:text-teal-600' ?>"></i>
      <span>Settings</span>
    </a>

    <!-- Link to Doctor Portal -->
    <a href="<?= doctor_url('views/dashboard/index.php') ?>" target="_blank"
       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/60 transition group border border-dashed border-slate-200">
      <div class="flex items-center gap-2">
        <i data-lucide="external-link" class="w-3.5 h-3.5 text-indigo-500"></i>
        <span>Doctor Portal</span>
      </div>
      <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono">MD</span>
    </a>

    <!-- Link to Register Portal -->
    <a href="<?= register_url('views/dashboard/index.php') ?>" target="_blank"
       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/60 transition group border border-dashed border-slate-200">
      <div class="flex items-center gap-2">
        <i data-lucide="external-link" class="w-3.5 h-3.5 text-indigo-500"></i>
        <span>Registrator Portal</span>
      </div>
      <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono">INTAKE</span>
    </a>

  </div>

  <!-- Nurse Mini Profile & Logout Footer -->
  <div class="p-4 border-t border-slate-100 bg-slate-50/50">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3 min-w-0">
        <img src="<?= e($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1594824476967-48c8b964ac31?auto=format&fit=crop&q=80&w=150&h=150') ?>" 
             alt="Nurse Avatar" 
             class="w-9 h-9 rounded-xl object-cover ring-2 ring-teal-500/20 shrink-0">
        <div class="min-w-0">
          <p class="text-xs font-bold text-slate-900 truncate"><?= e($currentUser['name'] ?? 'Maria Santos') ?></p>
          <p class="text-[11px] font-semibold text-teal-600 truncate">Nurse On Duty</p>
        </div>
      </div>
      <a href="<?= nurse_url('views/auth/login.php?logout=1') ?>" 
         title="Sign Out" 
         class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
        <i data-lucide="log-out" class="w-4 h-4"></i>
      </a>
    </div>
  </div>

</aside>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  sidebar.classList.toggle('-translate-x-full');
  overlay.classList.toggle('hidden');
}
</script>
