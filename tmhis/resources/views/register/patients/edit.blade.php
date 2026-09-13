@extends('layouts.register')

@section('title', 'Tupi Municipal Hospital')

@section('content')
<main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
  
  <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
        <a href="<?= route('register.patients.index') ?>" class="hover:text-blue-600">Patients</a>
        <span>/</span>
        <a href="<?= base_url('views/patients/view.php?id=' . $patient['PatientID']) ?>" class="hover:text-blue-600"><?= e($patient['PatientCode']) ?></a>
        <span>/</span>
        <span class="text-slate-700 font-bold">Edit Profile</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Patient Information</h1>
    </div>

    <a href="<?= base_url('views/patients/view.php?id=' . $patient['PatientID']) ?>" 
       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
      Cancel & View
    </a>
  </div>

  <form method="POST" action="" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

    <!-- Section 1: Personal Details -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-5">
      <h3 class="text-sm font-extrabold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
        <span>Personal Identity & Demographics</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">First Name *</label>
          <input type="text" name="FirstName" value="<?= e($patient['FirstName']) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Middle Name</label>
          <input type="text" name="MiddleName" value="<?= e($patient['MiddleName'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Last Name *</label>
          <input type="text" name="LastName" value="<?= e($patient['LastName']) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Date of Birth *</label>
          <input type="date" name="DateOfBirth" value="<?= e($patient['DateOfBirth']) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Gender *</label>
          <select name="Gender" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
            <option value="Male" <?= $patient['Gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $patient['Gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $patient['Gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Civil Status</label>
          <select name="CivilStatus" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
            <option value="Single" <?= $patient['CivilStatus'] === 'Single' ? 'selected' : '' ?>>Single</option>
            <option value="Married" <?= $patient['CivilStatus'] === 'Married' ? 'selected' : '' ?>>Married</option>
            <option value="Divorced" <?= $patient['CivilStatus'] === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
            <option value="Widowed" <?= $patient['CivilStatus'] === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Contact Number *</label>
          <input type="text" name="ContactNumber" value="<?= e($patient['ContactNumber']) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Email Address *</label>
          <input type="email" name="Email" value="<?= e($patient['Email']) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Blood Type</label>
          <select name="BloodType" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'] as $bt): ?>
              <option value="<?= $bt ?>" <?= $patient['BloodType'] === $bt ? 'selected' : '' ?>><?= $bt ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Complete Street Address *</label>
        <input type="text" name="Address" value="<?= e($patient['Address']) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Patient Category</label>
          <select name="PatientCategory" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
            <option value="Outpatient" <?= $patient['PatientCategory'] === 'Outpatient' ? 'selected' : '' ?>>Outpatient</option>
            <option value="Consultation" <?= $patient['PatientCategory'] === 'Consultation' ? 'selected' : '' ?>>Consultation</option>
            <option value="Admitted" <?= $patient['PatientCategory'] === 'Admitted' ? 'selected' : '' ?>>Admitted</option>
            <option value="Emergency" <?= $patient['PatientCategory'] === 'Emergency' ? 'selected' : '' ?>>Emergency</option>
            <option value="Inpatient" <?= $patient['PatientCategory'] === 'Inpatient' ? 'selected' : '' ?>>Inpatient</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Patient Status</label>
          <select name="Status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
            <option value="Active" <?= $patient['Status'] === 'Active' ? 'selected' : '' ?>>Active</option>
            <option value="Discharged" <?= $patient['Status'] === 'Discharged' ? 'selected' : '' ?>>Discharged</option>
            <option value="Inactive" <?= $patient['Status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Section 2: Emergency Contact -->
    <?php $emg = $patient['EmergencyContact']; ?>
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-5">
      <h3 class="text-sm font-extrabold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
        <i data-lucide="heart-handshake" class="w-4 h-4 text-amber-600"></i>
        <span>Emergency Contact Person</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Contact Name</label>
          <input type="text" name="ContactName" value="<?= e($emg['ContactName'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Relationship</label>
          <input type="text" name="Relationship" value="<?= e($emg['Relationship'] ?? 'Parent') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Emergency Phone</label>
          <input type="text" name="EmergencyPhone" value="<?= e($emg['ContactNumber'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
      </div>
    </div>

    <!-- Section 3: Medical Background -->
    <?php $med = $patient['MedicalHistory']; ?>
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-5">
      <h3 class="text-sm font-extrabold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
        <i data-lucide="clipboard-list" class="w-4 h-4 text-emerald-600"></i>
        <span>Medical Background & Clinical History</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Known Allergies</label>
          <input type="text" name="Allergies" value="<?= e($med['Allergies'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Existing Conditions</label>
          <input type="text" name="ExistingConditions" value="<?= e($med['ExistingConditions'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Current Medications</label>
          <input type="text" name="CurrentMedications" value="<?= e($med['CurrentMedications'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Previous Hospitalization</label>
          <input type="text" name="PreviousHospitalization" value="<?= e($med['PreviousHospitalization'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between pt-2">
      <a href="<?= base_url('views/patients/view.php?id=' . $patient['PatientID']) ?>" class="px-5 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
        Cancel Changes
      </a>

      <button type="submit" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-lg shadow-blue-600/20 transition flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>Save Changes to Database</span>
      </button>
    </div>

  </form>

</main>
@endsection
