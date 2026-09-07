/**
 * TUPI MUNICIPAL HOSPITAL INFORMATION MANAGEMENT SYSTEM
 * Role 3: Medical Records Officer
 * Main Application Logic & Interactivity
 */

document.addEventListener('DOMContentLoaded', () => {
  // Application State
  const state = {
    currentView: 'dashboard',
    patients: [...TMHIS_DATA.patients],
    requests: [...TMHIS_DATA.medicalRecordRequests],
    consultations: [...TMHIS_DATA.consultations],
    laboratories: [...TMHIS_DATA.laboratoryHistory],
    treatments: [...TMHIS_DATA.treatmentHistory],
    summaries: [...TMHIS_DATA.recordSummaries],
    registrationHistory: [...TMHIS_DATA.patientRegistrationHistory],
    auditLogs: [...TMHIS_DATA.auditTrail],
    notifications: [...TMHIS_DATA.notifications],
    
    // Filters & Active Selections
    patientFilter: 'all',
    patientSearchQuery: '',
    verificationFilter: 'all',
    requestFilter: 'all',
    requestPriorityFilter: 'all',
    selectedPatientId: 'P-2026-001',
    selectedHistoryTab: 'overview',
    
    // Wizard State for Request Processing
    wizard: {
      step: 1,
      requestId: null,
      selectedRecords: {
        patientInfo: true,
        medicalHistory: true,
        consultationHistory: true,
        laboratoryHistory: true,
        treatmentHistory: true
      }
    }
  };

  // DOM Elements
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const contentContainer = document.getElementById('mainContentArea');
  const pageTitle = document.getElementById('pageTitle');
  const pageSubtitle = document.getElementById('pageSubtitle');
  const navItems = document.querySelectorAll('.nav-item');
  const globalSearchInput = document.getElementById('globalSearchInput');
  const toastContainer = document.getElementById('toastContainer');
  const notifBadge = document.getElementById('headerNotifBadge');

  // Initialize Lucide Icons
  function refreshIcons() {
    if (window.lucide) {
      lucide.createIcons();
    }
  }

  // Toast Notification Generator
  function showToast(title, message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let iconName = 'check';
    if (type === 'error') iconName = 'alert-circle';
    if (type === 'warning') iconName = 'alert-triangle';
    if (type === 'info') iconName = 'info';

    toast.innerHTML = `
      <div class="toast-icon">
        <i data-lucide="${iconName}" style="width:16px;height:16px;"></i>
      </div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close">&times;</button>
    `;

    toastContainer.appendChild(toast);
    refreshIcons();

    toast.querySelector('.toast-close').addEventListener('click', () => {
      toast.remove();
    });

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(() => toast.remove(), 250);
    }, 4000);
  }

  // Audit Logger Helper
  function logAudit(action, targetId, targetName, details) {
    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0];
    const newLog = {
      id: `AUD-${Math.floor(1000 + Math.random() * 9000)}`,
      user: TMHIS_DATA.currentOfficer.name,
      role: "Medical Records Officer",
      action,
      targetId,
      targetName,
      date: dateStr,
      time: timeStr,
      details
    };
    state.auditLogs.unshift(newLog);
  }

  // Sidebar Toggle Logic
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      localStorage.setItem('hospity_sidebar_collapsed', sidebar.classList.contains('collapsed'));
    });
  }

  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('mobile-open');
    });
  }

  // Navigation Click Handler
  navItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      const view = item.getAttribute('data-view');
      if (view) {
        navigateTo(view);
        if (window.innerWidth <= 992) {
          sidebar.classList.remove('mobile-open');
        }
      }
    });
  });

  function navigateTo(view) {
    state.currentView = view;
    navItems.forEach(item => {
      if (item.getAttribute('data-view') === view) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });

    renderCurrentView();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Global live search
  if (globalSearchInput) {
    globalSearchInput.addEventListener('input', (e) => {
      const q = e.target.value.trim().toLowerCase();
      state.patientSearchQuery = q;
      if (state.currentView === 'patients' || state.currentView === 'verification') {
        renderCurrentView();
      } else if (q.length >= 2 && state.currentView !== 'patients') {
        navigateTo('patients');
      }
    });
  }

  // View Router & Renderer
  function renderCurrentView() {
    switch (state.currentView) {
      case 'dashboard':
        renderDashboard();
        break;
      case 'patients':
        renderPatientRecords();
        break;
      case 'verification':
        renderRecordVerification();
        break;
      case 'requests':
        renderMedicalRecordRequests();
        break;
      case 'history':
        renderPatientHistory();
        break;
      case 'summaries':
        renderRecordSummaries();
        break;
      case 'notifications':
        renderNotificationsView();
        break;
      case 'audit':
        renderAuditTrailView();
        break;
      case 'settings':
        renderSettingsView();
        break;
      default:
        renderDashboard();
    }
    refreshIcons();
    updateHeaderBadges();
  }

  function updateHeaderBadges() {
    const unreadCount = state.notifications.filter(n => n.unread).length;
    if (notifBadge) {
      notifBadge.style.display = unreadCount > 0 ? 'block' : 'none';
    }
  }

  /* ==========================================================================
     1. DASHBOARD VIEW
     ========================================================================== */
  function renderDashboard() {
    pageTitle.textContent = "MEDICAL RECORDS DASHBOARD";
    pageSubtitle.textContent = "Patient Record Administration & Medical Record Retrieval";

    const totalActivePatients = state.patients.filter(p => p.recordStatus === 'Active').length;
    const pendingVerifications = state.patients.filter(p => p.verificationStatus === 'Pending Verification').length;
    const pendingRequests = state.requests.filter(r => r.status === 'Pending' || r.status === 'Processing').length;
    const archivedPatients = state.patients.filter(p => p.recordStatus === 'Archived').length;

    contentContainer.innerHTML = `
      <!-- Summary Cards Grid -->
      <div class="summary-cards-grid">
        <div class="stat-card card-primary" onclick="window.hospityNavigate('patients')">
          <div class="stat-card-header">
            <span class="stat-card-title">TOTAL PATIENT RECORDS</span>
            <div class="stat-icon-wrapper">
              <i data-lucide="folder-open"></i>
            </div>
          </div>
          <div class="stat-card-value">${totalActivePatients + 1218}</div>
          <div class="stat-card-footer">
            <span>Active patient records</span>
            <span class="stat-indicator indicator-green"><i data-lucide="trending-up" style="width:12px;height:12px;"></i> +12 this week</span>
          </div>
        </div>

        <div class="stat-card card-warning" onclick="window.hospityNavigate('verification')">
          <div class="stat-card-header">
            <span class="stat-card-title">PENDING VERIFICATION</span>
            <div class="stat-icon-wrapper">
              <i data-lucide="file-check"></i>
            </div>
          </div>
          <div class="stat-card-value">${pendingVerifications}</div>
          <div class="stat-card-footer">
            <span>Records requiring verification</span>
            <span class="stat-indicator indicator-amber"><i data-lucide="alert-circle" style="width:12px;height:12px;"></i> Needs review</span>
          </div>
        </div>

        <div class="stat-card card-info" onclick="window.hospityNavigate('requests')">
          <div class="stat-card-header">
            <span class="stat-card-title">RECORD REQUESTS</span>
            <div class="stat-icon-wrapper">
              <i data-lucide="file-clock"></i>
            </div>
          </div>
          <div class="stat-card-value">${pendingRequests}</div>
          <div class="stat-card-footer">
            <span>Pending medical record requests</span>
            <span class="stat-indicator indicator-blue"><i data-lucide="clock" style="width:12px;height:12px;"></i> 3 Urgent</span>
          </div>
        </div>

        <div class="stat-card card-secondary" onclick="window.hospityNavigate('patients', 'Archived')">
          <div class="stat-card-header">
            <span class="stat-card-title">ARCHIVED RECORDS</span>
            <div class="stat-icon-wrapper">
              <i data-lucide="archive"></i>
            </div>
          </div>
          <div class="stat-card-value">${archivedPatients + 82}</div>
          <div class="stat-card-footer">
            <span>Archived patient records</span>
            <span class="stat-indicator"><i data-lucide="shield-check" style="width:12px;height:12px;"></i> Stored securely</span>
          </div>
        </div>
      </div>

      <!-- Quick Actions Bar -->
      <div style="display: flex; gap: 1rem; margin-bottom: 1.75rem; flex-wrap: wrap;">
        <button class="btn btn-primary" onclick="window.openCreatePatientModal()">
          <i data-lucide="user-plus"></i> + Create Patient Record
        </button>
        <button class="btn btn-secondary" onclick="window.hospityNavigate('verification')">
          <i data-lucide="file-check"></i> Verify Pending Records (${pendingVerifications})
        </button>
        <button class="btn btn-secondary" onclick="window.hospityNavigate('requests')">
          <i data-lucide="file-clock"></i> Process Record Requests
        </button>
        <button class="btn btn-secondary" onclick="window.openGenerateSummaryModal()">
          <i data-lucide="file-text"></i> Generate Record Summary
        </button>
      </div>

      <!-- Two Column Layout -->
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        
        <!-- Left: Urgent Medical Record Requests -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title-group">
              <div class="panel-title">
                <i data-lucide="clock" style="color:var(--primary);"></i>
                Active Medical Record Requests
              </div>
              <div class="panel-subtitle">Immediate requests requiring validation and abstract release</div>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="window.hospityNavigate('requests')">
              View All Requests <i data-lucide="arrow-right" style="width:14px;height:14px;"></i>
            </button>
          </div>
          <div class="panel-body no-padding">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Patient</th>
                    <th>Request Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  ${state.requests.slice(0, 5).map(req => `
                    <tr>
                      <td><span class="patient-id-badge">${req.id}</span></td>
                      <td>
                        <strong>${req.patientName}</strong>
                        <div style="font-size:0.72rem; color:var(--text-muted);">${req.patientId}</div>
                      </td>
                      <td>${req.requestType}</td>
                      <td>
                        <span class="badge ${req.priority === 'Urgent' ? 'badge-urgent' : 'badge-normal'}">
                          ${req.priority}
                        </span>
                      </td>
                      <td>
                        <span class="badge badge-${req.status.toLowerCase()}">${req.status}</span>
                      </td>
                      <td>
                        <button class="btn btn-secondary btn-sm" onclick="window.openProcessWizard('${req.id}')">
                          Process
                        </button>
                      </td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Right: Recent Activity Stream -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title-group">
              <div class="panel-title">
                <i data-lucide="activity" style="color:var(--primary);"></i>
                Recent Record Activity
              </div>
              <div class="panel-subtitle">Live audit feed for Medical Records</div>
            </div>
          </div>
          <div class="panel-body">
            <div class="timeline">
              ${TMHIS_DATA.recentActivities.map(act => `
                <div class="timeline-item">
                  <div class="timeline-dot dot-info"></div>
                  <div class="timeline-content">
                    <div class="timeline-header">
                      <span class="timeline-title">${act.title}</span>
                      <span class="timeline-date">${act.time}</span>
                    </div>
                    <div class="timeline-desc">${act.desc}</div>
                    <div class="timeline-meta">Performed by Medical Records Officer</div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        </div>

      </div>
    `;
  }

  /* ==========================================================================
     2. PATIENT RECORDS VIEW (MODULE 1)
     ========================================================================== */
  function renderPatientRecords() {
    pageTitle.textContent = "PATIENT RECORDS";
    pageSubtitle.textContent = "Patient Master Index & Record Administration";

    // Filtering
    let filtered = state.patients.filter(p => {
      if (state.patientFilter === 'active') return p.recordStatus === 'Active';
      if (state.patientFilter === 'archived') return p.recordStatus === 'Archived';
      if (state.patientFilter === 'pending') return p.verificationStatus === 'Pending Verification';
      if (state.patientFilter === 'verified') return p.verificationStatus === 'Verified';
      return true;
    });

    if (state.patientSearchQuery) {
      const q = state.patientSearchQuery.toLowerCase();
      filtered = filtered.filter(p => 
        p.id.toLowerCase().includes(q) ||
        `${p.firstName} ${p.lastName}`.toLowerCase().includes(q) ||
        p.contact.toLowerCase().includes(q) ||
        p.dob.toLowerCase().includes(q)
      );
    }

    contentContainer.innerHTML = `
      <div class="panel">
        <div class="panel-header">
          <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
            <div class="filter-tabs">
              <button class="filter-tab ${state.patientFilter === 'all' ? 'active' : ''}" onclick="window.setPatientFilter('all')">All (${state.patients.length})</button>
              <button class="filter-tab ${state.patientFilter === 'active' ? 'active' : ''}" onclick="window.setPatientFilter('active')">Active (${state.patients.filter(p => p.recordStatus === 'Active').length})</button>
              <button class="filter-tab ${state.patientFilter === 'archived' ? 'active' : ''}" onclick="window.setPatientFilter('archived')">Archived (${state.patients.filter(p => p.recordStatus === 'Archived').length})</button>
              <button class="filter-tab ${state.patientFilter === 'pending' ? 'active' : ''}" onclick="window.setPatientFilter('pending')">Pending Verification (${state.patients.filter(p => p.verificationStatus === 'Pending Verification').length})</button>
              <button class="filter-tab ${state.patientFilter === 'verified' ? 'active' : ''}" onclick="window.setPatientFilter('verified')">Verified (${state.patients.filter(p => p.verificationStatus === 'Verified').length})</button>
            </div>
          </div>
          <div class="panel-actions">
            <button class="btn btn-primary" onclick="window.openCreatePatientModal()">
              <i data-lucide="plus-circle"></i> + Create Patient Record
            </button>
          </div>
        </div>

        <div class="panel-body no-padding">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Patient ID</th>
                  <th>Patient Name</th>
                  <th>Date of Birth</th>
                  <th>Gender</th>
                  <th>Contact</th>
                  <th>Registration Date</th>
                  <th>Record Status</th>
                  <th>Verification</th>
                  <th>Last Updated</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                ${filtered.length === 0 ? `
                  <tr>
                    <td colspan="10" style="text-align:center; padding:2.5rem; color:var(--text-muted);">
                      <i data-lucide="search-x" style="width:32px;height:32px;margin:0 auto 0.5rem;display:block;"></i>
                      No patient records matched the specified filter criteria.
                    </td>
                  </tr>
                ` : filtered.map(p => `
                  <tr>
                    <td><span class="patient-id-badge">${p.id}</span></td>
                    <td>
                      <div class="patient-name-cell">
                        <div class="patient-avatar-mini">${p.firstName[0]}${p.lastName[0]}</div>
                        <div>
                          <strong>${p.firstName} ${p.middleName ? p.middleName[0] + '.' : ''} ${p.lastName} ${p.suffix || ''}</strong>
                          <div style="font-size:0.72rem; color:var(--text-muted);">${p.email || 'No email registered'}</div>
                        </div>
                      </div>
                    </td>
                    <td>${formatDate(p.dob)} <span style="color:var(--text-muted);font-size:0.72rem;">(${p.age}y)</span></td>
                    <td>${p.gender}</td>
                    <td>${p.contact}</td>
                    <td>${formatDate(p.registrationDate)}</td>
                    <td>
                      <span class="badge ${p.recordStatus === 'Active' ? 'badge-active' : 'badge-archived'}">
                        ${p.recordStatus}
                      </span>
                    </td>
                    <td>
                      <span class="badge ${p.verificationStatus === 'Verified' ? 'badge-verified' : (p.verificationStatus === 'Needs Correction' ? 'badge-correction' : 'badge-pending')}">
                        ${p.verificationStatus}
                      </span>
                    </td>
                    <td style="font-size:0.75rem; color:var(--text-muted);">${p.lastUpdated || '-'}</td>
                    <td>
                      <div class="action-btn-group">
                        <button class="btn btn-secondary btn-sm" onclick="window.viewPatientRecord('${p.id}')" title="View Patient Record">
                          <i data-lucide="eye" style="width:14px;height:14px;"></i> View
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="window.editPatientRecord('${p.id}')" title="Edit Patient Demographics">
                          <i data-lucide="edit-2" style="width:14px;height:14px;"></i> Edit
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="window.openVerificationModal('${p.id}')" title="Verify Record Accuracy">
                          <i data-lucide="check-square" style="width:14px;height:14px;"></i> Verify
                        </button>
                        ${p.recordStatus !== 'Archived' ? `
                          <button class="btn btn-secondary btn-sm text-danger" onclick="window.openArchiveModal('${p.id}')" title="Archive Record">
                            <i data-lucide="archive" style="width:14px;height:14px;"></i> Archive
                          </button>
                        ` : ''}
                      </div>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  /* ==========================================================================
     3. RECORD VERIFICATION VIEW (MODULE 2)
     ========================================================================== */
  function renderRecordVerification() {
    pageTitle.textContent = "RECORD VERIFICATION";
    pageSubtitle.textContent = "Verify Patient Record Accuracy & Registration History";

    let list = state.patients.filter(p => {
      if (state.verificationFilter === 'pending') return p.verificationStatus === 'Pending Verification';
      if (state.verificationFilter === 'verified') return p.verificationStatus === 'Verified';
      if (state.verificationFilter === 'correction') return p.verificationStatus === 'Needs Correction';
      return true;
    });

    contentContainer.innerHTML = `
      <div class="panel">
        <div class="panel-header">
          <div class="filter-tabs">
            <button class="filter-tab ${state.verificationFilter === 'all' ? 'active' : ''}" onclick="window.setVerificationFilter('all')">All Records (${state.patients.length})</button>
            <button class="filter-tab ${state.verificationFilter === 'pending' ? 'active' : ''}" onclick="window.setVerificationFilter('pending')">Pending Verification (${state.patients.filter(p => p.verificationStatus === 'Pending Verification').length})</button>
            <button class="filter-tab ${state.verificationFilter === 'correction' ? 'active' : ''}" onclick="window.setVerificationFilter('correction')">Needs Correction (${state.patients.filter(p => p.verificationStatus === 'Needs Correction').length})</button>
            <button class="filter-tab ${state.verificationFilter === 'verified' ? 'active' : ''}" onclick="window.setVerificationFilter('verified')">Verified (${state.patients.filter(p => p.verificationStatus === 'Verified').length})</button>
          </div>
        </div>

        <div class="panel-body no-padding">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Patient ID</th>
                  <th>Patient Name</th>
                  <th>Registration Date</th>
                  <th>Registration Type</th>
                  <th>Verification Status</th>
                  <th>Verified By</th>
                  <th>Last Updated</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                ${list.map(p => `
                  <tr>
                    <td><span class="patient-id-badge">${p.id}</span></td>
                    <td>
                      <div class="patient-name-cell">
                        <div class="patient-avatar-mini">${p.firstName[0]}${p.lastName[0]}</div>
                        <div>
                          <strong>${p.firstName} ${p.lastName} ${p.suffix || ''}</strong>
                          <div style="font-size:0.72rem; color:var(--text-muted);">${p.contact}</div>
                        </div>
                      </div>
                    </td>
                    <td>${formatDate(p.registrationDate)}</td>
                    <td>${p.registrationType || 'Outpatient'}</td>
                    <td>
                      <span class="badge ${p.verificationStatus === 'Verified' ? 'badge-verified' : (p.verificationStatus === 'Needs Correction' ? 'badge-correction' : 'badge-pending')}">
                        ${p.verificationStatus}
                      </span>
                    </td>
                    <td style="font-size:0.78rem;">${p.verifiedBy || '<span style="color:var(--text-muted);">Unverified</span>'}</td>
                    <td style="font-size:0.75rem; color:var(--text-muted);">${p.lastUpdated || '-'}</td>
                    <td>
                      <div class="action-btn-group">
                        <button class="btn btn-primary btn-sm" onclick="window.openVerificationModal('${p.id}')">
                          <i data-lucide="file-check" style="width:14px;height:14px;"></i> Verify Record
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="window.viewRegistrationHistory('${p.id}')">
                          <i data-lucide="history" style="width:14px;height:14px;"></i> History
                        </button>
                      </div>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  /* ==========================================================================
     4. MEDICAL RECORD REQUESTS (MODULE 3)
     ========================================================================== */
  function renderMedicalRecordRequests() {
    pageTitle.textContent = "MEDICAL RECORD REQUESTS";
    pageSubtitle.textContent = "Process Record Requests & Release Management";

    let list = state.requests.filter(r => {
      if (state.requestFilter !== 'all' && r.status.toLowerCase() !== state.requestFilter.toLowerCase()) return false;
      if (state.requestPriorityFilter !== 'all' && r.priority.toLowerCase() !== state.requestPriorityFilter.toLowerCase()) return false;
      return true;
    });

    contentContainer.innerHTML = `
      <div class="panel">
        <div class="panel-header">
          <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
            <div class="filter-tabs">
              <button class="filter-tab ${state.requestFilter === 'all' ? 'active' : ''}" onclick="window.setRequestFilter('all')">All Requests (${state.requests.length})</button>
              <button class="filter-tab ${state.requestFilter === 'pending' ? 'active' : ''}" onclick="window.setRequestFilter('pending')">Pending</button>
              <button class="filter-tab ${state.requestFilter === 'processing' ? 'active' : ''}" onclick="window.setRequestFilter('processing')">Processing</button>
              <button class="filter-tab ${state.requestFilter === 'ready' ? 'active' : ''}" onclick="window.setRequestFilter('ready')">Ready</button>
              <button class="filter-tab ${state.requestFilter === 'completed' ? 'active' : ''}" onclick="window.setRequestFilter('completed')">Completed</button>
              <button class="filter-tab ${state.requestFilter === 'rejected' ? 'active' : ''}" onclick="window.setRequestFilter('rejected')">Rejected</button>
            </div>
            <select class="form-control" style="width:140px; height:32px; font-size:0.75rem;" onchange="window.setRequestPriorityFilter(this.value)">
              <option value="all">All Priorities</option>
              <option value="urgent" ${state.requestPriorityFilter === 'urgent' ? 'selected' : ''}>Urgent</option>
              <option value="normal" ${state.requestPriorityFilter === 'normal' ? 'selected' : ''}>Normal</option>
            </select>
          </div>
        </div>

        <div class="panel-body no-padding">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Request ID</th>
                  <th>Patient</th>
                  <th>Patient ID</th>
                  <th>Request Type</th>
                  <th>Requested By</th>
                  <th>Request Date</th>
                  <th>Priority</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                ${list.map(r => `
                  <tr>
                    <td><span class="patient-id-badge">${r.id}</span></td>
                    <td><strong>${r.patientName}</strong></td>
                    <td><span class="patient-id-badge" style="background:#f1f5f9;color:#475569;border-color:#cbd5e1;">${r.patientId}</span></td>
                    <td>${r.requestType}</td>
                    <td>
                      <div>${r.requestedBy}</div>
                      <div style="font-size:0.7rem; color:var(--text-muted);">${r.requestingEntity}</div>
                    </td>
                    <td>${formatDate(r.requestDate)}</td>
                    <td>
                      <span class="badge ${r.priority === 'Urgent' ? 'badge-urgent' : 'badge-normal'}">${r.priority}</span>
                    </td>
                    <td>
                      <span class="badge badge-${r.status.toLowerCase()}">${r.status}</span>
                    </td>
                    <td>
                      <div class="action-btn-group">
                        <button class="btn btn-secondary btn-sm" onclick="window.viewRecordRequest('${r.id}')" title="View Request Details">
                          <i data-lucide="eye" style="width:14px;height:14px;"></i> View
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="window.openProcessWizard('${r.id}')" title="Process Multi-Step Request">
                          <i data-lucide="cpu" style="width:14px;height:14px;"></i> Process
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="window.openUpdateRequestStatusModal('${r.id}')" title="Update Status">
                          <i data-lucide="refresh-cw" style="width:14px;height:14px;"></i> Status
                        </button>
                      </div>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  /* ==========================================================================
     5. PATIENT HISTORY ACCESS (MODULE 3)
     ========================================================================== */
  function renderPatientHistory() {
    pageTitle.textContent = "PATIENT MEDICAL HISTORY";
    pageSubtitle.textContent = "View Patient Medical, Consultation, Laboratory & Treatment History";

    const patient = state.patients.find(p => p.id === state.selectedPatientId) || state.patients[0];
    const medHistory = TMHIS_DATA.patientMedicalHistories[patient.id] || {
      conditions: [
        { condition: "Seasonal Allergic Rhinitis", diagnosedDate: "2024-05-10", diagnosedBy: "Dr. Roberto De Leon, FPCP", status: "Active" }
      ],
      allergies: [
        { allergen: "No known drug allergies (NKDA)", reaction: "N/A", severity: "None" }
      ],
      procedures: [
        { procedure: "Routine Dental Extraction", date: "2023-11-12", surgeon: "Dr. L. Tan, DMD", facility: "TMHIS Dental", findings: "Uncomplicated" }
      ],
      treatments: [
        { treatment: "Cetirizine 10mg tab OD PRN", startDate: "2024-05-10", prescribingDoctor: "Dr. Roberto De Leon", notes: "For allergy flare-ups" }
      ],
      timeline: [
        { date: "2026-08-28", type: "Consultation", title: "Outpatient Assessment", practitioner: "Attending Physician", desc: "General health evaluation" },
        { date: "2026-08-20", type: "Registration", title: "Patient Registration", practitioner: "Medical Records Department", desc: "Record registered in master database" }
      ]
    };

    const patientConsultations = state.consultations.filter(c => c.patientId === patient.id);
    const patientLabs = state.laboratories.filter(l => l.patientId === patient.id);
    const patientTreatments = state.treatments.filter(t => t.patientId === patient.id);

    contentContainer.innerHTML = `
      <!-- Patient Selector & Header Profile -->
      <div class="panel" style="margin-bottom:1.5rem;">
        <div class="panel-body" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1.25rem;">
          <div style="display:flex; align-items:center; gap:1.25rem;">
            <div class="patient-avatar-mini" style="width:54px; height:54px; font-size:1.2rem; background:linear-gradient(135deg, #3b82f6, #1d4ed8); color:#fff;">
              ${patient.firstName[0]}${patient.lastName[0]}
            </div>
            <div>
              <div style="display:flex; align-items:center; gap:0.75rem;">
                <h2 style="font-family:var(--font-heading); font-size:1.35rem; font-weight:800; color:var(--text-main); margin:0;">
                  ${patient.firstName} ${patient.middleName ? patient.middleName[0] + '.' : ''} ${patient.lastName} ${patient.suffix || ''}
                </h2>
                <span class="patient-id-badge" style="font-size:0.85rem;">${patient.id}</span>
                <span class="badge ${patient.recordStatus === 'Active' ? 'badge-active' : 'badge-archived'}">${patient.recordStatus}</span>
              </div>
              <div style="display:flex; gap:1.25rem; font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">
                <span><strong>DOB:</strong> ${formatDate(patient.dob)} (${patient.age}y)</span>
                <span><strong>Gender:</strong> ${patient.gender}</span>
                <span><strong>Civil Status:</strong> ${patient.civilStatus}</span>
                <span><strong>Blood Type:</strong> ${patient.bloodType || 'O+'}</span>
                <span><strong>Contact:</strong> ${patient.contact}</span>
              </div>
            </div>
          </div>

          <div style="display:flex; align-items:center; gap:0.75rem;">
            <select class="form-control" style="width:240px;" onchange="window.selectHistoryPatient(this.value)">
              ${state.patients.map(p => `
                <option value="${p.id}" ${p.id === patient.id ? 'selected' : ''}>${p.id} - ${p.firstName} ${p.lastName}</option>
              `).join('')}
            </select>
            <button class="btn btn-primary btn-sm" onclick="window.openGenerateSummaryModal('${patient.id}')">
              <i data-lucide="file-text"></i> Generate Summary
            </button>
          </div>
        </div>
      </div>

      <!-- Read-Only Medical Boundary Banner -->
      <div class="read-only-banner">
        <i data-lucide="shield-alert"></i>
        <div>
          <strong>Role Boundary Notification:</strong> As a Medical Records Officer, you are authorized to <strong>VIEW</strong> medical, consultation, laboratory, and treatment histories for record administration and release purposes. Clinical data entry and modifications are strictly restricted to licensed attending physicians and clinical staff.
        </div>
      </div>

      <!-- Navigation Tabs for History Sections -->
      <div class="filter-tabs" style="margin-bottom:1.5rem; display:flex;">
        <button class="filter-tab ${state.selectedHistoryTab === 'overview' ? 'active' : ''}" onclick="window.setHistoryTab('overview')">
          <i data-lucide="git-commit"></i> Timeline & Overview
        </button>
        <button class="filter-tab ${state.selectedHistoryTab === 'medical' ? 'active' : ''}" onclick="window.setHistoryTab('medical')">
          <i data-lucide="clipboard"></i> Medical History
        </button>
        <button class="filter-tab ${state.selectedHistoryTab === 'consultations' ? 'active' : ''}" onclick="window.setHistoryTab('consultations')">
          <i data-lucide="stethoscope"></i> Consultations (${patientConsultations.length})
        </button>
        <button class="filter-tab ${state.selectedHistoryTab === 'laboratories' ? 'active' : ''}" onclick="window.setHistoryTab('laboratories')">
          <i data-lucide="flask-conical"></i> Laboratory History (${patientLabs.length})
        </button>
        <button class="filter-tab ${state.selectedHistoryTab === 'treatments' ? 'active' : ''}" onclick="window.setHistoryTab('treatments')">
          <i data-lucide="pill"></i> Treatment History (${patientTreatments.length})
        </button>
      </div>

      <!-- Dynamic Tab Content -->
      <div id="historyTabContentArea">
        ${renderHistoryTabContent(patient, medHistory, patientConsultations, patientLabs, patientTreatments)}
      </div>
    `;
  }

  function renderHistoryTabContent(patient, medHistory, consultations, labs, treatments) {
    if (state.selectedHistoryTab === 'overview') {
      return `
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem;">
          <div class="panel">
            <div class="panel-header">
              <div class="panel-title"><i data-lucide="clock"></i> Patient Medical Record Timeline</div>
            </div>
            <div class="panel-body">
              <div class="timeline">
                ${medHistory.timeline.map(item => `
                  <div class="timeline-item">
                    <div class="timeline-dot ${item.type === 'Consultation' ? 'dot-info' : (item.type === 'Laboratory' ? 'dot-success' : 'dot-purple')}"></div>
                    <div class="timeline-content">
                      <div class="timeline-header">
                        <span class="timeline-title">${item.title}</span>
                        <span class="timeline-date">${formatDate(item.date)}</span>
                      </div>
                      <div class="timeline-desc">${item.desc}</div>
                      <div class="timeline-meta">${item.practitioner}</div>
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>
          </div>

          <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="info-card-box">
              <div class="info-card-box-title"><i data-lucide="alert-octagon" style="color:var(--danger);"></i> Known Allergies</div>
              ${medHistory.allergies.map(a => `
                <div style="margin-bottom:0.6rem; padding-bottom:0.6rem; border-bottom:1px solid var(--border-color);">
                  <div style="font-weight:700; font-size:0.85rem; color:var(--danger);">${a.allergen}</div>
                  <div style="font-size:0.75rem; color:var(--text-muted);">Reaction: ${a.reaction}</div>
                  <span class="badge badge-urgent" style="font-size:0.65rem; margin-top:2px;">${a.severity}</span>
                </div>
              `).join('')}
            </div>

            <div class="info-card-box">
              <div class="info-card-box-title"><i data-lucide="activity" style="color:var(--primary);"></i> Active Diagnoses</div>
              ${medHistory.conditions.map(c => `
                <div style="margin-bottom:0.5rem;">
                  <div style="font-weight:700; font-size:0.82rem;">${c.condition}</div>
                  <div style="font-size:0.72rem; color:var(--text-muted);">${c.diagnosedBy} (${formatDate(c.diagnosedDate)})</div>
                </div>
              `).join('')}
            </div>
          </div>
        </div>
      `;
    }

    if (state.selectedHistoryTab === 'medical') {
      return `
        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.5rem;">
          <div class="panel">
            <div class="panel-header">
              <div class="panel-title"><i data-lucide="activity"></i> Previous Medical Conditions</div>
            </div>
            <div class="panel-body">
              ${medHistory.conditions.map(c => `
                <div class="info-card-box" style="margin-bottom:0.75rem;">
                  <div style="font-weight:700; font-size:0.9rem; color:var(--text-main);">${c.condition}</div>
                  <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.25rem;">
                    Diagnosed By: ${c.diagnosedBy} | Date: ${formatDate(c.diagnosedDate)}
                  </div>
                  <span class="badge badge-active" style="margin-top:0.4rem;">${c.status}</span>
                </div>
              `).join('')}
            </div>
          </div>

          <div class="panel">
            <div class="panel-header">
              <div class="panel-title"><i data-lucide="scissors"></i> Previous Surgical Procedures</div>
            </div>
            <div class="panel-body">
              ${medHistory.procedures.map(p => `
                <div class="info-card-box" style="margin-bottom:0.75rem;">
                  <div style="font-weight:700; font-size:0.9rem; color:var(--text-main);">${p.procedure}</div>
                  <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.25rem;">
                    Surgeon: ${p.surgeon} | Facility: ${p.facility} (${formatDate(p.date)})
                  </div>
                  <div style="font-size:0.75rem; color:var(--text-main); margin-top:0.35rem;">
                    <strong>Findings:</strong> ${p.findings}
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        </div>
      `;
    }

    if (state.selectedHistoryTab === 'consultations') {
      return `
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><i data-lucide="stethoscope"></i> Consultation History (Read-Only)</div>
          </div>
          <div class="panel-body no-padding">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Date & Time</th>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th>Chief Complaint</th>
                    <th>Diagnosis</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  ${consultations.length === 0 ? `
                    <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">No consultation records found for this patient.</td></tr>
                  ` : consultations.map(c => `
                    <tr>
                      <td>${formatDate(c.date)} <div style="font-size:0.7rem; color:var(--text-muted);">${c.time}</div></td>
                      <td><strong>${c.doctor}</strong></td>
                      <td>${c.department}</td>
                      <td style="max-width:200px;">${c.chiefComplaint}</td>
                      <td><span class="badge badge-active">${c.diagnosis}</span></td>
                      <td><span class="badge badge-completed">${c.status}</span></td>
                      <td>
                        <button class="btn btn-secondary btn-sm" onclick="window.viewConsultationDetails('${c.id}')">
                          <i data-lucide="file-search" style="width:14px;height:14px;"></i> View Details
                        </button>
                      </td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      `;
    }

    if (state.selectedHistoryTab === 'laboratories') {
      return `
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><i data-lucide="flask-conical"></i> Laboratory History (Read-Only)</div>
          </div>
          <div class="panel-body no-padding">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Laboratory Test</th>
                    <th>Requesting Doctor</th>
                    <th>Department</th>
                    <th>Result Status</th>
                    <th>Report</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  ${labs.length === 0 ? `
                    <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">No laboratory records found for this patient.</td></tr>
                  ` : labs.map(l => `
                    <tr>
                      <td>${formatDate(l.date)}</td>
                      <td><strong>${l.testName}</strong></td>
                      <td>${l.requestingDoctor}</td>
                      <td>${l.department}</td>
                      <td><span class="badge badge-verified">${l.resultStatus}</span></td>
                      <td><span class="badge badge-ready">${l.reportStatus}</span></td>
                      <td>
                        <button class="btn btn-secondary btn-sm" onclick="window.viewLabReport('${l.id}')">
                          <i data-lucide="file-text" style="width:14px;height:14px;"></i> View Report
                        </button>
                      </td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      `;
    }

    if (state.selectedHistoryTab === 'treatments') {
      return `
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><i data-lucide="pill"></i> Treatment History (Read-Only)</div>
          </div>
          <div class="panel-body no-padding">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Treatment / Protocol</th>
                    <th>Prescribing Doctor</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  ${treatments.length === 0 ? `
                    <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">No treatment records found for this patient.</td></tr>
                  ` : treatments.map(t => `
                    <tr>
                      <td>${formatDate(t.date)}</td>
                      <td><strong>${t.treatment}</strong></td>
                      <td>${t.doctor}</td>
                      <td>${t.department}</td>
                      <td><span class="badge badge-active">${t.status}</span></td>
                      <td style="max-width:240px; font-size:0.78rem;">${t.notes}</td>
                      <td>
                        <button class="btn btn-secondary btn-sm" onclick="window.viewTreatmentDetails('${t.id}')">
                          <i data-lucide="eye" style="width:14px;height:14px;"></i> View Details
                        </button>
                      </td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      `;
    }

    return '';
  }

  /* ==========================================================================
     6. MEDICAL RECORD SUMMARIES & OUTPUT (MODULE 4)
     ========================================================================== */
  function renderRecordSummaries() {
    pageTitle.textContent = "MEDICAL RECORD SUMMARIES";
    pageSubtitle.textContent = "Generate & Print Official Medical Record Summaries";

    contentContainer.innerHTML = `
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><i data-lucide="file-text"></i> Generated Medical Record Summaries</div>
          <button class="btn btn-primary" onclick="window.openGenerateSummaryModal()">
            <i data-lucide="plus-circle"></i> + Generate Medical Record Summary
          </button>
        </div>

        <div class="panel-body no-padding">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Summary ID</th>
                  <th>Patient Name</th>
                  <th>Patient ID</th>
                  <th>Purpose</th>
                  <th>Sections Included</th>
                  <th>Generated Date</th>
                  <th>Generated By</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                ${state.summaries.map(s => `
                  <tr>
                    <td><span class="patient-id-badge">${s.summaryId}</span></td>
                    <td><strong>${s.patientName}</strong></td>
                    <td><span class="patient-id-badge" style="background:#f1f5f9;color:#475569;border-color:#cbd5e1;">${s.patientId}</span></td>
                    <td>${s.purpose}</td>
                    <td>
                      <div style="display:flex; flex-wrap:wrap; gap:4px; max-width:250px;">
                        ${s.sectionsIncluded.map(sec => `<span class="badge badge-normal" style="font-size:0.65rem;">${sec}</span>`).join('')}
                      </div>
                    </td>
                    <td>${s.generatedDate}</td>
                    <td>${s.generatedBy}</td>
                    <td><span class="badge badge-verified">${s.status}</span></td>
                    <td>
                      <div class="action-btn-group">
                        <button class="btn btn-secondary btn-sm" onclick="window.previewSummaryDocument('${s.summaryId}')">
                          <i data-lucide="eye" style="width:14px;height:14px;"></i> View Summary
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="window.printSummaryDocument('${s.summaryId}')">
                          <i data-lucide="printer" style="width:14px;height:14px;"></i> Print Summary
                        </button>
                      </div>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  /* ==========================================================================
     7. NOTIFICATIONS VIEW
     ========================================================================== */
  function renderNotificationsView() {
    pageTitle.textContent = "RECORD NOTIFICATIONS";
    pageSubtitle.textContent = "System Alerts & Verification Notices";

    contentContainer.innerHTML = `
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><i data-lucide="bell"></i> Notifications Center</div>
          <button class="btn btn-secondary btn-sm" onclick="window.markAllNotificationsRead()">
            <i data-lucide="check-check"></i> Mark All as Read
          </button>
        </div>
        <div class="panel-body">
          ${state.notifications.map(n => `
            <div style="display:flex; align-items:flex-start; gap:1rem; padding:1rem; border-radius:var(--radius-md); background:${n.unread ? 'var(--primary-light)' : '#ffffff'}; border:1px solid ${n.unread ? 'var(--primary-border)' : 'var(--border-color)'}; margin-bottom:0.75rem;">
              <div class="toast-icon toast-${n.type}">
                <i data-lucide="${n.type === 'urgent' ? 'alert-triangle' : (n.type === 'warning' ? 'clock' : 'info')}" style="width:16px;height:16px;"></i>
              </div>
              <div style="flex:1;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                  <strong style="font-size:0.88rem; color:var(--text-main);">${n.title}</strong>
                  <span style="font-size:0.72rem; color:var(--text-muted);">${n.time}</span>
                </div>
                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">${n.message}</div>
              </div>
            </div>
          `).join('')}
        </div>
      </div>
    `;
  }

  /* ==========================================================================
     8. AUDIT TRAIL VIEW
     ========================================================================== */
  function renderAuditTrailView() {
    pageTitle.textContent = "SYSTEM AUDIT TRAIL";
    pageSubtitle.textContent = "Non-deletable log of Medical Records Officer operations";

    contentContainer.innerHTML = `
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><i data-lucide="shield-check"></i> Officer Activity Logs</div>
          <div style="font-size:0.75rem; color:var(--text-muted);">
            <i data-lucide="lock" style="width:12px;height:12px;"></i> Immutable Audit Trail Compliance
          </div>
        </div>
        <div class="panel-body no-padding">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Audit ID</th>
                  <th>Date & Time</th>
                  <th>User & Role</th>
                  <th>Action</th>
                  <th>Target Record</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>
                ${state.auditLogs.map(log => `
                  <tr>
                    <td><span class="patient-id-badge" style="background:#f1f5f9;color:#475569;border-color:#cbd5e1;">${log.id}</span></td>
                    <td style="font-size:0.78rem;">${log.date} <span style="color:var(--text-muted);">${log.time}</span></td>
                    <td>
                      <div><strong>${log.user}</strong></div>
                      <div style="font-size:0.7rem; color:var(--primary);">${log.role}</div>
                    </td>
                    <td><span class="badge badge-verified">${log.action}</span></td>
                    <td><strong>${log.targetName || log.targetId}</strong></td>
                    <td style="font-size:0.78rem; color:var(--text-muted);">${log.details}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  /* ==========================================================================
     9. SETTINGS VIEW
     ========================================================================== */
  function renderSettingsView() {
    pageTitle.textContent = "SETTINGS & PREFERENCES";
    pageSubtitle.textContent = "Medical Records Department Configuration";

    contentContainer.innerHTML = `
      <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem;">
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><i data-lucide="sliders"></i> Department Settings</div>
          </div>
          <div class="panel-body">
            <div class="form-grid">
              <div class="form-group col-6">
                <label class="form-label">Medical Records Officer Name</label>
                <input type="text" class="form-control" value="${TMHIS_DATA.currentOfficer.name}" readonly>
              </div>
              <div class="form-group col-6">
                <label class="form-label">Professional License Number</label>
                <input type="text" class="form-control" value="${TMHIS_DATA.currentOfficer.licenseNo}" readonly>
              </div>
              <div class="form-group col-12">
                <label class="form-label">Department / Unit</label>
                <input type="text" class="form-control" value="${TMHIS_DATA.currentOfficer.department}" readonly>
              </div>
              <div class="form-group col-6">
                <label class="form-label">Default Summary Watermark</label>
                <select class="form-control">
                  <option selected>CONFIDENTIAL MEDICAL DOCUMENT</option>
                  <option>OFFICIAL HOSPITAL RECORD</option>
                  <option>FOR MEDICAL EVALUATION ONLY</option>
                </select>
              </div>
              <div class="form-group col-6">
                <label class="form-label">Auto-Archive Inactive Records (Years)</label>
                <input type="number" class="form-control" value="5">
              </div>
            </div>
            <div style="margin-top:1.5rem; display:flex; justify-content:flex-end;">
              <button class="btn btn-primary" onclick="window.saveSettings()">Save Preferences</button>
            </div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><i data-lucide="shield"></i> Security Policies</div>
          </div>
          <div class="panel-body">
            <div style="font-size:0.8rem; color:var(--text-muted); line-height:1.6;">
              <p><strong>Philippine Data Privacy Act (RA 10173):</strong> All medical records accessed or processed are strictly classified as Sensitive Personal Information.</p>
              <br>
              <p><strong>DOH AO No. 2020-0019:</strong> Standard medical records retention and disposal schedule strictly enforced.</p>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  /* ==========================================================================
     GLOBAL HELPER FUNCTIONS & WINDOW ATTACHMENTS
     ========================================================================== */

  function formatDate(dStr) {
    if (!dStr) return '-';
    try {
      const d = new Date(dStr);
      return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } catch {
      return dStr;
    }
  }

  // Filter setters
  window.setPatientFilter = (filter) => {
    state.patientFilter = filter;
    renderPatientRecords();
    refreshIcons();
  };

  window.setVerificationFilter = (filter) => {
    state.verificationFilter = filter;
    renderRecordVerification();
    refreshIcons();
  };

  window.setRequestFilter = (filter) => {
    state.requestFilter = filter;
    renderMedicalRecordRequests();
    refreshIcons();
  };

  window.setRequestPriorityFilter = (priority) => {
    state.requestPriorityFilter = priority;
    renderMedicalRecordRequests();
    refreshIcons();
  };

  window.selectHistoryPatient = (patientId) => {
    state.selectedPatientId = patientId;
    renderPatientHistory();
    refreshIcons();
  };

  window.setHistoryTab = (tab) => {
    state.selectedHistoryTab = tab;
    renderPatientHistory();
    refreshIcons();
  };

  window.hospityNavigate = (view, filter = null) => {
    if (filter && view === 'patients') {
      state.patientFilter = filter.toLowerCase();
    }
    navigateTo(view);
  };

  window.markAllNotificationsRead = () => {
    state.notifications.forEach(n => n.unread = false);
    renderNotificationsView();
    updateHeaderBadges();
    showToast("Notifications", "All notifications marked as read.", "info");
  };

  window.saveSettings = () => {
    showToast("Settings Saved", "Department preferences have been updated successfully.", "success");
    logAudit("Updated Settings", "SETTINGS-01", "Department Preferences", "Modified summary watermark and default retention settings");
  };

  /* ==========================================================================
     MODAL CONTROLLERS
     ========================================================================== */

  // Modal helper
  function openModal(modalId) {
    const el = document.getElementById(modalId);
    if (el) el.classList.add('show');
  }

  function closeModal(modalId) {
    const el = document.getElementById(modalId);
    if (el) el.classList.remove('show');
  }

  // Attach backdrop close handlers
  document.querySelectorAll('.modal-backdrop, .drawer-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', (e) => {
      if (e.target === backdrop) {
        backdrop.classList.remove('show');
      }
    });
  });

  document.querySelectorAll('.modal-close-btn, [data-close-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      const modal = btn.closest('.modal-backdrop, .drawer-backdrop');
      if (modal) modal.classList.remove('show');
    });
  });

  /* --------------------------------------------------------------------------
     A. CREATE PATIENT RECORD MODAL
     -------------------------------------------------------------------------- */
  window.openCreatePatientModal = () => {
    const form = document.getElementById('createPatientForm');
    if (form) form.reset();
    openModal('createPatientModal');
  };

  window.submitCreatePatientRecord = (e) => {
    e.preventDefault();
    const form = document.getElementById('createPatientForm');
    if (!form) return;

    const firstName = form.firstName.value.trim();
    const middleName = form.middleName.value.trim();
    const lastName = form.lastName.value.trim();
    const suffix = form.suffix.value.trim();
    const dob = form.dob.value;
    const gender = form.gender.value;
    const civilStatus = form.civilStatus.value;
    const contact = form.contact.value.trim();
    const email = form.email.value.trim();
    const address = form.address.value.trim();
    const emergencyName = form.emergencyName.value.trim();
    const emergencyRel = form.emergencyRel.value.trim();
    const emergencyContact = form.emergencyContact.value.trim();
    const registrationType = form.registrationType.value;

    if (!firstName || !lastName || !dob || !contact) {
      showToast("Validation Error", "Please fill in all mandatory fields.", "error");
      return;
    }

    // Calculate age
    const birthDate = new Date(dob);
    const ageDiff = Date.now() - birthDate.getTime();
    const ageDate = new Date(ageDiff);
    const calculatedAge = Math.abs(ageDate.getUTCFullYear() - 1970);

    const nextIdNum = state.patients.length + 1;
    const newId = `P-2026-${String(nextIdNum).padStart(3, '0')}`;
    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);

    const newPatient = {
      id: newId,
      firstName,
      middleName,
      lastName,
      suffix,
      dob,
      age: calculatedAge,
      gender,
      civilStatus,
      bloodType: "O+",
      contact,
      email,
      address,
      emergencyContact: {
        name: emergencyName,
        relationship: emergencyRel,
        contact: emergencyContact,
        address
      },
      registrationDate: dateStr,
      registrationType,
      registeredBy: TMHIS_DATA.currentOfficer.name,
      recordStatus: "Active",
      verificationStatus: "Pending Verification",
      verifiedBy: null,
      verifiedDate: null,
      lastUpdated: `${dateStr} ${timeStr}`,
      lastUpdatedBy: TMHIS_DATA.currentOfficer.name
    };

    state.patients.unshift(newPatient);

    // Add registration history
    state.registrationHistory.unshift({
      id: `REG-HIST-${Math.floor(100 + Math.random() * 900)}`,
      patientId: newId,
      patientName: `${firstName} ${lastName}`,
      date: `${dateStr} ${timeStr}`,
      eventType: "Patient Registered",
      details: `New patient record created under ${registrationType}. Master Patient Index: ${newId}.`,
      performedBy: `${TMHIS_DATA.currentOfficer.name} (Medical Records Officer)`
    });

    // Log Audit Trail
    logAudit("Created Patient Record", newId, `${firstName} ${lastName}`, `New patient master file created for ${registrationType}`);

    closeModal('createPatientModal');
    showToast("Record Created", `Patient record ${newId} for ${firstName} ${lastName} created successfully!`, "success");
    renderCurrentView();
  };

  /* --------------------------------------------------------------------------
     B. VIEW PATIENT RECORD (DRAWER)
     -------------------------------------------------------------------------- */
  window.viewPatientRecord = (patientId) => {
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient) return;

    const drawer = document.getElementById('viewPatientDrawer');
    const content = document.getElementById('viewPatientDrawerContent');
    if (!drawer || !content) return;

    content.innerHTML = `
      <div class="read-only-banner">
        <i data-lucide="lock" style="color:#b91c1c;"></i>
        <div>
          <strong style="color:#b91c1c;">🔒 CONFIDENTIAL MEDICAL RECORD</strong>
          <div style="font-size:0.72rem; color:#475569;">Confidential medical information. Access restricted to authorized personnel.</div>
        </div>
      </div>

      <div style="display:flex; align-items:center; gap:1rem; border-bottom:1px solid var(--border-color); padding-bottom:1rem;">
        <div class="patient-avatar-mini" style="width:48px; height:48px; font-size:1.1rem; background:linear-gradient(135deg,#3b82f6,#1d4ed8); color:#fff;">
          ${patient.firstName[0]}${patient.lastName[0]}
        </div>
        <div>
          <h3 style="font-family:var(--font-heading); font-size:1.15rem; font-weight:700; margin:0;">
            ${patient.firstName} ${patient.middleName ? patient.middleName[0] + '.' : ''} ${patient.lastName} ${patient.suffix || ''}
          </h3>
          <div style="display:flex; gap:0.5rem; align-items:center; margin-top:0.25rem;">
            <span class="patient-id-badge">${patient.id}</span>
            <span class="badge ${patient.recordStatus === 'Active' ? 'badge-active' : 'badge-archived'}">${patient.recordStatus}</span>
            <span class="badge ${patient.verificationStatus === 'Verified' ? 'badge-verified' : 'badge-pending'}">${patient.verificationStatus}</span>
          </div>
        </div>
      </div>

      <!-- Personal Information -->
      <div class="info-card-box">
        <div class="info-card-box-title"><i data-lucide="user"></i> Personal Information</div>
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Date of Birth</span>
            <span class="info-value">${formatDate(patient.dob)}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Age</span>
            <span class="info-value">${patient.age} years old</span>
          </div>
          <div class="info-item">
            <span class="info-label">Gender</span>
            <span class="info-value">${patient.gender}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Civil Status</span>
            <span class="info-value">${patient.civilStatus}</span>
          </div>
        </div>
      </div>

      <!-- Contact Information -->
      <div class="info-card-box">
        <div class="info-card-box-title"><i data-lucide="phone"></i> Contact Information</div>
        <div class="info-grid">
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Residential Address</span>
            <span class="info-value">${patient.address}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Contact Number</span>
            <span class="info-value">${patient.contact}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Email Address</span>
            <span class="info-value">${patient.email || 'N/A'}</span>
          </div>
        </div>
      </div>

      <!-- Emergency Contact -->
      <div class="info-card-box">
        <div class="info-card-box-title"><i data-lucide="heart-handshake"></i> Emergency Contact</div>
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Contact Name</span>
            <span class="info-value">${patient.emergencyContact?.name || 'N/A'}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Relationship</span>
            <span class="info-value">${patient.emergencyContact?.relationship || 'N/A'}</span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Contact Number</span>
            <span class="info-value">${patient.emergencyContact?.contact || 'N/A'}</span>
          </div>
        </div>
      </div>

      <!-- Registration Information -->
      <div class="info-card-box">
        <div class="info-card-box-title"><i data-lucide="file-check"></i> Registration Information</div>
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Registration Date</span>
            <span class="info-value">${formatDate(patient.registrationDate)}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Registered By</span>
            <span class="info-value">${patient.registeredBy || 'Medical Records Dept'}</span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Last Updated</span>
            <span class="info-value">${patient.lastUpdated} (${patient.lastUpdatedBy})</span>
          </div>
        </div>
      </div>

      <div style="display:flex; flex-direction:column; gap:0.5rem; margin-top:0.5rem;">
        <button class="btn btn-primary" onclick="window.editPatientRecord('${patient.id}'); document.getElementById('viewPatientDrawer').classList.remove('show');">
          <i data-lucide="edit-2"></i> Edit Record
        </button>
        <button class="btn btn-secondary" onclick="window.openVerificationModal('${patient.id}'); document.getElementById('viewPatientDrawer').classList.remove('show');">
          <i data-lucide="check-square"></i> Verify Record Accuracy
        </button>
        <button class="btn btn-secondary" onclick="window.viewRegistrationHistory('${patient.id}'); document.getElementById('viewPatientDrawer').classList.remove('show');">
          <i data-lucide="history"></i> View Registration History
        </button>
        <button class="btn btn-secondary" onclick="window.selectHistoryPatient('${patient.id}'); window.hospityNavigate('history'); document.getElementById('viewPatientDrawer').classList.remove('show');">
          <i data-lucide="clipboard"></i> View Medical History
        </button>
      </div>
    `;

    openModal('viewPatientDrawer');
    refreshIcons();
  };

  /* --------------------------------------------------------------------------
     C. EDIT PATIENT RECORD MODAL
     -------------------------------------------------------------------------- */
  window.editPatientRecord = (patientId) => {
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient) return;

    const form = document.getElementById('editPatientForm');
    if (!form) return;

    form.patientId.value = patient.id;
    form.firstName.value = patient.firstName;
    form.middleName.value = patient.middleName || '';
    form.lastName.value = patient.lastName;
    form.suffix.value = patient.suffix || '';
    form.dob.value = patient.dob;
    form.gender.value = patient.gender;
    form.civilStatus.value = patient.civilStatus;
    form.contact.value = patient.contact;
    form.email.value = patient.email || '';
    form.address.value = patient.address;
    form.emergencyName.value = patient.emergencyContact?.name || '';
    form.emergencyRel.value = patient.emergencyContact?.relationship || '';
    form.emergencyContact.value = patient.emergencyContact?.contact || '';

    document.getElementById('editPatientModalTitle').textContent = `EDIT PATIENT RECORD: ${patient.id}`;
    openModal('editPatientModal');
  };

  window.submitUpdatePatientRecord = (e) => {
    e.preventDefault();
    const form = document.getElementById('editPatientForm');
    if (!form) return;

    const id = form.patientId.value;
    const patient = state.patients.find(p => p.id === id);
    if (!patient) return;

    patient.firstName = form.firstName.value.trim();
    patient.middleName = form.middleName.value.trim();
    patient.lastName = form.lastName.value.trim();
    patient.suffix = form.suffix.value.trim();
    patient.dob = form.dob.value;
    patient.gender = form.gender.value;
    patient.civilStatus = form.civilStatus.value;
    patient.contact = form.contact.value.trim();
    patient.email = form.email.value.trim();
    patient.address = form.address.value.trim();
    patient.emergencyContact = {
      name: form.emergencyName.value.trim(),
      relationship: form.emergencyRel.value.trim(),
      contact: form.emergencyContact.value.trim(),
      address: form.address.value.trim()
    };

    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);

    patient.lastUpdated = `${dateStr} ${timeStr}`;
    patient.lastUpdatedBy = TMHIS_DATA.currentOfficer.name;

    // Add registration history entry
    state.registrationHistory.unshift({
      id: `REG-HIST-${Math.floor(100 + Math.random() * 900)}`,
      patientId: id,
      patientName: `${patient.firstName} ${patient.lastName}`,
      date: `${dateStr} ${timeStr}`,
      eventType: "Patient Information Updated",
      details: `Demographic and contact information updated by Medical Records Officer.`,
      performedBy: `${TMHIS_DATA.currentOfficer.name} (Medical Records Officer)`
    });

    logAudit("Updated Patient Record", id, `${patient.firstName} ${patient.lastName}`, "Demographic information and contact details updated");

    closeModal('editPatientModal');
    showToast("Record Updated", `Patient record ${id} updated successfully!`, "success");
    renderCurrentView();
  };

  /* --------------------------------------------------------------------------
     D. ARCHIVE PATIENT RECORD MODAL
     -------------------------------------------------------------------------- */
  window.openArchiveModal = (patientId) => {
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient) return;

    document.getElementById('archivePatientId').value = patient.id;
    document.getElementById('archivePatientNameDisplay').textContent = `${patient.firstName} ${patient.lastName} (${patient.id})`;
    document.getElementById('archiveReasonSelect').value = "Inactive Record";
    openModal('archivePatientModal');
  };

  window.confirmArchivePatientRecord = () => {
    const patientId = document.getElementById('archivePatientId').value;
    const reason = document.getElementById('archiveReasonSelect').value;
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient) return;

    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);

    patient.recordStatus = "Archived";
    patient.archiveReason = reason;
    patient.archivedDate = dateStr;
    patient.archivedBy = TMHIS_DATA.currentOfficer.name;
    patient.lastUpdated = `${dateStr} ${timeStr}`;
    patient.lastUpdatedBy = TMHIS_DATA.currentOfficer.name;

    logAudit("Archived Patient Record", patient.id, `${patient.firstName} ${patient.lastName}`, `Reason: ${reason}`);

    closeModal('archivePatientModal');
    showToast("Record Archived", `Patient record ${patient.id} moved to Archived status. It remains stored and searchable.`, "info");
    renderCurrentView();
  };

  /* --------------------------------------------------------------------------
     E. VERIFY RECORD MODAL & CORRECTION
     -------------------------------------------------------------------------- */
  window.openVerificationModal = (patientId) => {
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient) return;

    const modal = document.getElementById('verifyRecordModal');
    const content = document.getElementById('verifyRecordModalContent');
    if (!modal || !content) return;

    content.innerHTML = `
      <div style="background:var(--bg-body); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:1rem; margin-bottom:1.25rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h4 style="font-size:1rem; font-weight:700; color:var(--text-main); margin:0;">
              ${patient.firstName} ${patient.middleName ? patient.middleName[0] + '.' : ''} ${patient.lastName} ${patient.suffix || ''}
            </h4>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
              ID: <strong>${patient.id}</strong> | DOB: ${formatDate(patient.dob)} (${patient.age}y) | ${patient.gender}
            </div>
          </div>
          <span class="badge ${patient.verificationStatus === 'Verified' ? 'badge-verified' : 'badge-pending'}">${patient.verificationStatus}</span>
        </div>
      </div>

      <h5 style="font-size:0.82rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); margin-bottom:0.75rem; letter-spacing:0.05em;">
        Verification Checklist (7 Points of Accuracy)
      </h5>

      <div class="checklist-item checked">
        <input type="checkbox" id="chk_name" class="checklist-checkbox" checked>
        <label for="chk_name" class="checklist-label">1. Patient Full Legal Name & Suffix</label>
      </div>
      <div class="checklist-item checked">
        <input type="checkbox" id="chk_dob" class="checklist-checkbox" checked>
        <label for="chk_dob" class="checklist-label">2. Date of Birth & Calculated Age</label>
      </div>
      <div class="checklist-item checked">
        <input type="checkbox" id="chk_gender" class="checklist-checkbox" checked>
        <label for="chk_gender" class="checklist-label">3. Biological Gender & Civil Status</label>
      </div>
      <div class="checklist-item checked">
        <input type="checkbox" id="chk_contact" class="checklist-checkbox" checked>
        <label for="chk_contact" class="checklist-label">4. Primary Contact Number & Email</label>
      </div>
      <div class="checklist-item checked">
        <input type="checkbox" id="chk_address" class="checklist-checkbox" checked>
        <label for="chk_address" class="checklist-label">5. Residential Address & Barangay</label>
      </div>
      <div class="checklist-item checked">
        <input type="checkbox" id="chk_emergency" class="checklist-checkbox" checked>
        <label for="chk_emergency" class="checklist-label">6. Emergency Contact Person & Phone</label>
      </div>
      <div class="checklist-item checked">
        <input type="checkbox" id="chk_reginfo" class="checklist-checkbox" checked>
        <label for="chk_reginfo" class="checklist-label">7. Registration Type & Module Validity</label>
      </div>
    `;

    document.getElementById('verifyActionBtn').onclick = () => window.executeVerifyRecord(patient.id);
    document.getElementById('requestCorrectionBtn').onclick = () => window.openRequestCorrectionModal(patient.id);

    openModal('verifyRecordModal');
    refreshIcons();
  };

  window.executeVerifyRecord = (patientId) => {
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient) return;

    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);

    patient.verificationStatus = "Verified";
    patient.verifiedBy = TMHIS_DATA.currentOfficer.name;
    patient.verifiedDate = dateStr;
    patient.lastUpdated = `${dateStr} ${timeStr}`;
    patient.lastUpdatedBy = TMHIS_DATA.currentOfficer.name;

    // Add registration history
    state.registrationHistory.unshift({
      id: `REG-HIST-${Math.floor(100 + Math.random() * 900)}`,
      patientId: patient.id,
      patientName: `${patient.firstName} ${patient.lastName}`,
      date: `${dateStr} ${timeStr}`,
      eventType: "Record Verified",
      details: "Full 7-point patient demographic and registration checklist certified accurate.",
      performedBy: `${TMHIS_DATA.currentOfficer.name} (Medical Records Officer)`
    });

    logAudit("Verified Patient Record", patient.id, `${patient.firstName} ${patient.lastName}`, "Certified 7-point verification accuracy");

    closeModal('verifyRecordModal');
    showToast("Record Verified", `Patient record ${patient.id} marked as VERIFIED.`, "success");
    renderCurrentView();
  };

  window.openRequestCorrectionModal = (patientId) => {
    closeModal('verifyRecordModal');
    document.getElementById('correctionPatientId').value = patientId;
    document.getElementById('correctionReasonInput').value = "Contact information needs confirmation with patient.";
    openModal('correctionModal');
  };

  window.submitRequestCorrection = () => {
    const patientId = document.getElementById('correctionPatientId').value;
    const reason = document.getElementById('correctionReasonInput').value.trim();
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient || !reason) return;

    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);

    patient.verificationStatus = "Needs Correction";
    patient.lastUpdated = `${dateStr} ${timeStr}`;
    patient.lastUpdatedBy = TMHIS_DATA.currentOfficer.name;

    logAudit("Requested Record Correction", patient.id, `${patient.firstName} ${patient.lastName}`, `Correction note: ${reason}`);

    closeModal('correctionModal');
    showToast("Correction Flagged", `Record ${patient.id} set to 'Needs Correction'.`, "warning");
    renderCurrentView();
  };

  /* --------------------------------------------------------------------------
     F. PATIENT REGISTRATION HISTORY VIEW
     -------------------------------------------------------------------------- */
  window.viewRegistrationHistory = (patientId) => {
    const patient = state.patients.find(p => p.id === patientId);
    if (!patient) return;

    const historyItems = state.registrationHistory.filter(h => h.patientId === patientId);
    const modal = document.getElementById('registrationHistoryModal');
    const content = document.getElementById('registrationHistoryContent');
    if (!modal || !content) return;

    content.innerHTML = `
      <div style="margin-bottom:1.25rem;">
        <h4 style="font-size:1.05rem; font-weight:700; color:var(--text-main); margin:0;">
          ${patient.firstName} ${patient.lastName} (${patient.id})
        </h4>
        <div style="font-size:0.75rem; color:var(--text-muted);">Audit log of registration & demographic lifecycle</div>
      </div>

      <div class="timeline">
        ${historyItems.length === 0 ? `
          <div class="timeline-item">
            <div class="timeline-dot dot-info"></div>
            <div class="timeline-content">
              <div class="timeline-header">
                <span class="timeline-title">Patient Registered</span>
                <span class="timeline-date">${formatDate(patient.registrationDate)}</span>
              </div>
              <div class="timeline-desc">Initial patient record created by Medical Records Department.</div>
              <div class="timeline-meta">Performed By: ${patient.registeredBy || 'Medical Records Officer'}</div>
            </div>
          </div>
        ` : historyItems.map(h => `
          <div class="timeline-item">
            <div class="timeline-dot ${h.eventType.includes('Verified') ? 'dot-success' : (h.eventType.includes('Updated') ? 'dot-info' : 'dot-purple')}"></div>
            <div class="timeline-content">
              <div class="timeline-header">
                <span class="timeline-title">${h.eventType}</span>
                <span class="timeline-date">${h.date}</span>
              </div>
              <div class="timeline-desc">${h.details}</div>
              <div class="timeline-meta">Performed By: ${h.performedBy}</div>
            </div>
          </div>
        `).join('')}
      </div>
    `;

    openModal('registrationHistoryModal');
    refreshIcons();
  };

  /* --------------------------------------------------------------------------
     G. MEDICAL RECORD REQUEST PROCESSING WIZARD (5 STEPS)
     -------------------------------------------------------------------------- */
  window.openProcessWizard = (requestId) => {
    const request = state.requests.find(r => r.id === requestId);
    if (!request) return;

    state.wizard = {
      step: 1,
      requestId: request.id,
      requestData: request,
      patientData: state.patients.find(p => p.id === request.patientId) || state.patients[0],
      selectedRecords: {
        patientInfo: true,
        medicalHistory: true,
        consultationHistory: true,
        laboratoryHistory: true,
        treatmentHistory: true
      }
    };

    renderWizardStep();
    openModal('processRequestWizardModal');
  };

  function renderWizardStep() {
    const { step, requestData, patientData } = state.wizard;
    const container = document.getElementById('wizardBodyContainer');
    if (!container) return;

    // Update wizard step indicator circles
    for (let i = 1; i <= 5; i++) {
      const stepEl = document.getElementById(`wiz_step_${i}`);
      if (stepEl) {
        stepEl.classList.remove('active', 'completed');
        if (i < step) stepEl.classList.add('completed');
        if (i === step) stepEl.classList.add('active');
      }
    }

    let stepHtml = '';

    if (step === 1) {
      stepHtml = `
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:1rem; color:var(--primary);">
          STEP 1: Verify Patient & Identity
        </h4>
        <div class="info-card-box" style="margin-bottom:1.25rem;">
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Patient Full Name</span>
              <span class="info-value">${patientData.firstName} ${patientData.lastName}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Master Patient ID</span>
              <span class="info-value">${patientData.id}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Date of Birth</span>
              <span class="info-value">${formatDate(patientData.dob)} (${patientData.age}y)</span>
            </div>
            <div class="info-item">
              <span class="info-label">Verification Status</span>
              <span class="info-value badge ${patientData.verificationStatus === 'Verified' ? 'badge-verified' : 'badge-pending'}">${patientData.verificationStatus}</span>
            </div>
          </div>
        </div>
        <div class="checklist-item checked">
          <input type="checkbox" id="wiz_chk_identity" class="checklist-checkbox" checked>
          <label for="wiz_chk_identity" class="checklist-label">Patient identity confirmed against hospital Master Patient Index (MPI)</label>
        </div>
      `;
    } else if (step === 2) {
      stepHtml = `
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:1rem; color:var(--primary);">
          STEP 2: Verify Authorization & Request Legality
        </h4>
        <div class="info-card-box" style="margin-bottom:1.25rem;">
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Request ID</span>
              <span class="info-value">${requestData.id}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Requested By</span>
              <span class="info-value">${requestData.requestedBy}</span>
            </div>
            <div class="info-item" style="grid-column: span 2;">
              <span class="info-label">Purpose of Release</span>
              <span class="info-value">${requestData.purpose}</span>
            </div>
            <div class="info-item" style="grid-column: span 2;">
              <span class="info-label">Legal Authorization</span>
              <span class="info-value" style="color:var(--success); font-weight:700;">${requestData.authorizedSignature}</span>
            </div>
          </div>
        </div>
        <div class="checklist-item checked">
          <input type="checkbox" id="wiz_chk_legal" class="checklist-checkbox" checked>
          <label for="wiz_chk_legal" class="checklist-label">Signed patient consent or statutory subpoena duces tecum verified on file</label>
        </div>
      `;
    } else if (step === 3) {
      stepHtml = `
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:1rem; color:var(--primary);">
          STEP 3: Select Medical Records to Include
        </h4>
        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1rem;">
          Choose the clinical modules to bundle in this official release abstract:
        </p>
        <div class="checklist-item ${state.wizard.selectedRecords.patientInfo ? 'checked' : ''}">
          <input type="checkbox" id="wiz_rec_patient" class="checklist-checkbox" ${state.wizard.selectedRecords.patientInfo ? 'checked' : ''} onchange="window.toggleWizardRec('patientInfo', this.checked)">
          <label for="wiz_rec_patient" class="checklist-label">☑ Patient Demographic & Registration Information</label>
        </div>
        <div class="checklist-item ${state.wizard.selectedRecords.medicalHistory ? 'checked' : ''}">
          <input type="checkbox" id="wiz_rec_med" class="checklist-checkbox" ${state.wizard.selectedRecords.medicalHistory ? 'checked' : ''} onchange="window.toggleWizardRec('medicalHistory', this.checked)">
          <label for="wiz_rec_med" class="checklist-label">☑ Medical History (Conditions, Allergies, Surgical Procedures)</label>
        </div>
        <div class="checklist-item ${state.wizard.selectedRecords.consultationHistory ? 'checked' : ''}">
          <input type="checkbox" id="wiz_rec_con" class="checklist-checkbox" ${state.wizard.selectedRecords.consultationHistory ? 'checked' : ''} onchange="window.toggleWizardRec('consultationHistory', this.checked)">
          <label for="wiz_rec_con" class="checklist-label">☑ Consultation & Clinical Assessment Records</label>
        </div>
        <div class="checklist-item ${state.wizard.selectedRecords.laboratoryHistory ? 'checked' : ''}">
          <input type="checkbox" id="wiz_rec_lab" class="checklist-checkbox" ${state.wizard.selectedRecords.laboratoryHistory ? 'checked' : ''} onchange="window.toggleWizardRec('laboratoryHistory', this.checked)">
          <label for="wiz_rec_lab" class="checklist-label">☑ Laboratory & Diagnostic Reports</label>
        </div>
        <div class="checklist-item ${state.wizard.selectedRecords.treatmentHistory ? 'checked' : ''}">
          <input type="checkbox" id="wiz_rec_tx" class="checklist-checkbox" ${state.wizard.selectedRecords.treatmentHistory ? 'checked' : ''} onchange="window.toggleWizardRec('treatmentHistory', this.checked)">
          <label for="wiz_rec_tx" class="checklist-label">☑ Treatment & Medication Administration History</label>
        </div>
      `;
    } else if (step === 4) {
      stepHtml = `
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:0.75rem; color:var(--primary);">
          STEP 4: Document Summary Preview
        </h4>
        <div style="background:#ffffff; border:2px dashed var(--border-dark); border-radius:var(--radius-md); padding:1.5rem;">
          <div style="text-align:center; border-bottom:1px solid #cbd5e1; padding-bottom:0.75rem; margin-bottom:1rem;">
            <div style="font-family:var(--font-heading); font-weight:800; font-size:1.2rem; letter-spacing:1px; color:#0f172a;">TUPI MUNICIPAL HOSPITAL INFORMATION MANAGEMENT SYSTEM</div>
            <div style="font-size:0.75rem; color:#64748b;">Health Information & Records Management Department</div>
            <div style="font-size:0.9rem; font-weight:700; margin-top:0.35rem; color:#2563eb;">OFFICIAL MEDICAL RECORD SUMMARY</div>
          </div>
          <div style="font-size:0.8rem; line-height:1.6;">
            <div><strong>Patient Name:</strong> ${patientData.firstName} ${patientData.lastName} (ID: ${patientData.id})</div>
            <div><strong>Date of Birth:</strong> ${formatDate(patientData.dob)} | <strong>Gender:</strong> ${patientData.gender}</div>
            <div><strong>Request Reference:</strong> ${requestData.id} (${requestData.requestType})</div>
            <div><strong>Requesting Party:</strong> ${requestData.requestedBy} - ${requestData.purpose}</div>
            <div><strong>Included Components:</strong> ${Object.keys(state.wizard.selectedRecords).filter(k => state.wizard.selectedRecords[k]).join(', ')}</div>
            <div style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid #e2e8f0; color:#059669; font-weight:600;">
              ✓ Compiled and digitally sealed by: ${TMHIS_DATA.currentOfficer.name} (Medical Records Officer)
            </div>
          </div>
        </div>
      `;
    } else if (step === 5) {
      stepHtml = `
        <div style="text-align:center; padding:1.5rem 0;">
          <div style="width:54px; height:54px; border-radius:50%; background:var(--success-light); color:var(--success); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
            <i data-lucide="check" style="width:28px;height:28px;"></i>
          </div>
          <h3 style="font-family:var(--font-heading); font-size:1.25rem; font-weight:700; margin-bottom:0.5rem;">
            STEP 5: Complete Request & Release
          </h3>
          <p style="font-size:0.85rem; color:var(--text-muted); max-width:460px; margin:0 auto 1.5rem;">
            The medical record summary has been successfully assembled. You can now finalize the request status to <strong>Ready for Release</strong> or <strong>Completed</strong>.
          </p>
          <div style="display:inline-flex; gap:1rem;">
            <button class="btn btn-success" onclick="window.finalizeWizardRequest('Completed')">
              <i data-lucide="check-circle"></i> Mark as Completed & Release
            </button>
            <button class="btn btn-secondary" onclick="window.finalizeWizardRequest('Ready')">
              <i data-lucide="file-check"></i> Set Status to Ready
            </button>
          </div>
        </div>
      `;
    }

    container.innerHTML = stepHtml;

    // Wizard footer buttons
    document.getElementById('wizBackBtn').style.display = step > 1 && step < 5 ? 'inline-flex' : 'none';
    document.getElementById('wizNextBtn').style.display = step < 5 ? 'inline-flex' : 'none';

    refreshIcons();
  }

  window.toggleWizardRec = (key, val) => {
    state.wizard.selectedRecords[key] = val;
  };

  window.wizardNextStep = () => {
    if (state.wizard.step < 5) {
      state.wizard.step++;
      renderWizardStep();
    }
  };

  window.wizardPrevStep = () => {
    if (state.wizard.step > 1) {
      state.wizard.step--;
      renderWizardStep();
    }
  };

  window.finalizeWizardRequest = (newStatus) => {
    const { requestData, patientData } = state.wizard;
    const req = state.requests.find(r => r.id === requestData.id);
    if (req) {
      req.status = newStatus;
    }

    // Add generated summary
    const summaryId = `MRS-2026-${String(state.summaries.length + 1).padStart(3, '0')}`;
    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);

    state.summaries.unshift({
      summaryId,
      patientId: patientData.id,
      patientName: `${patientData.firstName} ${patientData.lastName}`,
      generatedDate: `${dateStr} ${timeStr}`,
      generatedBy: TMHIS_DATA.currentOfficer.name,
      purpose: requestData.purpose,
      sectionsIncluded: Object.keys(state.wizard.selectedRecords).filter(k => state.wizard.selectedRecords[k]),
      status: "Official / Released"
    });

    logAudit("Processed Medical Record Request", req.id, `${patientData.firstName} ${patientData.lastName}`, `5-step request processed. Status updated to ${newStatus}. Generated ${summaryId}`);

    closeModal('processRequestWizardModal');
    showToast("Request Processed", `Request ${req.id} finalized as ${newStatus}. Summary ${summaryId} created!`, "success");
    renderCurrentView();
  };

  /* --------------------------------------------------------------------------
     H. VIEW REQUEST DETAILS & UPDATE STATUS
     -------------------------------------------------------------------------- */
  window.viewRecordRequest = (requestId) => {
    const req = state.requests.find(r => r.id === requestId);
    if (!req) return;

    const modal = document.getElementById('viewRequestModal');
    const content = document.getElementById('viewRequestModalContent');
    if (!modal || !content) return;

    content.innerHTML = `
      <div class="info-card-box" style="margin-bottom:1.25rem;">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Request ID</span>
            <span class="info-value">${req.id}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Status</span>
            <span class="info-value"><span class="badge badge-${req.status.toLowerCase()}">${req.status}</span></span>
          </div>
          <div class="info-item">
            <span class="info-label">Patient Name & ID</span>
            <span class="info-value">${req.patientName} (${req.patientId})</span>
          </div>
          <div class="info-item">
            <span class="info-label">Priority</span>
            <span class="info-value"><span class="badge ${req.priority === 'Urgent' ? 'badge-urgent' : 'badge-normal'}">${req.priority}</span></span>
          </div>
          <div class="info-item">
            <span class="info-label">Requested By</span>
            <span class="info-value">${req.requestedBy}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Requesting Entity</span>
            <span class="info-value">${req.requestingEntity}</span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Purpose</span>
            <span class="info-value">${req.purpose}</span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Legal Authorization</span>
            <span class="info-value" style="color:var(--success);">${req.authorizedSignature}</span>
          </div>
        </div>
      </div>

      <h5 style="font-size:0.82rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); margin-bottom:0.75rem;">
        Requested Records Checklist
      </h5>
      ${req.requestedRecords.map(r => `
        <div class="checklist-item checked" style="padding:0.5rem 0.85rem; margin-bottom:0.4rem;">
          <i data-lucide="check-circle" style="width:16px;height:16px;color:var(--success);"></i>
          <span style="font-size:0.82rem; font-weight:600;">${r}</span>
        </div>
      `).join('')}
    `;

    document.getElementById('viewReqProcessBtn').onclick = () => {
      closeModal('viewRequestModal');
      window.openProcessWizard(req.id);
    };

    document.getElementById('viewReqStatusBtn').onclick = () => {
      closeModal('viewRequestModal');
      window.openUpdateRequestStatusModal(req.id);
    };

    document.getElementById('viewReqPatientBtn').onclick = () => {
      closeModal('viewRequestModal');
      window.viewPatientRecord(req.patientId);
    };

    openModal('viewRequestModal');
    refreshIcons();
  };

  window.openUpdateRequestStatusModal = (requestId) => {
    const req = state.requests.find(r => r.id === requestId);
    if (!req) return;

    document.getElementById('statusRequestId').value = req.id;
    document.getElementById('statusRequestSelect').value = req.status;
    document.getElementById('rejectionReasonGroup').style.display = req.status === 'Rejected' ? 'block' : 'none';

    document.getElementById('statusRequestSelect').onchange = (e) => {
      document.getElementById('rejectionReasonGroup').style.display = e.target.value === 'Rejected' ? 'block' : 'none';
    };

    openModal('updateRequestStatusModal');
  };

  window.submitUpdateRequestStatus = () => {
    const id = document.getElementById('statusRequestId').value;
    const newStatus = document.getElementById('statusRequestSelect').value;
    const reason = document.getElementById('rejectionReasonText').value.trim();

    const req = state.requests.find(r => r.id === id);
    if (!req) return;

    if (newStatus === 'Rejected' && !reason) {
      showToast("Validation Error", "Please provide a reason for rejecting the request.", "error");
      return;
    }

    req.status = newStatus;
    if (newStatus === 'Rejected') {
      req.rejectionReason = reason;
    }

    logAudit("Updated Medical Record Request Status", req.id, req.patientName, `Status changed to ${newStatus}${newStatus === 'Rejected' ? ' (Reason: ' + reason + ')' : ''}`);

    closeModal('updateRequestStatusModal');
    showToast("Status Updated", `Request ${req.id} status updated to ${newStatus}.`, "success");
    renderCurrentView();
  };

  /* --------------------------------------------------------------------------
     I. CLINICAL DETAILS MODALS (READ-ONLY)
     -------------------------------------------------------------------------- */
  window.viewConsultationDetails = (conId) => {
    const con = state.consultations.find(c => c.id === conId);
    if (!con) return;

    const modal = document.getElementById('clinicalDetailModal');
    const title = document.getElementById('clinicalDetailModalTitle');
    const content = document.getElementById('clinicalDetailModalContent');
    if (!modal || !content) return;

    title.innerHTML = `<i data-lucide="stethoscope"></i> CONSULTATION DETAILS: ${con.id}`;
    content.innerHTML = `
      <div class="read-only-banner">
        <i data-lucide="lock"></i>
        <span>Clinical assessment entered by attending physician. Read-only view for Medical Records Officer.</span>
      </div>

      <div class="info-card-box">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Attending Doctor</span>
            <span class="info-value">${con.doctor}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Department</span>
            <span class="info-value">${con.department}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Date & Time</span>
            <span class="info-value">${formatDate(con.date)} at ${con.time}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Vital Signs</span>
            <span class="info-value" style="font-family:var(--font-mono); font-size:0.78rem;">${con.vitalSigns}</span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Chief Complaint</span>
            <span class="info-value">${con.chiefComplaint}</span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Clinical Diagnosis</span>
            <span class="info-value" style="color:var(--primary); font-weight:700;">${con.diagnosis}</span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Treatment Plan / Orders</span>
            <span class="info-value">${con.treatment}</span>
          </div>
        </div>
      </div>
    `;

    openModal('clinicalDetailModal');
    refreshIcons();
  };

  window.viewLabReport = (labId) => {
    const lab = state.laboratories.find(l => l.id === labId);
    if (!lab) return;

    const modal = document.getElementById('clinicalDetailModal');
    const title = document.getElementById('clinicalDetailModalTitle');
    const content = document.getElementById('clinicalDetailModalContent');
    if (!modal || !content) return;

    title.innerHTML = `<i data-lucide="flask-conical"></i> LABORATORY REPORT: ${lab.id}`;
    content.innerHTML = `
      <div class="read-only-banner">
        <i data-lucide="lock"></i>
        <span>Official laboratory result from Clinical Laboratory Department. Read-only verification copy.</span>
      </div>

      <div class="info-card-box" style="margin-bottom:1rem;">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Test Name</span>
            <span class="info-value">${lab.testName}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Section</span>
            <span class="info-value">${lab.department}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Requesting Doctor</span>
            <span class="info-value">${lab.requestingDoctor}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Date Completed</span>
            <span class="info-value">${formatDate(lab.date)}</span>
          </div>
        </div>
      </div>

      <table class="table" style="margin-bottom:1rem; border:1px solid var(--border-color); border-radius:var(--radius-md);">
        <thead>
          <tr>
            <th>Parameter / Analyte</th>
            <th>Patient Result</th>
            <th>Reference Range</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          ${lab.results.map(r => `
            <tr>
              <td><strong>${r.parameter}</strong></td>
              <td style="font-weight:700; color:var(--primary);">${r.value}</td>
              <td>${r.reference}</td>
              <td><span class="badge ${r.status === 'Normal' || r.status === 'Desirable' || r.status === 'Optimal' ? 'badge-verified' : 'badge-urgent'}">${r.status}</span></td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-muted); border-top:1px solid var(--border-color); padding-top:0.75rem;">
        <span>Pathologist: <strong>${lab.pathologist}</strong></span>
        <span>Medical Technologist: <strong>${lab.medTech}</strong></span>
      </div>
    `;

    openModal('clinicalDetailModal');
    refreshIcons();
  };

  window.viewTreatmentDetails = (txId) => {
    const tx = state.treatments.find(t => t.id === txId);
    if (!tx) return;

    const modal = document.getElementById('clinicalDetailModal');
    const title = document.getElementById('clinicalDetailModalTitle');
    const content = document.getElementById('clinicalDetailModalContent');
    if (!modal || !content) return;

    title.innerHTML = `<i data-lucide="pill"></i> TREATMENT RECORD: ${tx.id}`;
    content.innerHTML = `
      <div class="read-only-banner">
        <i data-lucide="lock"></i>
        <span>Clinical treatment protocol recorded by healthcare team. Read-only view for records administration.</span>
      </div>

      <div class="info-card-box">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Treatment Protocol</span>
            <span class="info-value">${tx.treatment}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Prescribing Physician</span>
            <span class="info-value">${tx.doctor}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Department</span>
            <span class="info-value">${tx.department}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Status</span>
            <span class="info-value"><span class="badge badge-active">${tx.status}</span></span>
          </div>
          <div class="info-item" style="grid-column: span 2;">
            <span class="info-label">Administration & Nursing Notes</span>
            <span class="info-value">${tx.notes}</span>
          </div>
        </div>
      </div>
    `;

    openModal('clinicalDetailModal');
    refreshIcons();
  };

  /* --------------------------------------------------------------------------
     J. GENERATE MEDICAL RECORD SUMMARY MODAL & PRINT
     -------------------------------------------------------------------------- */
  window.openGenerateSummaryModal = (patientId = null) => {
    const select = document.getElementById('genSummaryPatientSelect');
    if (select) {
      select.innerHTML = state.patients.map(p => `
        <option value="${p.id}" ${patientId === p.id ? 'selected' : ''}>${p.id} - ${p.firstName} ${p.lastName}</option>
      `).join('');
    }
    openModal('generateSummaryModal');
  };

  window.executeGenerateSummary = (e) => {
    e.preventDefault();
    const patientId = document.getElementById('genSummaryPatientSelect').value;
    const purpose = document.getElementById('genSummaryPurpose').value.trim() || 'General Medical Record Abstract';
    const patient = state.patients.find(p => p.id === patientId) || state.patients[0];

    const sections = [];
    if (document.getElementById('gen_chk_patient').checked) sections.push("Patient Information");
    if (document.getElementById('gen_chk_reg').checked) sections.push("Registration Information");
    if (document.getElementById('gen_chk_med').checked) sections.push("Medical History");
    if (document.getElementById('gen_chk_con').checked) sections.push("Consultation History");
    if (document.getElementById('gen_chk_lab').checked) sections.push("Laboratory History");
    if (document.getElementById('gen_chk_tx').checked) sections.push("Treatment History");

    const summaryId = `MRS-2026-${String(state.summaries.length + 1).padStart(3, '0')}`;
    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);

    const newSummary = {
      summaryId,
      patientId: patient.id,
      patientName: `${patient.firstName} ${patient.lastName}`,
      generatedDate: `${dateStr} ${timeStr}`,
      generatedBy: TMHIS_DATA.currentOfficer.name,
      purpose,
      sectionsIncluded: sections,
      status: "Official / Released"
    };

    state.summaries.unshift(newSummary);
    logAudit("Generated Medical Record Summary", summaryId, `${patient.firstName} ${patient.lastName}`, `Generated official summary for ${purpose}`);

    closeModal('generateSummaryModal');
    showToast("Summary Generated", `Medical Record Summary ${summaryId} generated successfully!`, "success");
    window.previewSummaryDocument(summaryId);
  };

  window.previewSummaryDocument = (summaryId) => {
    const summary = state.summaries.find(s => s.summaryId === summaryId) || state.summaries[0];
    const patient = state.patients.find(p => p.id === summary.patientId) || state.patients[0];
    const medHistory = TMHIS_DATA.patientMedicalHistories[patient.id] || TMHIS_DATA.patientMedicalHistories["P-2026-001"];
    const patientConsultations = state.consultations.filter(c => c.patientId === patient.id);
    const patientLabs = state.laboratories.filter(l => l.patientId === patient.id);
    const patientTreatments = state.treatments.filter(t => t.patientId === patient.id);

    const modal = document.getElementById('previewSummaryModal');
    const content = document.getElementById('previewSummaryModalContent');
    if (!modal || !content) return;

    content.innerHTML = `
      <div style="background:#ffffff; padding:2rem; border:1px solid var(--border-color); border-radius:var(--radius-md); box-shadow:var(--shadow-sm);">
        
        <!-- Header -->
        <div style="text-align:center; border-bottom:2px solid #0f172a; padding-bottom:1rem; margin-bottom:1.5rem;">
          <div style="font-family:var(--font-heading); font-size:1.5rem; font-weight:800; color:#0f172a;">TUPI MUNICIPAL HOSPITAL INFORMATION MANAGEMENT SYSTEM</div>
          <div style="font-size:0.8rem; color:#64748b;">HEALTH INFORMATION & RECORDS MANAGEMENT DEPARTMENT</div>
          <div style="font-size:0.75rem; color:#64748b;">DOH Tertiary Hospital Accreditation No. NCR-2026-8819 | ISO 9001:2015 Certified</div>
          <div style="margin-top:0.75rem; font-family:var(--font-heading); font-size:1.15rem; font-weight:700; color:#2563eb; letter-spacing:0.05em; text-transform:uppercase;">
            MEDICAL RECORD SUMMARY
          </div>
          <div style="font-size:0.75rem; color:#64748b;">Document Ref: <strong>${summary.summaryId}</strong> | Issued: ${summary.generatedDate}</div>
        </div>

        <!-- Purpose -->
        <div style="background:var(--bg-body); padding:0.6rem 1rem; border-radius:var(--radius-sm); margin-bottom:1.25rem; font-size:0.82rem;">
          <strong>Purpose of Release:</strong> ${summary.purpose}
        </div>

        <!-- Patient Demographics Section -->
        <div class="print-section">
          <div style="font-weight:700; font-size:0.9rem; text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:0.25rem; margin-bottom:0.5rem; color:#0f172a;">
            I. Patient Information
          </div>
          <div class="info-grid" style="font-size:0.85rem;">
            <div><strong>Patient Name:</strong> ${patient.firstName} ${patient.middleName || ''} ${patient.lastName} ${patient.suffix || ''}</div>
            <div><strong>Patient ID:</strong> ${patient.id}</div>
            <div><strong>Date of Birth:</strong> ${formatDate(patient.dob)} (${patient.age}y)</div>
            <div><strong>Gender:</strong> ${patient.gender}</div>
            <div><strong>Civil Status:</strong> ${patient.civilStatus}</div>
            <div><strong>Contact:</strong> ${patient.contact}</div>
            <div style="grid-column: span 2;"><strong>Address:</strong> ${patient.address}</div>
          </div>
        </div>

        <!-- Medical History Section -->
        ${summary.sectionsIncluded.includes("Medical History") ? `
          <div class="print-section" style="margin-top:1.25rem;">
            <div style="font-weight:700; font-size:0.9rem; text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:0.25rem; margin-bottom:0.5rem; color:#0f172a;">
              II. Medical History & Known Conditions
            </div>
            <div style="font-size:0.82rem; margin-bottom:0.5rem;">
              <strong>Active / Past Conditions:</strong>
              <ul style="padding-left:1.25rem; margin-top:0.25rem;">
                ${medHistory.conditions.map(c => `<li>${c.condition} (Diagnosed: ${formatDate(c.diagnosedDate)} by ${c.diagnosedBy}) - <em>${c.status}</em></li>`).join('')}
              </ul>
            </div>
            <div style="font-size:0.82rem;">
              <strong>Known Drug & Environmental Allergies:</strong>
              <ul style="padding-left:1.25rem; margin-top:0.25rem;">
                ${medHistory.allergies.map(a => `<li style="color:#b91c1c;">${a.allergen} (Reaction: ${a.reaction}, Severity: ${a.severity})</li>`).join('')}
              </ul>
            </div>
          </div>
        ` : ''}

        <!-- Consultation History Section -->
        ${summary.sectionsIncluded.includes("Consultation History") && patientConsultations.length > 0 ? `
          <div class="print-section" style="margin-top:1.25rem;">
            <div style="font-weight:700; font-size:0.9rem; text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:0.25rem; margin-bottom:0.5rem; color:#0f172a;">
              III. Consultation History
            </div>
            <table class="table" style="font-size:0.8rem; border:1px solid #cbd5e1;">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Doctor</th>
                  <th>Department</th>
                  <th>Diagnosis</th>
                  <th>Treatment Plan</th>
                </tr>
              </thead>
              <tbody>
                ${patientConsultations.map(c => `
                  <tr>
                    <td>${formatDate(c.date)}</td>
                    <td>${c.doctor}</td>
                    <td>${c.department}</td>
                    <td><strong>${c.diagnosis}</strong></td>
                    <td>${c.treatment}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : ''}

        <!-- Laboratory History Section -->
        ${summary.sectionsIncluded.includes("Laboratory History") && patientLabs.length > 0 ? `
          <div class="print-section" style="margin-top:1.25rem;">
            <div style="font-weight:700; font-size:0.9rem; text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:0.25rem; margin-bottom:0.5rem; color:#0f172a;">
              IV. Laboratory & Diagnostic History
            </div>
            <table class="table" style="font-size:0.8rem; border:1px solid #cbd5e1;">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Test Name</th>
                  <th>Requesting Doctor</th>
                  <th>Department</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                ${patientLabs.map(l => `
                  <tr>
                    <td>${formatDate(l.date)}</td>
                    <td><strong>${l.testName}</strong></td>
                    <td>${l.requestingDoctor}</td>
                    <td>${l.department}</td>
                    <td>${l.resultStatus} (${l.reportStatus})</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : ''}

        <!-- Treatment History Section -->
        ${summary.sectionsIncluded.includes("Treatment History") && patientTreatments.length > 0 ? `
          <div class="print-section" style="margin-top:1.25rem;">
            <div style="font-weight:700; font-size:0.9rem; text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:0.25rem; margin-bottom:0.5rem; color:#0f172a;">
              V. Treatment & Medication History
            </div>
            <table class="table" style="font-size:0.8rem; border:1px solid #cbd5e1;">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Treatment / Medication</th>
                  <th>Prescribing Doctor</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                ${patientTreatments.map(t => `
                  <tr>
                    <td>${formatDate(t.date)}</td>
                    <td><strong>${t.treatment}</strong></td>
                    <td>${t.doctor}</td>
                    <td>${t.status}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : ''}

        <!-- Signature Block -->
        <div style="margin-top:2.5rem; display:flex; justify-content:space-between; align-items:flex-end; font-size:0.82rem; border-top:1px solid #cbd5e1; padding-top:1.5rem;">
          <div>
            <div><strong>CONFIDENTIALITY NOTICE:</strong></div>
            <div style="font-size:0.72rem; color:#64748b; max-width:340px;">
              This medical document is issued under the strict confidentiality provisions of the Philippine Data Privacy Act of 2012 (RA 10173).
            </div>
          </div>
          <div style="text-align:center; min-width:240px;">
            <div style="font-family:'Brush Script MT', cursive; font-size:1.3rem; color:#1e40af; margin-bottom:2px;">M. Valenzuela</div>
            <div style="border-bottom:1px solid #000; margin-bottom:4px;"></div>
            <strong>${summary.generatedBy}</strong>
            <div style="font-size:0.72rem; color:#64748b;">${TMHIS_DATA.currentOfficer.title}</div>
            <div style="font-size:0.7rem; color:#64748b;">License No. ${TMHIS_DATA.currentOfficer.licenseNo}</div>
          </div>
        </div>

      </div>
    `;

    document.getElementById('previewSummaryPrintBtn').onclick = () => window.printSummaryDocument(summary.summaryId);

    openModal('previewSummaryModal');
    refreshIcons();
  };

  window.printSummaryDocument = (summaryId) => {
    const summary = state.summaries.find(s => s.summaryId === summaryId) || state.summaries[0];
    const patient = state.patients.find(p => p.id === summary.patientId) || state.patients[0];
    const medHistory = TMHIS_DATA.patientMedicalHistories[patient.id] || TMHIS_DATA.patientMedicalHistories["P-2026-001"];
    const patientConsultations = state.consultations.filter(c => c.patientId === patient.id);
    const patientLabs = state.laboratories.filter(l => l.patientId === patient.id);
    const patientTreatments = state.treatments.filter(t => t.patientId === patient.id);

    // Populate dedicated print container
    const printContainer = document.getElementById('printSummaryContainer');
    if (!printContainer) return;

    printContainer.innerHTML = `
      <div class="print-document-header">
        <div class="print-hospital-name">Tupi Municipal Hospital</div>
        <div class="print-hospital-sub">HEALTHCARE INFORMATION SYSTEM • HEALTH INFORMATION & RECORDS MANAGEMENT</div>
        <div class="print-hospital-sub">DOH Tertiary Hospital Accreditation No. NCR-2026-8819 | Republic of the Philippines</div>
        <div class="print-doc-title">MEDICAL RECORD SUMMARY</div>
        <div style="font-size:9pt; color:#475569;">Summary Ref: <strong>${summary.summaryId}</strong> | Generated Date: ${summary.generatedDate}</div>
      </div>

      <div class="print-section">
        <div class="print-section-title">Patient Information</div>
        <div class="print-grid">
          <div><strong>Patient Name:</strong> ${patient.firstName} ${patient.middleName || ''} ${patient.lastName} ${patient.suffix || ''}</div>
          <div><strong>Patient ID:</strong> ${patient.id}</div>
          <div><strong>Date of Birth:</strong> ${formatDate(patient.dob)} (${patient.age}y)</div>
          <div><strong>Gender:</strong> ${patient.gender}</div>
          <div><strong>Civil Status:</strong> ${patient.civilStatus}</div>
          <div><strong>Contact:</strong> ${patient.contact}</div>
          <div style="grid-column: span 2;"><strong>Address:</strong> ${patient.address}</div>
        </div>
      </div>

      ${summary.sectionsIncluded.includes("Medical History") ? `
        <div class="print-section">
          <div class="print-section-title">Medical History</div>
          <div style="font-size:9pt; margin-bottom:4px;">
            <strong>Diagnoses:</strong>
            ${medHistory.conditions.map(c => `${c.condition} (${formatDate(c.diagnosedDate)})`).join('; ')}
          </div>
          <div style="font-size:9pt;">
            <strong>Allergies:</strong>
            ${medHistory.allergies.map(a => `${a.allergen} (${a.reaction})`).join('; ')}
          </div>
        </div>
      ` : ''}

      ${summary.sectionsIncluded.includes("Consultation History") && patientConsultations.length > 0 ? `
        <div class="print-section">
          <div class="print-section-title">Consultation History</div>
          <table class="print-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Doctor</th>
                <th>Department</th>
                <th>Diagnosis</th>
                <th>Treatment</th>
              </tr>
            </thead>
            <tbody>
              ${patientConsultations.map(c => `
                <tr>
                  <td>${formatDate(c.date)}</td>
                  <td>${c.doctor}</td>
                  <td>${c.department}</td>
                  <td>${c.diagnosis}</td>
                  <td>${c.treatment}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      ` : ''}

      ${summary.sectionsIncluded.includes("Laboratory History") && patientLabs.length > 0 ? `
        <div class="print-section">
          <div class="print-section-title">Laboratory History</div>
          <table class="print-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Test Name</th>
                <th>Requesting Doctor</th>
                <th>Department</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${patientLabs.map(l => `
                <tr>
                  <td>${formatDate(l.date)}</td>
                  <td>${l.testName}</td>
                  <td>${l.requestingDoctor}</td>
                  <td>${l.department}</td>
                  <td>${l.resultStatus}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      ` : ''}

      ${summary.sectionsIncluded.includes("Treatment History") && patientTreatments.length > 0 ? `
        <div class="print-section">
          <div class="print-section-title">Treatment History</div>
          <table class="print-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Treatment</th>
                <th>Prescribing Doctor</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${patientTreatments.map(t => `
                <tr>
                  <td>${formatDate(t.date)}</td>
                  <td>${t.treatment}</td>
                  <td>${t.doctor}</td>
                  <td>${t.status}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      ` : ''}

      <div class="print-footer">
        <div>
          <div><strong>Generated By:</strong> ${summary.generatedBy}</div>
          <div><strong>Title:</strong> ${TMHIS_DATA.currentOfficer.title}</div>
          <div><strong>Date & Time:</strong> ${summary.generatedDate}</div>
          <div style="font-size:7.5pt; color:#64748b; margin-top:8px; max-width:320px;">
            🔒 CONFIDENTIAL MEDICAL DOCUMENT - STRICTLY FOR AUTHORIZED MEDICAL & ADMINISTRATIVE USE.
          </div>
        </div>
        <div class="print-signature-box">
          <div class="signature-line"></div>
          <div><strong>Authorized Officer Signature</strong></div>
          <div style="font-size:8pt; color:#64748b;">Health Information & Records Dept</div>
        </div>
      </div>
    `;

    // Trigger Print
    window.print();
  };

  // Initial Load
  renderCurrentView();
});
