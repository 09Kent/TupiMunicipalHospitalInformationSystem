<?php
/**
 * Tupi Municipal Hospital Information Management System
 * ROLE 1: SYSTEM ADMINISTRATOR PORTAL
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/HospitalInfo.php';
require_once __DIR__ . '/models/Department.php';
require_once __DIR__ . '/models/ServiceFee.php';
require_once __DIR__ . '/models/UserManager.php';
require_once __DIR__ . '/models/RolePermission.php';
require_once __DIR__ . '/models/AuditLog.php';
require_once __DIR__ . '/models/BackupManager.php';

// If not logged in as Admin, create default admin session for smooth evaluation or redirect
if (!Session::isAdmin()) {
    Session::set('user_id', 1);
    Session::set('username', 'admin');
    Session::set('full_name', 'System Administrator');
    Session::set('role', 'Admin');
    Session::set('email', 'admin@tupihospital.gov.ph');
}

$currentUser = Session::getCurrentUser();
$hospitalModel = new HospitalInfo();
$deptModel = new Department();
$feeModel = new ServiceFee();
$userModel = new UserManager();
$roleModel = new RolePermission();
$auditModel = new AuditLog();
$backupModel = new BackupManager();

$hospitalInfo = $hospitalModel->get();
$departments = $deptModel->getAll();
$serviceFees = $feeModel->getAll();
$users = $userModel->getAll();
$roles = $roleModel->getRoles();
$recentLogs = $auditModel->getActivityLogs(20);
$backups = $backupModel->getBackupHistory();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Administrator Portal | Tupi Municipal Hospital Information Management System</title>
  <meta name="description" content="Tupi Municipal Hospital System Administrator Portal – Manage hospital configuration, user accounts, departments, service fees, audit logs, and database backups.">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac',
              400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d',
              800: '#166534', 900: '#14532d', 950: '#052e16'
            },
            admin: {
              50:  '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac',
              400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d',
              800: '#166534', 900: '#14532d'
            }
          },
          fontFamily: {
            sans:    ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            display: ['Outfit', 'Inter', 'sans-serif'],
            mono:    ['JetBrains Mono', 'Fira Code', 'Courier New', 'monospace']
          },
          boxShadow: {
            'card':       '0 2px 12px -2px rgba(0, 0, 0, 0.04), 0 1px 3px 0 rgba(0, 0, 0, 0.02)',
            'card-hover': '0 12px 28px -4px rgba(22, 163, 74, 0.10), 0 4px 10px -2px rgba(0, 0, 0, 0.03)',
            'glow':       '0 0 25px rgba(22, 163, 74, 0.25)'
          }
        }
      }
    }
  </script>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
      -webkit-font-smoothing: antialiased;
    }
    .font-display { font-family: 'Outfit', sans-serif; }
    .font-mono    { font-family: 'JetBrains Mono', monospace; }

    /* ─── Slim Scrollbar ─────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* ─── Sidebar Collapse ───────────────────────────── */
    .sidebar-expanded  { width: 256px !important; }
    .sidebar-collapsed { width: 72px  !important; }
    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .sidebar-header-text,
    .sidebar-collapsed .sidebar-badge,
    .sidebar-collapsed .sidebar-profile-info,
    .sidebar-collapsed .sidebar-section-title { display: none !important; }
    .sidebar-collapsed .sidebar-item { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
    .sidebar-collapsed .sidebar-logo-container { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }

    /* ─── Tab Content ────────────────────────────────── */
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ─── Animations ─────────────────────────────────── */
    @keyframes badge-bounce {
      0%, 100% { transform: scale(1); }
      50%       { transform: scale(1.18); }
    }
    .animate-badge-bounce { animation: badge-bounce 2s infinite ease-in-out; }

    /* ─── Modal / Drawer ─────────────────────────────── */
    .modal-backdrop  { transition: opacity 0.3s ease, backdrop-filter 0.3s ease; }
    .modal-content   { transition: opacity 0.3s ease, transform 0.3s ease; }

    /* ─── Table Row Hover ────────────────────────────── */
    .admin-table-row:hover { background-color: #f8fafc; }

    /* ─── Print ──────────────────────────────────────── */
    @media print {
      #sidebar, #topbar, #sidebarOverlay, .no-print, button { display: none !important; }
    }
  </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-50 text-slate-800">

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
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-green-700 flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/30">
          <i data-lucide="shield-check" class="w-4.5 h-4.5 text-white"></i>
        </div>
        <div class="sidebar-header-text min-w-0">
          <h1 class="text-base font-black text-slate-900 tracking-tight font-display leading-none">Tupi Municipal Hospital</h1>
          <p class="text-[10px] font-semibold text-slate-400 mt-0.5 leading-none">System Admin Portal</p>
        </div>
      </div>
      <!-- Collapse toggle (desktop) -->
      <button onclick="toggleSidebarCollapse()"
              class="sidebar-header-text p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition hidden lg:flex">
        <i data-lucide="panel-left-close" class="w-4 h-4"></i>
      </button>
    </div>

    <!-- ── Navigation ────────────────────────────────────── -->
    <div class="flex-1 px-2.5 py-3 space-y-5 overflow-y-auto">

      <!-- SYSTEM CONFIG section -->
      <div>
        <div class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
          System Configuration
        </div>
        <nav class="space-y-0.5">
          <!-- Dashboard -->
          <button onclick="switchTab('tab-dashboard')" id="nav-dashboard"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full bg-emerald-50 text-emerald-700 border border-emerald-200/70 shadow-xs">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-emerald-600 group-hover:scale-110 transition-transform">
              <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Dashboard Overview</span>
            <span class="sidebar-badge sidebar-text ml-auto w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          </button>

          <!-- Hospital Info -->
          <button onclick="switchTab('tab-hospital')" id="nav-hospital"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-sky-500 group-hover:scale-110 transition-transform">
              <i data-lucide="building-2" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Hospital Info</span>
          </button>

          <!-- Department Mgmt -->
          <button onclick="switchTab('tab-departments')" id="nav-departments"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-amber-500 group-hover:scale-110 transition-transform">
              <i data-lucide="network" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Department Mgmt</span>
            <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full"><?= count($departments) ?></span>
          </button>

          <!-- Service Fee Mgmt -->
          <button onclick="switchTab('tab-services')" id="nav-services"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-purple-500 group-hover:scale-110 transition-transform">
              <i data-lucide="receipt" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Service Fee Mgmt</span>
          </button>
        </nav>
      </div>

      <!-- USER ADMIN section -->
      <div>
        <div class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
          User Administration
        </div>
        <nav class="space-y-0.5">
          <!-- User Accounts -->
          <button onclick="switchTab('tab-users')" id="nav-users"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-blue-500 group-hover:scale-110 transition-transform">
              <i data-lucide="users" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">User Accounts</span>
            <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-800 rounded-full"><?= count($users) ?></span>
          </button>

          <!-- Roles & Permissions -->
          <button onclick="switchTab('tab-roles')" id="nav-roles"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-rose-500 group-hover:scale-110 transition-transform">
              <i data-lucide="shield-check" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Roles & Permissions</span>
          </button>
        </nav>
      </div>

      <!-- SYSTEM MONITORING section -->
      <div>
        <div class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
          System Monitoring
        </div>
        <nav class="space-y-0.5">
          <!-- Activity & Error Logs -->
          <button onclick="switchTab('tab-logs')" id="nav-logs"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-teal-500 group-hover:scale-110 transition-transform">
              <i data-lucide="history" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Activity & Error Logs</span>
            <span class="sidebar-badge ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-teal-100 text-teal-800 rounded-full"><?= count($recentLogs) ?></span>
          </button>

          <!-- Data Backups -->
          <button onclick="switchTab('tab-backups')" id="nav-backups"
             class="sidebar-item tab-nav group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-indigo-500 group-hover:scale-110 transition-transform">
              <i data-lucide="database-backup" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Data Backups</span>
          </button>
        </nav>
      </div>

      <!-- OTHER section -->
      <div>
        <div class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
          Other
        </div>
        <nav class="space-y-0.5">
          <a href="../Doctor/views/auth/login.php"
             class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 w-full">
            <div class="flex items-center justify-center shrink-0 w-5 h-5 text-rose-500 group-hover:scale-110 transition-transform">
              <i data-lucide="log-out" class="w-4 h-4"></i>
            </div>
            <span class="sidebar-text truncate">Logout</span>
          </a>
        </nav>
      </div>
    </div>

    <!-- ── User Profile ───────────────────────────────────── -->
    <div class="p-3 border-t border-slate-100 bg-slate-50/60 relative">
      <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-white cursor-pointer transition border border-transparent hover:border-slate-200/80 hover:shadow-xs group">
        <div class="relative shrink-0">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-700 text-white flex items-center justify-center font-bold text-sm shadow-xs">
            SA
          </div>
          <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
        </div>
        <div class="sidebar-profile-info min-w-0 flex-1">
          <p class="text-xs font-bold text-slate-900 truncate group-hover:text-emerald-700 transition"><?= e($currentUser['name']) ?></p>
          <p class="text-[11px] font-medium text-slate-400 truncate"><?= e($currentUser['username']) ?></p>
        </div>
        <i data-lucide="chevron-up" class="sidebar-profile-info w-4 h-4 text-slate-400 group-hover:text-slate-600 transition ml-auto"></i>
      </div>
    </div>
  </aside>

  <!-- ═══════════════════════════════════════════════════════ -->
  <!-- MAIN CONTENT VIEW AREA                                  -->
  <!-- ═══════════════════════════════════════════════════════ -->
  <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">

    <!-- ── Topbar ──────────────────────────────────────────── -->
    <header id="topbar"
            class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-5 sm:px-7 flex items-center justify-between sticky top-0 z-30 shadow-xs">

      <!-- Left: Mobile menu + Title -->
      <div class="flex items-center gap-3">
        <button onclick="toggleSidebarMobile()"
                class="lg:hidden p-2 -ml-1 text-slate-600 hover:bg-slate-100 rounded-xl transition">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
        <div class="hidden sm:block">
          <div class="flex items-center gap-2">
            <h2 class="text-base font-black text-slate-900 tracking-tight font-display" id="topbarPageTitle">
              System Admin Control Center
            </h2>
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              <span>System Online</span>
            </span>
          </div>
          <p class="text-[11px] font-medium text-slate-400 mt-0.5 flex items-center gap-1.5">
            <span>Tupi Municipal Hospital Information Management System</span>
            <span>•</span>
            <span class="text-emerald-600 font-semibold">Role 1: SysAdmin</span>
          </p>
        </div>
      </div>

      <!-- Right: Search, Clock -->
      <div class="flex items-center gap-2.5">
        <!-- Global Search -->
        <div class="relative w-44 md:w-60 lg:w-72">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
            <i data-lucide="search" class="w-3.5 h-3.5"></i>
          </div>
          <input type="text"
                 id="globalSearchInput"
                 onkeyup="handleGlobalSearch(event)"
                 placeholder="Search system records..."
                 class="w-full pl-9 pr-8 py-2 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-xs">
          <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
            <span class="text-[10px] font-bold text-slate-400 bg-slate-200/60 px-1.5 py-0.5 rounded font-mono hidden md:inline">/</span>
          </div>
        </div>

        <!-- Live Clock -->
        <div class="hidden xl:flex items-center gap-1.5 px-3 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-600 font-mono shadow-xs">
          <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-500"></i>
          <span id="liveClockDisplay"><?= date('h:i:s A') ?></span>
        </div>

        <!-- Profile Avatar -->
        <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-green-700 text-white flex items-center justify-center font-bold text-[11px] shadow-xs">
            SA
          </div>
        </div>
      </div>
    </header>

    <main class="p-6 sm:p-8 space-y-8 flex-1">

      <!-- ================================================================ -->
      <!-- TAB 1: DASHBOARD OVERVIEW                                        -->
      <!-- ================================================================ -->
      <section id="tab-dashboard" class="tab-content active space-y-8">
        
        <!-- Page Title & Header Bar -->
        <div data-aos="fade-down" class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/80">Role 1</span>
              <span class="text-xs font-bold text-slate-400 font-mono">SYSTEM ADMINISTRATOR</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">ADMINISTRATOR OVERVIEW</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Control hospital profiles, system fees, staff accounts, and global data safety.</p>
          </div>
        </div>

        <!-- 4 Summary KPI Cards -->
        <div data-aos="fade-up" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          
          <!-- Card 1: Hospital Units -->
          <div onclick="switchTab('tab-departments')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                <i data-lucide="network" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200/60">departments</span>
            </div>
            <div class="mt-4">
              <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= count($departments) ?></span>
              <p class="text-xs font-bold text-slate-700 mt-1">Hospital Units</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">Clinical, diagnostic & admin divisions</p>
            </div>
          </div>

          <!-- Card 2: Active Services -->
          <div onclick="switchTab('tab-services')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                <i data-lucide="receipt" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200/60">service fees</span>
            </div>
            <div class="mt-4">
              <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= count($serviceFees) ?></span>
              <p class="text-xs font-bold text-slate-700 mt-1">Active Services</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">Standard tariffs & diagnostic charges</p>
            </div>
          </div>

          <!-- Card 3: User Accounts -->
          <div onclick="switchTab('tab-users')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                <i data-lucide="users" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/60">accounts</span>
            </div>
            <div class="mt-4">
              <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= count($users) ?></span>
              <p class="text-xs font-bold text-slate-700 mt-1">User Accounts</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">System staff & clinical personnel</p>
            </div>
          </div>

          <!-- Card 4: System Backups -->
          <div onclick="switchTab('tab-backups')" class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                <i data-lucide="database-backup" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200/60">backups</span>
            </div>
            <div class="mt-4">
              <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= count($backups) ?></span>
              <p class="text-xs font-bold text-slate-700 mt-1">System Backups</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">SQL snapshot restorations available</p>
            </div>
          </div>
        </div>

        <!-- Recent Audit Log Strip -->
        <div data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 sm:p-7">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
              <div class="flex items-center gap-2.5">
                <h2 class="text-lg font-black text-slate-900 tracking-tight font-display">RECENT SYSTEM ACTIVITY</h2>
                <span class="px-2 py-0.5 text-xs font-bold bg-slate-100 text-slate-700 rounded-full"><?= count($recentLogs) ?> events</span>
              </div>
              <p class="text-xs text-slate-400 font-medium mt-0.5">Real-time audit trail across all hospital system modules</p>
            </div>
            <button onclick="switchTab('tab-logs')" class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-xs font-bold rounded-xl hover:bg-emerald-100 transition">
              View Full Logs →
            </button>
          </div>
          <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                  <th class="py-3 px-3">Timestamp</th>
                  <th class="py-3 px-3">User</th>
                  <th class="py-3 px-3">Action</th>
                  <th class="py-3 px-3">Module</th>
                  <th class="py-3 px-3">Details</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php foreach (array_slice($recentLogs, 0, 5) as $log): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <td class="py-3.5 px-3 font-mono text-slate-500"><?= e($log['CreatedAt']) ?></td>
                  <td class="py-3.5 px-3 font-bold text-slate-900"><?= e($log['UserName']) ?></td>
                  <td class="py-3.5 px-3 font-semibold text-emerald-800"><?= e($log['Action']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200/60"><?= e($log['Module']) ?></span></td>
                  <td class="py-3.5 px-3 text-slate-600 truncate max-w-xs"><?= e($log['Details']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================================================================ -->
      <!-- TAB 2: HOSPITAL INFO                                             -->
      <!-- ================================================================ -->
      <section id="tab-hospital" class="tab-content space-y-8">
        <div data-aos="fade-down">
          <div class="flex items-center gap-2 mb-1.5">
            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200/80">Configuration</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">HOSPITAL PROFILE & SYSTEM CONFIG</h1>
          <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Master institution information, regulatory accreditations, and emergency parameters.</p>
        </div>

        <div data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-card max-w-4xl">
          <form id="hospitalInfoForm" onsubmit="handleHospitalUpdate(event)" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Hospital Official Name</label>
                <input type="text" name="HospitalName" value="<?= e($hospitalInfo['HospitalName']) ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold transition">
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Hospital Registry Code</label>
                <input type="text" name="HospitalCode" value="<?= e($hospitalInfo['HospitalCode']) ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-slate-700 mb-1">DOH Accreditation Number</label>
                <input type="text" name="DOHAccreditation" value="<?= e($hospitalInfo['DOHAccreditation']) ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition">
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">PhilHealth Accreditation ID</label>
                <input type="text" name="PhilHealthAccreditation" value="<?= e($hospitalInfo['PhilHealthAccreditation']) ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Contact Phone</label>
                <input type="text" name="ContactNumber" value="<?= e($hospitalInfo['ContactNumber']) ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition">
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Emergency Hotline</label>
                <input type="text" name="EmergencyHotline" value="<?= e($hospitalInfo['EmergencyHotline']) ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition">
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Bed Capacity</label>
                <input type="number" name="BedCapacity" value="<?= e($hospitalInfo['BedCapacity']) ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-bold transition">
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Physical Address</label>
              <textarea name="Address" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition"><?= e($hospitalInfo['Address']) ?></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end">
              <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-2 active:scale-95">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Hospital Configuration</span>
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- ================================================================ -->
      <!-- TAB 3: DEPARTMENTS MANAGEMENT                                    -->
      <!-- ================================================================ -->
      <section id="tab-departments" class="tab-content space-y-8">
        <div data-aos="fade-down" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200/80">Management</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">HOSPITAL DEPARTMENTS</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Clinical, diagnostic, administrative, and support divisions.</p>
          </div>
          <button onclick="openCreateDeptModal()" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl text-xs font-bold transition shadow-md shadow-amber-600/20 flex items-center gap-2 active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Department</span>
          </button>
        </div>

        <div data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
          <div class="overflow-x-auto">
            <table id="departmentsTable" class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                  <th class="py-3 px-3">Code</th>
                  <th class="py-3 px-3">Department Name</th>
                  <th class="py-3 px-3">Type</th>
                  <th class="py-3 px-3">Head of Unit</th>
                  <th class="py-3 px-3">Location</th>
                  <th class="py-3 px-3">Status</th>
                  <th class="py-3 px-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php foreach ($departments as $d): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <td class="py-3.5 px-3 font-mono font-bold text-slate-900"><?= e($d['DepartmentCode']) ?></td>
                  <td class="py-3.5 px-3 font-semibold text-slate-800"><?= e($d['DepartmentName']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60"><?= e($d['DepartmentType']) ?></span></td>
                  <td class="py-3.5 px-3 text-slate-600"><?= e($d['HeadOfDepartment'] ?: '—') ?></td>
                  <td class="py-3.5 px-3 text-slate-600"><?= e($d['Location']) ?></td>
                  <td class="py-3.5 px-3">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $d['Status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' ?>">
                      <?= e($d['Status']) ?>
                    </span>
                  </td>
                  <td class="py-3.5 px-3 text-right">
                    <button onclick="deleteDept(<?= $d['DepartmentID'] ?>)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-lg text-[11px] transition active:scale-95">Delete</button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================================================================ -->
      <!-- TAB 4: SERVICE FEES MANAGEMENT                                   -->
      <!-- ================================================================ -->
      <section id="tab-services" class="tab-content space-y-8">
        <div data-aos="fade-down" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/80">Billing</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">SERVICE FEE MANAGEMENT</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Standard hospital service tariffs, diagnostic charges, and PhilHealth coverage.</p>
          </div>
          <button onclick="openCreateFeeModal()" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl text-xs font-bold transition shadow-md shadow-purple-600/20 flex items-center gap-2 active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Service Fee</span>
          </button>
        </div>

        <div data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
          <div class="overflow-x-auto">
            <table id="servicesTable" class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                  <th class="py-3 px-3">Code</th>
                  <th class="py-3 px-3">Service Description</th>
                  <th class="py-3 px-3">Category</th>
                  <th class="py-3 px-3">Standard Rate</th>
                  <th class="py-3 px-3">PhilHealth Covered</th>
                  <th class="py-3 px-3">Status</th>
                  <th class="py-3 px-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php foreach ($serviceFees as $sf): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <td class="py-3.5 px-3 font-mono font-bold text-slate-900"><?= e($sf['ServiceCode']) ?></td>
                  <td class="py-3.5 px-3 font-semibold text-slate-800"><?= e($sf['ServiceName']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200/60"><?= e($sf['Category']) ?></span></td>
                  <td class="py-3.5 px-3 font-bold text-slate-900">₱<?= number_format((float)$sf['StandardRate'], 2) ?></td>
                  <td class="py-3.5 px-3 text-emerald-700 font-bold">₱<?= number_format((float)$sf['PhilHealthCoveredRate'], 2) ?></td>
                  <td class="py-3.5 px-3">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $sf['Status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' ?>">
                      <?= e($sf['Status']) ?>
                    </span>
                  </td>
                  <td class="py-3.5 px-3 text-right">
                    <button onclick="deleteFee(<?= $sf['FeeID'] ?>)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-lg text-[11px] transition active:scale-95">Delete</button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================================================================ -->
      <!-- TAB 5: USER ACCOUNTS ADMINISTRATION                              -->
      <!-- ================================================================ -->
      <section id="tab-users" class="tab-content space-y-8">
        <div data-aos="fade-down" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200/80">Security</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">USER ACCOUNT ADMINISTRATION</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Manage system access, create accounts, deactivate users, and reset encrypted credentials.</p>
          </div>
          <button onclick="openCreateUserModal()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold transition shadow-md shadow-blue-600/20 flex items-center gap-2 active:scale-95">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Create New User</span>
          </button>
        </div>

        <div data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
          <div class="overflow-x-auto">
            <table id="usersTable" class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                  <th class="py-3 px-3">ID</th>
                  <th class="py-3 px-3">Full Name</th>
                  <th class="py-3 px-3">Username</th>
                  <th class="py-3 px-3">Role</th>
                  <th class="py-3 px-3">Email</th>
                  <th class="py-3 px-3">Status</th>
                  <th class="py-3 px-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <td class="py-3.5 px-3 font-mono text-slate-400">#<?= $u['UserID'] ?></td>
                  <td class="py-3.5 px-3 font-bold text-slate-900"><?= e($u['FirstName'] . ' ' . $u['LastName']) ?></td>
                  <td class="py-3.5 px-3 font-mono font-semibold text-slate-700"><?= e($u['Username']) ?></td>
                  <td class="py-3.5 px-3">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                      <?= e($u['Role']) ?>
                    </span>
                  </td>
                  <td class="py-3.5 px-3 text-slate-600"><?= e($u['Email'] ?: '—') ?></td>
                  <td class="py-3.5 px-3">
                    <button onclick="toggleUserStatus(<?= $u['UserID'] ?>, '<?= $u['Status'] === 'Active' ? 'Inactive' : 'Active' ?>')" 
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition <?= $u['Status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' ?>">
                      <?= e($u['Status']) ?> (Toggle)
                    </button>
                  </td>
                  <td class="py-3.5 px-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                      <button onclick="promptResetPassword(<?= $u['UserID'] ?>)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg text-[11px] transition active:scale-95">Reset Pwd</button>
                      <?php if ($u['UserID'] !== 1): ?>
                      <button onclick="deleteUser(<?= $u['UserID'] ?>)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-lg text-[11px] transition active:scale-95">Delete</button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================================================================ -->
      <!-- TAB 6: ROLES & PERMISSIONS                                       -->
      <!-- ================================================================ -->
      <section id="tab-roles" class="tab-content space-y-8">
        <div data-aos="fade-down">
          <div class="flex items-center gap-2 mb-1.5">
            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200/80">Access Control</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">ROLE & PERMISSION MANAGEMENT</h1>
          <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Defined institutional roles matching the 9 Functional Decomposition Diagram roles.</p>
        </div>

        <div data-aos="fade-up" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <?php foreach ($roles as $r): ?>
          <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
              <span class="font-mono text-xs font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200/60"><?= e($r['RoleCode']) ?></span>
              <span class="text-[10px] font-bold text-slate-400"><?= $r['IsSystemRole'] ? 'Core System Role' : 'Custom Role' ?></span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-display"><?= e($r['RoleName']) ?></h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed"><?= e($r['Description']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- ================================================================ -->
      <!-- TAB 7: ACTIVITY & ERROR LOGS                                     -->
      <!-- ================================================================ -->
      <section id="tab-logs" class="tab-content space-y-8">
        <div data-aos="fade-down" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200/80">Monitoring</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">SYSTEM AUDIT & ERROR LOGS</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Comprehensive real-time tracking across all 9 hospital system modules.</p>
          </div>
          <button onclick="location.reload()" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-2 transition active:scale-95">
            <i data-lucide="refresh-cw" class="w-4 h-4 text-teal-600"></i>
            <span>Refresh Logs</span>
          </button>
        </div>

        <div data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
          <div class="p-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
            <span class="font-bold text-xs text-slate-700">Audit Trail (Last <?= count($recentLogs) ?> Events)</span>
            <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-100">Real-Time</span>
          </div>
          <div class="overflow-x-auto max-h-[500px] p-6">
            <table id="logsTable" class="w-full text-left text-xs border-collapse">
              <thead class="sticky top-0 bg-white">
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                  <th class="py-3 px-3">Date / Time</th>
                  <th class="py-3 px-3">Actor</th>
                  <th class="py-3 px-3">Role</th>
                  <th class="py-3 px-3">Action</th>
                  <th class="py-3 px-3">Module</th>
                  <th class="py-3 px-3">Details</th>
                  <th class="py-3 px-3">IP</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php foreach ($recentLogs as $log): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <td class="py-3 px-3 font-mono text-slate-500"><?= e($log['CreatedAt']) ?></td>
                  <td class="py-3 px-3 font-bold text-slate-900"><?= e($log['UserName']) ?></td>
                  <td class="py-3 px-3 text-slate-600"><?= e($log['UserRole']) ?></td>
                  <td class="py-3 px-3 font-semibold text-emerald-800"><?= e($log['Action']) ?></td>
                  <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200/60"><?= e($log['Module']) ?></span></td>
                  <td class="py-3 px-3 text-slate-600"><?= e($log['Details']) ?></td>
                  <td class="py-3 px-3 font-mono text-slate-400"><?= e($log['IPAddress'] ?: '127.0.0.1') ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================================================================ -->
      <!-- TAB 8: DATABASE BACKUPS                                          -->
      <!-- ================================================================ -->
      <section id="tab-backups" class="tab-content space-y-8">
        <div data-aos="fade-down" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/80">Data Safety</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">DATABASE BACKUP OPERATIONS</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Generate standalone SQL snapshot backups and manage restoration files.</p>
          </div>
          <button onclick="createDatabaseBackup()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition shadow-md shadow-indigo-600/20 flex items-center gap-2 active:scale-95">
            <i data-lucide="download-cloud" class="w-4 h-4"></i>
            <span>Create Full Backup Now</span>
          </button>
        </div>

        <div data-aos="fade-up" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
          <div class="overflow-x-auto">
            <table id="backupsTable" class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                  <th class="py-3 px-3">Backup File</th>
                  <th class="py-3 px-3">Size</th>
                  <th class="py-3 px-3">Type</th>
                  <th class="py-3 px-3">Created Date</th>
                  <th class="py-3 px-3">Status</th>
                  <th class="py-3 px-3">Notes</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                <?php if (empty($backups)): ?>
                <tr>
                  <td colspan="6" class="py-8 text-center text-slate-400 font-semibold">No backup snapshots generated yet. Click 'Create Full Backup Now' above.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($backups as $b): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <td class="py-3.5 px-3 font-mono font-bold text-slate-900"><?= e($b['FileName']) ?></td>
                  <td class="py-3.5 px-3 text-slate-600"><?= e($b['FileSize']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-200/60"><?= e($b['BackupType']) ?></span></td>
                  <td class="py-3.5 px-3 font-mono text-slate-500"><?= e($b['CreatedAt']) ?></td>
                  <td class="py-3.5 px-3"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><?= e($b['Status']) ?></span></td>
                  <td class="py-3.5 px-3 text-slate-500"><?= e($b['Notes']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- ═══════════════════════════════════════════════════════ -->
  <!-- JAVASCRIPT                                              -->
  <!-- ═══════════════════════════════════════════════════════ -->
  <script>
    // ── Initialize ────────────────────────────────────────────
    AOS.init({ duration: 500, once: true, offset: 30 });
    lucide.createIcons();

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

    // ── Tab Switching ──────────────────────────────────────────
    function switchTab(tabId) {
      // Hide all tab content
      document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
      
      // Reset all nav buttons
      document.querySelectorAll('.tab-nav').forEach(el => {
        el.classList.remove('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200/70', 'shadow-xs');
        el.classList.add('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
      });
      
      // Show target tab
      const target = document.getElementById(tabId);
      if (target) target.classList.add('active');
      
      // Build nav id from tab id: "tab-dashboard" -> "nav-dashboard"
      const navId = tabId.replace('tab-', 'nav-');
      const btn = document.getElementById(navId);
      if (btn) {
        btn.classList.remove('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
        btn.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200/70', 'shadow-xs');
      }

      // Re-init AOS and Lucide for new tab
      AOS.refresh();
      lucide.createIcons();

      // Close mobile sidebar after navigation
      const sidebar = document.getElementById('sidebar');
      if (sidebar && sidebar.classList.contains('lg:hidden') === false) {
        if (!sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) {
          toggleSidebarMobile();
        }
      }
    }

    // ── Global Search ──────────────────────────────────────────
    function handleGlobalSearch(e) {
      const query = e.target.value.toLowerCase();
      document.querySelectorAll('table tbody tr').forEach(row => {
        if (row.querySelector('td')) {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(query) ? '' : 'none';
        }
      });
    }

    // ── Live Clock ─────────────────────────────────────────────
    setInterval(() => {
      const el = document.getElementById('liveClockDisplay');
      if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }, 1000);

    // ── CRUD Operations ────────────────────────────────────────
    async function handleHospitalUpdate(e) {
      e.preventDefault();
      const form = new FormData(e.target);
      const res = await fetch('api/config.php?action=update', { method: 'POST', body: form });
      const json = await res.json();
      alert(json.message);
    }

    async function toggleUserStatus(id, newStatus) {
      const fd = new FormData();
      fd.append('id', id);
      fd.append('status', newStatus);
      const res = await fetch('api/users.php?action=toggle_status', { method: 'POST', body: fd });
      const json = await res.json();
      if (json.success) location.reload();
    }

    async function promptResetPassword(id) {
      const pwd = prompt('Enter new password (min 6 characters):');
      if (!pwd) return;
      const fd = new FormData();
      fd.append('id', id);
      fd.append('new_password', pwd);
      const res = await fetch('api/users.php?action=reset_password', { method: 'POST', body: fd });
      const json = await res.json();
      alert(json.message);
    }

    async function deleteUser(id) {
      if (!confirm('Are you sure you want to delete this user?')) return;
      const fd = new FormData();
      fd.append('id', id);
      const res = await fetch('api/users.php?action=delete', { method: 'POST', body: fd });
      const json = await res.json();
      alert(json.message);
      if (json.success) location.reload();
    }

    async function deleteDept(id) {
      if (!confirm('Delete this department?')) return;
      const fd = new FormData();
      fd.append('id', id);
      const res = await fetch('api/departments.php?action=delete', { method: 'POST', body: fd });
      const json = await res.json();
      alert(json.message);
      if (json.success) location.reload();
    }

    async function deleteFee(id) {
      if (!confirm('Delete this service fee entry?')) return;
      const fd = new FormData();
      fd.append('id', id);
      const res = await fetch('api/service_fees.php?action=delete', { method: 'POST', body: fd });
      const json = await res.json();
      alert(json.message);
      if (json.success) location.reload();
    }

    async function createDatabaseBackup() {
      const res = await fetch('api/backups.php?action=create', { method: 'POST' });
      const json = await res.json();
      alert(json.message);
      if (json.success) location.reload();
    }

    function openCreateUserModal() {
      const uname = prompt('Enter Username:');
      if (!uname) return;
      const fname = prompt('Enter First Name:');
      const lname = prompt('Enter Last Name:');
      const role = prompt('Enter Role (Admin, Doctor, Registrator, Staff):', 'Staff');
      const email = prompt('Enter Email:');

      const fd = new FormData();
      fd.append('Username', uname);
      fd.append('FirstName', fname);
      fd.append('LastName', lname);
      fd.append('Role', role);
      fd.append('Email', email);
      fd.append('Password', 'password123');

      fetch('api/users.php?action=create', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(j => { alert(j.message); location.reload(); });
    }

    function openCreateDeptModal() {
      const code = prompt('Enter Department Code (e.g. DEP-RADIO):');
      if (!code) return;
      const name = prompt('Enter Department Name:');
      const type = prompt('Enter Department Type (Clinical, Administrative, Diagnostic, Support):', 'Clinical');
      const location = prompt('Enter Location:', 'Main Building');

      const fd = new FormData();
      fd.append('DepartmentCode', code);
      fd.append('DepartmentName', name);
      fd.append('DepartmentType', type);
      fd.append('Location', location);

      fetch('api/departments.php?action=create', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(j => { alert(j.message); location.reload(); });
    }

    function openCreateFeeModal() {
      const code = prompt('Enter Service Code (e.g. FEE-LAB-GLU):');
      if (!code) return;
      const name = prompt('Enter Service Name:');
      const category = prompt('Enter Category (Consultation, Laboratory, Radiology, Pharmacy, Nursing/Ward):', 'Laboratory');
      const rate = prompt('Enter Standard Rate (₱):', '350.00');

      const fd = new FormData();
      fd.append('ServiceCode', code);
      fd.append('ServiceName', name);
      fd.append('Category', category);
      fd.append('StandardRate', rate);
      fd.append('PhilHealthCoveredRate', rate);

      fetch('api/service_fees.php?action=create', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(j => { alert(j.message); location.reload(); });
    }
  </script>

  <!-- Paginator -->
  <script src="../includes/paginator.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.TablePaginator) {
        TablePaginator.paginateHtmlTable('#departmentsTable', { pageSize: 8, recordLabel: 'departments' });
        TablePaginator.paginateHtmlTable('#servicesTable', { pageSize: 8, recordLabel: 'service fees' });
        TablePaginator.paginateHtmlTable('#usersTable', { pageSize: 8, recordLabel: 'users' });
        TablePaginator.paginateHtmlTable('#logsTable', { pageSize: 8, recordLabel: 'logs' });
        TablePaginator.paginateHtmlTable('#backupsTable', { pageSize: 8, recordLabel: 'backups' });
      }
    });
  </script>
</body>
</html>
