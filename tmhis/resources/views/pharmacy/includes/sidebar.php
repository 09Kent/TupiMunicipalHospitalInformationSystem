<?php
// Pharmacy/includes/sidebar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
$currentUser = Session::getCurrentUser();
?>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MOBILE OVERLAY                                          -->
<!-- ═══════════════════════════════════════════════════════ -->
<div id="sidebarOverlay"
     onclick="toggleSidebarMobile()"
     class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity duration-300">
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- SIDEBAR                                                 -->
<!-- ═══════════════════════════════════════════════════════ -->
<aside id="sidebar"
       class="sidebar-expanded fixed lg:relative inset-y-0 left-0 z-50 bg-white border-r border-slate-200/80 flex flex-col h-full transition-all duration-300 -translate-x-full lg:translate-x-0 shadow-xl lg:shadow-none overflow-hidden">

  <!-- ── Logo / Brand ──────────────────────────────────── -->
  <div class="sidebar-logo-container flex items-center justify-between px-4 pt-5 pb-4 border-b border-slate-100">
    <div class="flex items-center gap-3 min-w-0">
      <!-- Logo Mark -->
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-500 to-blue-700 flex items-center justify-center shrink-0 shadow-md shadow-sky-500/30">
        <i data-lucide="cross" class="w-4.5 h-4.5 text-white"></i>
      </div>
      <div class="sidebar-header-text min-w-0">
        <h1 class="text-base font-black text-slate-900 tracking-tight font-display leading-none">Tupi Municipal Hospital</h1>
        <p class="text-[10px] font-semibold text-slate-400 mt-0.5 leading-none">Pharmacy Portal</p>
      </div>
    </div>
    <!-- Collapse toggle (desktop) -->
    <button onclick="toggleSidebarCollapse()"
            class="sidebar-header-text p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition hidden lg:flex">
      <i data-lucide="panel-left-close" class="w-4 h-4"></i>
    </button>
  </div>

  <!-- ── Duty Status ────────────────────────────────────── -->
  <div class="px-3 py-2.5 border-b border-slate-100 bg-slate-50/60">
    <div class="flex items-center gap-2 px-2.5 py-1.5 bg-white border border-slate-200/80 rounded-xl shadow-xs">
      <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse shrink-0"></span>
      <span class="sidebar-text text-[11px] font-bold text-slate-600 truncate"><?= e($currentUser['shift']) ?></span>
    </div>
  </div>

  <!-- ── Navigation ────────────────────────────────────── -->
  <div class="flex-1 px-2.5 py-3 space-y-5 overflow-y-auto">
    
    <!-- MAIN section -->
    <div>
      <div class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
        Main
      </div>
      <nav class="space-y-0.5">

        <!-- Dashboard -->
        <a href="#dashboard" onclick="switchMainTab('dashboard')" id="nav-dashboard"
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-sky-50 text-sky-700 border border-sky-200/70 shadow-xs">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-sky-600 group-hover:scale-110 transition-transform">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Dashboard</span>
          <span class="sidebar-badge sidebar-text ml-auto w-1.5 h-1.5 rounded-full bg-sky-500"></span>
        </a>

        <!-- Prescriptions -->
        <a href="#prescriptions" onclick="switchMainTab('prescriptions')" id="nav-prescriptions"
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-indigo-500 group-hover:scale-110 transition-transform">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Prescriptions</span>
          <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-indigo-100 text-indigo-800 rounded-full">12</span>
        </a>

        <!-- Dispensing -->
        <a href="#dispensing" onclick="switchMainTab('dispensing')" id="nav-dispensing"
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-emerald-600 group-hover:scale-110 transition-transform">
            <i data-lucide="pill" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Dispensing</span>
          <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full">5</span>
        </a>

        <!-- Pharmacy Inventory -->
        <a href="#inventory" onclick="switchMainTab('inventory')" id="nav-inventory"
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-blue-600 group-hover:scale-110 transition-transform">
            <i data-lucide="boxes" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Pharmacy Inventory</span>
        </a>

        <!-- Medicine Catalog -->
        <a href="#catalog" onclick="switchMainTab('catalog')" id="nav-catalog"
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-slate-500 group-hover:scale-110 transition-transform">
            <i data-lucide="book-open" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Medicine Catalog</span>
        </a>

        <!-- Pharmacy Alerts -->
        <a href="#alerts" onclick="switchMainTab('alerts')" id="nav-alerts"
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-amber-500 group-hover:scale-110 transition-transform">
            <i data-lucide="triangle-alert" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Pharmacy Alerts</span>
          <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full animate-badge-bounce">10</span>
        </a>

        <!-- Activity / Reports -->
        <a href="#activity" onclick="switchMainTab('activity')" id="nav-activity"
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-slate-500 group-hover:scale-110 transition-transform">
            <i data-lucide="file-text" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Reports & Activity</span>
        </a>

      </nav>
    </div>

    <!-- OTHER section -->
    <div>
      <div class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
        Other
      </div>
      <nav class="space-y-0.5">
        <a href="#settings" onclick="openProfileModal()" 
           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all duration-200">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-slate-400 group-hover:scale-110 transition-transform">
            <i data-lucide="settings" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Settings</span>
        </a>
      </nav>
    </div>
  </div>

  <!-- ── User Profile ───────────────────────────────────── -->
  <div class="p-3 border-t border-slate-100 bg-slate-50/60 relative">
    <div onclick="toggleProfileDropdown()"
         class="flex items-center gap-3 p-2 rounded-2xl hover:bg-white cursor-pointer transition border border-transparent hover:border-slate-200/80 hover:shadow-xs group">
      <div class="relative shrink-0">
        <img src="<?= e($currentUser['avatar']) ?>"
             alt="Pharmacist Profile"
             class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-xs">
        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-sky-500 border-2 border-white rounded-full"></span>
      </div>
      <div class="sidebar-profile-info min-w-0 flex-1">
        <p class="text-xs font-bold text-slate-900 truncate group-hover:text-sky-700 transition"><?= e($currentUser['name']) ?></p>
        <p class="text-[11px] font-medium text-slate-400 truncate"><?= e($currentUser['role']) ?></p>
      </div>
      <i data-lucide="chevron-up" class="sidebar-profile-info w-4 h-4 text-slate-400 group-hover:text-slate-600 transition ml-auto"></i>
    </div>

    <!-- Profile Dropdown -->
    <div id="profileDropdown"
         class="absolute bottom-20 left-3 right-3 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 hidden z-50">
      <div class="px-3 py-2 border-b border-slate-100 mb-1">
        <p class="text-xs font-bold text-slate-900"><?= e($currentUser['name']) ?></p>
        <p class="text-[10px] text-slate-400 font-mono"><?= e($currentUser['license_no']) ?></p>
      </div>
      <button onclick="openProfileModal()"
              class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition">
        <i data-lucide="user-check" class="w-4 h-4 text-sky-600"></i>
        <span>My Profile</span>
      </button>
      <button onclick="switchMainTab('dispensing')"
              class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition">
        <i data-lucide="pill" class="w-4 h-4 text-emerald-600"></i>
        <span>My Dispensing Tasks</span>
      </button>
      <button onclick="switchMainTab('notifications')"
              class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition">
        <i data-lucide="bell" class="w-4 h-4 text-amber-500"></i>
        <span>Notifications</span>
      </button>
      <div class="border-t border-slate-100 my-1"></div>
      <button onclick="showToast('Logged out of pharmacy session', 'info')"
              class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition">
        <i data-lucide="log-out" class="w-4 h-4 text-rose-500"></i>
        <span>Logout</span>
      </button>
    </div>
  </div>

</aside>

<script>
// ── Sidebar Mobile Toggle ──────────────────────────────────
function toggleSidebarMobile() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  sidebar.classList.toggle('-translate-x-full');
  overlay.classList.toggle('hidden');
}

// ── Sidebar Desktop Collapse ──────────────────────────────
let sidebarCollapsed = false;
function toggleSidebarCollapse() {
  const sidebar = document.getElementById('sidebar');
  sidebarCollapsed = !sidebarCollapsed;
  if (sidebarCollapsed) {
    sidebar.classList.remove('sidebar-expanded');
    sidebar.classList.add('sidebar-collapsed');
  } else {
    sidebar.classList.add('sidebar-expanded');
    sidebar.classList.remove('sidebar-collapsed');
  }
  if (window.lucide) lucide.createIcons();
}

// ── Profile Dropdown ──────────────────────────────────────
function toggleProfileDropdown() {
  const dd = document.getElementById('profileDropdown');
  if (dd) dd.classList.toggle('hidden');
}
</script>
