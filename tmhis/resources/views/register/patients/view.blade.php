@extends('layouts.register')

@section('title', 'Patient Profile: \' . e($patient[\'FirstName\'] . \' \' . $patient[\'LastName\']) . \' (\' . e($patient[\'PatientCode\']) . \')')

@section('content')
<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
  
  <!-- Breadcrumb & Top Profile Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
        <a href="<?= route('register.patients.index') ?>" class="hover:text-blue-600">Patients</a>
        <span>/</span>
        <span class="text-slate-700 font-bold"><?= e($patient['PatientCode']) ?></span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
        <span><?= e($patient['FirstName'] . ' ' . ($patient['MiddleName'] ? $patient['MiddleName'] . ' ' : '') . $patient['LastName']) ?></span>
        <span class="text-xs font-extrabold px-3 py-1 rounded-full border <?= get_category_badge($patient['PatientCategory']) ?>">
          <?= e($patient['PatientCategory']) ?>
        </span>
      </h1>
    </div>

    <!-- Actions (Edit & Print) -->
    <div class="flex items-center gap-2 shrink-0 no-print">
      <a href="<?= base_url('views/patients/edit.php?id=' . $patient['PatientID']) ?>" 
         class="px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-2xs">
        <i data-lucide="edit-3" class="w-4 h-4 text-amber-600"></i>
        <span>Edit Patient</span>
      </a>

      <button type="button" onclick="window.print()" 
              class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition flex items-center gap-2">
        <i data-lucide="printer" class="w-4 h-4"></i>
        <span>Print Full Profile</span>
      </button>
    </div>
  </div>

  <!-- Master Patient Record Card -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-8" id="printable-patient-record">
    
    <!-- Top Identity Summary Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center font-black text-2xl shadow-md shadow-blue-500/20">
          <?= strtoupper(substr($patient['FirstName'], 0, 1)) ?>
        </div>
        <div>
          <div class="text-xs font-mono font-black text-blue-700 tracking-wider uppercase">Digital Patient ID</div>
          <div class="text-xl font-extrabold text-slate-900"><?= e($patient['PatientCode']) ?></div>
          <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
            <span><?= e($patient['Gender']) ?></span> • 
            <span><?= e($patient['Age']) ?> years old</span> • 
            <span>DOB: <?= format_date($patient['DateOfBirth']) ?></span> •
            <span class="font-bold text-blue-600">Blood: <?= e($patient['BloodType']) ?></span>
          </div>
        </div>
      </div>

      <div class="text-left sm:text-right space-y-1">
        <div class="text-[11px] font-bold text-slate-400 uppercase">Registration Timestamp</div>
        <div class="text-xs font-semibold text-slate-800"><?= format_date($patient['CreatedAt'], 'd M Y, h:i A') ?></div>
        <?php if (!empty($patient['TodayQueue'])): ?>
          <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-lg">
            <i data-lucide="ticket" class="w-3.5 h-3.5"></i>
            Today's Queue Ticket: #<?= e($patient['TodayQueue']['QueueNumber']) ?> (<?= e($patient['TodayQueue']['QueueStatus']) ?>)
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- 2-Column Clinical Profile Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      
      <!-- Column 1: Personal & Medical Background -->
      <div class="space-y-6">
        
        <!-- Contact & Residence -->
        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/70 space-y-3">
          <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-600"></i>
            <span>Contact Information & Address</span>
          </div>
          <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-slate-500">Phone Number:</span> <span class="font-bold text-slate-800"><?= e($patient['ContactNumber']) ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Email Address:</span> <span class="font-bold text-slate-800"><?= e($patient['Email']) ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Civil Status:</span> <span class="font-semibold text-slate-800"><?= e($patient['CivilStatus']) ?></span></div>
            <div class="pt-1"><span class="text-slate-500 block mb-0.5">Street Address:</span> <span class="font-medium text-slate-800 leading-relaxed"><?= e($patient['Address']) ?></span></div>
          </div>
        </div>

        <!-- Emergency Contact -->
        <?php $emg = $patient['EmergencyContact']; ?>
        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/70 space-y-3">
          <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <i data-lucide="heart-handshake" class="w-3.5 h-3.5 text-amber-600"></i>
            <span>Emergency Contact Person</span>
          </div>
          <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-slate-500">Full Name:</span> <span class="font-bold text-slate-800"><?= e($emg['ContactName'] ?? '—') ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Relationship:</span> <span class="font-semibold text-slate-800"><?= e($emg['Relationship'] ?? '—') ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Emergency Phone:</span> <span class="font-bold text-slate-800"><?= e($emg['ContactNumber'] ?? '—') ?></span></div>
          </div>
        </div>

        <!-- Medical History -->
        <?php $med = $patient['MedicalHistory']; ?>
        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/70 space-y-3">
          <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-emerald-600"></i>
            <span>Medical Background & Allergies</span>
          </div>
          <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-slate-500">Known Allergies:</span> <span class="font-bold text-slate-800"><?= e($med['Allergies'] ?? 'None') ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Existing Conditions:</span> <span class="font-bold text-slate-800"><?= e($med['ExistingConditions'] ?? 'None') ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Current Medications:</span> <span class="font-bold text-slate-800"><?= e($med['CurrentMedications'] ?? 'None') ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Prior Hospitalization:</span> <span class="font-bold text-slate-800"><?= e($med['PreviousHospitalization'] ?? 'None') ?></span></div>
          </div>
        </div>

      </div>

      <!-- Column 2: Clinical Complaint, System Classification & Appointments -->
      <div class="space-y-6">
        
        <!-- Chief Complaint & Classification -->
        <?php $comp = $patient['Complaint']; ?>
        <div class="bg-blue-50/40 p-5 rounded-2xl border border-blue-200/70 space-y-4">
          <div class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center justify-between pb-2 border-b border-blue-100">
            <div class="flex items-center gap-2">
              <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-600"></i>
              <span>Chief Complaint & Pre-Consultation Intake</span>
            </div>
            <?php if (!empty($comp['SystemName'])): ?>
              <span class="px-2.5 py-0.5 bg-blue-600 text-white rounded-full text-[10px] font-extrabold shadow-2xs inline-flex items-center gap-1">
                <i data-lucide="activity" class="w-3 h-3"></i>
                <span><?= e($comp['SystemName']) ?></span>
              </span>
            <?php endif; ?>
          </div>

          <?php if ($comp): ?>
            <div class="space-y-3 text-xs">
              <div>
                <span class="text-slate-400 block mb-0.5 font-semibold">Reported Description:</span>
                <p class="font-bold text-slate-800 italic leading-relaxed">"<?= e($comp['ComplaintDescription']) ?>"</p>
              </div>

              <div class="grid grid-cols-2 gap-3 pt-2 border-t border-blue-100">
                <div>
                  <span class="text-slate-400 font-semibold">Severity Scale:</span>
                  <span class="font-bold text-blue-700 ml-1">Level <?= e($comp['Severity']) ?>/5</span>
                </div>
                <div>
                  <span class="text-slate-400 font-semibold">Onset/Duration:</span>
                  <span class="font-bold text-slate-700 ml-1"><?= e($comp['Duration']) ?></span>
                </div>
              </div>

              <?php if (!empty($comp['LocationName'])): ?>
                <div class="pt-2 border-t border-blue-100">
                  <span class="text-slate-400 font-semibold">Estimated Anatomical Site:</span>
                  <div class="font-bold text-slate-800 mt-0.5">
                    <?= e($comp['LocationName']) ?> <span class="text-slate-500 font-normal">(<?= e($comp['SubRegion']) ?>)</span>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Selected Symptoms -->
              <?php if (!empty($comp['Symptoms'])): ?>
                <div class="pt-2 border-t border-blue-100">
                  <span class="text-slate-400 block mb-1.5 font-semibold">Reported Symptoms:</span>
                  <div class="flex flex-wrap gap-1.5">
                    <?php foreach ($comp['Symptoms'] as $sym): ?>
                      <span class="px-2 py-0.5 bg-white border border-blue-200 text-blue-900 rounded-md text-[11px] font-semibold">
                        <?= e($sym['SymptomName']) ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Possible Discussion Conditions -->
              <?php if (!empty($comp['Conditions'])): ?>
                <div class="pt-2 border-t border-blue-100">
                  <span class="text-slate-400 block mb-1 font-semibold">Relevant Clinical Discussion Topics:</span>
                  <div class="flex flex-wrap gap-1.5">
                    <?php foreach ($comp['Conditions'] as $cond): ?>
                      <span class="px-2.5 py-0.5 bg-sky-100 text-sky-900 rounded-md text-[11px] font-bold">
                        <?= e($cond['ConditionName']) ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

            </div>
          <?php else: ?>
            <div class="text-xs text-slate-400 italic">No formal pre-consultation complaint logged yet.</div>
          <?php endif; ?>
        </div>

        <!-- Consultation & Appointments History -->
        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/70 space-y-3">
          <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between pb-2 border-b border-slate-200/60">
            <div class="flex items-center gap-2">
              <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-indigo-600"></i>
              <span>Consultation Appointments</span>
            </div>
          </div>

          <?php if (!empty($patient['Appointments'])): ?>
            <div class="space-y-3">
              <?php foreach ($patient['Appointments'] as $app): ?>
                <div class="p-3 bg-white rounded-xl border border-slate-200/80 text-xs space-y-1">
                  <div class="flex items-center justify-between">
                    <span class="font-bold text-blue-700"><?= format_date($app['AppointmentDate']) ?> at <?= e($app['AppointmentTime']) ?></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= get_status_badge($app['Status']) ?>">
                      <?= e($app['Status']) ?>
                    </span>
                  </div>
                  <div class="text-slate-800 font-medium">Attending: Dr. <?= e($app['DocFirst'] . ' ' . $app['DocLast']) ?> (<?= e($app['Specialty']) ?>)</div>
                  <div class="text-[11px] text-slate-500"><?= e($app['ClinicRoom']) ?> • <?= e($app['ConsultationType']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-xs text-slate-400 italic py-2">No past consultations recorded.</div>
          <?php endif; ?>
        </div>

      </div>

    </div>

    <!-- Registration Audit Timeline (History) -->
    <?php if (!empty($patient['History'])): ?>
      <div class="pt-6 border-t border-slate-100 space-y-4">
        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
          <i data-lucide="history" class="w-3.5 h-3.5 text-slate-500"></i>
          <span>Patient Registration & Consultation Audit Trail</span>
        </h4>

        <div class="space-y-2.5">
          <?php foreach ($patient['History'] as $hist): ?>
            <div class="flex items-start gap-3 text-xs">
              <div class="w-2 h-2 rounded-full bg-blue-600 mt-1.5 shrink-0"></div>
              <div class="flex-1 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <div class="flex items-center justify-between">
                  <strong class="text-slate-800"><?= e($hist['Action']) ?></strong>
                  <span class="text-[10px] text-slate-400"><?= format_date($hist['CreatedAt'], 'd M Y, h:i A') ?></span>
                </div>
                <div class="text-slate-600 text-[11px] mt-0.5"><?= e($hist['Description']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>

</main>

<?php if (isset($_GET['print'])): ?>
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      window.print();
    });
  </script>
<?php endif; ?>
@endsection
