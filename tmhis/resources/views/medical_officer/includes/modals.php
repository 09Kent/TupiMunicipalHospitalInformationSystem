<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Modals and Dialog Components
 */
?>
<!-- 1. CREATE PATIENT RECORD MODAL -->
<div class="modal-backdrop" id="createPatientModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="user-plus" style="color:var(--primary);"></i>
        <span>CREATE PATIENT RECORD</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <form id="createPatientForm" onsubmit="window.submitCreatePatientRecord(event)">
      <div class="modal-body">
        <div class="form-grid">
          
          <!-- Personal Information -->
          <div class="form-section-header">PERSONAL INFORMATION</div>
          
          <div class="form-group col-4">
            <label class="form-label">First Name <span class="required">*</span></label>
            <input type="text" name="firstName" class="form-control" placeholder="e.g. Juan" required>
          </div>
          <div class="form-group col-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middleName" class="form-control" placeholder="e.g. Bautista">
          </div>
          <div class="form-group col-3">
            <label class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" name="lastName" class="form-control" placeholder="e.g. Dela Cruz" required>
          </div>
          <div class="form-group col-1">
            <label class="form-label">Suffix</label>
            <input type="text" name="suffix" class="form-control" placeholder="Jr.">
          </div>

          <div class="form-group col-4">
            <label class="form-label">Date of Birth <span class="required">*</span></label>
            <input type="date" name="dob" class="form-control" required>
          </div>
          <div class="form-group col-4">
            <label class="form-label">Gender <span class="required">*</span></label>
            <select name="gender" class="form-control" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="form-group col-4">
            <label class="form-label">Civil Status</label>
            <select name="civilStatus" class="form-control">
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Widowed">Widowed</option>
              <option value="Separated">Separated</option>
            </select>
          </div>

          <!-- Contact Information -->
          <div class="form-section-header">CONTACT INFORMATION</div>

          <div class="form-group col-12">
            <label class="form-label">Residential Address <span class="required">*</span></label>
            <input type="text" name="address" class="form-control" placeholder="Street, Barangay, City, Province" required>
          </div>
          <div class="form-group col-6">
            <label class="form-label">Contact Number <span class="required">*</span></label>
            <input type="tel" name="contact" class="form-control" placeholder="09XX-XXX-XXXX" required>
          </div>
          <div class="form-group col-6">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="patient@example.com">
          </div>

          <!-- Emergency Contact -->
          <div class="form-section-header">EMERGENCY CONTACT</div>

          <div class="form-group col-4">
            <label class="form-label">Emergency Contact Name</label>
            <input type="text" name="emergencyName" class="form-control" placeholder="e.g. Elena Dela Cruz">
          </div>
          <div class="form-group col-4">
            <label class="form-label">Relationship</label>
            <input type="text" name="emergencyRel" class="form-control" placeholder="e.g. Mother / Spouse">
          </div>
          <div class="form-group col-4">
            <label class="form-label">Emergency Contact Phone</label>
            <input type="tel" name="emergencyContact" class="form-control" placeholder="09XX-XXX-XXXX">
          </div>

          <!-- Registration Information -->
          <div class="form-section-header">REGISTRATION INFORMATION</div>

          <div class="form-group col-6">
            <label class="form-label">Registration Type <span class="required">*</span></label>
            <select name="registrationType" class="form-control" required>
              <option value="Inpatient Admission">Inpatient Admission</option>
              <option value="Outpatient Consultation">Outpatient Consultation</option>
              <option value="Emergency Room">Emergency Room</option>
              <option value="Executive Checkup">Executive Checkup</option>
            </select>
          </div>
          <div class="form-group col-6">
            <label class="form-label">Registration Officer</label>
            <input type="text" class="form-control" value="Mark Anthony Valenzuela, RMT (Role 3)" readonly>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn-primary">
          <i data-lucide="check"></i> Create Patient Record
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 2. VIEW PATIENT RECORD (RIGHT DRAWER) -->
<div class="drawer-backdrop" id="viewPatientDrawer">
  <div class="drawer-container">
    <div class="drawer-header">
      <div class="modal-title">
        <i data-lucide="folder" style="color:var(--primary);"></i>
        <span>PATIENT RECORD DETAILS</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="drawer-body" id="viewPatientDrawerContent">
      <!-- Rendered by js/app.js -->
    </div>
  </div>
</div>

<!-- 3. EDIT PATIENT RECORD MODAL -->
<div class="modal-backdrop" id="editPatientModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="edit-3" style="color:var(--primary);"></i>
        <span id="editPatientModalTitle">UPDATE PATIENT RECORD</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <form id="editPatientForm" onsubmit="window.submitUpdatePatientRecord(event)">
      <input type="hidden" name="patientId">
      <div class="modal-body">
        <div class="form-grid">
          
          <div class="form-section-header">PERSONAL INFORMATION</div>
          
          <div class="form-group col-4">
            <label class="form-label">First Name</label>
            <input type="text" name="firstName" class="form-control" required>
          </div>
          <div class="form-group col-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middleName" class="form-control">
          </div>
          <div class="form-group col-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="lastName" class="form-control" required>
          </div>
          <div class="form-group col-1">
            <label class="form-label">Suffix</label>
            <input type="text" name="suffix" class="form-control">
          </div>

          <div class="form-group col-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
          </div>
          <div class="form-group col-4">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-control">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="form-group col-4">
            <label class="form-label">Civil Status</label>
            <select name="civilStatus" class="form-control">
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Widowed">Widowed</option>
              <option value="Separated">Separated</option>
            </select>
          </div>

          <div class="form-section-header">CONTACT INFORMATION</div>

          <div class="form-group col-12">
            <label class="form-label">Residential Address</label>
            <input type="text" name="address" class="form-control" required>
          </div>
          <div class="form-group col-6">
            <label class="form-label">Contact Number</label>
            <input type="tel" name="contact" class="form-control" required>
          </div>
          <div class="form-group col-6">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control">
          </div>

          <div class="form-section-header">EMERGENCY CONTACT</div>

          <div class="form-group col-4">
            <label class="form-label">Contact Name</label>
            <input type="text" name="emergencyName" class="form-control">
          </div>
          <div class="form-group col-4">
            <label class="form-label">Relationship</label>
            <input type="text" name="emergencyRel" class="form-control">
          </div>
          <div class="form-group col-4">
            <label class="form-label">Contact Number</label>
            <input type="tel" name="emergencyContact" class="form-control">
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn-primary">
          <i data-lucide="save"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 4. ARCHIVE RECORD MODAL -->
<div class="modal-backdrop" id="archivePatientModal">
  <div class="modal-dialog" style="max-width: 500px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--danger);">
        <i data-lucide="alert-triangle"></i>
        <span>ARCHIVE PATIENT RECORD?</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="archivePatientId">
      <p style="font-size:0.85rem; margin-bottom:1rem; color:var(--text-main);">
        Are you sure you want to archive this patient record?
      </p>
      
      <div style="background:var(--bg-body); padding:0.85rem 1rem; border-radius:var(--radius-md); border:1px solid var(--border-color); margin-bottom:1.25rem;">
        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Selected Patient</div>
        <div style="font-weight:700; font-size:0.95rem;" id="archivePatientNameDisplay">Juan Dela Cruz (P-2026-001)</div>
      </div>

      <div class="form-group">
        <label class="form-label">Archive Reason <span class="required">*</span></label>
        <select id="archiveReasonSelect" class="form-control">
          <option value="Inactive Record">Inactive Record</option>
          <option value="Duplicate Record - Consolidated under Master File">Duplicate Record</option>
          <option value="Administrative Reason">Administrative Reason</option>
          <option value="Transferred to Provincial Facility">Transferred Facility</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div style="font-size:0.75rem; color:var(--text-muted); margin-top:1rem; line-height:1.4;">
        <strong>Important:</strong> Archiving does <strong>NOT</strong> delete the record. It will remain securely stored and searchable under the <strong>Archived</strong> tab.
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
      <button type="button" class="btn btn-danger" onclick="window.confirmArchivePatientRecord()">
        <i data-lucide="archive"></i> Archive Record
      </button>
    </div>
  </div>
</div>

<!-- 5. VERIFY PATIENT RECORD ACCURACY MODAL -->
<div class="modal-backdrop" id="verifyRecordModal">
  <div class="modal-dialog">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="file-check" style="color:var(--primary);"></i>
        <span>VERIFY PATIENT RECORD ACCURACY</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body" id="verifyRecordModalContent">
      <!-- Rendered by js/app.js -->
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-warning" id="requestCorrectionBtn">
        <i data-lucide="alert-circle"></i> Request Correction
      </button>
      <button type="button" class="btn btn-success" id="verifyActionBtn">
        <i data-lucide="check"></i> Verify Record
      </button>
    </div>
  </div>
</div>

<!-- 6. REQUEST CORRECTION MODAL -->
<div class="modal-backdrop" id="correctionModal">
  <div class="modal-dialog" style="max-width: 520px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--warning);">
        <i data-lucide="edit-3"></i>
        <span>REQUEST RECORD CORRECTION</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="correctionPatientId">
      <div class="form-group">
        <label class="form-label">Correction Reason / Specific Issue <span class="required">*</span></label>
        <textarea id="correctionReasonInput" class="form-control" rows="4" placeholder="e.g. Contact information needs confirmation or birth certificate discrepancy."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
      <button type="button" class="btn btn-warning" onclick="window.submitRequestCorrection()">
        <i data-lucide="send"></i> Flag for Correction
      </button>
    </div>
  </div>
</div>

<!-- 7. PATIENT REGISTRATION HISTORY MODAL -->
<div class="modal-backdrop" id="registrationHistoryModal">
  <div class="modal-dialog">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="history" style="color:var(--primary);"></i>
        <span>PATIENT REGISTRATION HISTORY</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body" id="registrationHistoryContent">
      <!-- Rendered by js/app.js -->
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-close-modal>Close</button>
    </div>
  </div>
</div>

<!-- 8. 5-STEP MEDICAL RECORD REQUEST PROCESSING WIZARD MODAL -->
<div class="modal-backdrop" id="processRequestWizardModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="cpu" style="color:var(--primary);"></i>
        <span>PROCESS MEDICAL RECORD REQUEST (5-STEP WIZARD)</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body">
      
      <!-- Step Progress Indicator -->
      <div class="wizard-steps">
        <div class="wizard-step active" id="wiz_step_1">
          <div class="wizard-step-circle">1</div>
          <div class="wizard-step-title">Verify Patient</div>
        </div>
        <div class="wizard-step" id="wiz_step_2">
          <div class="wizard-step-circle">2</div>
          <div class="wizard-step-title">Verify Request</div>
        </div>
        <div class="wizard-step" id="wiz_step_3">
          <div class="wizard-step-circle">3</div>
          <div class="wizard-step-title">Select Records</div>
        </div>
        <div class="wizard-step" id="wiz_step_4">
          <div class="wizard-step-circle">4</div>
          <div class="wizard-step-title">Generate Summary</div>
        </div>
        <div class="wizard-step" id="wiz_step_5">
          <div class="wizard-step-circle">5</div>
          <div class="wizard-step-title">Complete</div>
        </div>
      </div>

      <!-- Step Body -->
      <div id="wizardBodyContainer">
        <!-- Dynamic Step Content -->
      </div>

    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="wizBackBtn" onclick="window.wizardPrevStep()" style="display:none;">
        <i data-lucide="arrow-left"></i> Previous Step
      </button>
      <button type="button" class="btn btn-primary" id="wizNextBtn" onclick="window.wizardNextStep()">
        Next Step <i data-lucide="arrow-right"></i>
      </button>
    </div>
  </div>
</div>

<!-- 9. VIEW REQUEST DETAILS MODAL -->
<div class="modal-backdrop" id="viewRequestModal">
  <div class="modal-dialog">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="file-clock" style="color:var(--primary);"></i>
        <span>MEDICAL RECORD REQUEST DETAILS</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body" id="viewRequestModalContent">
      <!-- Rendered by js/app.js -->
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary btn-sm" id="viewReqPatientBtn">
        <i data-lucide="user"></i> View Patient
      </button>
      <button type="button" class="btn btn-secondary btn-sm" id="viewReqStatusBtn">
        <i data-lucide="refresh-cw"></i> Update Status
      </button>
      <button type="button" class="btn btn-primary btn-sm" id="viewReqProcessBtn">
        <i data-lucide="cpu"></i> Process Request
      </button>
    </div>
  </div>
</div>

<!-- 10. UPDATE REQUEST STATUS MODAL -->
<div class="modal-backdrop" id="updateRequestStatusModal">
  <div class="modal-dialog" style="max-width: 500px;">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="refresh-cw" style="color:var(--primary);"></i>
        <span>UPDATE REQUEST STATUS</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="statusRequestId">
      <div class="form-group" style="margin-bottom:1rem;">
        <label class="form-label">Request Status</label>
        <select id="statusRequestSelect" class="form-control">
          <option value="Pending">Pending</option>
          <option value="Processing">Processing</option>
          <option value="Ready">Ready</option>
          <option value="Completed">Completed</option>
          <option value="Rejected">Rejected</option>
        </select>
      </div>
      <div class="form-group" id="rejectionReasonGroup" style="display:none;">
        <label class="form-label">Reason for Rejection <span class="required">*</span></label>
        <textarea id="rejectionReasonText" class="form-control" placeholder="e.g. Required authorization is incomplete."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
      <button type="button" class="btn btn-primary" onclick="window.submitUpdateRequestStatus()">
        <i data-lucide="save"></i> Update Status
      </button>
    </div>
  </div>
</div>

<!-- 11. CLINICAL DETAIL VIEW MODAL (READ-ONLY) -->
<div class="modal-backdrop" id="clinicalDetailModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="clinicalDetailModalTitle">
        <i data-lucide="file-text"></i> CLINICAL DETAIL
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body" id="clinicalDetailModalContent">
      <!-- Rendered by js/app.js -->
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-close-modal>Close</button>
    </div>
  </div>
</div>

<!-- 12. GENERATE MEDICAL RECORD SUMMARY MODAL -->
<div class="modal-backdrop" id="generateSummaryModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="file-text" style="color:var(--primary);"></i>
        <span>GENERATE MEDICAL RECORD SUMMARY</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <form onsubmit="window.executeGenerateSummary(event)">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group col-12">
            <label class="form-label">Select Patient <span class="required">*</span></label>
            <select id="genSummaryPatientSelect" class="form-control" required></select>
          </div>
          <div class="form-group col-12">
            <label class="form-label">Purpose of Summary / Release Request <span class="required">*</span></label>
            <input type="text" id="genSummaryPurpose" class="form-control" placeholder="e.g. Pre-operative clearance, Insurance claim, Second opinion referral" required>
          </div>
          <div class="form-section-header">SELECT INFORMATION MODULES TO INCLUDE</div>
          <div class="col-12" style="display:flex; flex-direction:column; gap:0.5rem;">
            <div class="checklist-item checked">
              <input type="checkbox" id="gen_chk_patient" class="checklist-checkbox" checked>
              <label for="gen_chk_patient" class="checklist-label">☑ Patient Information (Demographics, Age, Gender, Contact)</label>
            </div>
            <div class="checklist-item checked">
              <input type="checkbox" id="gen_chk_reg" class="checklist-checkbox" checked>
              <label for="gen_chk_reg" class="checklist-label">☑ Registration Information (Admission Date, Type, Verification)</label>
            </div>
            <div class="checklist-item checked">
              <input type="checkbox" id="gen_chk_med" class="checklist-checkbox" checked>
              <label for="gen_chk_med" class="checklist-label">☑ Medical History (Conditions, Allergies, Surgical Procedures)</label>
            </div>
            <div class="checklist-item checked">
              <input type="checkbox" id="gen_chk_con" class="checklist-checkbox" checked>
              <label for="gen_chk_con" class="checklist-label">☑ Consultation History (Diagnoses, Physician Assessments)</label>
            </div>
            <div class="checklist-item checked">
              <input type="checkbox" id="gen_chk_lab" class="checklist-checkbox" checked>
              <label for="gen_chk_lab" class="checklist-label">☑ Laboratory History (Diagnostic Tests, Pathology Results)</label>
            </div>
            <div class="checklist-item checked">
              <input type="checkbox" id="gen_chk_tx" class="checklist-checkbox" checked>
              <label for="gen_chk_tx" class="checklist-label">☑ Treatment History (Medications, Procedures, Protocols)</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn-primary">
          <i data-lucide="file-plus"></i> Generate Summary
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 13. PREVIEW MEDICAL RECORD SUMMARY DOCUMENT MODAL -->
<div class="modal-backdrop" id="previewSummaryModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-header">
      <div class="modal-title">
        <i data-lucide="printer" style="color:var(--primary);"></i>
        <span>OFFICIAL MEDICAL RECORD SUMMARY PREVIEW</span>
      </div>
      <button class="modal-close-btn" data-close-modal>
        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
      </button>
    </div>
    <div class="modal-body" id="previewSummaryModalContent">
      <!-- Rendered by js/app.js -->
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-close-modal>Close</button>
      <button type="button" class="btn btn-primary" id="previewSummaryPrintBtn">
        <i data-lucide="printer"></i> Print Summary
      </button>
    </div>
  </div>
</div>

<!-- Dedicated Print Container (rendered exclusively by window.print()) -->
<div id="printSummaryContainer" class="print-summary-container"></div>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>
