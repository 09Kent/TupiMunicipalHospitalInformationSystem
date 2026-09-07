/**
 * Tupi Municipal Hospital Information Management System
 * Hospital Chief / Medical Director Dashboard (Role 2)
 * Client-Side Application Controller
 */

// ===== GLOBAL STATE =====
let currentView = 'dashboard';
let currentReportCode = '';
let chartInstances = {};
let staffFilters = {};
let docFilters = {};

// ===== SIDEBAR =====
function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const mw = document.getElementById('mainWrap');
  sb.classList.toggle('collapsed');
  mw.classList.toggle('expanded');
  localStorage.setItem('sidebar_collapsed', sb.classList.contains('collapsed') ? '1' : '0');
}

function openMobileSidebar() {
  document.getElementById('sidebar').classList.add('mobile-open');
}

function closeMobileSidebar() {
  document.getElementById('sidebar').classList.remove('mobile-open');
}

document.getElementById('sidebarToggle')?.addEventListener('click', toggleSidebar);

// Restore sidebar state
if (localStorage.getItem('sidebar_collapsed') === '1') {
  document.getElementById('sidebar')?.classList.add('collapsed');
  document.getElementById('mainWrap')?.classList.add('expanded');
}

// ===== USER POPOVER =====
function toggleUserPopover() {
  const pop = document.getElementById('userPopover');
  pop.classList.toggle('show');
}

document.addEventListener('click', (e) => {
  const pop = document.getElementById('userPopover');
  const trig = document.getElementById('userTrigger');
  if (pop && !pop.contains(e.target) && !trig.contains(e.target)) {
    pop.classList.remove('show');
  }
  // Also close export menu
  const em = document.getElementById('exportMenu');
  if (em && !em.parentElement.contains(e.target)) {
    em.classList.remove('show');
  }
  // Close notif panel
  const np = document.getElementById('notifPanel');
  const nb = document.getElementById('notifBellBtn');
  if (np && !np.contains(e.target) && nb && !nb.contains(e.target)) {
    np.classList.remove('show');
  }
});

// ===== VIEW SWITCHING =====
function switchView(viewName, btn) {
  document.querySelectorAll('.view-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));

  const target = document.getElementById('view-' + viewName);
  if (target) {
    target.classList.add('active');
    // Re-trigger animation
    target.style.animation = 'none';
    target.offsetHeight; // reflow
    target.style.animation = '';
  }

  if (btn) btn.classList.add('active');
  currentView = viewName;

  // Close mobile sidebar
  closeMobileSidebar();

  // Load data for specific views
  if (viewName === 'dashboard') loadDashboard();
  else if (viewName === 'reports') loadReports();
  else if (viewName === 'staff-activity') loadStaffActivity();
  else if (viewName === 'department-performance') loadDeptPerformance();
  else if (viewName === 'doctor-stats') loadDoctorStats();
  else if (viewName === 'notifications') loadNotificationsPage();
  else if (viewName === 'settings') loadAuditTrail();
}

// ===== NOTIFICATION PANEL =====
function toggleNotifPanel() {
  document.getElementById('notifPanel').classList.toggle('show');
  loadNotifications();
}

function markAllRead() {
  fetch('api/notifications.php?action=mark_read', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({})
  }).then(() => {
    loadNotifications();
    const dot = document.getElementById('notifDot');
    if (dot) dot.style.display = 'none';
    const badge = document.getElementById('sidebarNotifBadge');
    if (badge) badge.style.display = 'none';
    showToast('All notifications marked as read', 'success');
  });
}

function loadNotifications() {
  fetch('api/notifications.php?action=list')
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;
      const list = document.getElementById('notifList');
      if (!list) return;

      const catColors = {
        'Compliance': { bg: 'var(--amber-50)', color: 'var(--amber-600)' },
        'Reports': { bg: 'var(--primary-50)', color: 'var(--primary-600)' },
        'Performance': { bg: 'var(--emerald-50)', color: 'var(--emerald-600)' },
        'Staff': { bg: 'var(--indigo-50)', color: 'var(--indigo-600)' },
        'Pharmacy': { bg: 'var(--rose-50)', color: 'var(--rose-600)' },
        'Doctors': { bg: 'var(--violet-50)', color: 'var(--violet-600)' }
      };

      list.innerHTML = d.data.map(n => {
        const cc = catColors[n.category] || catColors['Reports'];
        return `<div class="notif-item ${n.is_read == 0 ? 'unread' : ''}">
          <div class="notif-dot-icon" style="background:${cc.bg};color:${cc.color};">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
          </div>
          <div class="notif-content">
            <div class="notif-title">${escHtml(n.title)}</div>
            <div class="notif-desc">${escHtml(n.description)}</div>
            <div class="notif-time">${n.relative_time || 'Just now'}</div>
          </div>
        </div>`;
      }).join('');

      // Update badge
      if (d.unread_count > 0) {
        const dot = document.getElementById('notifDot');
        if (dot) dot.style.display = 'block';
        const badge = document.getElementById('sidebarNotifBadge');
        if (badge) { badge.textContent = d.unread_count; badge.style.display = ''; }
      }
    });
}

// ===== DASHBOARD =====
function loadDashboard() {
  fetch('api/dashboard-stats.php')
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;
      const data = d.data;

      // Summary cards
      const sc = data.summary_cards;
      setText('cardCensus', sc.today_patient_census.value);
      setText('cardPerf', sc.monthly_performance.value);
      setText('cardRevenue', sc.monthly_revenue.value);
      setText('cardCompliance', sc.doh_compliance.value);

      // KPIs
      renderKPIs(data.kpis);

      // Activity Feed
      renderActivityFeed(data.recent_activity);

      // Charts
      renderCensusTrendChart(data.charts.census_trend);
      renderRevenueChart(data.charts.revenue_breakdown);
    })
    .catch(err => console.error('Dashboard load error:', err));
}

function renderKPIs(kpis) {
  const el = document.getElementById('kpiList');
  if (!el) return;
  el.innerHTML = kpis.map(k => `
    <div class="kpi-item">
      <div class="kpi-header">
        <span class="kpi-name">${escHtml(k.name)}</span>
        <span class="kpi-val">${k.value}%</span>
      </div>
      <div class="progress-track">
        <div class="progress-fill ${k.color}" style="width:0%" data-width="${k.value}"></div>
      </div>
    </div>
  `).join('');

  // Animate progress bars
  setTimeout(() => {
    el.querySelectorAll('.progress-fill').forEach(bar => {
      bar.style.width = bar.dataset.width + '%';
    });
  }, 100);
}

function renderActivityFeed(items) {
  const el = document.getElementById('activityFeed');
  if (!el) return;
  el.innerHTML = items.map(a => `
    <div class="feed-item">
      <div class="feed-dot ${a.type}">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="feed-body">
        <div class="feed-title">${escHtml(a.title)}</div>
        <div class="feed-desc">${escHtml(a.desc)}</div>
        <div class="feed-time">${escHtml(a.time)}</div>
      </div>
    </div>
  `).join('');
}

// ===== CHARTS =====
function destroyChart(id) {
  if (chartInstances[id]) { chartInstances[id].destroy(); delete chartInstances[id]; }
}

function renderCensusTrendChart(data) {
  destroyChart('censusTrendChart');
  const ctx = document.getElementById('censusTrendChart');
  if (!ctx || !data || data.length === 0) return;

  const labels = data.map(d => {
    const dt = new Date(d.census_date);
    return dt.toLocaleDateString('en-PH', { month: 'short', day: 'numeric' });
  });

  chartInstances['censusTrendChart'] = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        { label: 'Total Patients', data: data.map(d => d.total_patients), borderColor: '#0284c7', backgroundColor: 'rgba(2,132,199,0.08)', fill: true, tension: 0.35, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#0284c7' },
        { label: 'Inpatients', data: data.map(d => d.inpatients), borderColor: '#10b981', backgroundColor: 'transparent', tension: 0.35, borderWidth: 2, pointRadius: 2, borderDash: [4,4] },
        { label: 'Outpatients', data: data.map(d => d.outpatients), borderColor: '#6366f1', backgroundColor: 'transparent', tension: 0.35, borderWidth: 2, pointRadius: 2, borderDash: [4,4] }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 11, family: 'Inter' } } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Inter' }, color: '#94a3b8' } },
        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11, family: 'Inter' }, color: '#94a3b8' } }
      }
    }
  });
}

function renderRevenueChart(data) {
  destroyChart('revenueChart');
  const ctx = document.getElementById('revenueChart');
  if (!ctx || !data) return;

  const labels = Object.keys(data);
  const values = Object.values(data);
  const colors = ['#0284c7', '#6366f1', '#10b981', '#f59e0b'];

  chartInstances['revenueChart'] = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{ data: values, backgroundColor: colors, borderWidth: 0, hoverOffset: 8 }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 11, family: 'Inter' } } },
        tooltip: { callbacks: { label: (c) => ` ₱${Number(c.raw).toLocaleString()}` } }
      }
    }
  });
}

// ===== OPERATIONAL REPORTS =====
function loadReports() {
  const search = document.getElementById('reportSearch')?.value || '';
  const cat = document.getElementById('reportCategoryFilter')?.value || 'all';

  fetch(`api/reports.php?action=list&search=${encodeURIComponent(search)}&category=${encodeURIComponent(cat)}`)
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;

      // Grid cards (core reports only — first 7)
      const core = d.data.filter(r => !r.report_code.startsWith('RPT-ARCH'));
      const grid = document.getElementById('reportsGrid');
      if (grid) {
        grid.innerHTML = core.map(r => {
          const metrics = r.summary_metrics || {};
          const mainMetric = Object.values(metrics)[0] || '-';
          const statusCls = r.status === 'Finalized' ? 'finalized' : 'archived';
          return `<div class="rpt-card" onclick="openReport('${r.report_code}')">
            <div class="rpt-top">
              <div class="rpt-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg></div>
              <div class="rpt-meta">
                <div class="rpt-name">${escHtml(r.report_name)}</div>
                <div class="rpt-period">${escHtml(r.period_label)}</div>
              </div>
            </div>
            <div class="rpt-body">
              <div><span class="rpt-stat-lbl">Category</span><br><span class="rpt-stat-val">${escHtml(r.category)}</span></div>
              <div style="text-align:right;"><span class="rpt-stat-lbl">Key Metric</span><br><span class="rpt-stat-val">${escHtml(String(mainMetric))}</span></div>
            </div>
            <div class="rpt-foot">
              <span class="badge ${statusCls}">${escHtml(r.status)}</span>
              <button class="btn btn-sm btn-outline" onclick="event.stopPropagation();openReport('${r.report_code}')">View Report</button>
            </div>
          </div>`;
        }).join('');
      }

      // Archive table (all reports)
      const tbody = document.getElementById('reportsTableBody');
      if (tbody) {
        tbody.innerHTML = d.data.map(r => {
          const statusCls = r.status === 'Finalized' ? 'finalized' : 'archived';
          return `<tr>
            <td style="font-weight:600;font-size:.82rem;">${escHtml(r.report_code)}</td>
            <td>${escHtml(r.report_name)}</td>
            <td>${escHtml(r.category)}</td>
            <td>${escHtml(r.period_label)}</td>
            <td><span class="badge ${statusCls}">${escHtml(r.status)}</span></td>
            <td><button class="btn btn-sm btn-outline" onclick="openReport('${r.report_code}')">View</button></td>
          </tr>`;
        }).join('');
      }
    });
}

function filterReports() { loadReports(); }

// ===== REPORT VIEWER MODAL =====
function openReport(code) {
  currentReportCode = code;
  const modal = document.getElementById('reportModal');
  modal.classList.add('show');
  document.getElementById('reportModalTitle').textContent = 'Loading...';
  document.getElementById('reportModalBody').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--slate-400);">Loading report...</div>';

  fetch(`api/reports.php?action=get&code=${encodeURIComponent(code)}`)
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;
      const rpt = d.data;
      document.getElementById('reportModalTitle').textContent = rpt.report_name;
      renderReportContent(rpt);
    });
}

function renderReportContent(rpt) {
  const body = document.getElementById('reportModalBody');
  const metrics = rpt.summary_metrics || {};
  const detail = rpt.detailed_payload || {};

  let html = `
    <div class="report-formal-hdr">
      <div>
        <div class="hospital-name">Tupi Municipal Hospital</div>
        <div class="report-title-formal">${escHtml(rpt.report_name)}</div>
        <div style="font-size:.82rem;color:var(--text-muted);margin-top:.35rem;">Hospital Operations Report</div>
      </div>
      <div class="report-meta">
        <div><strong>Period:</strong> ${escHtml(rpt.period_label)}</div>
        <div><strong>Generated:</strong> ${new Date().toLocaleDateString('en-PH', { year:'numeric',month:'long',day:'numeric' })}</div>
        <div><strong>Prepared For:</strong> Dr. Maria Santos</div>
        <div style="margin-top:.25rem;"><span class="badge finalized">${escHtml(rpt.status)}</span></div>
      </div>
    </div>`;

  // Summary boxes
  const metricEntries = Object.entries(metrics).slice(0, 8);
  if (metricEntries.length > 0) {
    html += '<div class="report-boxes">';
    metricEntries.forEach(([k, v]) => {
      const label = k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
      html += `<div class="rpt-box"><div class="lbl">${escHtml(label)}</div><div class="val">${escHtml(String(v))}</div></div>`;
    });
    html += '</div>';
  }

  // Summary & Findings
  if (detail.summary) {
    html += `<div class="card" style="margin-bottom:1.25rem;"><div style="padding:1.25rem;"><h4 style="margin-bottom:.5rem;font-size:.95rem;">Executive Summary</h4><p style="font-size:.88rem;line-height:1.6;">${escHtml(detail.summary)}</p></div></div>`;
  }
  if (detail.findings) {
    html += `<div class="card" style="margin-bottom:1.25rem;"><div style="padding:1.25rem;"><h4 style="margin-bottom:.5rem;font-size:.95rem;">Key Findings</h4><p style="font-size:.88rem;line-height:1.6;">${escHtml(detail.findings)}</p></div></div>`;
  }

  // Detail tables based on report type
  const tableArrays = {
    'breakdown': detail.breakdown,
    'indicators': detail.indicators,
    'revenue_streams': detail.revenue_streams,
    'popular_tests': detail.popular_tests,
    'sample_inventory': detail.sample_inventory,
    'breakdown_by_dept': detail.breakdown_by_dept,
    'domains': detail.domains
  };

  for (const [title, arr] of Object.entries(tableArrays)) {
    if (arr && arr.length > 0) {
      const cols = Object.keys(arr[0]);
      const prettyTitle = title.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
      html += `<div class="card" style="margin-bottom:1.25rem;"><div style="padding:1.25rem;">
        <h4 style="margin-bottom:.75rem;font-size:.95rem;">${escHtml(prettyTitle)}</h4>
        <div class="tbl-wrap"><table class="tbl"><thead><tr>`;
      cols.forEach(c => { html += `<th>${escHtml(c.replace(/_/g, ' ').replace(/\b\w/g, ch => ch.toUpperCase()))}</th>`; });
      html += `</tr></thead><tbody>`;
      arr.forEach(row => {
        html += '<tr>';
        cols.forEach(c => {
          let val = row[c] || '-';
          // Color status cells
          if (c.toLowerCase().includes('status')) {
            const cls = val === 'Compliant' || val === 'Exceeded' ? 'compliant' : val === 'Met' ? 'active' : 'attention';
            val = `<span class="badge ${cls}">${escHtml(val)}</span>`;
            html += `<td>${val}</td>`;
          } else {
            html += `<td>${escHtml(String(val))}</td>`;
          }
        });
        html += '</tr>';
      });
      html += `</tbody></table></div></div></div>`;
    }
  }

  // Live data tables
  if (rpt.census_history && rpt.census_history.length > 0) {
    html += renderLiveTable('Patient Census History', rpt.census_history, ['census_date','total_patients','inpatients','outpatients','emergency','discharged','occupancy_rate']);
  }
  if (rpt.laboratory_tests && rpt.laboratory_tests.length > 0) {
    html += renderLiveTable('Laboratory Test Breakdown', rpt.laboratory_tests, ['test_name','category','requests_count','completed_count','pending_count','utilization_rate']);
  }
  if (rpt.pharmacy_inventory && rpt.pharmacy_inventory.length > 0) {
    html += renderLivePharmacyTable(rpt.pharmacy_inventory);
  }
  if (rpt.compliance_matrix && rpt.compliance_matrix.length > 0) {
    html += renderComplianceSection(rpt.compliance_matrix);
  }
  if (rpt.appointment_history && rpt.appointment_history.length > 0) {
    html += renderLiveTable('Appointment History', rpt.appointment_history, ['summary_date','total_appointments','completed','pending','cancelled','no_show']);
  }

  // Chart container for modal
  html += '<div style="margin-top:1.5rem;"><canvas id="modalChart" style="max-height:260px;"></canvas></div>';

  body.innerHTML = html;

  // Render modal chart
  setTimeout(() => renderModalChart(rpt), 200);
}

function renderLiveTable(title, data, cols) {
  let html = `<div class="card" style="margin-bottom:1.25rem;"><div style="padding:1.25rem;">
    <h4 style="margin-bottom:.75rem;font-size:.95rem;">${escHtml(title)}</h4>
    <div class="tbl-wrap"><table class="tbl"><thead><tr>`;
  cols.forEach(c => { html += `<th>${escHtml(c.replace(/_/g, ' ').replace(/\b\w/g, ch => ch.toUpperCase()))}</th>`; });
  html += `</tr></thead><tbody>`;
  data.forEach(row => {
    html += '<tr>';
    cols.forEach(c => { html += `<td>${escHtml(String(row[c] ?? '-'))}</td>`; });
    html += '</tr>';
  });
  html += `</tbody></table></div></div></div>`;
  return html;
}

function renderLivePharmacyTable(data) {
  let html = `<div class="card" style="margin-bottom:1.25rem;"><div style="padding:1.25rem;">
    <h4 style="margin-bottom:.75rem;font-size:.95rem;">Pharmacy Inventory</h4>
    <div class="tbl-wrap"><table class="tbl"><thead><tr>
      <th>Medicine</th><th>Dosage</th><th>Stock</th><th>Min Level</th><th>Status</th><th>Expiry</th>
    </tr></thead><tbody>`;
  data.forEach(m => {
    const cls = m.status === 'Normal' ? 'normal' : m.status === 'Low Stock' ? 'low-stock' : m.status === 'Critical' ? 'critical' : 'expired';
    html += `<tr>
      <td style="font-weight:600;">${escHtml(m.medicine_name)}</td>
      <td>${escHtml(m.dosage)}</td>
      <td>${m.current_stock} ${escHtml(m.unit)}</td>
      <td>${m.min_level}</td>
      <td><span class="badge ${cls}">${escHtml(m.status)}</span></td>
      <td>${escHtml(m.expiry_date)}</td>
    </tr>`;
  });
  html += `</tbody></table></div></div></div>`;
  return html;
}

function renderComplianceSection(domains) {
  let html = `<div class="card" style="margin-bottom:1.25rem;"><div style="padding:1.25rem;">
    <h4 style="margin-bottom:1rem;font-size:.95rem;">DOH Compliance Matrix</h4>`;
  domains.forEach(d => {
    const pct = parseFloat(d.compliance_score);
    const color = pct >= 97 ? 'emerald' : pct >= 95 ? 'primary' : 'amber';
    html += `<div class="kpi-item" style="margin-bottom:1rem;">
      <div class="kpi-header">
        <span class="kpi-name">${escHtml(d.domain_name)}</span>
        <span class="kpi-val">${pct}%</span>
      </div>
      <div class="progress-track">
        <div class="progress-fill ${color}" style="width:${pct}%"></div>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:.74rem;color:var(--slate-500);margin-top:.25rem;">
        <span><span class="badge compliant" style="font-size:.68rem;">${escHtml(d.status)}</span></span>
        <span>${escHtml(d.notes || '')}</span>
      </div>
    </div>`;
  });
  html += `<div style="display:flex;gap:2rem;font-size:.82rem;color:var(--slate-600);margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-subtle);">
    <span><strong>Last Inspection:</strong> ${escHtml(domains[0]?.last_inspection_date || '-')}</span>
    <span><strong>Next Review:</strong> ${escHtml(domains[0]?.next_review_date || '-')}</span>
  </div></div></div>`;
  return html;
}

function renderModalChart(rpt) {
  destroyChart('modalChart');
  const ctx = document.getElementById('modalChart');
  if (!ctx) return;

  if (rpt.census_history && rpt.census_history.length > 0) {
    const hist = rpt.census_history.slice().reverse().slice(0, 14);
    chartInstances['modalChart'] = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: hist.map(h => { const d = new Date(h.census_date); return d.toLocaleDateString('en-PH', {month:'short',day:'numeric'}); }),
        datasets: [
          { label: 'Total Patients', data: hist.map(h => h.total_patients), backgroundColor: 'rgba(2,132,199,0.6)', borderRadius: 4 }
        ]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { grid: { color: '#f1f5f9' } } } }
    });
  } else if (rpt.appointment_history && rpt.appointment_history.length > 0) {
    const hist = rpt.appointment_history.slice().reverse().slice(0, 14);
    chartInstances['modalChart'] = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: hist.map(h => { const d = new Date(h.summary_date); return d.toLocaleDateString('en-PH', {month:'short',day:'numeric'}); }),
        datasets: [
          { label: 'Completed', data: hist.map(h => h.completed), backgroundColor: '#10b981', borderRadius: 4 },
          { label: 'Pending', data: hist.map(h => h.pending), backgroundColor: '#f59e0b', borderRadius: 4 },
          { label: 'Cancelled', data: hist.map(h => h.cancelled), backgroundColor: '#f43f5e', borderRadius: 4 },
        ]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, font: { size: 11, family: 'Inter' } } } }, scales: { x: { stacked: true, grid: { display: false } }, y: { stacked: true, grid: { color: '#f1f5f9' } } } }
    });
  }
}

function closeReportModal() {
  document.getElementById('reportModal').classList.remove('show');
  destroyChart('modalChart');
}

// ===== EXPORT & PRINT =====
function toggleExportMenu() {
  document.getElementById('exportMenu').classList.toggle('show');
}

function exportReport(format) {
  document.getElementById('exportMenu').classList.remove('show');
  if (!currentReportCode) return;
  window.open(`api/export-report.php?code=${encodeURIComponent(currentReportCode)}&format=${format}`, '_blank');
  showToast(`Report exported as ${format.toUpperCase()} successfully.`, 'success');
}

function printReport() {
  window.print();
}

// ===== STAFF ACTIVITY =====
function loadStaffActivity() {
  const params = new URLSearchParams();
  const search = document.getElementById('staffSearch')?.value || '';
  const dept = document.getElementById('staffDeptFilter')?.value || 'all';
  const role = document.getElementById('staffRoleFilter')?.value || 'all';
  const type = document.getElementById('staffTypeFilter')?.value || 'all';
  const date = document.getElementById('staffDateFilter')?.value || 'all';

  params.set('search', search);
  params.set('department', dept);
  params.set('role', role);
  params.set('activity_type', type);
  params.set('date_range', date);

  fetch(`api/staff-activity.php?${params.toString()}`)
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;

      // Populate filter dropdowns (only first time)
      if (d.filters) {
        populateSelect('staffDeptFilter', d.filters.departments, dept);
        populateSelect('staffRoleFilter', d.filters.roles, role);
        populateSelect('staffTypeFilter', d.filters.activity_types, type);
      }

      const tbody = document.getElementById('staffActivityBody');
      if (!tbody) return;

      tbody.innerHTML = d.data.map(log => `
        <tr>
          <td>${escHtml(log.formatted_date)}</td>
          <td>${escHtml(log.formatted_time)}</td>
          <td style="font-weight:600;">${escHtml(log.staff_name)}</td>
          <td>${escHtml(log.role_title)}</td>
          <td>${escHtml(log.department_name)}</td>
          <td style="max-width:280px;">${escHtml(log.activity_description)}</td>
          <td><span class="badge completed">${escHtml(log.status)}</span></td>
          <td><button class="btn btn-sm btn-secondary" onclick='showStaffDetail(${JSON.stringify(log)})'>Details</button></td>
        </tr>
      `).join('');
    });
}

function filterStaffActivity() { loadStaffActivity(); }

function showStaffDetail(log) {
  const body = document.getElementById('staffDetailBody');
  body.innerHTML = `
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
      <div class="avatar" style="width:48px;height:48px;">${escHtml(log.staff_name.split(' ').map(n=>n[0]).join('').slice(0,2))}</div>
      <div>
        <div style="font-size:1.05rem;font-weight:700;color:var(--slate-900);">${escHtml(log.staff_name)}</div>
        <div style="font-size:.85rem;color:var(--primary-700);">${escHtml(log.role_title)}</div>
        <div style="font-size:.78rem;color:var(--slate-500);">${escHtml(log.department_name)}</div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
      <div style="background:var(--slate-50);padding:.85rem;border-radius:var(--radius-md);">
        <div style="font-size:.72rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;">Date</div>
        <div style="font-size:.88rem;font-weight:600;color:var(--slate-900);">${escHtml(log.formatted_date)}</div>
      </div>
      <div style="background:var(--slate-50);padding:.85rem;border-radius:var(--radius-md);">
        <div style="font-size:.72rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;">Time</div>
        <div style="font-size:.88rem;font-weight:600;color:var(--slate-900);">${escHtml(log.formatted_time)}</div>
      </div>
    </div>
    <div style="margin-bottom:1rem;">
      <div style="font-size:.78rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;margin-bottom:.35rem;">Activity Type</div>
      <div style="font-size:.88rem;color:var(--slate-800);">${escHtml(log.activity_type)}</div>
    </div>
    <div style="margin-bottom:1rem;">
      <div style="font-size:.78rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;margin-bottom:.35rem;">Activity Description</div>
      <div style="font-size:.88rem;color:var(--slate-800);line-height:1.6;">${escHtml(log.activity_description)}</div>
    </div>
    <div style="display:flex;gap:1rem;">
      <div><span class="badge completed">${escHtml(log.status)}</span></div>
    </div>
  `;
  document.getElementById('staffDetailModal').classList.add('show');
}

// ===== DEPARTMENT PERFORMANCE =====
function loadDeptPerformance() {
  fetch('api/department-performance.php')
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;

      // Summary cards
      const s = d.summary;
      const sumCards = document.getElementById('deptSummaryCards');
      if (sumCards) {
        sumCards.innerHTML = `
          <div class="card summary-card c-primary"><div class="sc-top"><span class="sc-title">TOTAL DEPARTMENTS</span></div><div class="sc-value">${s.total_departments}</div><div class="sc-bottom"><span class="sc-desc">${s.total_staff} total staff members</span></div></div>
          <div class="card summary-card c-emerald"><div class="sc-top"><span class="sc-title">HOSPITAL EFFICIENCY</span></div><div class="sc-value">${s.hospital_efficiency_score}%</div><div class="sc-bottom"><span class="sc-desc">Average performance score</span></div></div>
          <div class="card summary-card c-indigo"><div class="sc-top"><span class="sc-title">TASK COMPLETION</span></div><div class="sc-value">${s.task_completion_rate}%</div><div class="sc-bottom"><span class="sc-desc">${s.total_completed_tasks} completed / ${s.total_pending_tasks} pending</span></div></div>
        `;
      }

      // Dept chart
      renderDeptChart(d.data);

      // Dept grid cards
      const grid = document.getElementById('deptGrid');
      if (grid) {
        grid.innerHTML = d.data.map(dept => {
          const scoreColor = dept.performance_score >= 95 ? 'emerald' : dept.performance_score >= 90 ? 'primary' : 'amber';
          return `<div class="card dept-card">
            <div class="dept-name">${escHtml(dept.name)}</div>
            <div class="dept-head">${escHtml(dept.head)}</div>
            <div class="dept-stats">
              <div class="dept-stat-item"><div class="dept-stat-val">${dept.total_staff}</div><div class="dept-stat-lbl">Staff</div></div>
              <div class="dept-stat-item"><div class="dept-stat-val">${dept.active_rate}%</div><div class="dept-stat-lbl">Activity Rate</div></div>
              <div class="dept-stat-item"><div class="dept-stat-val">${dept.completed_tasks}</div><div class="dept-stat-lbl">Completed</div></div>
              <div class="dept-stat-item"><div class="dept-stat-val">${dept.pending_tasks}</div><div class="dept-stat-lbl">Pending</div></div>
            </div>
            <div class="kpi-item">
              <div class="kpi-header"><span class="kpi-name">Performance</span><span class="kpi-val">${dept.performance_score}%</span></div>
              <div class="progress-track"><div class="progress-fill ${scoreColor}" style="width:${dept.performance_score}%"></div></div>
            </div>
          </div>`;
        }).join('');
      }
    });
}

function renderDeptChart(departments) {
  destroyChart('deptPerfChart');
  const ctx = document.getElementById('deptPerfChart');
  if (!ctx) return;

  chartInstances['deptPerfChart'] = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: departments.map(d => d.name),
      datasets: [
        { label: 'Performance Score', data: departments.map(d => d.performance_score), backgroundColor: departments.map(d => d.performance_score >= 95 ? '#10b981' : d.performance_score >= 90 ? '#0284c7' : '#f59e0b'), borderRadius: 6 }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false, indexAxis: 'y',
      plugins: { legend: { display: false } },
      scales: { x: { min: 80, max: 100, grid: { color: '#f1f5f9' }, ticks: { callback: v => v + '%', font: { size: 11, family: 'Inter' } } }, y: { grid: { display: false }, ticks: { font: { size: 11, family: 'Inter' } } } }
    }
  });
}

// ===== DOCTOR STATISTICS =====
function loadDoctorStats() {
  const search = document.getElementById('docSearch')?.value || '';
  const dept = document.getElementById('docDeptFilter')?.value || 'all';

  fetch(`api/doctor-stats.php?search=${encodeURIComponent(search)}&department=${encodeURIComponent(dept)}`)
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;
      const s = d.summary;

      // Populate dept filter
      const depts = [...new Set(d.data.map(doc => doc.department_name))];
      populateSelect('docDeptFilter', depts, dept);

      // Summary cards
      const cards = document.getElementById('docSummaryCards');
      if (cards) {
        cards.innerHTML = `
          <div class="card summary-card c-primary"><div class="sc-top"><span class="sc-title">TOTAL DOCTORS</span></div><div class="sc-value">${s.total_doctors}</div><div class="sc-bottom"><span class="sc-desc">Active physicians</span></div></div>
          <div class="card summary-card c-emerald"><div class="sc-top"><span class="sc-title">TOTAL CONSULTATIONS</span></div><div class="sc-value">${s.total_consultations.toLocaleString()}</div><div class="sc-bottom"><span class="sc-desc">This month</span></div></div>
          <div class="card summary-card c-indigo"><div class="sc-top"><span class="sc-title">COMPLETION RATE</span></div><div class="sc-value">${s.completion_rate}%</div><div class="sc-bottom"><span class="sc-desc">${s.cancelled_consultations} cancelled</span></div></div>
          <div class="card summary-card c-amber"><div class="sc-top"><span class="sc-title">AVG DAILY / DOCTOR</span></div><div class="sc-value">${s.avg_daily_per_doctor}</div><div class="sc-bottom"><span class="sc-desc">Consultations per day</span></div></div>
        `;
      }

      // Charts
      renderDocDeptChart(d.charts.by_department);
      renderDocTrendChart(d.charts.monthly_trend);

      // Table
      const tbody = document.getElementById('docTableBody');
      if (tbody) {
        tbody.innerHTML = d.data.map(doc => `
          <tr>
            <td style="font-weight:600;">${escHtml(doc.doctor_name)}</td>
            <td>${escHtml(doc.department_name)}</td>
            <td style="font-size:.82rem;">${escHtml(doc.specialty)}</td>
            <td style="font-weight:700;">${doc.total_consultations}</td>
            <td>${doc.completed_consultations}</td>
            <td>${doc.cancelled_consultations}</td>
            <td>${doc.avg_daily_consultations}</td>
            <td><span class="badge compliant">${doc.satisfaction_score}%</span></td>
          </tr>
        `).join('');
      }
    });
}

function filterDoctors() { loadDoctorStats(); }

function renderDocDeptChart(data) {
  destroyChart('docDeptChart');
  const ctx = document.getElementById('docDeptChart');
  if (!ctx || !data) return;

  const colors = ['#0284c7','#10b981','#6366f1','#f59e0b','#8b5cf6','#f43f5e','#14b8a6'];
  chartInstances['docDeptChart'] = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: data.map(d => d.department_name),
      datasets: [{ data: data.map(d => d.total_vol), backgroundColor: colors.slice(0, data.length), borderWidth: 0, hoverOffset: 6 }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '55%', plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', font: { size: 10, family: 'Inter' } } } } }
  });
}

function renderDocTrendChart(data) {
  destroyChart('docTrendChart');
  const ctx = document.getElementById('docTrendChart');
  if (!ctx || !data) return;

  chartInstances['docTrendChart'] = new Chart(ctx, {
    type: 'line',
    data: {
      labels: data.map(d => d.month),
      datasets: [{ label: 'Consultations', data: data.map(d => d.consultations), borderColor: '#0284c7', backgroundColor: 'rgba(2,132,199,0.08)', fill: true, tension: 0.35, borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#0284c7' }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { grid: { color: '#f1f5f9' } } } }
  });
}

// ===== NOTIFICATIONS PAGE =====
function loadNotificationsPage() {
  fetch('api/notifications.php?action=list')
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;
      const el = document.getElementById('notificationsFullList');
      if (!el) return;

      const catColors = {
        'Compliance': { bg: 'var(--amber-50)', color: 'var(--amber-600)', icon: 'shield' },
        'Reports': { bg: 'var(--primary-50)', color: 'var(--primary-600)', icon: 'file' },
        'Performance': { bg: 'var(--emerald-50)', color: 'var(--emerald-600)', icon: 'chart' },
        'Staff': { bg: 'var(--indigo-50)', color: 'var(--indigo-600)', icon: 'user' },
        'Pharmacy': { bg: 'var(--rose-50)', color: 'var(--rose-600)', icon: 'pill' },
        'Doctors': { bg: 'var(--violet-50)', color: 'var(--violet-600)', icon: 'stethoscope' }
      };

      el.innerHTML = d.data.map(n => {
        const cc = catColors[n.category] || catColors['Reports'];
        return `<div style="display:flex;gap:1rem;padding:1.25rem;border-bottom:1px solid var(--border-subtle);${n.is_read == 0 ? 'background:#f0f9ff;' : ''}">
          <div style="width:40px;height:40px;border-radius:var(--radius-full);background:${cc.bg};color:${cc.color};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
          </div>
          <div style="flex:1;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
              <div style="font-weight:600;color:var(--slate-900);font-size:.95rem;">${escHtml(n.title)}</div>
              <span class="badge ${n.urgency === 'high' ? 'attention' : 'active'}" style="font-size:.68rem;">${escHtml(n.urgency)}</span>
            </div>
            <div style="font-size:.85rem;color:var(--text-muted);margin-top:.25rem;">${escHtml(n.description)}</div>
            <div style="font-size:.75rem;color:var(--slate-400);margin-top:.35rem;">${n.relative_time || 'Just now'} • ${escHtml(n.category)}</div>
          </div>
        </div>`;
      }).join('');
    });
}

// ===== AUDIT TRAIL =====
function loadAuditTrail() {
  fetch('api/audit-trail.php')
    .then(r => r.json())
    .then(d => {
      if (d.status !== 'success') return;
      const tbody = document.getElementById('auditTableBody');
      if (!tbody) return;

      tbody.innerHTML = d.data.map(a => `
        <tr>
          <td style="font-weight:600;">${escHtml(a.user_name)}</td>
          <td>${escHtml(a.role_title)}</td>
          <td>${escHtml(a.action_performed)}</td>
          <td>${escHtml(a.report_affected)}</td>
          <td>${escHtml(a.formatted_date)}</td>
          <td>${escHtml(a.formatted_time)}</td>
        </tr>
      `).join('');
    });
}

function showSettingsTab(tab) {
  document.getElementById('settingsProfilePane').style.display = tab === 'profile' ? 'block' : 'none';
  document.getElementById('settingsAuditPane').style.display = tab === 'audit' ? 'block' : 'none';
  document.getElementById('profileTab').className = tab === 'profile' ? 'btn btn-primary' : 'btn btn-secondary';
  document.getElementById('auditTab').className = tab === 'audit' ? 'btn btn-primary' : 'btn btn-secondary';
  if (tab === 'audit') loadAuditTrail();
}

// ===== UTILITY =====
function setText(id, text) {
  const el = document.getElementById(id);
  if (el) el.textContent = text;
}

function escHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function populateSelect(id, options, currentVal) {
  const sel = document.getElementById(id);
  if (!sel) return;
  const firstOption = sel.options[0]; // "All..." option
  sel.innerHTML = '';
  sel.appendChild(firstOption);
  options.forEach(o => {
    const opt = document.createElement('option');
    opt.value = o;
    opt.textContent = o;
    if (o === currentVal) opt.selected = true;
    sel.appendChild(opt);
  });
}

function closeModal(id) {
  document.getElementById(id)?.classList.remove('show');
}

function showToast(message, type = 'info') {
  const box = document.getElementById('toastBox');
  if (!box) return;
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>${escHtml(message)}`;
  box.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
}

function handleGlobalSearch(query) {
  // Simple global search redirect
  if (query.length > 2) {
    // search across sections
    console.log('Global search:', query);
  }
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', () => {
  loadDashboard();
  loadNotifications();
});
