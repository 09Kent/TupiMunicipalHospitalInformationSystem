<?php
// Med_Tech/includes/topbar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$currentUser = Session::getCurrentUser();
$notifications = getDemoNotifications();
$unreadCount = count(array_filter($notifications, fn($n) => $n['unread']));
?>

<!-- TOP NAVIGATION BAR -->
<header id="topbar" class="h-20 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
  
  <!-- Left Side: Mobile Menu Button & Title / Breadcrumb -->
  <div class="flex items-center gap-3">
    
    <!-- Mobile Hamburger Toggle -->
    <button onclick="toggleSidebarMobile()" class="lg:hidden p-2 -ml-2 text-slate-600 hover:bg-slate-100 rounded-xl transition">
      <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    <!-- Page Title / Department Status -->
    <div class="hidden sm:block">
      <div class="flex items-center gap-2">
        <h2 class="text-lg font-black text-slate-900 tracking-tight font-display" id="topbarPageTitle">
          Laboratory Dashboard
        </h2>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-indigo-50 text-indigo-700 border-indigo-200">
          <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
          <span>LIS Online</span>
        </span>
      </div>
      <p class="text-xs font-medium text-slate-400 mt-0.5 flex items-center gap-2">
        <span><?= e($currentUser['department']) ?></span>
        <span>•</span>
        <span class="text-emerald-600 font-semibold"><?= e($currentUser['shift']) ?></span>
      </p>
    </div>
  </div>

  <!-- Right Side: Global Search, Quick Actions, Clock & Notifications -->
  <div class="flex items-center gap-3">
    
    <!-- Global Quick Search Bar -->
    <div class="relative w-48 md:w-64 lg:w-72">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
        <i data-lucide="search" class="w-4 h-4"></i>
      </div>
      <input type="text" id="globalSearchInput" onkeyup="handleGlobalSearch(event)" placeholder="Search requests, samples, patients..." class="w-full pl-9 pr-8 py-2 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition shadow-xs">
      <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
        <span class="text-[10px] font-bold text-slate-400 bg-slate-200/60 px-1.5 py-0.5 rounded font-mono hidden md:inline">/</span>
      </div>
    </div>

    <!-- Quick Action: Receive Sample / New Result -->
    <div class="hidden md:flex items-center gap-2">
      <button onclick="openCreateSampleModal()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl flex items-center gap-1.5 transition active:scale-95 border border-slate-200/60">
        <i data-lucide="plus-circle" class="w-4 h-4 text-cyan-600"></i>
        <span>Receive Sample</span>
      </button>

      <button onclick="openCreateResultModal()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition shadow-sm shadow-indigo-600/20 active:scale-95">
        <i data-lucide="file-plus" class="w-4 h-4"></i>
        <span>Record Result</span>
      </button>
    </div>

    <!-- Live Digital Clock -->
    <div class="hidden xl:flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-600 font-mono shadow-xs">
      <i data-lucide="clock" class="w-3.5 h-3.5 text-indigo-500"></i>
      <span id="liveClockDisplay"><?= date('h:i:s A') ?></span>
    </div>

    <!-- Notifications Bell with Dropdown -->
    <div class="relative">
      <button onclick="toggleNotificationDropdown()" class="relative p-2.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl border border-slate-200/60 transition active:scale-95">
        <i data-lucide="bell" class="w-4 h-4 text-slate-700"></i>
        <?php if ($unreadCount > 0): ?>
          <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full animate-ping"></span>
          <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
        <?php endif; ?>
      </button>

      <!-- Notification Dropdown Panel -->
      <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 hidden z-50 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <h4 class="text-sm font-bold text-slate-900">Laboratory Alerts</h4>
            <span class="px-2 py-0.5 text-[10px] font-bold bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100"><?= $unreadCount ?> unread</span>
          </div>
          <button onclick="markAllNotificationsRead()" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 hover:underline">Mark all read</button>
        </div>

        <div class="mt-3 space-y-2.5 max-h-80 overflow-y-auto pr-1">
          <?php foreach ($notifications as $notif): ?>
            <div onclick="handleNotificationClick('<?= $notif['action_link'] ?>')" class="p-3 rounded-xl border <?= $notif['unread'] ? 'bg-indigo-50/40 border-indigo-100' : 'bg-white border-slate-100 hover:bg-slate-50' ?> transition cursor-pointer flex gap-3 items-start group">
              <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5 <?= $notif['type'] === 'critical' ? 'bg-rose-100 text-rose-600' : ($notif['type'] === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600') ?>">
                <i data-lucide="<?= $notif['type'] === 'critical' ? 'alert-triangle' : ($notif['type'] === 'warning' ? 'alert-circle' : 'info') ?>" class="w-4 h-4"></i>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                  <h5 class="text-xs font-bold text-slate-900 truncate group-hover:text-indigo-600 transition"><?= e($notif['title']) ?></h5>
                  <span class="text-[10px] text-slate-400 font-medium shrink-0"><?= e($notif['time']) ?></span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed"><?= e($notif['message']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="pt-3 mt-3 border-t border-slate-100 text-center">
          <button onclick="switchMainTab('notifications'); toggleNotificationDropdown();" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">View All Notifications →</button>
        </div>
      </div>
    </div>

    <!-- Small Profile Indicator -->
    <div onclick="openProfileModal()" class="flex items-center gap-2 pl-2 border-l border-slate-200 cursor-pointer group">
      <img src="<?= e($currentUser['avatar']) ?>" alt="Avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200 shadow-xs group-hover:ring-2 group-hover:ring-indigo-500 transition">
    </div>
  </div>
</header>
