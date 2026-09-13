<?php
// Doctor/views/certificates/print.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/MedicalCertificate.php';
require_once __DIR__ . '/../../models/Patient.php';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$certId = (int)($_GET['id'] ?? 0);
$certModel = new MedicalCertificate();
$patientModel = new Patient();
$cert = $certModel->findById($certId);

if (!$cert) {
    Session::setFlash('error', 'Medical Certificate not found.');
    header('Location: ' . doctor_url('views/certificates/index.php'));
    exit;
}

// Doctor-Patient Isolation Check
if ((int)$cert['DoctorID'] !== $doctorId && !$patientModel->hasDoctorAccess($doctorId, (int)$cert['PatientID'])) {
    Session::setFlash('error', 'Access Denied: You are not authorized to view or print this medical certificate.');
    header('Location: ' . doctor_url('views/certificates/index.php'));
    exit;
}

$pageTitle = 'Print Medical Certificate: ' . $cert['CertificateCode'] . ' • Tupi Municipal Hospital';
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&family=Cinzel:wght@600;700;800&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif; }
    .font-formal { font-family: 'Cinzel', serif; }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; padding: 0 !important; }
      #printableDoc { border: none !important; box-shadow: none !important; margin: 0 auto !important; }
    }
  </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center justify-start text-slate-800">

  <!-- Top Action Bar (No Print) -->
  <div class="max-w-3xl w-full flex items-center justify-between mb-6 no-print">
    <a href="<?= doctor_url('views/certificates/index.php') ?>" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 shadow-xs transition">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
      <span>Back to Certificates</span>
    </a>

    <button onclick="window.print()" 
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-blue-600/25 transition">
      <i data-lucide="printer" class="w-4 h-4"></i>
      <span>Print Official Certificate</span>
    </button>
  </div>

  <!-- Printable Medical Certificate Sheet -->
  <div id="printableDoc" class="max-w-3xl w-full bg-white rounded-3xl p-10 sm:p-14 border-8 border-double border-slate-300 shadow-2xl space-y-8 relative">
    
    <!-- Watermark Logo -->
    <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
      <svg class="w-[450px] h-[450px]" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
      </svg>
    </div>

    <!-- 1. Hospital Header -->
    <div class="text-center space-y-1.5 border-b-2 border-slate-800 pb-6 relative z-10">
      <div class="flex items-center justify-center gap-2 text-blue-700">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
        </svg>
        <span class="text-2xl font-black tracking-tight text-slate-900 font-display">AURAHEALTH MEDICAL CENTER</span>
      </div>
      <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Office of Clinical Evaluation & Medical Certification</p>
      <p class="text-[11px] text-slate-400">742 Healthcare Boulevard, Metro Health District • Tel: (555) 019-2834</p>
    </div>

    <!-- 2. Certificate Title -->
    <div class="text-center space-y-1 relative z-10">
      <h2 class="text-xl sm:text-2xl font-bold uppercase tracking-widest text-slate-900 font-formal">
        <?= strtoupper($cert['CertificateType']) ?>
      </h2>
      <p class="text-xs font-mono font-bold text-blue-700">Certificate Reference: <?= e($cert['CertificateCode']) ?></p>
    </div>

    <!-- 3. Certification Body Text -->
    <div class="space-y-6 text-sm text-slate-800 leading-relaxed relative z-10 pt-4 text-justify">
      
      <p class="font-bold text-slate-900">
        DATE: <span class="border-b border-slate-700 pb-0.5 px-2 font-mono"><?= format_date($cert['IssueDate']) ?></span>
      </p>

      <p class="text-base font-serif italic text-slate-700 text-center font-bold">
        TO WHOM IT MAY CONCERN:
      </p>

      <p>
        This is to certify that 
        <strong class="text-slate-900 border-b border-slate-800 pb-0.5 font-bold uppercase tracking-wide">
          <?= e($cert['PatientFirstName'] . ' ' . $cert['MiddleName'] . ' ' . $cert['PatientLastName']) ?>
        </strong>, 
        <strong><?= e($cert['Age']) ?></strong> years of age, 
        <strong><?= e($cert['Gender']) ?></strong>, 
        residing at <em><?= e($cert['Address']) ?></em>, was examined and treated at this medical facility on 
        <strong><?= format_date($cert['DurationStart']) ?></strong>.
      </p>

      <!-- Diagnosis Box -->
      <div class="p-4 bg-slate-50 border-l-4 border-blue-600 rounded-r-2xl space-y-1">
        <span class="text-xs font-black uppercase tracking-wider text-slate-500">Clinical Diagnosis / Medical Findings:</span>
        <p class="text-base font-bold text-slate-900"><?= e($cert['Diagnosis']) ?></p>
      </div>

      <!-- Recommendation & Days -->
      <p>
        and would require medical attention / convalescent rest from 
        <strong class="border-b border-slate-800 pb-0.5 font-mono"><?= format_date($cert['DurationStart']) ?></strong> 
        to 
        <strong class="border-b border-slate-800 pb-0.5 font-mono"><?= format_date($cert['DurationEnd']) ?></strong> 
        (a total duration of <strong><?= $cert['DaysExcused'] ?></strong> days), barring unforeseen clinical complications.
      </p>

      <?php if (!empty($cert['Remarks'])): ?>
        <p class="text-xs text-slate-600">
          <strong>Physician Remarks:</strong> <?= e($cert['Remarks']) ?>
        </p>
      <?php endif; ?>

    </div>

    <!-- 4. Signatures and Official Stamp Section -->
    <div class="pt-12 border-t border-slate-200 grid grid-cols-2 gap-8 items-end relative z-10">
      
      <!-- Hospital Seal Area -->
      <div class="flex items-center gap-3">
        <div class="w-20 h-20 rounded-full border-2 border-dashed border-blue-300 flex items-center justify-center text-center p-2 text-[9px] font-bold uppercase text-blue-400">
          Official Hospital Seal
        </div>
        <div class="text-[10px] text-slate-400 space-y-0.5">
          <p class="font-mono">VALID WITH DRY SEAL</p>
          <p>Verified through Tupi Municipal Hospital Information Management System</p>
        </div>
      </div>

      <!-- Attending Physician Signature Line -->
      <div class="text-center space-y-1">
        <div class="h-12 flex items-end justify-center">
          <span class="font-serif italic text-blue-900 text-lg font-bold">Dr. <?= e($cert['DoctorFirstName'] . ' ' . $cert['DoctorLastName']) ?></span>
        </div>
        <div class="border-t-2 border-slate-900 pt-1">
          <p class="text-xs font-black text-slate-900 uppercase tracking-wide">Dr. <?= e($cert['DoctorFirstName'] . ' ' . $cert['DoctorLastName']) ?>, <?= e($cert['DoctorTitle'] ?: 'MD') ?></p>
          <p class="text-[11px] font-bold text-blue-700"><?= e($cert['Specialty']) ?></p>
          <p class="text-[10px] text-slate-500 font-mono">License No: <?= e($cert['LicenseNumber']) ?></p>
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
