@extends('layouts.register')

@section('title', 'Patients Directory | Tupi Municipal Hospital Information Management System')

@section('content')
<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
  
  <!-- Header & Page Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
        EHR Patient Master Directory
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Patient Records</h1>
      <p class="text-xs text-slate-500 mt-0.5">Database-driven patient directory with 10 records per page, search, and full clinical profiles.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
      <a href="<?= route('register.registration.index') ?>" 
         class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-blue-600/20 transition flex items-center gap-2">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span>+ Register New Patient</span>
      </a>

      <button type="button" onclick="window.print()" class="no-print px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
        <i data-lucide="printer" class="w-4 h-4"></i>
        <span>Print Directory</span>
      </button>
    </div>
  </div>

  <!-- Search & Filter Controls Bar (No Print) -->
  <div class="no-print bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs">
    <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
      
      <!-- Search Input -->
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
          <i data-lucide="search" class="w-4 h-4"></i>
        </div>
        <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search name, code, phone..." 
               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
      </div>

      <!-- Category Filter -->
      <div>
        <select name="category" onchange="this.form.submit()" 
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
          <option value="">-- All Categories --</option>
          <option value="Outpatient" <?= $category === 'Outpatient' ? 'selected' : '' ?>>Outpatient</option>
          <option value="Consultation" <?= $category === 'Consultation' ? 'selected' : '' ?>>Consultation</option>
          <option value="Admitted" <?= $category === 'Admitted' ? 'selected' : '' ?>>Admitted</option>
          <option value="Emergency" <?= $category === 'Emergency' ? 'selected' : '' ?>>Emergency</option>
          <option value="Inpatient" <?= $category === 'Inpatient' ? 'selected' : '' ?>>Inpatient</option>
        </select>
      </div>

      <!-- Status Filter -->
      <div>
        <select name="status" onchange="this.form.submit()" 
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
          <option value="">-- All Statuses --</option>
          <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
          <option value="Discharged" <?= $status === 'Discharged' ? 'selected' : '' ?>>Discharged</option>
          <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>

      <!-- Submit & Reset -->
      <div class="flex items-center gap-2">
        <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
          <i data-lucide="filter" class="w-3.5 h-3.5"></i>
          <span>Apply Filter</span>
        </button>
        <?php if (!empty($search) || !empty($category) || !empty($status)): ?>
          <a href="<?= route('register.patients.index') ?>" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition" title="Reset Filters">
            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
          </a>
        <?php endif; ?>
      </div>

    </form>
  </div>

  <!-- Master Patients Table Card -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
      <div class="text-xs font-bold text-slate-700">
        Showing <span class="text-blue-600 font-extrabold"><?= min($offset + 1, $totalPatients) ?>–<?= min($offset + count($patients), $totalPatients) ?></span> of <span class="text-slate-900 font-black"><?= $totalPatients ?></span> patients
      </div>
      <div class="text-[11px] text-slate-400 font-semibold">
        Paginated: Exactly 10 records per page (MySQL LIMIT 10 OFFSET)
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 font-bold">
            <th class="pb-3.5">Patient ID</th>
            <th class="pb-3.5">Patient Name & Demographics</th>
            <th class="pb-3.5">Category</th>
            <th class="pb-3.5">Registration Date</th>
            <th class="pb-3.5">Chief Complaint</th>
            <th class="pb-3.5">Assigned Specialist</th>
            <th class="pb-3.5">Status</th>
            <th class="pb-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($patients)): ?>
            <?php foreach ($patients as $pat): ?>
              <tr class="hover:bg-slate-50/70 transition">
                
                <td class="py-3.5 font-mono font-bold text-blue-700">
                  <a href="<?= base_url('views/patients/view.php?id=' . $pat['PatientID']) ?>" class="hover:underline flex items-center gap-1.5">
                    <span><?= e($pat['PatientCode']) ?></span>
                  </a>
                </td>

                <td class="py-3.5">
                  <div class="font-bold text-slate-800 text-sm"><?= e($pat['FirstName'] . ' ' . $pat['LastName']) ?></div>
                  <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                    <span><?= e($pat['Gender']) ?></span> • 
                    <span><?= e($pat['Age']) ?> yrs old</span> • 
                    <span><?= e($pat['ContactNumber']) ?></span>
                  </div>
                </td>

                <td class="py-3.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border <?= get_category_badge($pat['PatientCategory']) ?>">
                    <?= e($pat['PatientCategory']) ?>
                  </span>
                </td>

                <td class="py-3.5 text-slate-500 font-medium">
                  <?= format_date($pat['CreatedAt']) ?>
                </td>

                <td class="py-3.5 text-slate-600 max-w-[200px] truncate" title="<?= e($pat['ComplaintDescription']) ?>">
                  <?= e($pat['ComplaintDescription'] ?: 'None recorded') ?>
                </td>

                <td class="py-3.5">
                  <?php if (!empty($pat['DoctorLast'])): ?>
                    <div class="font-bold text-slate-800">Dr. <?= e($pat['DoctorLast']) ?></div>
                    <div class="text-[10px] text-slate-500"><?= e($pat['DoctorSpecialty']) ?></div>
                  <?php else: ?>
                    <span class="text-slate-400 italic">Unassigned</span>
                  <?php endif; ?>
                </td>

                <td class="py-3.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold <?= $pat['Status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' ?>">
                    <?= e($pat['Status']) ?>
                  </span>
                </td>

                <td class="py-3.5 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="<?= base_url('views/patients/view.php?id=' . $pat['PatientID']) ?>" 
                       class="p-2 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition" 
                       title="View Full Profile">
                      <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>

                    <a href="<?= base_url('views/patients/edit.php?id=' . $pat['PatientID']) ?>" 
                       class="p-2 rounded-xl text-slate-500 hover:text-amber-600 hover:bg-amber-50 border border-slate-200 hover:border-amber-200 transition" 
                       title="Edit Patient">
                      <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>

                    <a href="<?= base_url('views/patients/view.php?id=' . $pat['PatientID'] . '&print=1') ?>" 
                       class="p-2 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 transition" 
                       title="Print Record">
                      <i data-lucide="printer" class="w-4 h-4"></i>
                    </a>
                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="py-10 text-center text-slate-400 italic">
                No patients found matching the current search criteria.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls (10 Patients Per Page) -->
    <?php if ($totalPages > 1): ?>
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 text-xs">
        
        <div class="text-slate-500 font-medium">
          Page <strong class="text-slate-900 font-black"><?= $page ?></strong> of <strong class="text-slate-900 font-black"><?= $totalPages ?></strong>
        </div>

        <div class="flex items-center gap-1.5">
          <!-- Previous Page Button -->
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&status=<?= urlencode($status) ?>" 
               class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1">
              <i data-lucide="chevron-left" class="w-4 h-4"></i>
              <span>Prev</span>
            </a>
          <?php endif; ?>

          <!-- Numeric Pages -->
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&status=<?= urlencode($status) ?>" 
               class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition <?= $i === $page ? 'bg-blue-600 text-white shadow-xs' : 'border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>

          <!-- Next Page Button -->
          <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&status=<?= urlencode($status) ?>" 
               class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1">
              <span>Next</span>
              <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
          <?php endif; ?>
        </div>

      </div>
    <?php endif; ?>

  </div>

</main>
@endsection
