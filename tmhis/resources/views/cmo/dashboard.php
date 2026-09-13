<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Hospital Chief / Medical Director Dashboard (Role 2)
 * Main Entry Point
 */

declare(strict_types=1);

// Initialize database on first load
require_once __DIR__ . '/config/db.php';
\TMHIS\Database::getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medical Director Dashboard | Tupi Municipal Hospital Information Management System</title>
  <meta name="description" content="Hospital Chief / Medical Director Executive Dashboard for Tupi Municipal Hospital Information Management System. View operational reports, staff activity, department performance, and DOH compliance.">
  <link rel="stylesheet" href="css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-container">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main-wrap" id="mainWrap">
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="content-body">

      <!-- ============================================================
           VIEW 1: DASHBOARD
           ============================================================ -->
      <section class="view-section active" id="view-dashboard">
        <div class="page-header">
          <div>
            <h1 class="page-title">Medical Director Dashboard</h1>
            <p class="page-subtitle">Hospital Operations, Performance & Staff Activity Overview</p>
          </div>
          <div class="page-actions">
            <button class="btn btn-secondary" onclick="switchView('reports', document.querySelector('[data-view=reports]'))">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
              View Reports
            </button>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="cards-grid" id="summaryCards">
          <div class="card summary-card c-primary">
            <div class="sc-top">
              <span class="sc-title">TODAY'S PATIENT CENSUS</span>
              <div class="sc-icon primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
            </div>
            <div class="sc-value" id="cardCensus">126</div>
            <div class="sc-bottom">
              <span class="sc-desc">Current patients recorded today</span>
              <span class="trend up" id="cardCensusTrend">↑ +4.2%</span>
            </div>
          </div>

          <div class="card summary-card c-emerald">
            <div class="sc-top">
              <span class="sc-title">MONTHLY OPERATIONAL PERFORMANCE</span>
              <div class="sc-icon emerald">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/></svg>
              </div>
            </div>
            <div class="sc-value" id="cardPerf">92%</div>
            <div class="sc-bottom">
              <span class="sc-desc">Overall operational performance</span>
              <span class="trend up">↑ +3.5%</span>
            </div>
          </div>

          <div class="card summary-card c-indigo">
            <div class="sc-top">
              <span class="sc-title">MONTHLY REVENUE</span>
              <div class="sc-icon indigo">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
            </div>
            <div class="sc-value" id="cardRevenue">₱1,284,500</div>
            <div class="sc-bottom">
              <span class="sc-desc">Current monthly billing & revenue</span>
              <span class="trend up">↑ +8.4%</span>
            </div>
          </div>

          <div class="card summary-card c-amber">
            <div class="sc-top">
              <span class="sc-title">DOH COMPLIANCE</span>
              <div class="sc-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
              </div>
            </div>
            <div class="sc-value" id="cardCompliance">96%</div>
            <div class="sc-bottom">
              <span class="sc-desc">Current compliance status</span>
              <span class="trend up">Level 2 Certified</span>
            </div>
          </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid-2col">
          <!-- Chart: Patient Census Trend -->
          <div class="card">
            <div class="card-hdr">
              <div>
                <div class="card-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                  Patient Census Trend
                </div>
                <div class="card-subtitle">7-day patient volume overview</div>
              </div>
            </div>
            <div class="chart-container"><canvas id="censusTrendChart"></canvas></div>
          </div>

          <!-- KPI Indicators -->
          <div class="card">
            <div class="card-hdr">
              <div>
                <div class="card-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/></svg>
                  Hospital Performance Indicators
                </div>
              </div>
            </div>
            <div class="kpi-list" id="kpiList">
              <!-- Filled by JS -->
            </div>
          </div>
        </div>

        <div class="grid-2col">
          <!-- Revenue Breakdown Chart -->
          <div class="card">
            <div class="card-hdr">
              <div>
                <div class="card-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                  Revenue Breakdown
                </div>
                <div class="card-subtitle">Monthly revenue by department</div>
              </div>
            </div>
            <div class="chart-container"><canvas id="revenueChart"></canvas></div>
          </div>

          <!-- Recent Activity -->
          <div class="card">
            <div class="card-hdr">
              <div>
                <div class="card-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  Recent Hospital Activity
                </div>
              </div>
            </div>
            <div class="feed-list" id="activityFeed">
              <!-- Filled by JS -->
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================
           VIEW 2: OPERATIONAL REPORTS
           ============================================================ -->
      <section class="view-section" id="view-reports">
        <div class="page-header">
          <div>
            <h1 class="page-title">Operational Reports</h1>
            <p class="page-subtitle">View and export hospital operational reports</p>
          </div>
        </div>

        <div class="filter-bar">
          <div class="filter-left">
            <div class="filter-search">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" placeholder="Search reports..." id="reportSearch" onkeyup="filterReports()">
            </div>
            <select class="filter-sel" id="reportCategoryFilter" onchange="filterReports()">
              <option value="all">All Categories</option>
              <option value="Census & Inpatient">Census & Inpatient</option>
              <option value="Hospital Performance">Hospital Performance</option>
              <option value="Financial Oversight">Financial Oversight</option>
              <option value="Clinical Diagnostics">Clinical Diagnostics</option>
              <option value="Pharmacy & Supply">Pharmacy & Supply</option>
              <option value="Outpatient Flow">Outpatient Flow</option>
              <option value="Regulatory & Accreditation">Regulatory & Accreditation</option>
            </select>
          </div>
        </div>

        <div class="reports-grid" id="reportsGrid">
          <!-- Filled by JS -->
        </div>

        <div class="card" style="margin-top:1rem;">
          <div class="card-hdr">
            <div class="card-title">Report Archive</div>
          </div>
          <div class="tbl-wrap">
            <table class="tbl" id="reportsTable">
              <thead>
                <tr>
                  <th>Report Code</th>
                  <th>Report Name</th>
                  <th>Category</th>
                  <th>Period</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="reportsTableBody">
                <!-- Filled by JS -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ============================================================
           VIEW 3: STAFF ACTIVITY
           ============================================================ -->
      <section class="view-section" id="view-staff-activity">
        <div class="page-header">
          <div>
            <h1 class="page-title">Staff Activity Log</h1>
            <p class="page-subtitle">Monitor staff activities across all departments</p>
          </div>
        </div>

        <div class="filter-bar">
          <div class="filter-left">
            <div class="filter-search">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" placeholder="Search staff..." id="staffSearch" onkeyup="filterStaffActivity()">
            </div>
            <select class="filter-sel" id="staffDeptFilter" onchange="filterStaffActivity()"><option value="all">All Departments</option></select>
            <select class="filter-sel" id="staffRoleFilter" onchange="filterStaffActivity()"><option value="all">All Roles</option></select>
            <select class="filter-sel" id="staffTypeFilter" onchange="filterStaffActivity()"><option value="all">All Activity Types</option></select>
            <select class="filter-sel" id="staffDateFilter" onchange="filterStaffActivity()">
              <option value="all">All Time</option>
              <option value="today">Today</option>
              <option value="yesterday">Yesterday</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
            </select>
          </div>
        </div>

        <div class="card">
          <div class="tbl-wrap">
            <table class="tbl">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Staff Name</th>
                  <th>Role</th>
                  <th>Department</th>
                  <th>Activity</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="staffActivityBody">
                <!-- Filled by JS -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ============================================================
           VIEW 4: DEPARTMENT PERFORMANCE
           ============================================================ -->
      <section class="view-section" id="view-department-performance">
        <div class="page-header">
          <div>
            <h1 class="page-title">Department Performance</h1>
            <p class="page-subtitle">Performance metrics across all hospital departments</p>
          </div>
        </div>

        <!-- Dept Summary Cards -->
        <div class="cards-grid" id="deptSummaryCards" style="grid-template-columns:repeat(3,1fr);">
          <!-- Filled by JS -->
        </div>

        <!-- Dept Chart -->
        <div class="card" style="margin-bottom:1.5rem;">
          <div class="card-hdr">
            <div class="card-title">Department Efficiency Comparison</div>
          </div>
          <div class="chart-container"><canvas id="deptPerfChart"></canvas></div>
        </div>

        <!-- Dept cards -->
        <div class="dept-grid" id="deptGrid">
          <!-- Filled by JS -->
        </div>
      </section>

      <!-- ============================================================
           VIEW 5: DOCTOR STATISTICS
           ============================================================ -->
      <section class="view-section" id="view-doctor-stats">
        <div class="page-header">
          <div>
            <h1 class="page-title">Doctor Consultation Statistics</h1>
            <p class="page-subtitle">Consultation volumes, completion rates, and monthly trends</p>
          </div>
        </div>

        <div class="filter-bar">
          <div class="filter-left">
            <div class="filter-search">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" placeholder="Search doctors..." id="docSearch" onkeyup="filterDoctors()">
            </div>
            <select class="filter-sel" id="docDeptFilter" onchange="filterDoctors()">
              <option value="all">All Departments</option>
            </select>
          </div>
        </div>

        <!-- Doctor summary -->
        <div class="cards-grid" id="docSummaryCards" style="grid-template-columns:repeat(4,1fr);">
          <!-- Filled by JS -->
        </div>

        <!-- Charts -->
        <div class="grid-2eq">
          <div class="card">
            <div class="card-hdr"><div class="card-title">Consultations by Department</div></div>
            <div class="chart-container"><canvas id="docDeptChart"></canvas></div>
          </div>
          <div class="card">
            <div class="card-hdr"><div class="card-title">Monthly Consultation Trend</div></div>
            <div class="chart-container"><canvas id="docTrendChart"></canvas></div>
          </div>
        </div>

        <!-- Table -->
        <div class="card">
          <div class="card-hdr"><div class="card-title">Doctor Performance Table</div></div>
          <div class="tbl-wrap">
            <table class="tbl">
              <thead>
                <tr>
                  <th>Doctor</th>
                  <th>Department</th>
                  <th>Specialty</th>
                  <th>Total</th>
                  <th>Completed</th>
                  <th>Cancelled</th>
                  <th>Avg Daily</th>
                  <th>Satisfaction</th>
                </tr>
              </thead>
              <tbody id="docTableBody">
                <!-- Filled by JS -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ============================================================
           VIEW 6: NOTIFICATIONS
           ============================================================ -->
      <section class="view-section" id="view-notifications">
        <div class="page-header">
          <div>
            <h1 class="page-title">Administrative Notifications</h1>
            <p class="page-subtitle">Executive alerts and system notifications</p>
          </div>
          <div class="page-actions">
            <button class="btn btn-secondary" onclick="markAllRead()">Mark All as Read</button>
          </div>
        </div>
        <div class="card">
          <div id="notificationsFullList">
            <!-- Filled by JS -->
          </div>
        </div>
      </section>

      <!-- ============================================================
           VIEW 7: SETTINGS & AUDIT TRAIL
           ============================================================ -->
      <section class="view-section" id="view-settings">
        <div class="page-header">
          <div>
            <h1 class="page-title">Settings & Audit Trail</h1>
            <p class="page-subtitle">System preferences and executive activity log</p>
          </div>
        </div>

        <!-- Tabs -->
        <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;">
          <button class="btn btn-primary" id="profileTab" onclick="showSettingsTab('profile')">Profile</button>
          <button class="btn btn-secondary" id="auditTab" onclick="showSettingsTab('audit')">Audit Trail</button>
        </div>

        <!-- Profile Tab -->
        <div id="settingsProfilePane">
          <div class="card" style="max-width:600px;">
            <div class="card-hdr"><div class="card-title">Executive Profile</div></div>
            <div style="display:flex;align-items:center;gap:1.25rem;margin-bottom:1.5rem;">
              <div class="avatar" style="width:64px;height:64px;font-size:1.4rem;">MS</div>
              <div>
                <div style="font-size:1.15rem;font-weight:700;color:var(--slate-900);">Dr. Maria Santos, MD, MHA</div>
                <div style="font-size:.88rem;color:var(--primary-700);font-weight:600;">Hospital Chief / Medical Director</div>
                <div style="font-size:.82rem;color:var(--slate-500);">Hospital Administration</div>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
              <div>
                <div style="font-size:.74rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;">Email</div>
                <div style="font-size:.88rem;color:var(--slate-800);">m.santos@hospity.ph</div>
              </div>
              <div>
                <div style="font-size:.74rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;">Contact</div>
                <div style="font-size:.88rem;color:var(--slate-800);">0917-100-0001</div>
              </div>
              <div>
                <div style="font-size:.74rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;">Role Access Level</div>
                <div style="font-size:.88rem;color:var(--slate-800);">Level 2 — Executive Monitoring</div>
              </div>
              <div>
                <div style="font-size:.74rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;">Permissions</div>
                <div style="font-size:.88rem;color:var(--slate-800);">View, Monitor, Export, Print</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Audit Tab -->
        <div id="settingsAuditPane" style="display:none;">
          <div class="card">
            <div class="card-hdr">
              <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary-600)"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                Executive Audit Trail
              </div>
              <div class="card-subtitle">Immutable activity record — cannot be deleted</div>
            </div>
            <div class="tbl-wrap">
              <table class="tbl">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>Report / Section</th>
                    <th>Date</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody id="auditTableBody">
                  <!-- Filled by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

    </div><!-- end content-body -->
  </main>
</div>

<?php include __DIR__ . '/includes/modals.php'; ?>

<script src="js/app.js"></script>
</body>
</html>
