<?php
// Doctor/views/dashboard/index.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Consultation.php';
require_once __DIR__ . '/../models/Diagnosis.php';
require_once __DIR__ . '/../includes/anatomy_model.php';

$pageTitle = 'Dashboard | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'dashboard';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;
$specialty = $currentUser['specialty'] ?? 'Cardiologist';
$specialtyCode = strtolower($currentUser['username'] ?? 'cardio');

$doctorModel = new Doctor();
$patientModel = new Patient();
$consultationModel = new Consultation();
$diagnosisModel = new Diagnosis();

// Fetch metrics & today's queue
$metrics = $doctorModel->getDashboardMetrics($doctorId);
$todayQueue = $patientModel->getTodayQueue($doctorId);
$activeConsultation = $consultationModel->getActiveConsultation($doctorId);
$diagnosisStats = $diagnosisModel->getDistributionStats($doctorId);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">

    <!-- Active Consultation Alert Banner (If doctor is currently in consultation) -->
    <?php if ($activeConsultation): ?>
      <div data-aos="fade-down" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-3xl p-6 shadow-xl shadow-blue-500/20 flex flex-col md:flex-row md:items-center justify-between gap-4 border border-blue-400/30">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white ring-4 ring-white/10 shrink-0">
            <i data-lucide="stethoscope" class="w-6 h-6 animate-pulse"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-400 text-emerald-950">In Progress</span>
              <h3 class="text-base font-bold">Consultation with <?= e($activeConsultation['FirstName'] . ' ' . $activeConsultation['LastName']) ?></h3>
            </div>
            <p class="text-xs text-blue-100 mt-0.5">
              Queue #<?= e($activeConsultation['QueueNumber'] ?? '01') ?> • Patient Code: <span class="font-mono"><?= e($activeConsultation['PatientCode']) ?></span> • <?= e($activeConsultation['Reason'] ?: 'Routine follow-up') ?>
            </p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <a href="<?= doctor_url('views/patients/view.php?id=' . $activeConsultation['PatientID']) ?>" 
             class="px-4 py-2.5 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-xl text-xs shadow-lg transition flex items-center gap-2">
            <span>Open Patient Chart</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
          <a href="<?= doctor_url('views/diagnosis/index.php?patient_id=' . $activeConsultation['PatientID'] . '&app_id=' . $activeConsultation['AppointmentID']) ?>" 
             class="px-4 py-2.5 bg-blue-700/80 hover:bg-blue-800 text-white font-bold rounded-xl text-xs shadow-lg transition flex items-center gap-2 border border-white/20">
            <span>Write Diagnosis / Rx</span>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <!-- 4 KPI Metrics Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5" data-aos="fade-up">
      
      <!-- Card 1: Today's Patients -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-200 group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Patients</span>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i data-lucide="users" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-baseline gap-2">
          <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $metrics['today_patients'] ?></span>
          <span class="text-xs font-semibold text-emerald-600 flex items-center gap-0.5">
            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
            <span>Assigned Queue</span>
          </span>
        </div>
        <p class="text-[11px] text-slate-400 mt-1 font-medium">Specialty queue active for today</p>
      </div>

      <!-- Card 2: Upcoming Appointments -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-200 group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Upcoming Appointments</span>
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i data-lucide="calendar" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-baseline gap-2">
          <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $metrics['upcoming_appointments'] ?></span>
          <span class="text-xs font-semibold text-blue-600 flex items-center gap-0.5">
            <span>Scheduled Today</span>
          </span>
        </div>
        <p class="text-[11px] text-slate-400 mt-1 font-medium">In-person & follow-up consultations</p>
      </div>

      <!-- Card 3: Completed Consultations -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-200 group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Completed Consultations</span>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-baseline gap-2">
          <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $metrics['completed_consultations'] ?></span>
          <span class="text-xs font-semibold text-emerald-600">Discharged / Treated</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-1 font-medium">Clinical notes & orders archived</p>
      </div>

      <!-- Card 4: Pending Laboratory Requests -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-200 group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Lab Requests</span>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i data-lucide="flask-conical" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-baseline gap-2">
          <span class="text-3xl font-black text-slate-900 tracking-tight font-display"><?= $metrics['pending_laboratory'] ?></span>
          <span class="text-xs font-semibold text-amber-600">Awaiting Lab Processing</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-1 font-medium">Diagnostic orders in progress</p>
      </div>

    </section>

    <!-- Main Grid: Today's Appointments & Specialty-Based Body Model Insight -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- Left 7 Cols: Today's Appointments Queue -->
      <div class="lg:col-span-7 space-y-5" data-aos="fade-right">
        
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight font-display">Today's Appointment Queue</h3>
            <p class="text-xs text-slate-400 font-medium">Automated routing from Registrator intake for <?= e($specialty) ?></p>
          </div>
          <a href="<?= doctor_url('views/patients/index.php') ?>" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
            <span>View All Patients</span>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
          </a>
        </div>

        <!-- Appointment Cards List -->
        <div class="space-y-3.5">
          <?php if (empty($todayQueue)): ?>
            <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-3 shadow-card">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                <i data-lucide="calendar-check" class="w-6 h-6"></i>
              </div>
              <p class="text-sm font-bold text-slate-800">No appointments scheduled for today</p>
              <p class="text-xs text-slate-400">All registered patients for your specialty will automatically appear here.</p>
            </div>
          <?php else: ?>
            <?php foreach ($todayQueue as $idx => $patient): ?>
              <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group">
                
                <div class="flex items-start gap-4">
                  <!-- Queue Number Badge -->
                  <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex flex-col items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                    <span class="text-[9px] font-bold uppercase tracking-wider opacity-80">Queue</span>
                    <span class="text-base font-black leading-none font-display"><?= e($patient['QueueNumber']) ?></span>
                  </div>
                  
                  <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                      <h4 class="text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition">
                        <?= e($patient['FirstName'] . ' ' . $patient['LastName']) ?>
                      </h4>
                      <span class="text-xs text-slate-400 font-medium">(<?= e($patient['Age']) ?>y, <?= e($patient['Gender']) ?>)</span>
                      <span class="px-2 py-0.5 text-[10px] rounded-full border <?= get_priority_badge($patient['Priority'] ?? 'Normal') ?>">
                        <?= e($patient['Priority'] ?? 'Normal') ?>
                      </span>
                    </div>

                    <p class="text-xs text-slate-600 line-clamp-1">
                      <strong class="text-slate-700">Complaint:</strong> <?= e($patient['ComplaintDescription'] ?: ($patient['Reason'] ?: 'Initial consultation review')) ?>
                    </p>

                    <div class="flex items-center gap-3 text-[11px] text-slate-400 font-medium">
                      <span class="flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span><?= e($patient['AppointmentTime'] ?: '09:00 AM') ?></span>
                      </span>
                      <span>•</span>
                      <span class="flex items-center gap-1 text-slate-500">
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-500"></i>
                        <span><?= e($patient['SystemName'] ?: $specialty) ?></span>
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Action Button: Join / Start Consultation -->
                <div class="flex items-center gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                  <a href="<?= doctor_url('views/patients/view.php?id=' . $patient['PatientID']) ?>" 
                     class="p-2.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl border border-slate-200 transition text-xs font-semibold"
                     title="View Full Profile">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </a>
                  <a href="<?= doctor_url('views/diagnosis/index.php?patient_id=' . $patient['PatientID'] . '&app_id=' . ($patient['AppointmentID'] ?? '') . '&start=1') ?>" 
                     class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5 group-hover:scale-105">
                    <i data-lucide="play" class="w-3.5 h-3.5"></i>
                    <span>Join Consultation</span>
                  </a>
                </div>

              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </div>

      <!-- Right 5 Cols: Specialty Identity Anatomy Model -->
      <div class="lg:col-span-5 space-y-4" data-aos="fade-left">
        
        <?php
          // Render anatomy model tailored to logged in doctor's specialty
          render_anatomy_model(
              $specialtyCode,
              'Body-System Insight',
              $specialty . ' Anatomical Focus'
          );
        ?>

        <!-- Diagnostic Quick Notes & Protocol Tips -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card space-y-3">
          <div class="flex items-center gap-2 text-slate-800 font-bold text-xs">
            <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
            <span>Specialty Clinical Protocol (<?= e($specialty) ?>)</span>
          </div>
          <ul class="text-xs text-slate-500 space-y-2 list-disc list-inside">
            <li>Pre-consultation algorithm automatically categorizes vital signs & chief complaints.</li>
            <li>Directly issue <strong class="text-slate-700">Electronic Prescriptions</strong> with authentic hospital licensing metadata.</li>
            <li>Order STAT or routine <strong class="text-slate-700">Laboratory Requests</strong> with automated result updates.</li>
          </ul>
        </div>

      </div>

    </section>

    <!-- Doctor Dashboard Analytics with Chart.js -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8" data-aos="fade-up">
      
      <!-- Chart 1: Weekly Consultations Trend -->
      <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-extrabold text-slate-900 tracking-tight font-display">Weekly Consultations Trend</h3>
            <p class="text-xs text-slate-400 font-medium">Patient consultations completed over the past 7 days</p>
          </div>
          <span class="px-2.5 py-1 text-[11px] font-bold rounded-xl bg-blue-50 text-blue-700 border border-blue-100">
            This Week
          </span>
        </div>
        <div class="h-64 relative">
          <canvas id="weeklyConsultationsChart"></canvas>
        </div>
      </div>

      <!-- Chart 2: Top Diagnosis Distribution -->
      <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-extrabold text-slate-900 tracking-tight font-display">Top Diagnosis Distribution</h3>
            <p class="text-xs text-slate-400 font-medium">Common conditions diagnosed in <?= e($specialty) ?></p>
          </div>
          <span class="px-2.5 py-1 text-[11px] font-bold rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
            Specialty EMR
          </span>
        </div>
        <div class="h-64 relative flex items-center justify-center">
          <canvas id="diagnosisDistributionChart"></canvas>
        </div>
      </div>

    </section>

  </main>

</div>

<!-- Chart.js Setup Scripts -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  
  // 1. Weekly Consultations Line/Bar Chart
  const ctxWeekly = document.getElementById('weeklyConsultationsChart');
  if (ctxWeekly) {
    new Chart(ctxWeekly, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Consultations',
          data: [6, 8, 12, 9, 14, 7, 4],
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37, 99, 235, 0.08)',
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointBackgroundColor: '#2563eb',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 10,
            borderRadius: 12,
            titleFont: { size: 12, weight: 'bold' },
            bodyFont: { size: 12 }
          }
        },
        scales: {
          x: {
            grid: { display: false }
          },
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9' }
          }
        }
      }
    });
  }

  // 2. Diagnosis Distribution Doughnut Chart
  const ctxDiag = document.getElementById('diagnosisDistributionChart');
  if (ctxDiag) {
    const diagLabels = <?= json_encode(array_column($diagnosisStats, 'DiagnosisName')) ?>;
    const diagData = <?= json_encode(array_map('intval', array_column($diagnosisStats, 'TotalCount'))) ?>;

    new Chart(ctxDiag, {
      type: 'doughnut',
      data: {
        labels: diagLabels,
        datasets: [{
          data: diagData,
          backgroundColor: [
            '#3b82f6',
            '#06b6d4',
            '#10b981',
            '#f59e0b',
            '#8b5cf6'
          ],
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              padding: 12,
              font: { size: 11, weight: '600' }
            }
          }
        }
      }
    });
  }

});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
