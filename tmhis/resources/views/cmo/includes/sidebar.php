<?php
/**
 * TMHIS - Left Vertical Sidebar Component
 * Hospital Chief / Medical Director (Role 2)
 */
?>
<aside class="sidebar" id="sidebar">
  <!-- Brand Header -->
  <div class="sidebar-header">
    <div class="brand-wrap">
      <div class="brand-icon">H</div>
      <div class="brand-text">
        <div class="brand-title">Tupi Municipal Hospital</div>
        <div class="brand-tag">Healthcare System</div>
      </div>
    </div>
    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="sidebar-nav">
    <!-- MAIN section -->
    <div>
      <div class="nav-label">MAIN</div>
      <ul class="nav-list">
        <li class="nav-item" data-tooltip="Dashboard">
          <button class="nav-link active" data-view="dashboard" onclick="switchView('dashboard', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            <span class="nav-text">Dashboard</span>
          </button>
        </li>
        <li class="nav-item" data-tooltip="Operational Reports">
          <button class="nav-link" data-view="reports" onclick="switchView('reports', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
            <span class="nav-text">Operational Reports</span>
            <span class="nav-badge">7</span>
          </button>
        </li>
        <li class="nav-item" data-tooltip="Staff Activity">
          <button class="nav-link" data-view="staff-activity" onclick="switchView('staff-activity', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/></svg>
            <span class="nav-text">Staff Activity</span>
          </button>
        </li>
        <li class="nav-item" data-tooltip="Department Performance">
          <button class="nav-link" data-view="department-performance" onclick="switchView('department-performance', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
            <span class="nav-text">Department Performance</span>
          </button>
        </li>
        <li class="nav-item" data-tooltip="Doctor Statistics">
          <button class="nav-link" data-view="doctor-stats" onclick="switchView('doctor-stats', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>
            <span class="nav-text">Doctor Statistics</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- OTHER section -->
    <div>
      <div class="nav-label">OTHER</div>
      <ul class="nav-list">
        <li class="nav-item" data-tooltip="Notifications">
          <button class="nav-link" data-view="notifications" onclick="switchView('notifications', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
            <span class="nav-text">Notifications</span>
            <span class="nav-badge alert" id="sidebarNotifBadge">4</span>
          </button>
        </li>
        <li class="nav-item" data-tooltip="Settings">
          <button class="nav-link" data-view="settings" onclick="switchView('settings', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
            <span class="nav-text">Settings</span>
          </button>
        </li>
      </ul>
    </div>
  </nav>

  <!-- User Profile Footer -->
  <div class="sidebar-footer">
    <div class="user-popover" id="userPopover">
      <button class="popover-item" onclick="switchView('settings', document.querySelector('[data-view=settings]'))">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profile
      </button>
      <button class="popover-item" onclick="switchView('staff-activity', document.querySelector('[data-view=staff-activity]'))">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/></svg>
        Activity History
      </button>
      <button class="popover-item" onclick="switchView('notifications', document.querySelector('[data-view=notifications]'))">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        Notifications
      </button>
      <div class="popover-divider"></div>
      <button class="popover-item logout" onclick="window.location.href='/logout'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
        Logout
      </button>
    </div>
    <button class="user-trigger" id="userTrigger" onclick="toggleUserPopover()">
      <div class="avatar">
        MS
        <span class="online-dot"></span>
      </div>
      <div class="user-info">
        <span class="user-name">Dr. Maria Santos</span>
        <span class="user-role">Hospital Chief</span>
        <span class="user-sub">Medical Director</span>
      </div>
    </button>
  </div>
</aside>

<!-- Mobile overlay -->
<div class="sidebar-mobile-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

<style>
.sidebar-mobile-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(15,23,42,0.4); z-index: 39;
}
@media (max-width: 768px) {
  .sidebar.mobile-open ~ .sidebar-mobile-overlay { display: block; }
}
</style>
