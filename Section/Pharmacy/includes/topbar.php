<?php
// Pharmacy/includes/topbar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/demo_data.php';

$currentUser  = Session::getCurrentUser();
$notifications = getDemoNotifications();
$unreadCount  = count(array_filter($notifications, fn($n) => $n['unread']));
?>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- TOP NAVIGATION BAR                                      -->
<!-- ═══════════════════════════════════════════════════════ -->
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
          Pharmacy Dashboard
        </h2>
        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-sky-50 text-sky-700 border-sky-200">
          <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
          <span>PMS Online</span>
        </span>
      </div>
      <p class="text-[11px] font-medium text-slate-400 mt-0.5 flex items-center gap-1.5">
        <span><?= e($currentUser['department']) ?></span>
        <span>•</span>
        <span class="text-sky-600 font-semibold"><?= e($currentUser['shift']) ?></span>
      </p>
    </div>
  </div>

  <!-- Right: Search, Quick Actions, Clock, Notifications -->
  <div class="flex items-center gap-2.5">

    <!-- Global Search -->
    <div class="relative w-44 md:w-60 lg:w-72">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
        <i data-lucide="search" class="w-3.5 h-3.5"></i>
      </div>
      <input type="text"
             id="globalSearchInput"
             onkeyup="handleGlobalSearch(event)"
             placeholder="Search pharmacy records..."
             class="w-full pl-9 pr-8 py-2 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition shadow-xs">
      <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
        <span class="text-[10px] font-bold text-slate-400 bg-slate-200/60 px-1.5 py-0.5 rounded font-mono hidden md:inline">/</span>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="hidden md:flex items-center gap-2">
      <button onclick="openVerifyRxModal(getdemoPrescriptions()[0])"
              class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl flex items-center gap-1.5 transition active:scale-95 border border-slate-200/60">
        <i data-lucide="clipboard-check" class="w-3.5 h-3.5 text-indigo-500"></i>
        <span>Verify Rx</span>
      </button>
      <button onclick="openStockInModal()"
              class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition shadow-sm shadow-sky-600/20 active:scale-95">
        <i data-lucide="package-plus" class="w-3.5 h-3.5"></i>
        <span>Stock In</span>
      </button>
    </div>

    <!-- Live Clock -->
    <div class="hidden xl:flex items-center gap-1.5 px-3 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-600 font-mono shadow-xs">
      <i data-lucide="clock" class="w-3.5 h-3.5 text-sky-500"></i>
      <span id="liveClockDisplay"><?= date('h:i:s A') ?></span>
    </div>

    <!-- Notifications Bell -->
    <div class="relative">
      <button onclick="toggleNotificationDropdown()"
              class="relative p-2.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl border border-slate-200/60 transition active:scale-95">
        <i data-lucide="bell" class="w-4 h-4 text-slate-700"></i>
        <?php if ($unreadCount > 0): ?>
          <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full animate-ping"></span>
          <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
        <?php endif; ?>
      </button>

      <!-- Notification Dropdown -->
      <div id="notificationDropdown"
           class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 hidden z-50">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <h4 class="text-sm font-bold text-slate-900">Pharmacy Alerts</h4>
            <span class="px-2 py-0.5 text-[10px] font-bold bg-sky-50 text-sky-700 rounded-full border border-sky-100"><?= $unreadCount ?> unread</span>
          </div>
          <button onclick="markAllRead()" class="text-[11px] font-semibold text-sky-600 hover:text-sky-700 hover:underline">Mark all read</button>
        </div>
        <div class="mt-3 space-y-2 max-h-80 overflow-y-auto pr-1">
          <?php foreach ($notifications as $notif): ?>
            <div onclick="handleNotifClick('<?= e($notif['action_link']) ?>')"
                 class="p-3 rounded-xl border <?= $notif['unread'] ? 'bg-sky-50/40 border-sky-100' : 'bg-white border-slate-100 hover:bg-slate-50' ?> transition cursor-pointer flex gap-3 items-start group">
              <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5 
                <?= $notif['type'] === 'critical' ? 'bg-rose-100 text-rose-600' : ($notif['type'] === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600') ?>">
                <i data-lucide="<?= $notif['type'] === 'critical' ? 'alert-triangle' : ($notif['type'] === 'warning' ? 'alert-circle' : 'info') ?>" class="w-3.5 h-3.5"></i>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                  <h5 class="text-xs font-bold text-slate-900 truncate group-hover:text-sky-700 transition"><?= e($notif['title']) ?></h5>
                  <span class="text-[10px] text-slate-400 font-medium shrink-0 ml-2"><?= e($notif['time']) ?></span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed"><?= e($notif['message']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="pt-3 mt-2 border-t border-slate-100 text-center">
          <button onclick="switchMainTab('activity'); toggleNotificationDropdown();" class="text-xs font-bold text-sky-600 hover:text-sky-700">View All Notifications →</button>
        </div>
      </div>
    </div>

    <!-- Profile Avatar -->
    <div onclick="openProfileModal()" class="flex items-center gap-2 pl-2 border-l border-slate-200 cursor-pointer group">
      <img src="<?= e($currentUser['avatar']) ?>" alt="Pharmacist" class="w-8 h-8 rounded-xl object-cover border border-slate-200 shadow-xs group-hover:ring-2 group-hover:ring-sky-500 transition">
    </div>

  </div>
</header>
