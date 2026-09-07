<?php
// Nurse/includes/topbar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$currentUser = Session::getCurrentUser();

$hour = (int)date('H');
if ($hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}
?>

<!-- TOP NAVIGATION BAR -->
<header class="h-20 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
  
  <!-- Mobile Menu Toggle -->
  <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-slate-600 hover:bg-slate-100 rounded-xl transition mr-3">
    <i data-lucide="menu" class="w-5 h-5"></i>
  </button>

  <!-- Left: Nurse Greeting & Department -->
  <div class="flex items-center gap-4">
    <div>
      <div class="flex items-center gap-2.5 flex-wrap">
        <h2 class="text-xl font-black text-slate-900 tracking-tight font-display">
          <?= e($greeting) ?>, <?= e($currentUser['name'] ?? 'Maria Santos') ?>
        </h2>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border bg-teal-50 text-teal-700 border-teal-200 shadow-xs">
          <span>🩺</span>
          <span>Nurse On Duty</span>
        </span>
      </div>
      <p class="text-xs font-medium text-slate-400 mt-0.5 flex items-center gap-2">
        <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Shift Active
        </span>
        <span>•</span>
        <span><?= e($currentUser['department'] ?? 'General Ward') ?></span>
      </p>
    </div>
  </div>

  <!-- Right: Date, Quick Actions & Notifications -->
  <div class="flex items-center gap-3.5">
    
    <!-- Today Date & Live Clock -->
    <div class="hidden lg:flex items-center gap-2 px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-600 shadow-xs">
      <i data-lucide="calendar" class="w-3.5 h-3.5 text-teal-600"></i>
      <span><?= date('l, d M Y') ?></span>
      <span class="text-slate-300">|</span>
      <i data-lucide="clock" class="w-3.5 h-3.5 text-teal-600"></i>
      <span id="liveClock" class="font-mono text-slate-800"><?= date('h:i A') ?></span>
    </div>

    <!-- Quick Action: New Vital Signs -->
    <button onclick="openModal('createVitalSignsModal')" 
       class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-teal-50 text-teal-700 hover:bg-teal-100/80 border border-teal-200 rounded-xl text-xs font-bold transition shadow-xs">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i>
      <span>New Vitals</span>
    </button>

    <!-- Notifications Dropdown Trigger -->
    <div class="relative">
      <button type="button" 
              onclick="document.getElementById('notifDropdown').classList.toggle('hidden')"
              class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 transition relative">
        <i data-lucide="bell" class="w-4 h-4"></i>
        <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white animate-badge-bounce"></span>
      </button>

      <!-- Dropdown -->
      <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl p-4 space-y-3 z-50">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
          <span class="text-xs font-bold text-slate-900">Department Notifications</span>
          <span class="text-[10px] font-bold bg-teal-100 text-teal-800 px-2 py-0.5 rounded-full">4 New</span>
        </div>
        <div class="space-y-2 text-xs max-h-64 overflow-y-auto">
          <div class="p-2.5 bg-blue-50/60 rounded-xl border border-blue-100 space-y-0.5 cursor-pointer hover:bg-blue-50 transition">
            <p class="font-bold text-blue-900">New Doctor Instruction</p>
            <p class="text-[11px] text-slate-500">Dr. Reyes added instructions for Room 204.</p>
            <p class="text-[10px] text-slate-400 mt-1">2 minutes ago</p>
          </div>
          <div class="p-2.5 bg-amber-50/60 rounded-xl border border-amber-100 space-y-0.5 cursor-pointer hover:bg-amber-50 transition">
            <p class="font-bold text-amber-900">Patient Status Changed</p>
            <p class="text-[11px] text-slate-500">Juan Dela Cruz changed to "Needs Attention."</p>
            <p class="text-[10px] text-slate-400 mt-1">5 minutes ago</p>
          </div>
          <div class="p-2.5 bg-teal-50/60 rounded-xl border border-teal-100 space-y-0.5 cursor-pointer hover:bg-teal-50 transition">
            <p class="font-bold text-teal-900">New Assigned Task</p>
            <p class="text-[11px] text-slate-500">You have been assigned a new patient monitoring task.</p>
            <p class="text-[10px] text-slate-400 mt-1">12 minutes ago</p>
          </div>
          <div class="p-2.5 bg-purple-50/60 rounded-xl border border-purple-100 space-y-0.5 cursor-pointer hover:bg-purple-50 transition">
            <p class="font-bold text-purple-900">Queue Update</p>
            <p class="text-[11px] text-slate-500">Patient queue has been updated by Registrar.</p>
            <p class="text-[10px] text-slate-400 mt-1">18 minutes ago</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Nurse Avatar -->
    <a href="<?= nurse_url('views/settings/index.php') ?>" class="flex items-center gap-2.5 pl-1">
      <img src="<?= e($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1594824476967-48c8b964ac31?auto=format&fit=crop&q=80&w=150&h=150') ?>" 
           alt="Nurse Photo" 
           class="w-10 h-10 rounded-xl object-cover ring-2 ring-teal-600/30 hover:ring-teal-600 transition shadow-xs">
    </a>

  </div>

</header>

<script>
  function updateLiveClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }
  setInterval(updateLiveClock, 1000);

  // Close notification dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notifDropdown');
    const trigger = e.target.closest('button');
    if (dropdown && !dropdown.contains(e.target) && (!trigger || !trigger.querySelector('[data-lucide="bell"]'))) {
      dropdown.classList.add('hidden');
    }
  });
</script>
