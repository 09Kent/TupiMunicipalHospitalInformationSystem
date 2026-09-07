<?php
// Nurse/includes/patient_monitor.php

/**
 * Render Interactive Patient Monitoring Visualization
 * Replaces the anatomy model from Doctor section with a nurse-specific patient monitoring view
 */
function render_patient_monitor(array $patient): void
{
    $status = $patient['status'] ?? 'Stable';
    $statusColor = '#14b8a6'; // teal for stable
    $statusGlow = 'rgba(20, 184, 166, 0.6)';
    
    if ($status === 'Critical') {
        $statusColor = '#ef4444';
        $statusGlow = 'rgba(239, 68, 68, 0.6)';
    } elseif ($status === 'Needs Attention') {
        $statusColor = '#f59e0b';
        $statusGlow = 'rgba(245, 158, 11, 0.6)';
    } elseif ($status === 'Under Observation') {
        $statusColor = '#3b82f6';
        $statusGlow = 'rgba(59, 130, 246, 0.6)';
    } elseif ($status === 'For Discharge') {
        $statusColor = '#8b5cf6';
        $statusGlow = 'rgba(139, 92, 246, 0.6)';
    }
    
    $hrStatus = getVitalStatus('heart_rate', $patient['heart_rate']);
    $tempStatus = getVitalStatus('temperature', $patient['temperature']);
    $spo2Status = getVitalStatus('spo2', $patient['spo2']);
    $bpParts = explode('/', $patient['bp']);
    $bpStatus = getVitalStatus('bp_systolic', (int)$bpParts[0]);
?>

<div class="bg-gradient-to-b from-slate-900 to-slate-950 text-white rounded-3xl p-6 shadow-2xl relative overflow-hidden border border-slate-800 min-h-[520px]" id="patientMonitorPanel">
  
  <!-- Ambient Background Glows -->
  <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl opacity-20 pointer-events-none" style="background-color: <?= $statusColor ?>;"></div>
  <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full blur-3xl opacity-15 pointer-events-none bg-teal-500"></div>

  <!-- Header -->
  <div class="relative z-10 flex items-center justify-between mb-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full animate-ping" style="background-color: <?= $statusColor ?>;"></span>
        <h3 class="text-sm font-black uppercase tracking-wider text-slate-300">Patient Monitoring</h3>
      </div>
      <p class="text-[11px] text-slate-500 mt-0.5">Real-time Vital Sign Observation</p>
    </div>
    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border" 
          style="background-color: <?= $statusColor ?>20; color: <?= $statusColor ?>; border-color: <?= $statusColor ?>40;">
      <?= e($status) ?>
    </span>
  </div>

  <!-- Patient Info -->
  <div class="relative z-10 bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10 mb-4">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white" style="background-color: <?= $statusColor ?>30;">
        <i data-lucide="user" class="w-6 h-6"></i>
      </div>
      <div>
        <h4 class="text-base font-bold text-white" id="monitorPatientName"><?= e($patient['name']) ?></h4>
        <div class="flex items-center gap-3 text-xs text-slate-400 mt-0.5">
          <span>Age: <?= e($patient['age']) ?></span>
          <span>•</span>
          <span><?= e($patient['room']) ?></span>
          <span>•</span>
          <span><?= e($patient['gender']) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ECG / Monitoring Visualization -->
  <div class="relative z-10 mb-4">
    <svg viewBox="0 0 400 80" class="w-full h-20 opacity-60" preserveAspectRatio="none">
      <defs>
        <linearGradient id="ecgGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" style="stop-color:<?= $statusColor ?>;stop-opacity:0" />
          <stop offset="50%" style="stop-color:<?= $statusColor ?>;stop-opacity:1" />
          <stop offset="100%" style="stop-color:<?= $statusColor ?>;stop-opacity:0" />
        </linearGradient>
      </defs>
      <!-- ECG waveform -->
      <polyline fill="none" stroke="url(#ecgGrad)" stroke-width="2" class="ecg-line"
        points="0,40 30,40 40,40 50,38 55,42 60,40 80,40 90,40 95,20 100,60 105,10 110,55 115,40 130,40 160,40 170,40 180,38 185,42 190,40 210,40 220,40 225,20 230,60 235,10 240,55 245,40 260,40 290,40 300,40 310,38 315,42 320,40 340,40 350,40 355,20 360,60 365,10 370,55 375,40 400,40" />
      <!-- Grid lines -->
      <line x1="0" y1="20" x2="400" y2="20" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/>
      <line x1="0" y1="40" x2="400" y2="40" stroke="rgba(255,255,255,0.08)" stroke-width="0.5"/>
      <line x1="0" y1="60" x2="400" y2="60" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/>
    </svg>
  </div>

  <!-- Vital Sign Indicators Grid -->
  <div class="relative z-10 grid grid-cols-2 gap-3">
    
    <!-- Heart Rate -->
    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10 group hover:bg-white/10 transition">
      <div class="flex items-center justify-between mb-1">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Heart Rate</span>
        <div class="animate-heartbeat">
          <i data-lucide="heart" class="w-4 h-4" style="color: <?= $hrStatus === 'Normal' ? '#ef4444' : ($hrStatus === 'Warning' ? '#f59e0b' : '#ef4444') ?>;"></i>
        </div>
      </div>
      <div class="flex items-baseline gap-1">
        <span class="text-2xl font-black text-white counter-value" id="monitorHR"><?= $patient['heart_rate'] ?></span>
        <span class="text-xs text-slate-400">BPM</span>
      </div>
      <div class="flex items-center gap-1 mt-1">
        <span class="w-1.5 h-1.5 rounded-full <?= $hrStatus === 'Normal' ? 'bg-emerald-400' : ($hrStatus === 'Warning' ? 'bg-amber-400' : 'bg-red-400 animate-pulse') ?>"></span>
        <span class="text-[10px] font-semibold <?= $hrStatus === 'Normal' ? 'text-emerald-400' : ($hrStatus === 'Warning' ? 'text-amber-400' : 'text-red-400') ?>"><?= $hrStatus ?></span>
      </div>
    </div>

    <!-- Blood Pressure -->
    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10 group hover:bg-white/10 transition">
      <div class="flex items-center justify-between mb-1">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Blood Pressure</span>
        <i data-lucide="activity" class="w-4 h-4 text-blue-400 animate-vital-glow"></i>
      </div>
      <div class="flex items-baseline gap-1">
        <span class="text-2xl font-black text-white counter-value" id="monitorBP"><?= e($patient['bp']) ?></span>
        <span class="text-xs text-slate-400">mmHg</span>
      </div>
      <div class="flex items-center gap-1 mt-1">
        <span class="w-1.5 h-1.5 rounded-full <?= $bpStatus === 'Normal' ? 'bg-emerald-400' : ($bpStatus === 'Warning' ? 'bg-amber-400' : 'bg-red-400 animate-pulse') ?>"></span>
        <span class="text-[10px] font-semibold <?= $bpStatus === 'Normal' ? 'text-emerald-400' : ($bpStatus === 'Warning' ? 'text-amber-400' : 'text-red-400') ?>"><?= $bpStatus ?></span>
      </div>
    </div>

    <!-- Temperature -->
    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10 group hover:bg-white/10 transition">
      <div class="flex items-center justify-between mb-1">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Temperature</span>
        <i data-lucide="thermometer" class="w-4 h-4 text-orange-400"></i>
      </div>
      <div class="flex items-baseline gap-1">
        <span class="text-2xl font-black text-white counter-value" id="monitorTemp"><?= $patient['temperature'] ?></span>
        <span class="text-xs text-slate-400">°C</span>
      </div>
      <div class="flex items-center gap-1 mt-1">
        <span class="w-1.5 h-1.5 rounded-full <?= $tempStatus === 'Normal' ? 'bg-emerald-400' : ($tempStatus === 'Warning' ? 'bg-amber-400' : 'bg-red-400 animate-pulse') ?>"></span>
        <span class="text-[10px] font-semibold <?= $tempStatus === 'Normal' ? 'text-emerald-400' : ($tempStatus === 'Warning' ? 'text-amber-400' : 'text-red-400') ?>"><?= $tempStatus ?></span>
      </div>
    </div>

    <!-- SpO2 -->
    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10 group hover:bg-white/10 transition">
      <div class="flex items-center justify-between mb-1">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">SpO₂</span>
        <i data-lucide="wind" class="w-4 h-4 text-cyan-400 animate-breathe"></i>
      </div>
      <div class="flex items-baseline gap-1">
        <span class="text-2xl font-black text-white counter-value" id="monitorSpO2"><?= $patient['spo2'] ?></span>
        <span class="text-xs text-slate-400">%</span>
      </div>
      <div class="flex items-center gap-1 mt-1">
        <span class="w-1.5 h-1.5 rounded-full <?= $spo2Status === 'Normal' ? 'bg-emerald-400' : ($spo2Status === 'Warning' ? 'bg-amber-400' : 'bg-red-400 animate-pulse') ?>"></span>
        <span class="text-[10px] font-semibold <?= $spo2Status === 'Normal' ? 'text-emerald-400' : ($spo2Status === 'Warning' ? 'text-amber-400' : 'text-red-400') ?>"><?= $spo2Status ?></span>
      </div>
    </div>

  </div>

  <!-- Last Updated -->
  <div class="relative z-10 mt-4 flex items-center justify-between text-[10px] text-slate-500">
    <span class="flex items-center gap-1">
      <i data-lucide="clock" class="w-3 h-3"></i>
      Last updated: <?= e($patient['last_update']) ?>
    </span>
    <span class="flex items-center gap-1">
      <i data-lucide="shield-check" class="w-3 h-3"></i>
      Live Monitoring
    </span>
  </div>

</div>

<?php } ?>
