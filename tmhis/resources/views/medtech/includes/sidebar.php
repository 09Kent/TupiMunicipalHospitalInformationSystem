<?php
// Med_Tech/includes/sidebar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$currentUser = Session::getCurrentUser();
$activeMenu = $activeMenu ?? 'dashboard';
?>

<!-- Mobile Backdrop Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden backdrop-blur-xs transition-opacity duration-300" onclick="toggleSidebarMobile()"></div>

<!-- Left Vertical Sidebar (Collapsible: 250px <-> 72px) -->
<aside id="sidebar" class="sidebar-expanded bg-white border-r border-slate-200/80 flex flex-col fixed lg:sticky top-0 h-screen z-50 shadow-xl lg:shadow-none -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out shrink-0 select-none">
  
  <!-- Sidebar Header: Logo & Collapse Button -->
  <div class="h-20 border-b border-slate-100 flex items-center justify-between px-5 sidebar-logo-container transition-all">
    <div class="flex items-center gap-3 overflow-hidden">
      <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-500 flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0">
        <i data-lucide="flask-conical" class="w-5 h-5 text-white"></i>
      </div>
      <div class="sidebar-header-text">
        <h1 class="text-base font-black text-slate-900 tracking-tight font-display flex items-center gap-1.5 leading-none">
          Tupi Municipal Hospital
          <span class="px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-md">LAB</span>
        </h1>
        <p class="text-[11px] font-semibold text-slate-400 mt-1">Medical Technologist</p>
      </div>
    </div>

    <!-- Toggle Collapse (Desktop) -->
    <button onclick="toggleSidebarCollapse()" class="hidden lg:flex p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" title="Toggle Sidebar Width">
      <i data-lucide="panel-left-close" id="sidebarCollapseIcon" class="w-4 h-4"></i>
    </button>
  </div>

  <!-- Navigation Links Container -->
  <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-6">
    
    <!-- MAIN Section -->
    <div>
      <div class="sidebar-section-title px-3 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
        Main
      </div>
      <nav class="space-y-1">
        
        <!-- Dashboard -->
        <a href="#dashboard" onclick="switchMainTab('dashboard')" id="nav-dashboard" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'dashboard' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-indigo-600 group-hover:scale-110 transition-transform">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Dashboard</span>
          <span class="sidebar-badge ml-auto w-2 h-2 rounded-full bg-indigo-500 <?= $activeMenu === 'dashboard' ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' ?> transition"></span>
        </a>

        <!-- Laboratory Requests -->
        <a href="#requests" onclick="switchMainTab('requests')" id="nav-requests" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'requests' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-indigo-600 group-hover:scale-110 transition-transform">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Laboratory Requests</span>
          <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full">12</span>
        </a>

        <!-- Sample Tracking -->
        <a href="#samples" onclick="switchMainTab('samples')" id="nav-samples" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'samples' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-cyan-600 group-hover:scale-110 transition-transform">
            <i data-lucide="test-tube" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Sample Tracking</span>
          <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-800 rounded-full">8</span>
        </a>

        <!-- Test Results -->
        <a href="#results" onclick="switchMainTab('results')" id="nav-results" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'results' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-emerald-600 group-hover:scale-110 transition-transform">
            <i data-lucide="file-check-2" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Test Results</span>
          <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-purple-100 text-purple-800 rounded-full">5</span>
        </a>

        <!-- Test Catalog -->
        <a href="#catalog" onclick="switchMainTab('catalog')" id="nav-catalog" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'catalog' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-slate-500 group-hover:scale-110 transition-transform">
            <i data-lucide="book-open" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Test Catalog</span>
        </a>

        <!-- Laboratory Reports -->
        <a href="#reports" onclick="switchMainTab('reports')" id="nav-reports" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'reports' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-slate-500 group-hover:scale-110 transition-transform">
            <i data-lucide="file-text" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Laboratory Reports</span>
        </a>

        <!-- Reference Ranges -->
        <a href="#ranges" onclick="switchMainTab('ranges')" id="nav-ranges" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'ranges' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-slate-500 group-hover:scale-110 transition-transform">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Reference Ranges</span>
        </a>

        <!-- Notifications -->
        <a href="#notifications" onclick="switchMainTab('notifications')" id="nav-notifications" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $activeMenu === 'notifications' ? 'bg-slate-100/90 text-slate-900 font-bold border border-slate-200/70 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-amber-500 group-hover:scale-110 transition-transform">
            <i data-lucide="bell" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Notifications</span>
          <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-700 rounded-full">2</span>
        </a>
      </nav>
    </div>

    <!-- OTHER Section -->
    <div>
      <div class="sidebar-section-title px-3 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
        Other
      </div>
      <nav class="space-y-1">
        <a href="#settings" onclick="openProfileModal()" class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all duration-200">
          <div class="flex items-center justify-center shrink-0 w-5 h-5 text-slate-400 group-hover:scale-110 transition-transform">
            <i data-lucide="settings" class="w-4 h-4"></i>
          </div>
          <span class="sidebar-text truncate">Settings</span>
        </a>
      </nav>
    </div>
  </div>

  <!-- User Profile Section at Bottom -->
  <div class="p-3 border-t border-slate-100 bg-slate-50/60 relative">
    <div onclick="toggleProfileDropdown()" class="flex items-center gap-3 p-2 rounded-2xl hover:bg-white cursor-pointer transition border border-transparent hover:border-slate-200/80 hover:shadow-xs group">
      <div class="relative shrink-0">
        <img src="<?= e($currentUser['avatar']) ?>" alt="MedTech Profile" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-xs">
        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
      </div>
      <div class="sidebar-profile-info min-w-0 flex-1">
        <p class="text-xs font-bold text-slate-900 truncate group-hover:text-indigo-600 transition"><?= e($currentUser['name']) ?></p>
        <p class="text-[11px] font-medium text-slate-400 truncate"><?= e($currentUser['role']) ?></p>
      </div>
      <i data-lucide="chevron-up" class="sidebar-profile-info w-4 h-4 text-slate-400 group-hover:text-slate-600 transition ml-auto"></i>
    </div>

    <!-- Interactive Profile Popover Dropdown -->
    <div id="profileDropdown" class="absolute bottom-20 left-3 right-3 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 hidden z-50 backdrop-blur-lg">
      <div class="px-3 py-2 border-b border-slate-100 mb-1">
        <p class="text-xs font-bold text-slate-900"><?= e($currentUser['name']) ?></p>
        <p class="text-[10px] text-slate-400 font-mono"><?= e($currentUser['license_no']) ?></p>
      </div>
      <button onclick="openProfileModal()" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition">
        <i data-lucide="user-check" class="w-4 h-4 text-indigo-600"></i>
        <span>My Technologist Profile</span>
      </button>
      <button onclick="switchMainTab('requests')" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition">
        <i data-lucide="list-checks" class="w-4 h-4 text-cyan-600"></i>
        <span>My Laboratory Tasks</span>
      </button>
      <button onclick="switchMainTab('notifications')" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition">
        <i data-lucide="bell" class="w-4 h-4 text-amber-500"></i>
        <span>Notifications</span>
      </button>
      <div class="border-t border-slate-100 my-1"></div>
      <button onclick="showToast('Logged out of laboratory session', 'info')" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition">
        <i data-lucide="log-out" class="w-4 h-4 text-rose-500"></i>
        <span>Logout Session</span>
      </button>
    </div>
  </div>
</aside>
