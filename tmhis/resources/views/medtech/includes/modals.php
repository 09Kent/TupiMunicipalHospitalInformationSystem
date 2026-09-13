<?php
// Med_Tech/includes/modals.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$currentUser = Session::getCurrentUser();
$catalog = getDemoTestCatalog();
$referenceRanges = getDemoReferenceRanges();
$patients = getDemoPatients();
$requests = getDemoLabRequests();
?>

<!-- 1. VIEW LABORATORY TEST REQUEST MODAL / DRAWER -->
<div id="viewRequestModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
          <i data-lucide="clipboard-list" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono" id="vReqId">LAB-2026-001</span>
          <h3 class="text-lg font-black text-slate-900 font-display">Laboratory Test Request</h3>
        </div>
      </div>
      <button onclick="closeModal('viewRequestModal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Modal Content -->
    <div class="py-6 space-y-6">
      
      <!-- Patient Information Card -->
      <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 sm:p-5">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
          <i data-lucide="user" class="w-4 h-4 text-indigo-600"></i>
          <span>Patient Information</span>
        </h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
          <div>
            <span class="text-slate-400 font-medium block">Patient Name</span>
            <span class="text-slate-900 font-bold mt-0.5 block text-sm" id="vPatientName">Juan Dela Cruz</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Patient ID</span>
            <span class="text-slate-900 font-mono font-bold mt-0.5 block" id="vPatientId">P-2026-0001</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Age / Gender</span>
            <span class="text-slate-900 font-semibold mt-0.5 block" id="vAgeGender">45 yrs • Male</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Blood Group</span>
            <span class="text-indigo-600 font-bold mt-0.5 block" id="vBloodType">O+</span>
          </div>
        </div>
      </div>

      <!-- Request Details Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div class="p-4 bg-white border border-slate-200/80 rounded-2xl">
          <span class="text-slate-400 font-medium block">Requested Test</span>
          <span class="text-slate-900 font-bold mt-1 block text-sm" id="vTestName">Complete Blood Count (CBC)</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5 block" id="vCategory">Hematology Section</span>
        </div>

        <div class="p-4 bg-white border border-slate-200/80 rounded-2xl">
          <span class="text-slate-400 font-medium block">Attending Physician</span>
          <span class="text-slate-900 font-bold mt-1 block text-sm" id="vDoctor">Dr. Michael Reyes, MD</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">Internal Medicine</span>
        </div>

        <div class="p-4 bg-white border border-slate-200/80 rounded-2xl">
          <span class="text-slate-400 font-medium block">Request Date & Priority</span>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-slate-900 font-semibold" id="vRequestDate">28 Aug 2026, 08:15 AM</span>
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold" id="vPriorityBadge">High</span>
          </div>
        </div>

        <div class="p-4 bg-white border border-slate-200/80 rounded-2xl">
          <span class="text-slate-400 font-medium block">Current Status</span>
          <div class="flex items-center gap-2 mt-1">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" id="vStatusBadge">Processing</span>
            <span class="text-slate-400 font-mono text-[11px]" id="vSampleId">SMP-2026-00124</span>
          </div>
        </div>
      </div>

      <!-- Clinical Notes -->
      <div class="p-4 bg-amber-50/60 border border-amber-200/70 rounded-2xl">
        <span class="text-[11px] font-black uppercase tracking-wider text-amber-900 block mb-1">Clinical Notes / Indication</span>
        <p class="text-xs text-amber-900 leading-relaxed" id="vClinicalNotes">
          Patient febrile T: 38.6°C. Monitor leukocytosis and left shift.
        </p>
      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
      <button onclick="closeModal('viewRequestModal')" class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
        Close
      </button>

      <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
        <button onclick="acceptRequestPrompt()" id="vAcceptBtn" class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition shadow-sm shadow-emerald-600/20 active:scale-95">
          <i data-lucide="check-circle" class="w-4 h-4"></i>
          <span>Accept Request</span>
        </button>

        <button onclick="openUpdateRequestStatusModal()" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition shadow-sm shadow-indigo-600/20 active:scale-95">
          <i data-lucide="refresh-cw" class="w-4 h-4"></i>
          <span>Update Status</span>
        </button>
      </div>
    </div>
  </div>
</div>


<!-- 2. UPDATE LABORATORY TEST REQUEST STATUS MODAL -->
<div id="updateRequestStatusModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-md p-6 sm:p-7 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="text-base font-black text-slate-900 font-display flex items-center gap-2">
        <i data-lucide="refresh-cw" class="w-4 h-4 text-indigo-600"></i>
        <span>Update Request Status</span>
      </h3>
      <button onclick="closeModal('updateRequestStatusModal')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <form onsubmit="handleUpdateRequestStatus(event)" class="py-4 space-y-4 text-xs">
      <input type="hidden" id="uReqIdHidden" value="LAB-2026-001">
      
      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Selected Request</label>
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex justify-between items-center">
          <span class="font-mono font-bold text-slate-900" id="uReqIdText">LAB-2026-001</span>
          <span class="text-slate-600 font-medium" id="uReqPatientText">Juan Dela Cruz</span>
        </div>
      </div>

      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">New Laboratory Status</label>
        <select id="uReqStatusSelect" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
          <option value="Pending">Pending (Waiting for Specimen)</option>
          <option value="Received">Received (Specimen in Laboratory)</option>
          <option value="Processing" selected>Processing (In Analyzer/Test Run)</option>
          <option value="Completed">Completed (Results Verified)</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Technologist Remarks (Optional)</label>
        <textarea id="uReqRemarks" rows="2" placeholder="e.g. Specimen loaded to Sysmex XN-1000..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"></textarea>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
        <button type="button" onclick="closeModal('updateRequestStatusModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-sm shadow-indigo-600/20">
          Save Status
        </button>
      </div>
    </form>
  </div>
</div>


<!-- 3. CREATE SAMPLE COLLECTION RECORD MODAL -->
<div id="createSampleModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
          <i data-lucide="test-tube" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-lg font-black text-slate-900 font-display">Create Sample Collection Record</h3>
          <p class="text-xs text-slate-400 font-medium">Log specimen intake, barcoding, and initial quality condition</p>
        </div>
      </div>
      <button onclick="closeModal('createSampleModal')" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form onsubmit="handleCreateSample(event)" class="py-6 space-y-4 text-xs">
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Sample ID (Auto-Generated)</label>
          <input type="text" id="csSampleId" value="SMP-2026-<?= rand(10000, 99999) ?>" readonly class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-indigo-700 outline-none">
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Associated Lab Request</label>
          <select id="csRequestId" onchange="syncSamplePatientFromRequest(this.value)" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 outline-none">
            <?php foreach ($requests as $req): ?>
              <option value="<?= e($req['request_id']) ?>" data-patient="<?= e($req['patient_name']) ?>" data-test="<?= e($req['test']) ?>">
                <?= e($req['request_id']) ?> — <?= e($req['patient_name']) ?> (<?= e($req['test']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Patient Name</label>
          <input type="text" id="csPatientName" value="Juan Dela Cruz" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 outline-none">
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Sample Type</label>
          <select id="csSampleType" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 outline-none">
            <option value="Whole Blood (EDTA - Lavender Top)">Whole Blood (EDTA - Lavender Top)</option>
            <option value="Serum (SST - Gold Top)" selected>Serum (SST - Gold Top)</option>
            <option value="Citrated Plasma (Light Blue Top)">Citrated Plasma (Light Blue Top)</option>
            <option value="Clean-catch Midstream Urine">Clean-catch Midstream Urine</option>
            <option value="Stool / Fecal Specimen">Stool / Fecal Specimen</option>
            <option value="Sputum Specimen">Sputum Specimen</option>
            <option value="Nasopharyngeal Swab (VTM)">Nasopharyngeal Swab (VTM)</option>
            <option value="Other Specimen">Other Specimen</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Collection Date</label>
          <input type="date" id="csDate" value="<?= date('Y-m-d') ?>" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none">
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Collection Time</label>
          <input type="time" id="csTime" value="<?= date('H:i') ?>" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none">
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Collected By</label>
          <input type="text" id="csCollectedBy" value="Nurse Maria Santos, RN" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Sample Quality Condition</label>
          <select id="csCondition" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 outline-none">
            <option value="Acceptable (Optimal Quality)" selected>Acceptable (Optimal Quality)</option>
            <option value="Rejected (Grossly Hemolyzed)">Rejected (Grossly Hemolyzed)</option>
            <option value="Rejected (Lipemic / Clotted)">Rejected (Lipemic / Clotted)</option>
            <option value="Insufficient Volume (QNS)">Insufficient Volume (QNS)</option>
            <option value="Contaminated / Unlabeled">Contaminated / Unlabeled</option>
          </select>
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Rack / Storage Location</label>
          <input type="text" id="csRackLocation" value="RACK-HEM-B02" placeholder="e.g. RACK-CHEM-A01" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-mono text-slate-800 outline-none">
        </div>
      </div>

      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Internal Technologist Notes</label>
        <textarea id="csNotes" rows="2" placeholder="Add specimen intake details, barcode scan remarks, or handling notes..." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-slate-800 outline-none"></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="closeModal('createSampleModal')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition shadow-sm shadow-cyan-600/20 active:scale-95 flex items-center gap-1.5">
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Save Sample Record</span>
        </button>
      </div>
    </form>
  </div>
</div>


<!-- 4. UPDATE SAMPLE PROCESSING STATUS & TIMELINE MODAL -->
<div id="updateSampleStatusModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-lg p-6 sm:p-7 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="text-base font-black text-slate-900 font-display flex items-center gap-2">
        <i data-lucide="git-commit" class="w-4 h-4 text-cyan-600"></i>
        <span>Update Sample Processing Status</span>
      </h3>
      <button onclick="closeModal('updateSampleStatusModal')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <form onsubmit="handleUpdateSampleStatus(event)" class="py-4 space-y-5 text-xs">
      <input type="hidden" id="usSampleIdHidden" value="SMP-2026-00124">

      <!-- Sample Summary Header -->
      <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
        <div>
          <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wider">Sample ID</span>
          <span class="text-slate-900 font-mono font-bold text-sm" id="usSampleIdText">SMP-2026-00124</span>
        </div>
        <div class="text-right">
          <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wider">Patient</span>
          <span class="text-slate-900 font-bold" id="usPatientNameText">Juan Dela Cruz</span>
        </div>
      </div>

      <!-- Dynamic Timeline Preview of Progression -->
      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-2">Processing Flow Progression</label>
        <div class="grid grid-cols-5 gap-1 text-center font-bold text-[9px]">
          <div class="p-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">1. Collected</div>
          <div class="p-2 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">2. Received</div>
          <div class="p-2 rounded-lg bg-indigo-100 text-indigo-800 border border-indigo-300">3. Processing</div>
          <div class="p-2 rounded-lg bg-purple-50 text-purple-700 border border-purple-200">4. Verify</div>
          <div class="p-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">5. Done</div>
        </div>
      </div>

      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Select New Processing Stage</label>
        <select id="usStageSelect" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-800 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 outline-none">
          <option value="Collected">Collected (Phlebotomy Complete)</option>
          <option value="Received">Received (In Laboratory Triage)</option>
          <option value="Processing" selected>Processing (In Automated Analyzer)</option>
          <option value="For Verification">For Verification (Awaiting Pathologist)</option>
          <option value="Completed">Completed (Released to Patient/Physician)</option>
          <option value="Rejected">Rejected (Recollection Required)</option>
        </select>
      </div>

      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Technologist Stage Notes</label>
        <input type="text" id="usStageNotes" value="Sample loaded on Sysmex XN-1000 hematology analyzer." placeholder="e.g. Centrifuged at 3000 RPM, optical scan normal..." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-slate-800 outline-none">
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
        <button type="button" onclick="closeModal('updateSampleStatusModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition shadow-sm shadow-cyan-600/20">
          Update Progress
        </button>
      </div>
    </form>
  </div>
</div>


<!-- 5. SAMPLE TRACKING HISTORY DRAWER / MODAL -->
<div id="viewSampleHistoryModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-700">
          <i data-lucide="history" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[11px] font-bold text-slate-400 font-mono" id="shSampleId">SMP-2026-00124</span>
          <h3 class="text-base font-black text-slate-900 font-display">Sample Audit & Custody History</h3>
        </div>
      </div>
      <button onclick="closeModal('viewSampleHistoryModal')" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Timeline Entries -->
    <div class="py-6 space-y-4 text-xs">
      <div class="relative pl-6 border-l-2 border-indigo-200 space-y-5">
        
        <div class="relative">
          <span class="absolute -left-[31px] top-0.5 w-3.5 h-3.5 rounded-full bg-indigo-600 border-2 border-white"></span>
          <div class="flex items-center justify-between">
            <span class="font-bold text-slate-900 text-sm">Processing in Analyzer</span>
            <span class="text-slate-400 text-[11px]">Today, 08:40 AM</span>
          </div>
          <p class="text-slate-500 mt-1">Sample barcoded and placed into automated carousel slot #04.</p>
          <span class="text-[10px] font-mono text-indigo-600 font-bold block mt-1">Updated By: Robert Santos, RMT</span>
        </div>

        <div class="relative">
          <span class="absolute -left-[31px] top-0.5 w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white"></span>
          <div class="flex items-center justify-between">
            <span class="font-bold text-slate-900">Received at Lab Triage</span>
            <span class="text-slate-400 text-[11px]">Today, 08:32 AM</span>
          </div>
          <p class="text-slate-500 mt-1">Quality inspection verified (No hemolysis, volume 2.0 mL).</p>
          <span class="text-[10px] font-mono text-slate-500 block mt-1">Updated By: Robert Santos, RMT</span>
        </div>

        <div class="relative">
          <span class="absolute -left-[31px] top-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white"></span>
          <div class="flex items-center justify-between">
            <span class="font-bold text-slate-900">Specimen Phlebotomy Collected</span>
            <span class="text-slate-400 text-[11px]">Today, 08:20 AM</span>
          </div>
          <p class="text-slate-500 mt-1">Venipuncture performed on patient right antecubital vein.</p>
          <span class="text-[10px] font-mono text-slate-500 block mt-1">Updated By: Nurse Maria Santos, RN</span>
        </div>
      </div>
    </div>

    <div class="pt-4 border-t border-slate-100 text-right">
      <button onclick="closeModal('viewSampleHistoryModal')" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-xs">
        Close History
      </button>
    </div>
  </div>
</div>


<!-- 6. CREATE LABORATORY TEST RESULT (DYNAMIC FORM BUILDER) -->
<div id="createResultModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
          <i data-lucide="file-check-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-lg font-black text-slate-900 font-display">Record Laboratory Test Result</h3>
          <p class="text-xs text-slate-400 font-medium">Input numerical parameters with automated reference range flags</p>
        </div>
      </div>
      <button onclick="closeModal('createResultModal')" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form onsubmit="handleSaveTestResult(event)" class="py-6 space-y-6 text-xs">
      
      <!-- Selection Bar: Request, Patient & Test -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 border border-slate-200/80 rounded-2xl p-4">
        <div>
          <label class="block text-slate-400 font-bold uppercase tracking-wider text-[10px] mb-1">Laboratory Request</label>
          <select id="crRequestId" onchange="handleResultRequestChange(this.value)" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-bold text-slate-900 outline-none">
            <?php foreach ($requests as $req): ?>
              <option value="<?= e($req['request_id']) ?>" data-patient="<?= e($req['patient_name']) ?>" data-patientid="<?= e($req['patient_id']) ?>" data-test="<?= e($req['test']) ?>" data-sample="<?= e($req['sample_id']) ?>" data-doctor="<?= e($req['doctor']) ?>">
                <?= e($req['request_id']) ?> — <?= e($req['patient_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-slate-400 font-bold uppercase tracking-wider text-[10px] mb-1">Patient Name & Sample ID</label>
          <div class="px-3 py-2 bg-white border border-slate-200 rounded-xl">
            <span class="font-bold text-slate-900 block truncate" id="crPatientNameText">Juan Dela Cruz</span>
            <span class="text-[10px] font-mono text-indigo-600 font-bold" id="crSampleIdText">SMP-2026-00124</span>
          </div>
        </div>

        <div>
          <label class="block text-slate-400 font-bold uppercase tracking-wider text-[10px] mb-1">Test Type Profile</label>
          <select id="crTestType" onchange="renderDynamicResultFields(this.value)" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-bold text-indigo-700 outline-none">
            <option value="CBC" selected>Complete Blood Count (CBC)</option>
            <option value="FBS">Fasting Blood Sugar (FBS)</option>
            <option value="Lipid">Lipid Profile Panel</option>
            <option value="LFT">Liver Function Test (LFT)</option>
            <option value="Renal">Kidney Function Panel</option>
            <option value="Urinalysis">Routine Urinalysis</option>
          </select>
        </div>
      </div>

      <!-- Dynamic Test Parameters Grid with Live Auto-Flagging -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <h4 class="text-xs font-black uppercase tracking-wider text-slate-700">Test Parameters & Reference Range Evaluation</h4>
          <span class="text-[11px] text-slate-400">Values are evaluated automatically</span>
        </div>

        <div id="dynamicParametersContainer" class="space-y-2.5">
          <!-- Populated dynamically via JS renderDynamicResultFields() -->
        </div>
      </div>

      <!-- Overall Clinical Interpretation -->
      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Medical Technologist Clinical Interpretation</label>
        <textarea id="crInterpretation" rows="2" placeholder="e.g. Mild neutrophilic leukocytosis noted. No abnormal blast cells seen on peripheral smear..." class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
      </div>

      <!-- Attesting Technologist Sign-off Block -->
      <div class="p-4 bg-emerald-50/60 border border-emerald-200/80 rounded-2xl flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="<?= e($currentUser['avatar']) ?>" class="w-10 h-10 rounded-xl object-cover border border-emerald-300">
          <div>
            <span class="text-slate-900 font-bold block"><?= e($currentUser['name']) ?></span>
            <span class="text-[10px] text-emerald-800 font-mono font-semibold">Registered Medical Technologist • <?= e($currentUser['license_no']) ?></span>
          </div>
        </div>
        <div class="text-right">
          <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Verification Mode</span>
          <span class="px-2.5 py-0.5 text-[11px] font-bold bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">Electronic Sign-Off</span>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="closeModal('createResultModal')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm shadow-emerald-600/20 active:scale-95 flex items-center gap-1.5">
          <i data-lucide="save" class="w-4 h-4"></i>
          <span>Save & Authorize Result</span>
        </button>
      </div>
    </form>
  </div>
</div>


<!-- 7. VIEW LABORATORY TEST RESULT MODAL -->
<div id="viewResultModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
          <i data-lucide="file-text" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono" id="vrResultId">RES-2026-001</span>
          <h3 class="text-lg font-black text-slate-900 font-display">Laboratory Diagnostic Result</h3>
        </div>
      </div>
      <button onclick="closeModal('viewResultModal')" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <div class="py-6 space-y-6 text-xs">
      
      <!-- Patient & Test Meta -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
        <div>
          <span class="text-slate-400 font-medium block">Patient Name</span>
          <span class="text-slate-900 font-bold text-sm mt-0.5 block" id="vrPatientName">Juan Dela Cruz</span>
        </div>
        <div>
          <span class="text-slate-400 font-medium block">Patient ID / Age</span>
          <span class="text-slate-900 font-semibold mt-0.5 block" id="vrPatientIdAge">P-2026-0001 (45 M)</span>
        </div>
        <div>
          <span class="text-slate-400 font-medium block">Attending Doctor</span>
          <span class="text-slate-900 font-semibold mt-0.5 block" id="vrDoctor">Dr. Michael Reyes</span>
        </div>
        <div>
          <span class="text-slate-400 font-medium block">Result Status</span>
          <span class="px-2.5 py-0.5 rounded-full font-bold inline-block mt-0.5 bg-purple-50 text-purple-700 border border-purple-200" id="vrStatus">For Verification</span>
        </div>
      </div>

      <!-- Result Table -->
      <div>
        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
          <table class="w-full text-left">
            <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
              <tr>
                <th class="py-2.5 px-4">Parameter</th>
                <th class="py-2.5 px-4">Result Value</th>
                <th class="py-2.5 px-4">Unit</th>
                <th class="py-2.5 px-4">Reference Range</th>
                <th class="py-2.5 px-4 text-center">Flag</th>
              </tr>
            </thead>
            <tbody id="vrTableBody" class="divide-y divide-slate-100 text-slate-800">
              <!-- Rendered via JS -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Clinical Interpretation -->
      <div class="p-4 bg-indigo-50/60 border border-indigo-100 rounded-2xl">
        <span class="text-[11px] font-black uppercase tracking-wider text-indigo-900 block mb-1">Technologist Diagnostic Summary</span>
        <p class="text-slate-700 leading-relaxed" id="vrInterpretation">
          Significant neutrophilic leukocytosis noted consistent with acute bacterial inflammatory reaction.
        </p>
      </div>

      <!-- Signatures Block -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div class="p-4 border border-slate-200 rounded-2xl bg-white">
          <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Medical Technologist</span>
          <span class="text-xs font-bold text-slate-900 mt-1 block">Robert Santos, RMT</span>
          <span class="text-[10px] text-slate-500 font-mono">PRC License: 0084920</span>
        </div>
        <div class="p-4 border border-slate-200 rounded-2xl bg-white">
          <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Consultant Pathologist</span>
          <span class="text-xs font-bold text-slate-900 mt-1 block">Dr. Vicente Gomez, MD, FPSP</span>
          <span class="text-[10px] text-slate-500 font-mono">PRC License: 0041289</span>
        </div>
      </div>
    </div>

    <!-- Modal Actions -->
    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
      <button onclick="closeModal('viewResultModal')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-xs">
        Close
      </button>

      <div class="flex items-center gap-2">
        <button onclick="openUpdateResultModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-xs flex items-center gap-1.5">
          <i data-lucide="edit-3" class="w-4 h-4"></i>
          <span>Update Result</span>
        </button>

        <button onclick="openGenerateReportModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition text-xs flex items-center gap-1.5 shadow-sm shadow-indigo-600/20 active:scale-95">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span>Generate Report</span>
        </button>
      </div>
    </div>
  </div>
</div>


<!-- 8. UPDATE LABORATORY TEST RESULT (WITH AUDIT TRAIL) -->
<div id="updateResultModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-lg p-6 sm:p-7 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="text-base font-black text-slate-900 font-display flex items-center gap-2">
        <i data-lucide="edit-3" class="w-4 h-4 text-indigo-600"></i>
        <span>Update Test Result (Audit Logged)</span>
      </h3>
      <button onclick="closeModal('updateResultModal')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <form onsubmit="handleUpdateTestResult(event)" class="py-4 space-y-4 text-xs">
      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">Parameter to Amend</label>
        <select id="urParamName" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl font-bold text-slate-800 outline-none">
          <option value="WBC Count">WBC Count (Currently: 14.8 x10⁹/L)</option>
          <option value="Hemoglobin">Hemoglobin (Currently: 14.2 g/dL)</option>
          <option value="Platelet Count">Platelet Count (Currently: 245 x10⁹/L)</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">Current Value</label>
          <input type="text" id="urCurrentVal" value="14.8" readonly class="w-full px-3.5 py-2 bg-slate-100 border border-slate-200 rounded-xl font-mono text-slate-600 outline-none">
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">New Verified Value</label>
          <input type="text" id="urNewVal" value="15.1" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl font-mono font-bold text-indigo-700 outline-none">
        </div>
      </div>

      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">Clinical Reason for Amendment *</label>
        <textarea id="urReason" rows="2" required placeholder="e.g. Rerun confirmed on second Sysmex analyzer channel..." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-slate-800 outline-none"></textarea>
      </div>

      <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-[11px]">
        <strong>Audit Trail:</strong> Any value amendments will be recorded with timestamp and technologist PRC credential.
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
        <button type="button" onclick="closeModal('updateResultModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-sm">
          Save Amendment
        </button>
      </div>
    </form>
  </div>
</div>


<!-- 9. GENERATE & PRINT LABORATORY REPORT MODAL -->
<div id="generateReportModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-4xl max-h-[95vh] overflow-y-auto p-6 sm:p-10 animate-in fade-in zoom-in-95 duration-200 printable-report-container">
    
    <!-- Printable Formal Medical Header -->
    <div class="flex items-center justify-between pb-6 border-b-2 border-slate-900">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-md">
          H
        </div>
        <div>
          <h1 class="text-xl font-black tracking-tight text-slate-900 font-display">TUPI MUNICIPAL HOSPITAL</h1>
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Department of Clinical Pathology & Diagnostic Laboratories</p>
          <p class="text-[11px] text-slate-400">108 Medical Center Boulevard, Metro Manila • ISO 15189 Accredited</p>
        </div>
      </div>
      
      <div class="text-right">
        <span class="inline-block px-3 py-1 bg-slate-900 text-white font-mono text-xs font-bold rounded-lg uppercase tracking-wider">OFFICIAL LAB REPORT</span>
        <p class="text-[11px] text-slate-500 font-mono mt-1">Ref: LAB-REP-2026-00124</p>
      </div>
    </div>

    <!-- Patient, Sample & Doctor Details Box -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-5 border-b border-slate-200 text-xs">
      <div>
        <span class="text-slate-400 font-bold uppercase text-[9px] block">Patient Name</span>
        <span class="text-slate-900 font-black text-sm block" id="repPatientName">Juan Dela Cruz</span>
        <span class="text-slate-500 font-mono text-[10px]" id="repPatientId">ID: P-2026-0001</span>
      </div>
      <div>
        <span class="text-slate-400 font-bold uppercase text-[9px] block">Age / Gender / Blood</span>
        <span class="text-slate-900 font-bold block" id="repAgeGender">45 yrs • Male • O+</span>
        <span class="text-slate-500 text-[10px]" id="repRoom">Room 204 (General Ward)</span>
      </div>
      <div>
        <span class="text-slate-400 font-bold uppercase text-[9px] block">Attending Physician</span>
        <span class="text-slate-900 font-bold block" id="repDoctor">Dr. Michael Reyes, MD</span>
        <span class="text-slate-500 text-[10px]">Internal Medicine</span>
      </div>
      <div>
        <span class="text-slate-400 font-bold uppercase text-[9px] block">Collection / Report Date</span>
        <span class="text-slate-900 font-bold block" id="repDate">28 Aug 2026, 09:10 AM</span>
        <span class="text-indigo-600 font-mono font-bold text-[10px]" id="repSampleId">SMP-2026-00124</span>
      </div>
    </div>

    <!-- Official Diagnostic Results Table -->
    <div class="py-6">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900" id="repTestTitle">COMPLETE BLOOD COUNT (CBC) WITH DIFFERENTIAL</h3>
        <span class="text-xs font-semibold text-slate-500">Specimen: Whole Blood (EDTA)</span>
      </div>

      <table class="w-full text-left text-xs border border-slate-300">
        <thead class="bg-slate-100 text-slate-800 font-bold uppercase text-[10px] border-b border-slate-300">
          <tr>
            <th class="py-2 px-3 border-r border-slate-200">Investigation / Parameter</th>
            <th class="py-2 px-3 border-r border-slate-200">Result</th>
            <th class="py-2 px-3 border-r border-slate-200">Unit</th>
            <th class="py-2 px-3 border-r border-slate-200">Biological Reference Range</th>
            <th class="py-2 px-3 text-center">Status Flag</th>
          </tr>
        </thead>
        <tbody id="repTableBody" class="divide-y divide-slate-200 text-slate-800">
          <!-- Injected via JS -->
        </tbody>
      </table>
    </div>

    <!-- Diagnostic Interpretation Box -->
    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs mb-8">
      <span class="font-bold text-slate-900 block mb-1 uppercase text-[10px] tracking-wider">Clinical Remarks & Pathology Impression:</span>
      <p class="text-slate-700 leading-relaxed" id="repInterpretation">
        Significant neutrophilic leukocytosis noted consistent with acute bacterial inflammatory reaction. Platelet and red cell indices are within normal physiological thresholds.
      </p>
    </div>

    <!-- Signatures & Verification Seal -->
    <div class="grid grid-cols-2 gap-8 pt-6 border-t-2 border-slate-200 text-xs">
      <div class="text-center">
        <div class="h-12 flex items-end justify-center font-serif italic text-slate-700 text-base">
          Robert Santos, RMT
        </div>
        <div class="border-t border-slate-900 pt-1">
          <span class="font-bold text-slate-900 block">ROBERT SANTOS, RMT</span>
          <span class="text-[10px] text-slate-500 block">Medical Technologist • PRC Lic. No. 0084920</span>
        </div>
      </div>

      <div class="text-center">
        <div class="h-12 flex items-end justify-center font-serif italic text-slate-700 text-base">
          Vicente Gomez, MD
        </div>
        <div class="border-t border-slate-900 pt-1">
          <span class="font-bold text-slate-900 block">VICENTE GOMEZ, MD, FPSP</span>
          <span class="text-[10px] text-slate-500 block">Consultant Pathologist • PRC Lic. No. 0041289</span>
        </div>
      </div>
    </div>

    <!-- Action Bar (Hidden in Print) -->
    <div class="pt-8 mt-6 border-t border-slate-100 flex items-center justify-between no-print">
      <button onclick="closeModal('generateReportModal')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-xs">
        Close Preview
      </button>

      <div class="flex items-center gap-3">
        <button onclick="showToast('Diagnostic report exported to PDF', 'success')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl transition text-xs flex items-center gap-1.5">
          <i data-lucide="download" class="w-4 h-4 text-indigo-600"></i>
          <span>Export PDF</span>
        </button>

        <button onclick="triggerPrintLabReport()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition text-xs flex items-center gap-1.5 shadow-md shadow-indigo-600/20 active:scale-95">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span>Print Laboratory Report</span>
        </button>
      </div>
    </div>
  </div>
</div>


<!-- 10. CREATE / EDIT TEST CATALOG MODAL -->
<div id="catalogModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <h3 class="text-base font-black text-slate-900 font-display flex items-center gap-2" id="catalogModalTitle">
        <i data-lucide="book-plus" class="w-5 h-5 text-indigo-600"></i>
        <span>Create Test Catalog Entry</span>
      </h3>
      <button onclick="closeModal('catalogModal')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <form onsubmit="handleSaveCatalogEntry(event)" class="py-5 space-y-4 text-xs">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Test ID</label>
          <input type="text" id="catTestId" value="TST-013" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-slate-800 outline-none">
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Test Name</label>
          <input type="text" id="catTestName" placeholder="e.g. Serum Ferritin" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-900 outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Laboratory Section / Category</label>
          <select id="catCategory" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none">
            <option value="Hematology">Hematology</option>
            <option value="Clinical Chemistry" selected>Clinical Chemistry</option>
            <option value="Clinical Microscopy">Clinical Microscopy</option>
            <option value="Immunology / Serology">Immunology / Serology</option>
            <option value="Blood Bank">Blood Bank</option>
            <option value="Microbiology">Microbiology</option>
            <option value="Molecular Diagnostics">Molecular Diagnostics</option>
          </select>
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Specimen Type</label>
          <input type="text" id="catSpecimen" value="Serum (SST - Gold Top)" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Turnaround Time (TAT)</label>
          <input type="text" id="catTat" value="2 - 3 Hours" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none">
        </div>

        <div>
          <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Catalog Status</label>
          <select id="catStatus" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-800 outline-none">
            <option value="Active" selected>Active</option>
            <option value="Maintenance">Under Maintenance</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5">Clinical Description & Methodology</label>
        <textarea id="catDescription" rows="2" placeholder="Methodology description and clinical indications..." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-slate-800 outline-none"></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="closeModal('catalogModal')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
          Cancel
        </button>
        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-sm shadow-indigo-600/20 active:scale-95">
          Save Catalog Entry
        </button>
      </div>
    </form>
  </div>
</div>


<!-- 11. TECHNOLOGIST PROFILE MODAL -->
<div id="profileModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
  <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-md p-6 sm:p-7 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="text-base font-black text-slate-900 font-display">Medical Technologist Profile</h3>
      <button onclick="closeModal('profileModal')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <div class="py-5 text-center space-y-4 text-xs">
      <img src="<?= e($currentUser['avatar']) ?>" class="w-20 h-20 rounded-3xl object-cover mx-auto border-4 border-indigo-100 shadow-md">
      <div>
        <h4 class="text-base font-black text-slate-900"><?= e($currentUser['name']) ?></h4>
        <p class="text-indigo-600 font-bold"><?= e($currentUser['title']) ?></p>
        <span class="inline-block mt-1 px-3 py-1 bg-slate-100 text-slate-600 font-mono text-[11px] font-bold rounded-lg"><?= e($currentUser['license_no']) ?></span>
      </div>

      <div class="text-left bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2 text-slate-700">
        <div class="flex justify-between">
          <span class="text-slate-400 font-medium">Department:</span>
          <span class="font-bold"><?= e($currentUser['department']) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-400 font-medium">Active Shift:</span>
          <span class="font-bold text-emerald-600"><?= e($currentUser['shift']) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-400 font-medium">Email:</span>
          <span class="font-mono"><?= e($currentUser['email']) ?></span>
        </div>
      </div>
    </div>

    <div class="pt-3 border-t border-slate-100 text-center">
      <button onclick="closeModal('profileModal')" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-xs">
        Close Profile
      </button>
    </div>
  </div>
</div>

<script>
  // Helper to open/close modals
  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.remove('hidden');
      if (window.lucide) lucide.createIcons();
    }
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('hidden');
  }

  function openProfileModal() {
    openModal('profileModal');
    const dd = document.getElementById('profileDropdown');
    if (dd) dd.classList.add('hidden');
  }

  function openCreateSampleModal() {
    openModal('createSampleModal');
  }

  function openCreateResultModal(reqId, sampleId, patientName, testName) {
    if (reqId) {
      const sel = document.getElementById('crRequestId');
      if (sel) sel.value = reqId;
      if (patientName) {
        const pEl = document.getElementById('crPatientNameText');
        if (pEl) pEl.textContent = patientName;
      }
      if (sampleId) {
        const sEl = document.getElementById('crSampleIdText');
        if (sEl) sEl.textContent = sampleId;
      }
    }
    renderDynamicResultFields(document.getElementById('crTestType').value || 'CBC');
    openModal('createResultModal');
  }

  // Sync Create Sample patient name when request changes
  function syncSamplePatientFromRequest(reqId) {
    const sel = document.getElementById('csRequestId');
    const opt = sel.options[sel.selectedIndex];
    if (opt) {
      const patient = opt.getAttribute('data-patient');
      const pInput = document.getElementById('csPatientName');
      if (pInput && patient) pInput.value = patient;
    }
  }

  // Handle Result Request dropdown change
  function handleResultRequestChange(reqId) {
    const sel = document.getElementById('crRequestId');
    const opt = sel.options[sel.selectedIndex];
    if (opt) {
      const pName = opt.getAttribute('data-patient');
      const sId = opt.getAttribute('data-sample');
      document.getElementById('crPatientNameText').textContent = pName;
      document.getElementById('crSampleIdText').textContent = sId;
    }
  }

  // Render Dynamic Parameter Fields based on test category with auto-flag calculation
  const testParameterDefinitions = {
    'CBC': [
      { name: 'Hemoglobin', unit: 'g/dL', min: 13.0, max: 17.5, default: '14.2', rangeText: '13.0 – 17.5' },
      { name: 'Hematocrit', unit: '%', min: 40.0, max: 52.0, default: '42.5', rangeText: '40.0 – 52.0' },
      { name: 'WBC Count', unit: 'x10⁹/L', min: 4.5, max: 11.0, default: '14.8', rangeText: '4.5 – 11.0' },
      { name: 'RBC Count', unit: 'x10¹²/L', min: 4.5, max: 5.9, default: '4.85', rangeText: '4.5 – 5.9' },
      { name: 'Platelet Count', unit: 'x10⁹/L', min: 150.0, max: 450.0, default: '245', rangeText: '150 – 450' },
      { name: 'Segmenters / Neutrophils', unit: '%', min: 50.0, max: 70.0, default: '78', rangeText: '50 – 70' },
      { name: 'Lymphocytes', unit: '%', min: 20.0, max: 40.0, default: '16', rangeText: '20 – 40' },
      { name: 'Monocytes', unit: '%', min: 2.0, max: 8.0, default: '4', rangeText: '2 – 8' },
      { name: 'Eosinophils', unit: '%', min: 1.0, max: 4.0, default: '2', rangeText: '1 – 4' },
      { name: 'Basophils', unit: '%', min: 0.0, max: 1.0, default: '0', rangeText: '0 – 1' }
    ],
    'FBS': [
      { name: 'Fasting Blood Glucose', unit: 'mg/dL', min: 70.0, max: 100.0, default: '92', rangeText: '70 – 100' }
    ],
    'Lipid': [
      { name: 'Total Cholesterol', unit: 'mg/dL', min: 0.0, max: 200.0, default: '248', rangeText: '< 200' },
      { name: 'Triglycerides', unit: 'mg/dL', min: 0.0, max: 150.0, default: '210', rangeText: '< 150' },
      { name: 'HDL Cholesterol', unit: 'mg/dL', min: 40.0, max: 90.0, default: '36', rangeText: '> 40' },
      { name: 'LDL Cholesterol', unit: 'mg/dL', min: 0.0, max: 100.0, default: '170', rangeText: '< 100' }
    ],
    'LFT': [
      { name: 'SGPT / ALT', unit: 'U/L', min: 0.0, max: 45.0, default: '38', rangeText: '< 45' },
      { name: 'SGOT / AST', unit: 'U/L', min: 0.0, max: 40.0, default: '32', rangeText: '< 40' },
      { name: 'Total Bilirubin', unit: 'mg/dL', min: 0.2, max: 1.2, default: '0.8', rangeText: '0.2 – 1.2' },
      { name: 'Total Protein', unit: 'g/dL', min: 6.4, max: 8.3, default: '7.1', rangeText: '6.4 – 8.3' },
      { name: 'Serum Albumin', unit: 'g/dL', min: 3.5, max: 5.0, default: '4.2', rangeText: '3.5 – 5.0' }
    ],
    'Renal': [
      { name: 'Blood Urea Nitrogen (BUN)', unit: 'mg/dL', min: 7.0, max: 20.0, default: '16.5', rangeText: '7 – 20' },
      { name: 'Serum Creatinine', unit: 'mg/dL', min: 0.7, max: 1.3, default: '1.05', rangeText: '0.7 – 1.3' },
      { name: 'Serum Uric Acid', unit: 'mg/dL', min: 3.5, max: 7.2, default: '5.8', rangeText: '3.5 – 7.2' }
    ],
    'Urinalysis': [
      { name: 'Specific Gravity', unit: '—', min: 1.010, max: 1.030, default: '1.020', rangeText: '1.010 – 1.030' },
      { name: 'pH Level', unit: 'pH', min: 5.0, max: 7.5, default: '6.5', rangeText: '5.0 – 7.5' },
      { name: 'WBC (Pus Cells)', unit: '/hpf', min: 0.0, max: 5.0, default: '2', rangeText: '0 – 5' },
      { name: 'RBC (Erythrocytes)', unit: '/hpf', min: 0.0, max: 2.0, default: '1', rangeText: '0 – 2' }
    ]
  };

  function renderDynamicResultFields(testType) {
    const container = document.getElementById('dynamicParametersContainer');
    if (!container) return;

    const params = testParameterDefinitions[testType] || testParameterDefinitions['CBC'];
    container.innerHTML = '';

    params.forEach((p, idx) => {
      const row = document.createElement('div');
      row.className = 'grid grid-cols-12 gap-2 items-center p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl hover:border-slate-300 transition text-xs';
      
      row.innerHTML = `
        <div class="col-span-4 font-bold text-slate-800">${p.name}</div>
        <div class="col-span-3">
          <input type="number" step="any" value="${p.default}" oninput="evaluateLiveFlag(this, ${p.min}, ${p.max}, 'flag-${idx}')" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg font-mono font-bold text-slate-900 outline-none focus:border-indigo-500">
        </div>
        <div class="col-span-2 text-slate-500 font-medium">${p.unit}</div>
        <div class="col-span-2 text-slate-400 font-mono text-[11px]">${p.rangeText}</div>
        <div class="col-span-1 text-center">
          <span id="flag-${idx}" class="px-1.5 py-0.5 rounded text-[10px] font-bold ${calculateInitialFlagClass(parseFloat(p.default), p.min, p.max)}">${calculateInitialFlagText(parseFloat(p.default), p.min, p.max)}</span>
        </div>
      `;
      container.appendChild(row);
    });
  }

  function calculateInitialFlagText(val, min, max) {
    if (val < min) return 'LOW';
    if (val > max) return 'HIGH';
    return 'NORM';
  }

  function calculateInitialFlagClass(val, min, max) {
    if (val < min) return 'bg-blue-100 text-blue-800';
    if (val > max) return 'bg-amber-100 text-amber-800';
    return 'bg-emerald-100 text-emerald-800';
  }

  function evaluateLiveFlag(input, min, max, flagId) {
    const val = parseFloat(input.value);
    const flagEl = document.getElementById(flagId);
    if (!flagEl || isNaN(val)) return;

    if (val < min) {
      flagEl.textContent = 'LOW';
      flagEl.className = 'px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800';
    } else if (val > max) {
      flagEl.textContent = 'HIGH';
      flagEl.className = 'px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800';
    } else {
      flagEl.textContent = 'NORM';
      flagEl.className = 'px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800';
    }
  }

  // Interactive Form Submissions & Handlers
  function handleUpdateRequestStatus(e) {
    e.preventDefault();
    const reqId = document.getElementById('uReqIdHidden').value;
    const newStatus = document.getElementById('uReqStatusSelect').value;
    
    // Update live table badge if on requests view
    const badge = document.getElementById(`req-status-badge-${reqId}`);
    if (badge) {
      badge.textContent = newStatus;
      badge.className = `px-2.5 py-0.5 rounded-full text-xs font-bold ${
        newStatus === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
        newStatus === 'Processing' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' :
        newStatus === 'Received' ? 'bg-sky-50 text-sky-700 border border-sky-200' :
        newStatus === 'Cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-200' :
        'bg-amber-50 text-amber-700 border border-amber-200'
      }`;
    }

    closeModal('updateRequestStatusModal');
    closeModal('viewRequestModal');
    showToast(`Request ${reqId} status updated to "${newStatus}"`, 'success');
  }

  function handleCreateSample(e) {
    e.preventDefault();
    const sampleId = document.getElementById('csSampleId').value;
    const patientName = document.getElementById('csPatientName').value;
    
    closeModal('createSampleModal');
    showToast(`Sample record created for ${patientName} (${sampleId})`, 'success');
  }

  function handleUpdateSampleStatus(e) {
    e.preventDefault();
    const sampleId = document.getElementById('usSampleIdHidden').value;
    const stage = document.getElementById('usStageSelect').value;

    closeModal('updateSampleStatusModal');
    showToast(`Sample ${sampleId} progressed to "${stage}"`, 'success');
  }

  function handleSaveTestResult(e) {
    e.preventDefault();
    const patient = document.getElementById('crPatientNameText').textContent;
    const reqId = document.getElementById('crRequestId').value;

    closeModal('createResultModal');
    showToast(`Laboratory result for ${patient} (${reqId}) authorized and saved!`, 'success');
  }

  function handleUpdateTestResult(e) {
    e.preventDefault();
    const param = document.getElementById('urParamName').value;
    const newVal = document.getElementById('urNewVal').value;

    closeModal('updateResultModal');
    showToast(`Amended ${param} to ${newVal}. Audit logged to laboratory registry.`, 'success');
  }

  function handleSaveCatalogEntry(e) {
    e.preventDefault();
    const testName = document.getElementById('catTestName').value;
    closeModal('catalogModal');
    showToast(`Test catalog entry for "${testName}" saved successfully!`, 'success');
  }

  function acceptRequestPrompt() {
    closeModal('viewRequestModal');
    showToast('Laboratory request accepted. Sample collection ticket queued.', 'success');
  }

  function openUpdateRequestStatusModal() {
    openModal('updateRequestStatusModal');
  }

  function openUpdateResultModal() {
    openModal('updateResultModal');
  }

  function openGenerateReportModal() {
    // Populate report modal with CBC demo
    const repTable = document.getElementById('repTableBody');
    if (repTable) {
      repTable.innerHTML = `
        <tr><td class="py-2 px-3 font-semibold border-r border-slate-200">Hemoglobin</td><td class="py-2 px-3 font-bold border-r border-slate-200">14.2</td><td class="py-2 px-3 border-r border-slate-200">g/dL</td><td class="py-2 px-3 border-r border-slate-200">13.0 – 17.5</td><td class="py-2 px-3 text-center text-emerald-700 font-bold">NORMAL</td></tr>
        <tr class="bg-slate-50"><td class="py-2 px-3 font-semibold border-r border-slate-200">Hematocrit</td><td class="py-2 px-3 font-bold border-r border-slate-200">42.5</td><td class="py-2 px-3 border-r border-slate-200">%</td><td class="py-2 px-3 border-r border-slate-200">40.0 – 52.0</td><td class="py-2 px-3 text-center text-emerald-700 font-bold">NORMAL</td></tr>
        <tr class="bg-amber-50/70"><td class="py-2 px-3 font-bold text-amber-900 border-r border-slate-200">WBC Count</td><td class="py-2 px-3 font-black text-amber-900 border-r border-slate-200">14.8</td><td class="py-2 px-3 border-r border-slate-200">x10⁹/L</td><td class="py-2 px-3 border-r border-slate-200">4.5 – 11.0</td><td class="py-2 px-3 text-center text-amber-800 font-black">HIGH ▲</td></tr>
        <tr><td class="py-2 px-3 font-semibold border-r border-slate-200">RBC Count</td><td class="py-2 px-3 font-bold border-r border-slate-200">4.85</td><td class="py-2 px-3 border-r border-slate-200">x10¹²/L</td><td class="py-2 px-3 border-r border-slate-200">4.5 – 5.9</td><td class="py-2 px-3 text-center text-emerald-700 font-bold">NORMAL</td></tr>
        <tr class="bg-slate-50"><td class="py-2 px-3 font-semibold border-r border-slate-200">Platelet Count</td><td class="py-2 px-3 font-bold border-r border-slate-200">245</td><td class="py-2 px-3 border-r border-slate-200">x10⁹/L</td><td class="py-2 px-3 border-r border-slate-200">150 – 450</td><td class="py-2 px-3 text-center text-emerald-700 font-bold">NORMAL</td></tr>
        <tr class="bg-amber-50/70"><td class="py-2 px-3 font-bold text-amber-900 border-r border-slate-200">Segmenters / Neutrophils</td><td class="py-2 px-3 font-black text-amber-900 border-r border-slate-200">78</td><td class="py-2 px-3 border-r border-slate-200">%</td><td class="py-2 px-3 border-r border-slate-200">50 – 70</td><td class="py-2 px-3 text-center text-amber-800 font-black">HIGH ▲</td></tr>
        <tr class="bg-blue-50/70"><td class="py-2 px-3 font-bold text-blue-900 border-r border-slate-200">Lymphocytes</td><td class="py-2 px-3 font-black text-blue-900 border-r border-slate-200">16</td><td class="py-2 px-3 border-r border-slate-200">%</td><td class="py-2 px-3 border-r border-slate-200">20 – 40</td><td class="py-2 px-3 text-center text-blue-800 font-black">LOW ▼</td></tr>
      `;
    }
    openModal('generateReportModal');
  }

  // Open Detailed View Request
  function openViewRequestModal(reqId, patientName, patientId, ageGender, doctor, testName, priority, date, status, sampleId, notes, category) {
    document.getElementById('vReqId').textContent = reqId;
    document.getElementById('vPatientName').textContent = patientName;
    document.getElementById('vPatientId').textContent = patientId;
    document.getElementById('vAgeGender').textContent = ageGender;
    document.getElementById('vDoctor').textContent = doctor;
    document.getElementById('vTestName').textContent = testName;
    document.getElementById('vCategory').textContent = category || 'Clinical Diagnostic Section';
    document.getElementById('vRequestDate').textContent = date;
    document.getElementById('vSampleId').textContent = sampleId;
    document.getElementById('vClinicalNotes').textContent = notes || 'No special clinical notes provided.';
    
    // Status text for update modal
    document.getElementById('uReqIdHidden').value = reqId;
    document.getElementById('uReqIdText').textContent = reqId;
    document.getElementById('uReqPatientText').textContent = patientName;

    openModal('viewRequestModal');
  }

  // Open Sample Status modal
  function openUpdateSampleStatusModal(sampleId, patientName) {
    document.getElementById('usSampleIdHidden').value = sampleId;
    document.getElementById('usSampleIdText').textContent = sampleId;
    document.getElementById('usPatientNameText').textContent = patientName;
    openModal('updateSampleStatusModal');
  }

  // Open Sample History Modal
  function openViewSampleHistoryModal(sampleId) {
    document.getElementById('shSampleId').textContent = sampleId;
    openModal('viewSampleHistoryModal');
  }

  // Open View Result Modal
  function openViewResultModal(resId, reqId, patientName, patientIdAge, doctor, status, interp) {
    document.getElementById('vrResultId').textContent = resId;
    document.getElementById('vrPatientName').textContent = patientName;
    document.getElementById('vrPatientIdAge').textContent = patientIdAge;
    document.getElementById('vrDoctor').textContent = doctor;
    document.getElementById('vrStatus').textContent = status;
    document.getElementById('vrInterpretation').textContent = interp;

    const tbody = document.getElementById('vrTableBody');
    if (tbody) {
      tbody.innerHTML = `
        <tr><td class="py-2.5 px-4 font-semibold">Hemoglobin</td><td class="py-2.5 px-4 font-bold">14.2</td><td class="py-2.5 px-4 text-slate-500">g/dL</td><td class="py-2.5 px-4 text-slate-400 font-mono">13.0 – 17.5</td><td class="py-2.5 px-4 text-center"><span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded">Normal</span></td></tr>
        <tr class="bg-slate-50"><td class="py-2.5 px-4 font-semibold">Hematocrit</td><td class="py-2.5 px-4 font-bold">42.5</td><td class="py-2.5 px-4 text-slate-500">%</td><td class="py-2.5 px-4 text-slate-400 font-mono">40.0 – 52.0</td><td class="py-2.5 px-4 text-center"><span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded">Normal</span></td></tr>
        <tr class="bg-amber-50/60"><td class="py-2.5 px-4 font-bold text-amber-900">WBC Count</td><td class="py-2.5 px-4 font-black text-amber-900">14.8</td><td class="py-2.5 px-4 text-slate-500">x10⁹/L</td><td class="py-2.5 px-4 text-slate-400 font-mono">4.5 – 11.0</td><td class="py-2.5 px-4 text-center"><span class="px-2 py-0.5 text-[10px] font-black bg-amber-200 text-amber-900 rounded">HIGH</span></td></tr>
        <tr><td class="py-2.5 px-4 font-semibold">RBC Count</td><td class="py-2.5 px-4 font-bold">4.85</td><td class="py-2.5 px-4 text-slate-500">x10¹²/L</td><td class="py-2.5 px-4 text-slate-400 font-mono">4.5 – 5.9</td><td class="py-2.5 px-4 text-center"><span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded">Normal</span></td></tr>
        <tr class="bg-slate-50"><td class="py-2.5 px-4 font-semibold">Platelet Count</td><td class="py-2.5 px-4 font-bold">245</td><td class="py-2.5 px-4 text-slate-500">x10⁹/L</td><td class="py-2.5 px-4 text-slate-400 font-mono">150 – 450</td><td class="py-2.5 px-4 text-center"><span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded">Normal</span></td></tr>
      `;
    }
    openModal('viewResultModal');
  }
</script>
