<?php
// Doctor/views/prescriptions/print.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Prescription.php';
require_once __DIR__ . '/../../models/Patient.php';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$rxId = (int)($_GET['id'] ?? 0);
$rxModel = new Prescription();
$patientModel = new Patient();
$rx = $rxModel->findById($rxId);

if (!$rx) {
    Session::setFlash('error', 'Prescription not found.');
    header('Location: ' . doctor_url('views/prescriptions/index.php'));
    exit;
}

// Doctor-Patient Isolation Check
if ((int)$rx['DoctorID'] !== $doctorId && !$patientModel->hasDoctorAccess($doctorId, (int)$rx['PatientID'])) {
    Session::setFlash('error', 'Access Denied: You are not authorized to view or print this prescription.');
    header('Location: ' . doctor_url('views/prescriptions/index.php'));
    exit;
}

$pageTitle = 'Print Prescription: ' . $rx['PrescriptionCode'] . ' • Tupi Municipal Hospital';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif; }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
      #printableDoc { border: none !important; box-shadow: none !important; }
    }
  </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center justify-start text-slate-800">

  <!-- Top Action Bar (No Print) -->
  <div class="max-w-3xl w-full flex items-center justify-between mb-6 no-print">
    <a href="<?= doctor_url('views/prescriptions/index.php') ?>" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 shadow-xs transition">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
      <span>Back to Prescriptions</span>
    </a>

    <button onclick="window.print()" 
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-blue-600/25 transition">
      <i data-lucide="printer" class="w-4 h-4"></i>
      <span>Print Official Prescription</span>
    </button>
  </div>

  <!-- Printable Prescription Sheet (Standard Medical Rx Document) -->
  <div id="printableDoc" class="max-w-3xl w-full bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-2xl space-y-8 relative">
    
    <!-- Watermark Logo -->
    <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
      <svg class="w-96 h-96" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
      </svg>
    </div>

    <!-- 1. Hospital Header & Doctor Licensing Info -->
    <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6 relative z-10">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-md">
          <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-black text-slate-900 tracking-tight font-display">AURAHEALTH MEDICAL CENTER</h1>
          <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Department of Clinical Medicine & Specialty Practice</p>
          <p class="text-[11px] text-slate-400 mt-0.5">742 Healthcare Boulevard, Metro Health District • Tel: (555) 019-2834</p>
        </div>
      </div>

      <div class="text-right">
        <h2 class="text-base font-extrabold text-slate-900">Dr. <?= e($rx['DoctorFirstName'] . ' ' . $rx['DoctorLastName']) ?>, <?= e($rx['DoctorTitle'] ?: 'MD') ?></h2>
        <p class="text-xs font-bold text-blue-700"><?= e($rx['Specialty']) ?></p>
        <p class="text-[11px] text-slate-500 font-mono">Lic. No: <?= e($rx['LicenseNumber']) ?></p>
        <p class="text-[11px] text-slate-400"><?= e($rx['ClinicRoom']) ?></p>
      </div>
    </div>

    <!-- 2. Patient Demographics & Prescription Metadata -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs relative z-10">
      <div>
        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Patient Name</span>
        <strong class="text-slate-900 text-sm"><?= e($rx['PatientFirstName'] . ' ' . $rx['PatientLastName']) ?></strong>
      </div>
      <div>
        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Age / Gender</span>
        <strong class="text-slate-900"><?= e($rx['Age']) ?> Yrs / <?= e($rx['Gender']) ?></strong>
      </div>
      <div>
        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Date Issued</span>
        <strong class="text-slate-900"><?= format_date($rx['IssuedDate']) ?></strong>
      </div>
      <div>
        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Rx Reference No.</span>
        <strong class="text-blue-700 font-mono"><?= e($rx['PrescriptionCode']) ?></strong>
      </div>
    </div>

    <!-- 3. The Rx Body Symbol & Medication Orders -->
    <div class="space-y-6 pt-2 relative z-10 min-h-[220px]">
      
      <!-- Iconic Rx Symbol -->
      <div class="text-4xl font-serif font-black text-slate-900 select-none">
        ℞
      </div>

      <!-- Prescribed Medication Block -->
      <div class="pl-8 space-y-3">
        
        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
          <h3 class="text-lg font-black text-slate-900 tracking-tight">
            <?= e($rx['MedicineName']) ?> <span class="text-blue-700 font-bold"><?= e($rx['Dosage']) ?></span>
          </h3>
          <span class="text-xs font-bold text-slate-600 font-mono">Dispense: # <?= e($rx['Quantity']) ?></span>
        </div>

        <!-- Dosage & Regimen Instructions -->
        <div class="space-y-2 text-xs text-slate-700">
          <div class="flex items-start gap-2">
            <span class="font-bold text-slate-900 min-w-[90px]">Sig / Regimen:</span>
            <span class="font-semibold text-slate-800"><?= e($rx['Frequency']) ?> for <?= e($rx['Duration']) ?></span>
          </div>

          <div class="flex items-start gap-2">
            <span class="font-bold text-slate-900 min-w-[90px]">Instructions:</span>
            <span class="text-slate-600 italic bg-blue-50/50 px-2 py-1 rounded-lg border border-blue-100 inline-block">
              "<?= e($rx['Instructions']) ?>"
            </span>
          </div>

          <div class="flex items-start gap-2 text-[11px] text-slate-400 pt-1">
            <span class="font-bold min-w-[90px]">Refills:</span>
            <span><?= $rx['Refills'] > 0 ? e($rx['Refills']) . ' Refill(s) authorized' : 'No refills (Single fill only)' ?></span>
          </div>
        </div>

      </div>

    </div>

    <!-- 4. Physician Signature & Hospital Validation Seal -->
    <div class="pt-8 border-t border-slate-200 flex items-end justify-between relative z-10">
      
      <!-- Barcode / Electronic Verification Seal -->
      <div class="space-y-1">
        <div class="px-3 py-1.5 bg-slate-100 rounded-xl border border-slate-200 inline-block font-mono text-[10px] text-slate-600">
          SECURE-EMR-AUTH-HASH: <?= strtoupper(substr(md5($rx['PrescriptionCode']), 0, 16)) ?>
        </div>
        <p class="text-[10px] text-slate-400">Valid only with registered physician license credentials.</p>
      </div>

      <!-- Signature Line -->
      <div class="text-center w-64 space-y-1">
        <div class="h-12 flex items-end justify-center">
          <span class="font-serif italic text-blue-900 text-lg font-bold">Dr. <?= e($rx['DoctorFirstName'] . ' ' . $rx['DoctorLastName']) ?></span>
        </div>
        <div class="border-t-2 border-slate-800 pt-1">
          <p class="text-xs font-black text-slate-900 uppercase tracking-wide">Dr. <?= e($rx['DoctorFirstName'] . ' ' . $rx['DoctorLastName']) ?>, <?= e($rx['DoctorTitle'] ?: 'MD') ?></p>
          <p class="text-[11px] text-slate-500">Attending Physician • Lic #<?= e($rx['LicenseNumber']) ?></p>
        </div>
      </div>

    </div>

  </div>

  <script>
    if (window.lucide) {
      lucide.createIcons();
    }
  </script>
</body>
</html>
