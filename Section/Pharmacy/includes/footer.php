<?php
// Pharmacy/includes/footer.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/demo_data.php';
$currentUser = Session::getCurrentUser();
?>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- GLOBAL MODALS                                           -->
<!-- ═══════════════════════════════════════════════════════ -->

<!-- 1. VIEW / VERIFY PRESCRIPTION MODAL ────────────────── -->
<div id="viewRxModal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-2xl max-h-[92vh] overflow-y-auto transform p-6 sm:p-8">
    
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
          <i data-lucide="clipboard-list" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono" id="vRxId">RX-2026-001</span>
          <h3 class="text-lg font-black text-slate-900 font-display">Electronic Prescription</h3>
        </div>
      </div>
      <button onclick="closeModal('viewRxModal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Patient Card -->
    <div class="mt-5 bg-slate-50 border border-slate-200/80 rounded-2xl p-4">
      <h4 class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
        <i data-lucide="user" class="w-3.5 h-3.5 text-sky-600"></i> Patient Information
      </h4>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div><span class="text-slate-400 font-medium block">Patient</span><span class="font-bold text-slate-900 mt-0.5 block text-sm" id="vPatientName">—</span></div>
        <div><span class="text-slate-400 font-medium block">Patient ID</span><span class="font-mono font-bold text-slate-900 mt-0.5 block" id="vPatientId">—</span></div>
        <div><span class="text-slate-400 font-medium block">Age / Gender</span><span class="font-semibold text-slate-900 mt-0.5 block" id="vAgeGender">—</span></div>
        <div><span class="text-slate-400 font-medium block">Allergies</span><span class="font-bold text-rose-600 mt-0.5 block" id="vAllergies">—</span></div>
      </div>
    </div>

    <!-- Doctor & Dates -->
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4">
        <span class="text-slate-400 font-medium block">Prescribing Doctor</span>
        <span class="font-bold text-slate-900 mt-1 block text-sm" id="vDoctor">—</span>
        <span class="text-[11px] text-slate-500 mt-0.5 block" id="vSpecialty">—</span>
      </div>
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4">
        <span class="text-slate-400 font-medium block">Prescription Date</span>
        <span class="font-bold text-slate-900 mt-1 block text-sm" id="vRxDate">—</span>
        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block" id="vPriority">—</span>
      </div>
    </div>

    <!-- Medication Details -->
    <div class="mt-4 bg-sky-50/60 border border-sky-200/60 rounded-2xl p-4">
      <h4 class="text-[10px] font-black uppercase tracking-wider text-sky-700 mb-3 flex items-center gap-1.5">
        <i data-lucide="pill" class="w-3.5 h-3.5"></i> Medication Details
      </h4>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
        <div><span class="text-slate-400 font-medium block">Medicine</span><span class="font-bold text-slate-900 mt-0.5 block" id="vMedicine">—</span></div>
        <div><span class="text-slate-400 font-medium block">Dosage</span><span class="font-bold text-sky-700 mt-0.5 block" id="vDosage">—</span></div>
        <div><span class="text-slate-400 font-medium block">Frequency</span><span class="font-semibold text-slate-900 mt-0.5 block" id="vFrequency">—</span></div>
        <div><span class="text-slate-400 font-medium block">Route</span><span class="font-semibold text-slate-900 mt-0.5 block" id="vRoute">—</span></div>
        <div><span class="text-slate-400 font-medium block">Duration</span><span class="font-semibold text-slate-900 mt-0.5 block" id="vDuration">—</span></div>
        <div><span class="text-slate-400 font-medium block">Quantity</span><span class="font-bold text-emerald-700 mt-0.5 block" id="vQuantity">—</span></div>
      </div>
      <div class="mt-3 pt-3 border-t border-sky-200/60">
        <span class="text-slate-400 font-medium block text-xs mb-1">Doctor's Instructions</span>
        <p class="text-xs text-slate-700 leading-relaxed" id="vInstructions">—</p>
      </div>
    </div>

    <!-- Drug Interaction Warning -->
    <div id="drugInteractionBanner" class="mt-4 hidden">
      <div class="flex items-start gap-3 bg-amber-50 border border-amber-300 rounded-2xl p-4">
        <i data-lucide="triangle-alert" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
        <div>
          <p class="text-xs font-bold text-amber-800">Drug Interaction Warning</p>
          <p class="text-xs text-amber-700 mt-0.5" id="vInteraction">—</p>
        </div>
      </div>
    </div>

    <!-- Status & Actions -->
    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Current Status</span>
        <span class="px-3 py-1 rounded-full text-xs font-bold border" id="vStatus">—</span>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <button onclick="closeModal('viewRxModal'); openVerifyRxModal(currentViewedRx)"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
          <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
          Verify Prescription
        </button>
        <button onclick="closeModal('viewRxModal'); openDispensingModal(currentViewedRx)"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
          <i data-lucide="pill" class="w-3.5 h-3.5"></i>
          Dispense
        </button>
        <button onclick="closeModal('viewRxModal')"
                class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- 2. VERIFY PRESCRIPTION MODAL ───────────────────────── -->
<div id="verifyRxModal"
     class="fixed inset-0 z-[110] hidden items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-xl max-h-[90vh] overflow-y-auto p-6 sm:p-8">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
          <i data-lucide="shield-check" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono" id="vRxId2">RX-2026-001</span>
          <h3 class="text-lg font-black text-slate-900 font-display">Prescription Verification</h3>
        </div>
      </div>
      <button onclick="closeModal('verifyRxModal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Verification Checklist -->
    <div class="mt-5 space-y-2">
      <?php
      $checks = [
        ['Patient Information', 'user', 'sky'],
        ['Doctor Information', 'stethoscope', 'indigo'],
        ['Medicine / Generic Name', 'pill', 'sky'],
        ['Dosage', 'thermometer', 'emerald'],
        ['Quantity', 'hash', 'emerald'],
        ['Frequency', 'repeat', 'sky'],
        ['Duration', 'calendar', 'sky'],
        ['Route of Administration', 'route', 'indigo'],
        ['Allergy Check', 'shield', 'amber'],
        ['Drug Interaction Check', 'triangle-alert', 'rose'],
      ];
      foreach ($checks as [$label, $icon, $color]):
      ?>
      <div class="flex items-center gap-3 px-4 py-2.5 bg-slate-50 hover:bg-emerald-50/40 border border-slate-200/60 rounded-xl transition group">
        <div class="w-7 h-7 rounded-lg bg-<?= $color ?>-50 border border-<?= $color ?>-100 flex items-center justify-center shrink-0">
          <i data-lucide="<?= $icon ?>" class="w-3.5 h-3.5 text-<?= $color ?>-600"></i>
        </div>
        <span class="text-xs font-semibold text-slate-700 flex-1"><?= $label ?></span>
        <div class="w-5 h-5 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-600">
          <i data-lucide="check" class="w-3 h-3"></i>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Reject Reason (Hidden by default) -->
    <div id="rejectReasonSection" class="mt-4 hidden">
      <label class="text-xs font-bold text-slate-700 block mb-1.5">
        Reason for Rejection <span class="text-rose-500">*</span>
      </label>
      <textarea id="rejectReasonText" rows="3"
                class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition resize-none"
                placeholder="Enter reason for rejection..."></textarea>
    </div>

    <!-- Action Buttons -->
    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-2">
      <button onclick="confirmVerification()"
              class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
        Verify Prescription
      </button>
      <button onclick="requestClarification()"
              class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
        <i data-lucide="message-circle" class="w-4 h-4"></i>
        Request Clarification
      </button>
      <button onclick="toggleRejectSection()"
              class="flex-1 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition border border-rose-200 flex items-center justify-center gap-2">
        <i data-lucide="x-circle" class="w-4 h-4"></i>
        Reject
      </button>
    </div>
  </div>
</div>

<!-- 3. DISPENSING RECORD MODAL ──────────────────────────── -->
<div id="dispensingModal"
     class="fixed inset-0 z-[110] hidden items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-xl max-h-[90vh] overflow-y-auto p-6 sm:p-8">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600">
          <i data-lucide="pill" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono" id="dRxId">DISP-2026-021</span>
          <h3 class="text-lg font-black text-slate-900 font-display">Create Dispensing Record</h3>
        </div>
      </div>
      <button onclick="closeModal('dispensingModal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <div class="mt-5 space-y-4">
      <!-- Patient & Rx (read-only) -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Patient</label>
          <div class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900" id="dPatient">—</div>
        </div>
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Prescription ID</label>
          <div class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-semibold text-slate-900" id="dRx">—</div>
        </div>
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Medicine</label>
        <div class="px-3 py-2.5 bg-sky-50 border border-sky-200 rounded-xl text-xs font-bold text-sky-800" id="dMedicine">—</div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Qty Prescribed</label>
          <div class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900" id="dQtyPrescribed">—</div>
        </div>
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Qty to Dispense</label>
          <input type="number" id="dQtyDispense"
                 class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                 placeholder="Enter quantity">
        </div>
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Dispensing Date</label>
        <input type="date" id="dDate" value="<?= date('Y-m-d') ?>"
               class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Dispensed By</label>
        <div class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900"><?= e($currentUser['name']) ?></div>
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Patient Instructions</label>
        <textarea rows="2" id="dInstructions"
                  class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition resize-none"
                  placeholder="Add patient counseling notes..."></textarea>
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Notes</label>
        <textarea rows="2" id="dNotes"
                  class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition resize-none"
                  placeholder="Optional pharmacist notes..."></textarea>
      </div>
    </div>

    <div class="mt-5 pt-4 border-t border-slate-100 flex gap-2">
      <button onclick="confirmDispensing()"
              class="flex-1 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
        Confirm Dispensing
      </button>
      <button onclick="closeModal('dispensingModal')"
              class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
        Cancel
      </button>
    </div>
  </div>
</div>

<!-- 4. STOCK-IN MODAL ───────────────────────────────────── -->
<div id="stockInModal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-xl max-h-[90vh] overflow-y-auto p-6 sm:p-8">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600">
          <i data-lucide="package-plus" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">STOCK ENTRY</span>
          <h3 class="text-lg font-black text-slate-900 font-display">Create Stock-In Entry</h3>
        </div>
      </div>
      <button onclick="closeModal('stockInModal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <div class="mt-5 space-y-4">
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Medicine <span class="text-rose-500">*</span></label>
        <select class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
          <option value="">Select medicine...</option>
          <?php foreach (getDemoCatalog() as $m): ?>
          <option value="<?= e($m['med_id']) ?>"><?= e($m['generic_name']) ?> <?= e($m['strength']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Batch Number</label>
          <input type="text" placeholder="e.g. B-2026-050" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
        </div>
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Supplier</label>
          <input type="text" placeholder="e.g. Zuellig Pharma" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Quantity <span class="text-rose-500">*</span></label>
          <input type="number" placeholder="e.g. 200" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
        </div>
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Unit</label>
          <select class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
            <option>Tablet</option><option>Capsule</option><option>Vial</option><option>Inhaler</option><option>Bottle</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Manufacturing Date</label>
          <input type="date" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
        </div>
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Expiry Date <span class="text-rose-500">*</span></label>
          <input type="date" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
        </div>
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Received Date</label>
        <input type="date" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Notes</label>
        <textarea rows="2" placeholder="Purchase order number, delivery notes..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition resize-none"></textarea>
      </div>
    </div>
    <div class="mt-5 pt-4 border-t border-slate-100 flex gap-2">
      <button onclick="saveStockIn()"
              class="flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
        <i data-lucide="save" class="w-4 h-4"></i>
        Save Stock-In Entry
      </button>
      <button onclick="closeModal('stockInModal')"
              class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
        Cancel
      </button>
    </div>
  </div>
</div>

<!-- 5. STOCK DISPOSAL MODAL ─────────────────────────────── -->
<div id="disposalModal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 sm:p-8">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">DISPOSAL RECORD</span>
          <h3 class="text-lg font-black text-slate-900 font-display">Create Disposal Record</h3>
        </div>
      </div>
      <button onclick="closeModal('disposalModal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <div class="mt-5 space-y-4">
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Medicine <span class="text-rose-500">*</span></label>
        <select class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
          <option value="">Select medicine...</option>
          <?php foreach (getDemoInventory() as $inv): ?>
          <option value="<?= e($inv['inv_id']) ?>"><?= e($inv['medicine']) ?> – Batch <?= e($inv['batch']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Quantity <span class="text-rose-500">*</span></label>
          <input type="number" placeholder="Quantity to dispose" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
        </div>
        <div>
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Reason <span class="text-rose-500">*</span></label>
          <select class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
            <option>Expired</option>
            <option>Damaged</option>
            <option>Contaminated</option>
            <option>Recalled</option>
            <option>Other</option>
          </select>
        </div>
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Disposal Date</label>
        <input type="date" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
      </div>
      <div>
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Notes</label>
        <textarea rows="2" placeholder="Disposal method, authorized by..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition resize-none"></textarea>
      </div>
    </div>
    <div class="mt-5 pt-4 border-t border-slate-100 flex gap-2">
      <button onclick="saveDisposal()"
              class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Confirm Disposal
      </button>
      <button onclick="closeModal('disposalModal')"
              class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
        Cancel
      </button>
    </div>
  </div>
</div>

<!-- 6. PROFILE MODAL ────────────────────────────────────── -->
<div id="profileModal"
     class="fixed inset-0 z-[120] hidden items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-md p-6 sm:p-8">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <h3 class="text-lg font-black text-slate-900 font-display">Pharmacist Profile</h3>
      <button onclick="closeModal('profileModal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <div class="mt-5 flex flex-col items-center text-center">
      <img src="<?= e($currentUser['avatar']) ?>" alt="Profile" class="w-20 h-20 rounded-2xl object-cover border-2 border-sky-200 shadow-md">
      <h4 class="text-xl font-black text-slate-900 mt-3 font-display"><?= e($currentUser['name']) ?></h4>
      <p class="text-sm font-semibold text-sky-600"><?= e($currentUser['title']) ?></p>
      <p class="text-xs text-slate-500 mt-1"><?= e($currentUser['department']) ?></p>
    </div>
    <div class="mt-5 space-y-2 text-xs">
      <?php
      $profileFields = [
        ['License No.',  $currentUser['license_no'],  'badge'],
        ['Email',        $currentUser['email'],        'mail'],
        ['Shift',        $currentUser['shift'],        'clock'],
        ['Pharmacist ID',$currentUser['pharmacist_id'],'hash'],
      ];
      foreach ($profileFields as [$label, $value, $icon]): ?>
      <div class="flex items-center gap-3 px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl">
        <i data-lucide="<?= $icon ?>" class="w-4 h-4 text-sky-600 shrink-0"></i>
        <div class="min-w-0">
          <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wider"><?= $label ?></span>
          <span class="font-semibold text-slate-900 block"><?= e($value) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <button onclick="closeModal('profileModal')"
            class="mt-5 w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl transition text-sm">
      Close
    </button>
  </div>
</div>

<!-- 7. TOAST NOTIFICATION ───────────────────────────────── -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 items-end pointer-events-none"></div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- GLOBAL JAVASCRIPT                                       -->
<!-- ═══════════════════════════════════════════════════════ -->
<script>
// ── AOS Init ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  AOS.init({ duration: 450, easing: 'ease-out-cubic', once: true, offset: 30, delay: 0 });
  if (window.lucide) lucide.createIcons();
  startLiveClock();
  renderInventoryChart();
  renderCategoryChart();
});

// ── Live Clock ────────────────────────────────────────────
function startLiveClock() {
  const el = document.getElementById('liveClockDisplay');
  if (!el) return;
  setInterval(() => {
    const now = new Date();
    el.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }, 1000);
}

// ── Modal Open/Close ──────────────────────────────────────
function openModal(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.remove('hidden');
  m.classList.add('flex');
  if (window.lucide) lucide.createIcons();
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.add('hidden');
  m.classList.remove('flex');
}

// ── Toast ─────────────────────────────────────────────────
function showToast(message, type = 'info', duration = 3500) {
  const colors = {
    info:    'bg-sky-600',
    success: 'bg-emerald-600',
    warning: 'bg-amber-500',
    error:   'bg-rose-600'
  };
  const icons = {
    info: 'info', success: 'check-circle-2', warning: 'triangle-alert', error: 'x-circle'
  };
  const toast = document.createElement('div');
  toast.className = `flex items-center gap-3 px-4 py-3 ${colors[type] || colors.info} text-white text-xs font-semibold rounded-2xl shadow-xl pointer-events-auto max-w-xs transition-all duration-300 opacity-0 translate-y-2`;
  toast.innerHTML = `<i data-lucide="${icons[type] || 'info'}" class="w-4 h-4 shrink-0"></i><span>${message}</span>`;
  document.getElementById('toastContainer').appendChild(toast);
  if (window.lucide) lucide.createIcons();
  requestAnimationFrame(() => {
    toast.classList.remove('opacity-0', 'translate-y-2');
  });
  setTimeout(() => {
    toast.classList.add('opacity-0', 'translate-y-2');
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// ── Global Search ─────────────────────────────────────────
function handleGlobalSearch(e) {
  const q = (e.target.value || '').toLowerCase().trim();
  document.querySelectorAll('.searchable-row').forEach(row => {
    row.style.display = (row.textContent || '').toLowerCase().includes(q) ? '' : 'none';
  });
}

// Shortcut '/' for search
document.addEventListener('keydown', e => {
  if (e.key === '/' && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) {
    e.preventDefault();
    document.getElementById('globalSearchInput')?.focus();
  }
  if (e.key === 'Escape') {
    ['viewRxModal','verifyRxModal','dispensingModal','stockInModal','disposalModal','profileModal'].forEach(closeModal);
  }
});

// ── Close dropdowns on outside click ─────────────────────
document.addEventListener('click', e => {
  const profileDd = document.getElementById('profileDropdown');
  const notifDd   = document.getElementById('notificationDropdown');
  if (profileDd && !e.target.closest('#sidebar') && !profileDd.classList.contains('hidden')) {
    profileDd.classList.add('hidden');
  }
  if (notifDd && !e.target.closest('#topbar') && !notifDd.classList.contains('hidden')) {
    notifDd.classList.add('hidden');
  }
});

// ── Notification Helpers ──────────────────────────────────
function toggleNotificationDropdown() {
  document.getElementById('notificationDropdown')?.classList.toggle('hidden');
}
function markAllRead() {
  showToast('All notifications marked as read', 'info');
  document.getElementById('notificationDropdown')?.classList.add('hidden');
}
function handleNotifClick(tab) {
  if (tab) switchMainTab(tab);
  document.getElementById('notificationDropdown')?.classList.add('hidden');
}

// ── Profile Modal ─────────────────────────────────────────
function openProfileModal() {
  document.getElementById('profileDropdown')?.classList.add('hidden');
  openModal('profileModal');
}

// ── SPA Tab Switcher ──────────────────────────────────────
const ALL_TABS = ['dashboard','prescriptions','dispensing','inventory','catalog','alerts','activity','notifications'];
const TAB_TITLES = {
  dashboard:     'Pharmacy Dashboard',
  prescriptions: 'Prescription Queue & Verification',
  dispensing:    'Medicine Dispensing',
  inventory:     'Pharmacy Inventory',
  catalog:       'Medicine Catalog',
  alerts:        'Pharmacy Alerts',
  activity:      'Reports & Activity Log',
  notifications: 'Department Notifications'
};

function switchMainTab(tabId) {
  ALL_TABS.forEach(t => {
    const sec = document.getElementById(`tab-section-${t}`);
    const nav = document.getElementById(`nav-${t}`);
    if (sec) sec.classList.toggle('hidden', t !== tabId);
    if (nav) {
      if (t === tabId) {
        nav.classList.add('bg-sky-50','text-sky-700','border','border-sky-200/70','shadow-xs');
        nav.classList.remove('text-slate-600');
      } else {
        nav.classList.remove('bg-sky-50','text-sky-700','border','border-sky-200/70','shadow-xs');
        nav.classList.add('text-slate-600');
      }
    }
  });
  const titleEl = document.getElementById('topbarPageTitle');
  if (titleEl) titleEl.textContent = TAB_TITLES[tabId] || 'Pharmacy Dashboard';

  // Close mobile sidebar
  if (window.innerWidth < 1024) {
    document.getElementById('sidebar')?.classList.add('-translate-x-full');
    document.getElementById('sidebarOverlay')?.classList.add('hidden');
  }
  if (window.lucide) lucide.createIcons();
  // Re-trigger AOS
  AOS.refresh();
}

// ── Prescription Actions ──────────────────────────────────
let currentViewedRx = null;
const prescriptions = <?php echo json_encode(getdemoPrescriptions()); ?>;

function openViewRxModal(rxData) {
  currentViewedRx = rxData;
  document.getElementById('vRxId').textContent       = rxData.rx_id;
  document.getElementById('vPatientName').textContent = rxData.patient_name;
  document.getElementById('vPatientId').textContent   = rxData.patient_id;
  document.getElementById('vAgeGender').textContent   = `${rxData.age} yrs • ${rxData.gender}`;
  document.getElementById('vAllergies').textContent   = rxData.allergies;
  document.getElementById('vDoctor').textContent      = rxData.doctor;
  document.getElementById('vSpecialty').textContent   = rxData.specialty;
  document.getElementById('vRxDate').textContent      = rxData.rx_date;
  document.getElementById('vMedicine').textContent    = rxData.medicine;
  document.getElementById('vDosage').textContent      = rxData.dosage;
  document.getElementById('vFrequency').textContent   = rxData.frequency;
  document.getElementById('vRoute').textContent       = rxData.route;
  document.getElementById('vDuration').textContent    = rxData.duration;
  document.getElementById('vQuantity').textContent    = rxData.quantity;
  document.getElementById('vInstructions').textContent= rxData.instructions;

  // Priority badge
  const priEl = document.getElementById('vPriority');
  priEl.textContent = rxData.priority;
  priEl.className = rxData.priority === 'STAT'
    ? 'text-[10px] font-black px-2 py-0.5 rounded-full mt-1 inline-block bg-rose-100 text-rose-700 border border-rose-300 animate-pulse'
    : rxData.priority === 'Urgent'
    ? 'text-[10px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block bg-amber-100 text-amber-700 border border-amber-300'
    : 'text-[10px] font-semibold px-2 py-0.5 rounded-full mt-1 inline-block bg-slate-100 text-slate-600 border border-slate-200';

  // Drug Interaction
  const interBanner = document.getElementById('drugInteractionBanner');
  if (rxData.drug_interaction && rxData.drug_interaction !== 'None detected') {
    document.getElementById('vInteraction').textContent = rxData.drug_interaction;
    interBanner.classList.remove('hidden');
  } else {
    interBanner.classList.add('hidden');
  }

  // Status
  const statEl = document.getElementById('vStatus');
  statEl.textContent = rxData.status;
  statEl.className = `px-3 py-1 rounded-full text-xs font-bold border ${getRxStatusClass(rxData.status)}`;

  openModal('viewRxModal');
}

function getRxStatusClass(status) {
  const map = {
    'Pending':              'bg-amber-50 text-amber-700 border-amber-200',
    'Under Verification':   'bg-blue-50 text-blue-700 border-blue-200',
    'Verified':             'bg-indigo-50 text-indigo-700 border-indigo-200',
    'Ready for Dispensing': 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'Dispensed':            'bg-teal-50 text-teal-700 border-teal-200',
    'Rejected':             'bg-rose-50 text-rose-700 border-rose-200',
  };
  return map[status] || 'bg-slate-100 text-slate-700 border-slate-200';
}

function openVerifyRxModal(rxData) {
  if (!rxData) return;
  currentViewedRx = rxData;
  document.getElementById('vRxId2').textContent = rxData.rx_id;
  document.getElementById('rejectReasonSection')?.classList.add('hidden');
  openModal('verifyRxModal');
}

function confirmVerification() {
  closeModal('verifyRxModal');
  showToast(`Prescription ${currentViewedRx?.rx_id || ''} verified successfully`, 'success');
}

function requestClarification() {
  closeModal('verifyRxModal');
  showToast('Clarification request sent to prescribing physician', 'warning');
}

function toggleRejectSection() {
  const sec = document.getElementById('rejectReasonSection');
  const isHidden = sec.classList.contains('hidden');
  sec.classList.toggle('hidden', !isHidden);
  if (!isHidden) {
    // Confirm rejection
    const reason = document.getElementById('rejectReasonText')?.value;
    if (!reason?.trim()) { showToast('Please provide a reason for rejection', 'error'); sec.classList.remove('hidden'); return; }
    closeModal('verifyRxModal');
    showToast(`Prescription rejected: ${reason}`, 'error');
  }
}

// ── Dispensing Actions ────────────────────────────────────
function openDispensingModal(rxData) {
  if (!rxData) return;
  currentViewedRx = rxData;
  document.getElementById('dRxId').textContent         = `DISP-${new Date().getFullYear()}-${String(Math.floor(Math.random()*99)+21).padStart(3,'0')}`;
  document.getElementById('dPatient').textContent      = rxData.patient_name;
  document.getElementById('dRx').textContent           = rxData.rx_id;
  document.getElementById('dMedicine').textContent     = rxData.medicine;
  document.getElementById('dQtyPrescribed').textContent= rxData.quantity;
  openModal('dispensingModal');
}

function confirmDispensing() {
  const qty = document.getElementById('dQtyDispense')?.value;
  if (!qty || qty < 1) { showToast('Please enter a valid quantity', 'error'); return; }
  closeModal('dispensingModal');
  showToast(`Medicine dispensed successfully to ${currentViewedRx?.patient_name || 'patient'}`, 'success');
}

// ── Stock-In Modal ────────────────────────────────────────
function openStockInModal() { openModal('stockInModal'); }
function saveStockIn() {
  closeModal('stockInModal');
  showToast('Stock-In entry saved. Inventory updated.', 'success');
}

// ── Disposal Modal ────────────────────────────────────────
function openDisposalModal() { openModal('disposalModal'); }
function saveDisposal() {
  closeModal('disposalModal');
  showToast('Disposal record created. Inventory reduced.', 'info');
}

// ── Inventory Chart ───────────────────────────────────────
function renderInventoryChart() {
  const ctx = document.getElementById('inventoryStatusChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Expiring Soon', 'Expired'],
      datasets: [{
        data: [26, 8, 2, 3, 1],
        backgroundColor: ['#10b981','#f59e0b','#ef4444','#f97316','#dc2626'],
        borderWidth: 0,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '72%',
      plugins: {
        legend: { position: 'right', labels: { boxWidth: 10, padding: 14, font: { size: 10, family: 'Inter', weight: '600' } } },
        tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} items` } }
      }
    }
  });
}

function renderCategoryChart() {
  const ctx = document.getElementById('categoryStockChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Antibiotic', 'Antihypert.', 'Antidiabetic', 'Analgesic', 'Vitamin', 'Other'],
      datasets: [{
        label: 'Units',
        data: [503, 505, 310, 447, 630, 350],
        backgroundColor: ['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#ec4899','#94a3b8'],
        borderRadius: 8, borderSkipped: false
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 9, family: 'Inter' } } },
        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 9, family: 'Inter' } } }
      }
    }
  });
}

// ── Pharmacy Visualizer Cycle ─────────────────────────────
const visualizerMeds = [
  { name: 'Amoxicillin 500mg', stock: 120, batch: 'B-2026-011', status: 'IN STOCK', color: '#0ea5e9' },
  { name: 'Clopidogrel 75mg',  stock: 80,  batch: 'B-2026-022', status: 'IN STOCK', color: '#8b5cf6' },
  { name: 'Paracetamol 500mg', stock: 35,  batch: 'B-2026-012', status: 'LOW STOCK', color: '#f59e0b' },
  { name: 'Insulin Glargine',  stock: 15,  batch: 'B-2026-040', status: 'IN STOCK', color: '#10b981' },
];
let vizIdx = 0;

function cycleVisualizer() {
  vizIdx = (vizIdx + 1) % visualizerMeds.length;
  const m = visualizerMeds[vizIdx];
  const nameEl   = document.getElementById('vizMedName');
  const stockEl  = document.getElementById('vizStock');
  const batchEl  = document.getElementById('vizBatch');
  const statusEl = document.getElementById('vizStatus');
  const glowEl   = document.getElementById('vizGlow');
  if (nameEl)  nameEl.textContent  = m.name;
  if (stockEl) stockEl.textContent = m.stock + ' Units';
  if (batchEl) batchEl.textContent = m.batch;
  if (statusEl){ statusEl.textContent = m.status; statusEl.style.color = m.color; }
  if (glowEl)  glowEl.style.background = m.color;
  showToast(`Pharmacy display: ${m.name} (${m.stock} units)`, 'info');
}

setInterval(cycleVisualizer, 8000);

// ── Filter Rx Table ───────────────────────────────────────
function filterRxTable(status, btn) {
  document.querySelectorAll('#rxTableBody tr').forEach(row => {
    const rowStatus = row.getAttribute('data-status');
    row.style.display = (status === 'all' || rowStatus === status) ? '' : 'none';
  });
  document.querySelectorAll('.rx-filter-btn').forEach(b => {
    b.className = 'rx-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition';
  });
  if (btn) btn.className = 'rx-filter-btn px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-semibold transition active:scale-95';
}

// ── Filter Inventory Table ────────────────────────────────
function filterInventory(status, btn) {
  document.querySelectorAll('#inventoryTableBody tr').forEach(row => {
    const rowStatus = row.getAttribute('data-status');
    row.style.display = (status === 'all' || rowStatus === status) ? '' : 'none';
  });
  document.querySelectorAll('.inv-filter-btn').forEach(b => {
    b.className = 'inv-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition';
  });
  if (btn) btn.className = 'inv-filter-btn px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-semibold transition active:scale-95';
}
</script>
<script src="../includes/paginator.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (window.TablePaginator) {
      TablePaginator.paginateHtmlTable('#pharmacyRxTable', { pageSize: 8, recordLabel: 'prescriptions' });
      TablePaginator.paginateHtmlTable('#pharmacyDispensingTable', { pageSize: 8, recordLabel: 'dispensing logs' });
      TablePaginator.paginateHtmlTable('#pharmacyInventoryTable', { pageSize: 8, recordLabel: 'medicines' });
      TablePaginator.paginateHtmlTable('#pharmacyCatalogTable', { pageSize: 8, recordLabel: 'formulary items' });
    }
  });
</script>

</body>
</html>
