<?php
/**
 * TMHIS - Top Executive Header Component
 * Hospital Chief / Medical Director (Role 2)
 */
?>
<header class="top-header">
  <div class="header-left">
    <button class="mobile-toggle" id="mobileToggle" onclick="openMobileSidebar()">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
    </button>
    <div class="search-box">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="text" placeholder="Search reports, staff, departments..." id="globalSearch" onkeyup="handleGlobalSearch(this.value)">
    </div>
  </div>

  <div class="header-right">
    <div class="exec-badge">
      <span class="pulse-dot"></span>
      Medical Director
    </div>

    <div class="date-filter">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
      <select class="date-filter" id="dateRangeFilter">
        <option value="today">Today</option>
        <option value="week">This Week</option>
        <option value="month" selected>This Month</option>
        <option value="quarter">This Quarter</option>
        <option value="year">This Year</option>
      </select>
    </div>

    <div style="position:relative;">
      <button class="hdr-btn" id="notifBellBtn" onclick="toggleNotifPanel()">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        <span class="notif-dot" id="notifDot"></span>
      </button>

      <!-- Notification Dropdown -->
      <div class="notif-panel" id="notifPanel">
        <div class="notif-hdr">
          <span style="font-weight:700;font-size:.95rem;color:var(--slate-900);">Notifications</span>
          <button class="btn btn-sm btn-secondary" onclick="markAllRead()">Mark all read</button>
        </div>
        <div class="notif-list" id="notifList">
          <!-- Filled by JS -->
        </div>
      </div>
    </div>

    <button class="hdr-btn" title="Audit Trail" onclick="switchView('settings', document.querySelector('[data-view=settings]'));setTimeout(()=>document.getElementById('auditTab')?.click(),100);">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
    </button>
  </div>
</header>
