<?php
// Nurse/views/notifications/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/demo_data.php';

$pageTitle = 'Department Notifications | Nurse Portal • Tupi Municipal Hospital';
$activeMenu = 'notifications';

$currentUser = Session::getCurrentUser();
$notifications = getDemoNotifications();

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
          <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">Alert Center</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-display">Department Notifications</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Real-time alerts, physician order updates, and queue dispatches</p>
      </div>

      <div class="flex items-center gap-3">
        <button onclick="markAllAsRead()" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-2">
          <i data-lucide="check-check" class="w-4 h-4 text-teal-600"></i>
          <span>Mark All Read</span>
        </button>
      </div>
    </section>

    <!-- Notification Feed -->
    <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card space-y-4 max-w-4xl" data-aos="fade-up">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <h3 class="font-bold text-sm text-slate-900 font-display">Recent Activity Timeline</h3>
        <span class="text-xs font-semibold text-teal-600">Active Shift Feed</span>
      </div>

      <div class="space-y-3" id="notifList">
        <?php foreach ($notifications as $n): ?>
        <div class="p-4 rounded-2xl border transition-all duration-200 flex items-start gap-4 <?= !$n['read'] ? 'bg-teal-50/40 border-teal-100' : 'bg-slate-50/50 border-slate-100' ?> hover:shadow-xs">
          <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 <?= !$n['read'] ? 'bg-teal-100 text-teal-700' : 'bg-slate-200 text-slate-500' ?>">
            <i data-lucide="<?= $n['icon'] ?>" class="w-5 h-5"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
              <h4 class="font-bold text-sm text-slate-900"><?= e($n['title']) ?></h4>
              <span class="text-[11px] text-slate-400 shrink-0 font-medium"><?= e($n['time']) ?></span>
            </div>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= e($n['message']) ?></p>
          </div>
          <?php if (!$n['read']): ?>
            <span class="w-2.5 h-2.5 rounded-full bg-teal-500 shrink-0 mt-2"></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

  </main>
</div>

<script>
function markAllAsRead() {
  alert('All departmental notifications marked as read.');
  document.querySelectorAll('#notifList .bg-teal-50\\/40').forEach(el => {
    el.classList.remove('bg-teal-50/40', 'border-teal-100');
    el.classList.add('bg-slate-50/50', 'border-slate-100');
  });
  document.querySelectorAll('#notifList .bg-teal-500').forEach(dot => dot.remove());
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
