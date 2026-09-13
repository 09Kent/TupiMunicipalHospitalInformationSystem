<?php
// Doctor/includes/topbar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$currentUser = Session::getCurrentUser();
$specialty = $currentUser['specialty'] ?? 'Cardiologist';

// Time based greeting
$hour = (int)date('H');
if ($hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}

// Map specialty to badge color
$badgeClasses = 'bg-blue-50 text-blue-700 border-blue-200';
$specialtyEmoji = '🩺';
if (stripos($specialty, 'Cardio') !== false) {
    $badgeClasses = 'bg-rose-50 text-rose-700 border-rose-200';
    $specialtyEmoji = '🫀';
} elseif (stripos($specialty, 'Neuro') !== false) {
    $badgeClasses = 'bg-purple-50 text-purple-700 border-purple-200';
    $specialtyEmoji = '🧠';
} elseif (stripos($specialty, 'Gastro') !== false) {
    $badgeClasses = 'bg-amber-50 text-amber-700 border-amber-200';
    $specialtyEmoji = '🧪';
} elseif (stripos($specialty, 'Pulmo') !== false) {
    $badgeClasses = 'bg-cyan-50 text-cyan-700 border-cyan-200';
    $specialtyEmoji = '🫁';
} elseif (stripos($specialty, 'Ortho') !== false) {
    $badgeClasses = 'bg-emerald-50 text-emerald-700 border-emerald-200';
    $specialtyEmoji = '🦴';
} elseif (stripos($specialty, 'Pedia') !== false) {
    $badgeClasses = 'bg-pink-50 text-pink-700 border-pink-200';
    $specialtyEmoji = '👶';
} elseif (stripos($specialty, 'Surgeon') !== false) {
    $badgeClasses = 'bg-indigo-50 text-indigo-700 border-indigo-200';
    $specialtyEmoji = '✂️';
}
?>

<!-- TOP NAVIGATION BAR -->
<header class="h-20 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
  
  <!-- Left: Doctor Greeting & Specialty -->
  <div class="flex items-center gap-4">
    <div>
      <div class="flex items-center gap-2.5">
        <h2 class="text-xl font-black text-slate-900 tracking-tight font-display">
          <?= e($greeting) ?>, <?= e($currentUser['name'] ?? 'Dr. Daniel Lewis') ?>
        </h2>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $badgeClasses ?> shadow-xs">
          <span><?= $specialtyEmoji ?></span>
          <span><?= e($specialty) ?></span>
        </span>
      </div>
      <p class="text-xs font-medium text-slate-400 mt-0.5 flex items-center gap-2">
        <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Clinical Session Active
        </span>
        <span>•</span>
        <span><?= e($currentUser['clinic'] ?? 'Cardiology Wing (Suite 501)') ?></span>
      </p>
    </div>
  </div>

  <!-- Right: Real-time Date, Quick Shortcuts & Notifications -->
  <div class="flex items-center gap-3.5">
    
    <!-- Today Date & Live Clock -->
    <div class="hidden lg:flex items-center gap-2 px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-600 shadow-xs">
      <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600"></i>
      <span><?= date('l, d M Y') ?></span>
      <span class="text-slate-300">|</span>
      <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600"></i>
      <span id="liveClock" class="font-mono text-slate-800"><?= date('h:i A') ?></span>
    </div>

    <!-- Quick Action: New Prescription -->
    <a href="<?= doctor_url('views/prescriptions/index.php?new=1') ?>" 
       class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100/80 border border-blue-200 rounded-xl text-xs font-bold transition shadow-xs">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i>
      <span>New Rx</span>
    </a>

    <!-- Quick Action: Lab Request -->
    <a href="<?= doctor_url('views/laboratory/index.php?new=1') ?>" 
       class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold transition shadow-xs">
      <i data-lucide="flask-conical" class="w-3.5 h-3.5 text-indigo-600"></i>
      <span>Order Lab</span>
    </a>

    <!-- Notifications Dropdown Trigger -->
    <div class="relative">
      <button type="button" 
              onclick="document.getElementById('notifDropdown').classList.toggle('hidden')"
              class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 transition relative">
        <i data-lucide="bell" class="w-4 h-4"></i>
        <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
      </button>

      <!-- Dropdown -->
      <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl p-4 space-y-3 z-50 animate-in fade-in slide-in-from-top-2">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
          <span class="text-xs font-bold text-slate-900">Clinical Notifications</span>
          <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">3 New</span>
        </div>
        <div class="space-y-2 text-xs">
          <div class="p-2.5 bg-blue-50/60 rounded-xl border border-blue-100 space-y-0.5">
            <p class="font-bold text-blue-900">New Patient Assigned to Queue</p>
            <p class="text-[11px] text-slate-500">Sophia Reyes routed via Pre-Consultation intake.</p>
          </div>
          <div class="p-2.5 bg-emerald-50/60 rounded-xl border border-emerald-100 space-y-0.5">
            <p class="font-bold text-emerald-900">Lab Results Ready</p>
            <p class="text-[11px] text-slate-500">ECG & Lipid Panel for David Miller completed.</p>
          </div>
          <div class="p-2.5 bg-purple-50/60 rounded-xl border border-purple-100 space-y-0.5">
            <p class="font-bold text-purple-900">Referral Accepted</p>
            <p class="text-[11px] text-slate-500">Dr. Elena Villanueva accepted neurology referral.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Doctor Avatar -->
    <a href="<?= doctor_url('views/settings/index.php') ?>" class="flex items-center gap-2.5 pl-1">
      <img src="<?= e($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=150&h=150') ?>" 
           alt="Doctor Photo" 
           class="w-10 h-10 rounded-xl object-cover ring-2 ring-blue-600/30 hover:ring-blue-600 transition shadow-xs">
    </a>

  </div>

</header>

<script>
  // Update live clock every second
  function updateLiveClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }
  setInterval(updateLiveClock, 1000);
</script>
