<?php
// Doctor/views/settings/index.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../models/Doctor.php';

$pageTitle = 'Doctor Profile & Clinical Settings | Doctor Portal • Tupi Municipal Hospital';
$activeMenu = 'settings';

$currentUser = Session::getCurrentUser();
$doctorId = $currentUser['doctor_id'] ?? 11;

$doctorModel = new Doctor();
$doctor = $doctorModel->findById($doctorId);

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        Session::setFlash('error', 'Invalid security token.');
    } elseif ($action === 'update_profile') {
        $data = [
            'first_name'       => trim($_POST['first_name'] ?? $doctor['FirstName']),
            'last_name'        => trim($_POST['last_name'] ?? $doctor['LastName']),
            'title'            => trim($_POST['title'] ?? $doctor['Title']),
            'contact_number'   => trim($_POST['contact_number'] ?? $doctor['ContactNumber']),
            'email'            => trim($_POST['email'] ?? $doctor['Email']),
            'clinic'           => trim($_POST['clinic'] ?? $doctor['Clinic']),
            'clinic_room'      => trim($_POST['clinic_room'] ?? $doctor['ClinicRoom']),
            'consultation_fee' => trim($_POST['consultation_fee'] ?? $doctor['ConsultationFee']),
            'experience_years' => (int)($_POST['experience_years'] ?? $doctor['ExperienceYears']),
            'bio'              => trim($_POST['bio'] ?? $doctor['Bio']),
            'education'        => trim($_POST['education'] ?? $doctor['Education']),
            'languages'        => trim($_POST['languages'] ?? $doctor['Languages']),
            'status'           => $_POST['status'] ?? $doctor['Status']
        ];

        $doctorModel->update($doctorId, $data);

        // Update session name and email
        Session::set('full_name', 'Dr. ' . $data['first_name'] . ' ' . $data['last_name']);
        Session::set('email', $data['email']);

        Session::setFlash('success', 'Doctor clinical profile updated successfully.');
        header("Location: " . doctor_url("views/settings/index.php"));
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
  
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-5xl">

    <div data-aos="fade-down">
      <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">Physician Profile & Clinical Settings</h1>
      <p class="text-xs text-slate-500 font-medium mt-1">Manage attending credentials, clinic consultation fees, licensing, and availability.</p>
    </div>

    <!-- Doctor Profile Card Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card space-y-6" data-aos="fade-up">
      
      <!-- Top Avatar & Bio Header -->
      <div class="flex flex-col sm:flex-row items-center gap-6 border-b border-slate-100 pb-6">
        <img src="<?= e($doctor['ProfileImage'] ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300') ?>" 
             alt="Doctor Photo" 
             class="w-24 h-24 rounded-3xl object-cover ring-4 ring-blue-500/20 shadow-lg shrink-0">
        <div class="space-y-1 text-center sm:text-left min-w-0">
          <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
            <h2 class="text-xl font-black text-slate-900 font-display">Dr. <?= e($doctor['FirstName'] . ' ' . $doctor['LastName']) ?>, <?= e($doctor['Title']) ?></h2>
            <span class="px-3 py-1 bg-blue-50 text-blue-800 rounded-full text-xs font-bold border border-blue-200">
              <?= e($doctor['SpecialtyName'] ?: $doctor['Specialty']) ?>
            </span>
          </div>
          <p class="text-xs text-slate-400 font-mono">License Number: <strong class="text-slate-700"><?= e($doctor['LicenseNumber']) ?></strong></p>
          <p class="text-xs text-slate-500"><?= e($doctor['Clinic']) ?> • <?= e($doctor['ClinicRoom']) ?></p>
        </div>
      </div>

      <form method="POST" action="" class="space-y-5 text-xs">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
        <input type="hidden" name="action" value="update_profile" />

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">First Name *</label>
            <input type="text" name="first_name" value="<?= e($doctor['FirstName']) ?>" required 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Last Name *</label>
            <input type="text" name="last_name" value="<?= e($doctor['LastName']) ?>" required 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Professional Title</label>
            <input type="text" name="title" value="<?= e($doctor['Title']) ?>" placeholder="MD, FACC, etc." 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Email Address *</label>
            <input type="email" name="email" value="<?= e($doctor['Email']) ?>" required 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Direct Contact Number</label>
            <input type="text" name="contact_number" value="<?= e($doctor['ContactNumber']) ?>" 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Experience (Years)</label>
            <input type="number" name="experience_years" value="<?= e($doctor['ExperienceYears']) ?>" 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Clinic Facility Name</label>
            <input type="text" name="clinic" value="<?= e($doctor['Clinic']) ?>" 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Suite / Room Location</label>
            <input type="text" name="clinic_room" value="<?= e($doctor['ClinicRoom']) ?>" 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Standard Consultation Fee</label>
            <input type="text" name="consultation_fee" value="<?= e($doctor['ConsultationFee']) ?>" 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Education & Credentials</label>
          <input type="text" name="education" value="<?= e($doctor['Education'] ?? '') ?>" placeholder="Medical school, residency, fellowships..." 
                 class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Clinical Biography & Specializations</label>
          <textarea name="bio" rows="3" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($doctor['Bio'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Languages Spoken</label>
            <input type="text" name="languages" value="<?= e($doctor['Languages']) ?>" 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Clinical Status</label>
            <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">
              <option value="Available" <?= $doctor['Status'] === 'Available' ? 'selected' : '' ?>>Available (Accepting Queue)</option>
              <option value="Busy" <?= $doctor['Status'] === 'Busy' ? 'selected' : '' ?>>Busy (In Consultation)</option>
              <option value="On Leave" <?= $doctor['Status'] === 'On Leave' ? 'selected' : '' ?>>On Leave</option>
              <option value="Inactive" <?= $doctor['Status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
          <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
            <i data-lucide="save" class="w-4 h-4"></i>
            <span>Save Profile Changes</span>
          </button>
        </div>
      </form>

    </div>

  </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
