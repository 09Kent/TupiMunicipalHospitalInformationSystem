<?php
// Med_Tech/includes/lab_visualizer.php

/**
 * Render Laboratory Sample Analysis & Automated Hematology/Chemistry Visualizer Centerpiece
 * High-tech medical visualization with animated scanner, optical density reading, and multi-parameter sensor readout
 */
function render_lab_visualizer(array $sample): void
{
    $status = $sample['status'] ?? 'Processing';
    $statusColor = '#6366f1'; // Indigo default
    $statusGlow = 'rgba(99, 102, 241, 0.4)';
    $statusLabel = 'ANALYZER SCANNING';

    if ($status === 'Completed') {
        $statusColor = '#10b981';
        $statusGlow = 'rgba(16, 185, 129, 0.4)';
        $statusLabel = 'ANALYSIS COMPLETE';
    } elseif ($status === 'For Verification') {
        $statusColor = '#8b5cf6';
        $statusGlow = 'rgba(139, 92, 246, 0.4)';
        $statusLabel = 'PENDING VERIFICATION';
    } elseif ($status === 'Received') {
        $statusColor = '#0284c7';
        $statusGlow = 'rgba(2, 132, 199, 0.4)';
        $statusLabel = 'SAMPLE QUEUED';
    } elseif ($status === 'Rejected') {
        $statusColor = '#f43f5e';
        $statusGlow = 'rgba(244, 63, 94, 0.4)';
        $statusLabel = 'SAMPLE REJECTED';
    }
?>

<div class="bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-7 shadow-2xl relative overflow-hidden border border-slate-800/80 min-h-[460px] flex flex-col justify-between" id="labVisualizerPanel">
  
  <!-- Ambient Background Glows -->
  <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full blur-3xl opacity-25 pointer-events-none transition-all duration-700" id="ambientGlow1" style="background-color: <?= $statusColor ?>;"></div>
  <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full blur-3xl opacity-20 pointer-events-none bg-cyan-500"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(120,119,198,0.08),rgba(255,255,255,0))] pointer-events-none"></div>

  <!-- Header & Live Telemetry Indicator -->
  <div class="relative z-10 flex items-center justify-between pb-4 border-b border-slate-800/80">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center justify-center text-cyan-400 shadow-inner">
        <i data-lucide="microscope" class="w-5 h-5"></i>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <span class="text-xs font-black uppercase tracking-widest text-slate-400 font-mono">SPECIMEN ANALYZER V4.2</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold border" id="visualizerStatusBadge" style="background-color: <?= $statusGlow ?>; border-color: <?= $statusColor ?>; color: #fff;">
            <span class="w-1.5 h-1.5 rounded-full animate-ping" style="background-color: <?= $statusColor ?>;"></span>
            <span id="visualizerStatusText"><?= $statusLabel ?></span>
          </span>
        </div>
        <h3 class="text-base font-bold text-white tracking-tight flex items-center gap-2 mt-0.5">
          <span id="visualizerPatientName"><?= e($sample['patient_name']) ?></span>
          <span class="text-xs font-normal text-slate-400 font-mono" id="visualizerPatientId">(<?= e($sample['patient_id']) ?>)</span>
        </h3>
      </div>
    </div>

    <!-- Active Sample & Test Tag -->
    <div class="text-right">
      <span class="inline-block text-[11px] font-bold font-mono text-cyan-300 bg-cyan-950/60 border border-cyan-800/60 px-2.5 py-1 rounded-lg" id="visualizerSampleId">
        <?= e($sample['sample_id']) ?>
      </span>
      <p class="text-[11px] text-slate-400 font-medium mt-1" id="visualizerTestName"><?= e($sample['test']) ?></p>
    </div>
  </div>

  <!-- Centerpiece: 3D-Styled Laboratory Sample Tube Chamber with Animated Laser Scanner & Cytometry Particles -->
  <div class="relative z-10 py-6 my-auto flex flex-col md:flex-row items-center justify-around gap-6">
    
    <!-- Left Telemetry Readout -->
    <div class="space-y-3.5 w-full md:w-44 text-left order-2 md:order-1">
      <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/60 rounded-xl p-3 hover:border-slate-600 transition">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Specimen Type</span>
        <div class="flex items-center gap-2 mt-1">
          <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
          <span class="text-xs font-semibold text-slate-100" id="visualizerSpecimenType"><?= e($sample['sample_type']) ?></span>
        </div>
      </div>

      <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/60 rounded-xl p-3 hover:border-slate-600 transition">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Cell Flow Count</span>
        <div class="flex items-baseline gap-1.5 mt-1">
          <span class="text-lg font-black text-cyan-300 font-mono tracking-tight" id="visualizerCellCount">4.82 M</span>
          <span class="text-[10px] text-slate-400">cells/μL</span>
        </div>
        <div class="w-full bg-slate-700/50 h-1.5 rounded-full overflow-hidden mt-1.5">
          <div class="bg-gradient-to-r from-cyan-500 to-indigo-500 h-full rounded-full w-4/5 animate-pulse"></div>
        </div>
      </div>

      <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/60 rounded-xl p-3 hover:border-slate-600 transition">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Optical Density</span>
        <div class="flex items-baseline justify-between mt-1">
          <span class="text-xs font-bold text-emerald-400 font-mono">0.482 OD</span>
          <span class="text-[10px] text-slate-400">540nm</span>
        </div>
      </div>
    </div>

    <!-- Center: High-Tech Specimen Chamber with Animated Laser Scanner Line & Fluid Serum Separation -->
    <div class="relative flex items-center justify-center order-1 md:order-2">
      
      <!-- Rotating Chamber Rings -->
      <div class="absolute w-56 h-56 rounded-full border border-slate-800 border-dashed animate-[spin_20s_linear_infinite] opacity-60"></div>
      <div class="absolute w-44 h-44 rounded-full border border-cyan-500/20 animate-[spin_12s_linear_infinite_reverse]"></div>
      
      <!-- Specimen Tube Graphical Component -->
      <div class="relative w-28 h-64 flex flex-col items-center justify-center group cursor-pointer" onclick="cycleVisualizerSample()">
        
        <!-- Tube Cap (Lavender EDTA Top) -->
        <div class="w-14 h-7 rounded-t-lg bg-gradient-to-r from-purple-700 via-purple-500 to-purple-800 shadow-md border-b-2 border-purple-900 flex items-center justify-center relative z-20">
          <div class="w-10 h-1.5 bg-purple-300/40 rounded-full"></div>
        </div>

        <!-- Glass Tube Body -->
        <div class="w-12 h-48 bg-slate-900/60 backdrop-blur-md rounded-b-3xl border-2 border-slate-500/40 relative overflow-hidden shadow-2xl flex flex-col justify-end p-1">
          
          <!-- Tube Glass Reflection Overlay -->
          <div class="absolute left-1 top-2 bottom-4 w-1 bg-white/20 rounded-full pointer-events-none z-30"></div>
          
          <!-- Barcode Strip on Tube -->
          <div class="absolute top-4 right-1 left-1 bg-white/90 rounded px-1 py-1 flex flex-col gap-0.5 shadow-sm opacity-80 z-10">
            <div class="h-3 bg-[repeating-linear-gradient(90deg,#000_0px,#000_2px,transparent_2px,transparent_4px)]"></div>
            <span class="text-[7px] text-black font-mono font-bold text-center leading-none">SMP-00124</span>
          </div>

          <!-- Liquid Layer 1: Serum / Plasma Layer (Amber-Gold) -->
          <div class="w-full h-16 bg-gradient-to-b from-amber-300/80 via-amber-400/80 to-amber-500/80 rounded-t-sm relative transition-all duration-500">
            <div class="absolute inset-0 bg-white/10 animate-pulse"></div>
          </div>

          <!-- Liquid Layer 2: Buffy Coat (White/WBC Band) -->
          <div class="w-full h-2 bg-slate-100/90 shadow-sm border-y border-white/40"></div>

          <!-- Liquid Layer 3: Packed Red Blood Cells (Deep Crimson) -->
          <div class="w-full h-20 bg-gradient-to-b from-rose-800 via-red-800 to-red-950 rounded-b-2xl relative overflow-hidden">
            <!-- Cellular Floating Micro-particles -->
            <div class="absolute w-1.5 h-1.5 rounded-full bg-red-400/80 top-3 left-2 animate-bounce"></div>
            <div class="absolute w-1 h-1 rounded-full bg-rose-300/70 top-8 right-3 animate-ping"></div>
            <div class="absolute w-2 h-2 rounded-full bg-red-300/60 bottom-4 left-4 animate-pulse"></div>
          </div>

          <!-- Animated Laser Scanning Line -->
          <div class="absolute left-0 right-0 h-1 bg-cyan-400 shadow-[0_0_12px_#22d3ee] animate-[scan_2.8s_ease-in-out_infinite] z-20 pointer-events-none"></div>
        </div>

        <!-- Scan Base Stand / Sensor Mount -->
        <div class="w-20 h-4 bg-slate-800 border-t border-slate-700 rounded-b-xl shadow-lg flex items-center justify-center gap-1.5 mt-0.5">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
          <span class="text-[8px] font-mono text-slate-400">LASER-ON</span>
        </div>
      </div>
    </div>

    <!-- Right Telemetry Readout -->
    <div class="space-y-3.5 w-full md:w-44 text-right order-3">
      <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/60 rounded-xl p-3 hover:border-slate-600 transition">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Rack Location</span>
        <span class="text-xs font-mono font-bold text-slate-200 mt-1 block" id="visualizerRack"><?= e($sample['rack_location'] ?? 'RACK-HEM-A04') ?></span>
      </div>

      <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/60 rounded-xl p-3 hover:border-slate-600 transition">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Centrifuge Speed</span>
        <div class="flex items-baseline justify-end gap-1.5 mt-1">
          <span class="text-lg font-black text-indigo-300 font-mono">3,200</span>
          <span class="text-[10px] text-slate-400">RPM</span>
        </div>
        <p class="text-[9px] text-emerald-400 font-medium mt-1">● Rotor Stable (RCF 1,800g)</p>
      </div>

      <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/60 rounded-xl p-3 hover:border-slate-600 transition">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Analyzer Chamber</span>
        <span class="text-xs font-semibold text-cyan-300 mt-1 block">Sysmex XN-1000</span>
      </div>
    </div>
  </div>

  <!-- Footer Quick Bar: Sample Progress Step Indicators & Switch Sample Button -->
  <div class="relative z-10 pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
    
    <!-- Step Stages Timeline -->
    <div class="flex items-center gap-2 w-full sm:w-auto">
      <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Progress:</span>
      <div class="flex items-center gap-1.5 bg-slate-950/80 border border-slate-800 px-3 py-1.5 rounded-xl flex-1 sm:flex-initial">
        <span class="flex items-center gap-1 text-[11px] font-semibold text-emerald-400">
          <i data-lucide="check" class="w-3 h-3"></i> Collected
        </span>
        <span class="text-slate-600">→</span>
        <span class="flex items-center gap-1 text-[11px] font-semibold text-emerald-400">
          <i data-lucide="check" class="w-3 h-3"></i> Received
        </span>
        <span class="text-slate-600">→</span>
        <span class="flex items-center gap-1 text-[11px] font-bold text-cyan-400 animate-pulse">
          <i data-lucide="refresh-cw" class="w-3 h-3 animate-spin"></i> Processing
        </span>
        <span class="text-slate-600">→</span>
        <span class="text-[11px] text-slate-500 font-medium">Verify</span>
        <span class="text-slate-600">→</span>
        <span class="text-[11px] text-slate-500 font-medium">Release</span>
      </div>
    </div>

    <!-- Quick Action / Interactive Switch Sample -->
    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
      <button onclick="cycleVisualizerSample()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition active:scale-95 shadow-sm">
        <i data-lucide="shuffle" class="w-3.5 h-3.5 text-cyan-400"></i>
        <span>Switch Specimen</span>
      </button>
      <button onclick="openCreateResultModal('LAB-2026-001', 'SMP-2026-00124', 'Juan Dela Cruz', 'Complete Blood Count (CBC)')" class="px-3.5 py-1.5 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition shadow-lg shadow-indigo-500/20 active:scale-95">
        <i data-lucide="file-plus" class="w-3.5 h-3.5"></i>
        <span>Record Result</span>
      </button>
    </div>
  </div>
</div>

<style>
@keyframes scan {
  0% { top: 12%; opacity: 0.8; }
  50% { top: 88%; opacity: 1; }
  100% { top: 12%; opacity: 0.8; }
}
</style>
<?php
}
