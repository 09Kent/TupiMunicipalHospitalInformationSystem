<?php
// Doctor/includes/sidebar.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../models/Doctor.php';

$activeMenu = $activeMenu ?? 'dashboard';
$currentUser = Session::getCurrentUser();

// Fetch all available doctors for quick specialty switcher
$docObj = new Doctor();
$allSpecialistDocs = $docObj->getAll();
?>

<!-- LEFT SIDEBAR -->
<aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col shrink-0 h-screen sticky top-0 z-40 select-none shadow-xs transition-all duration-300">
  
  <!-- Hospital Branding Header -->
  <div class="h-20 px-6 flex items-center gap-3 border-b border-slate-100">
    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/25 ring-4 ring-blue-50">
      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
      </svg>
    </div>
    <div>
      <div class="flex items-center gap-1.5">
        <h1 class="font-extrabold text-slate-900 tracking-tight text-lg leading-tight font-display">Tupi Municipal Hospital</h1>
        <span class="px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 rounded-md">EMR</span>
      </div>
      <p class="text-[11px] font-semibold text-slate-400">Doctor Clinical Portal</p>
    </div>
  </div>

  <!-- Specialty Switcher (Live Multi-Specialty Demo Bar) -->
  <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100">
    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5 flex items-center justify-between">
      <span>Active Specialist</span>
      <span class="text-[9px] text-blue-600 font-extrabold lowercase">Live Switch</span>
    </label>
    <div class="relative">
      <select onchange="if(this.value) window.location.href=this.value;" 
              class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-xs cursor-pointer">
        <?php foreach ($allSpecialistDocs as $specDoc): ?>
          <option value="<?= doctor_url('views/dashboard/index.php?switch_doctor=' . $specDoc['DoctorID']) ?>" 
                  <?= ($specDoc['DoctorID'] == ($currentUser['doctor_id'] ?? 11)) ? 'selected' : '' ?>>
            <?= e($specDoc['SpecialtyName'] ?: $specDoc['Specialty']) ?> — Dr. <?= e($specDoc['LastName']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Navigation Menu -->
  <div class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
    
    <!-- Dashboard -->
    <a href="<?= doctor_url('views/dashboard/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'dashboard' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="layout-dashboard" class="w-4 h-4 <?= $activeMenu === 'dashboard' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Dashboard</span>
      <?php if ($activeMenu === 'dashboard'): ?>
        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
      <?php endif; ?>
    </a>

    <!-- Patients -->
    <a href="<?= doctor_url('views/patients/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'patients' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="users" class="w-4 h-4 <?= $activeMenu === 'patients' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Patients</span>
    </a>

    <!-- Diagnosis & Treatment -->
    <a href="<?= doctor_url('views/diagnosis/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'diagnosis' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="stethoscope" class="w-4 h-4 <?= $activeMenu === 'diagnosis' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Diagnosis & Treatment</span>
    </a>

    <!-- Prescription -->
    <a href="<?= doctor_url('views/prescriptions/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'prescriptions' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="file-text" class="w-4 h-4 <?= $activeMenu === 'prescriptions' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Prescription</span>
    </a>

    <!-- Laboratory Request -->
    <a href="<?= doctor_url('views/laboratory/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'laboratory' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="flask-conical" class="w-4 h-4 <?= $activeMenu === 'laboratory' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Laboratory Request</span>
    </a>

    <!-- Referral -->
    <a href="<?= doctor_url('views/referrals/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'referrals' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="share-2" class="w-4 h-4 <?= $activeMenu === 'referrals' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Referral</span>
    </a>

    <!-- Medical Certificate -->
    <a href="<?= doctor_url('views/certificates/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'certificates' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="award" class="w-4 h-4 <?= $activeMenu === 'certificates' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Medical Certificate</span>
    </a>

    <div class="pt-3 pb-1">
      <div class="border-t border-slate-100 my-1"></div>
      <span class="px-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Management</span>
    </div>

    <!-- Settings -->
    <a href="<?= doctor_url('views/settings/index.php') ?>" 
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group <?= $activeMenu === 'settings' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/60' ?>">
      <i data-lucide="settings" class="w-4 h-4 <?= $activeMenu === 'settings' ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' ?>"></i>
      <span>Settings</span>
    </a>

    <!-- Switch to Registrator Portal -->
    <a href="<?= register_url('views/dashboard/index.php') ?>" target="_blank"
       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/60 transition group border border-dashed border-slate-200">
      <div class="flex items-center gap-2">
        <i data-lucide="external-link" class="w-3.5 h-3.5 text-indigo-500"></i>
        <span>Registrator Portal</span>
      </div>
      <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono">INTAKE</span>
    </a>

  </div>

  <!-- Doctor Mini Profile & Logout Footer -->
  <div class="p-4 border-t border-slate-100 bg-slate-50/50">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3 min-w-0">
        <img src="<?= e($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=150&h=150') ?>" 
             alt="Doctor Avatar" 
             class="w-9 h-9 rounded-xl object-cover ring-2 ring-blue-500/20 shrink-0">
        <div class="min-w-0">
          <p class="text-xs font-bold text-slate-900 truncate"><?= e($currentUser['name'] ?? 'Dr. Daniel Lewis') ?></p>
          <p class="text-[11px] font-semibold text-blue-600 truncate"><?= e($currentUser['specialty'] ?? 'Cardiologist') ?></p>
        </div>
      </div>
      <a href="<?= doctor_url('views/auth/login.php?logout=1') ?>" 
         title="Sign Out" 
         class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
        <i data-lucide="log-out" class="w-4 h-4"></i>
      </a>
    </div>
  </div>

</aside>
