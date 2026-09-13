@extends('layouts.register')

@section('title', 'Reports & Clinical Analytics | Tupi Municipal Hospital Information Management System')

@section('content')
<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
        Clinical Analytics & Executive Intelligence
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Reports & Clinical Statistics</h1>
      <p class="text-xs text-slate-500 mt-0.5">Comprehensive reports powered directly by real MySQL clinical database records.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0 no-print">
      <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition flex items-center gap-2">
        <i data-lucide="printer" class="w-4 h-4"></i>
        <span>Print Executive Report</span>
      </button>
    </div>
  </div>

  <!-- Summary Statistics Strip -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-1">
      <div class="text-[11px] font-bold text-slate-400 uppercase">Total Registered</div>
      <div class="text-2xl font-black text-slate-900"><?= number_format($kpis['total_patients']) ?> Patients</div>
      <div class="text-[11px] text-emerald-600 font-semibold">Active Database Records</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-1">
      <div class="text-[11px] font-bold text-slate-400 uppercase">Today's Intake Volume</div>
      <div class="text-2xl font-black text-blue-700"><?= number_format($kpis['today_registrations']) ?> New</div>
      <div class="text-[11px] text-slate-500">Intakes recorded today</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-1">
      <div class="text-[11px] font-bold text-slate-400 uppercase">Consultations Scheduled</div>
      <div class="text-2xl font-black text-indigo-700"><?= number_format($kpis['today_consultations']) ?> Sessions</div>
      <div class="text-[11px] text-indigo-600 font-semibold">Today's schedule</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-1">
      <div class="text-[11px] font-bold text-slate-400 uppercase">Active Inpatient Census</div>
      <div class="text-2xl font-black text-purple-700"><?= number_format($kpis['admitted_patients']) ?> Admitted</div>
      <div class="text-[11px] text-purple-600 font-semibold">Ward beds occupied</div>
    </div>
  </div>

  <!-- Charts Grid Row 1 -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Chart 1: Registration Trend -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-extrabold text-slate-900">Patient Registration Trend</h3>
          <p class="text-[11px] text-slate-400">Intake volume over the past 14 days</p>
        </div>
        <span class="text-xs font-bold text-blue-600">Daily Intake</span>
      </div>
      <div class="h-64">
        <canvas id="chart-registration-trend"></canvas>
      </div>
    </div>

    <!-- Chart 2: Body System Distribution -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-extrabold text-slate-900">Body System Classifications</h3>
          <p class="text-[11px] text-slate-400">Distribution across 9 physiological systems</p>
        </div>
        <span class="text-xs font-bold text-emerald-600">Triage Breakdown</span>
      </div>
      <div class="h-64 flex items-center justify-center">
        <canvas id="chart-body-systems"></canvas>
      </div>
    </div>

  </div>

  <!-- Charts Grid Row 2 -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Chart 3: Patient Categories -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-4">
      <div class="pb-3 border-b border-slate-100">
        <h3 class="text-sm font-extrabold text-slate-900">Patient Categories</h3>
        <p class="text-[11px] text-slate-400">Outpatient vs Emergency vs Admitted</p>
      </div>
      <div class="h-56">
        <canvas id="chart-patient-categories"></canvas>
      </div>
    </div>

    <!-- Chart 4: Consultation Status Distribution -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-4">
      <div class="pb-3 border-b border-slate-100">
        <h3 class="text-sm font-extrabold text-slate-900">Consultation Statuses</h3>
        <p class="text-[11px] text-slate-400">Completed, In Consultation, Waiting</p>
      </div>
      <div class="h-56">
        <canvas id="chart-consultation-stats"></canvas>
      </div>
    </div>

    <!-- Chart 5: Doctor Workload -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-4">
      <div class="pb-3 border-b border-slate-100">
        <h3 class="text-sm font-extrabold text-slate-900">Doctor Consultation Volume</h3>
        <p class="text-[11px] text-slate-400">Sessions by attending physician</p>
      </div>
      <div class="h-56">
        <canvas id="chart-doctor-workload"></canvas>
      </div>
    </div>

  </div>

</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // 1. Registration Trend Line Chart
    const trendCtx = document.getElementById('chart-registration-trend').getContext('2d');
    new Chart(trendCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($trend['labels']) ?>,
        datasets: [{
          label: 'New Patients',
          data: <?= json_encode($trend['data']) ?>,
          borderColor: '#2563EB',
          backgroundColor: 'rgba(37, 99, 235, 0.08)',
          borderWidth: 3,
          fill: true,
          tension: 0.35,
          pointBackgroundColor: '#1D4ED8',
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#F1F5F9' } },
          x: { grid: { display: false } }
        }
      }
    });

    // 2. Body Systems Doughnut Chart
    const sysCtx = document.getElementById('chart-body-systems').getContext('2d');
    new Chart(sysCtx, {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($systems['labels']) ?>,
        datasets: [{
          data: <?= json_encode($systems['data']) ?>,
          backgroundColor: [
            '#2563EB', '#0284C7', '#6366F1', '#06B6D4', 
            '#F59E0B', '#10B981', '#14B8A6', '#8B5CF6', '#64748B'
          ],
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } }
        }
      }
    });

    // 3. Patient Categories Pie Chart
    const catCtx = document.getElementById('chart-patient-categories').getContext('2d');
    new Chart(catCtx, {
      type: 'pie',
      data: {
        labels: <?= json_encode($categories['labels']) ?>,
        datasets: [{
          data: <?= json_encode($categories['data']) ?>,
          backgroundColor: ['#10B981', '#2563EB', '#F59E0B', '#EF4444', '#8B5CF6']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } }
      }
    });

    // 4. Consultation Stats Bar Chart
    const consCtx = document.getElementById('chart-consultation-stats').getContext('2d');
    new Chart(consCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($consultations['labels']) ?>,
        datasets: [{
          data: <?= json_encode($consultations['data']) ?>,
          backgroundColor: '#3B82F6',
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // 5. Doctor Workload Horizontal Bar Chart
    const docCtx = document.getElementById('chart-doctor-workload').getContext('2d');
    new Chart(docCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($workload['labels']) ?>,
        datasets: [{
          data: <?= json_encode($workload['data']) ?>,
          backgroundColor: '#4F46E5',
          borderRadius: 8
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
      }
    });
  });
</script>
@endsection
