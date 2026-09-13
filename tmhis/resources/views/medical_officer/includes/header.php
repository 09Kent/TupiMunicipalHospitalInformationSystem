<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Top Header Component
 */
?>
<header class="top-header">
  
  <div class="header-left">
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
      <i data-lucide="menu" style="width: 22px; height: 22px;"></i>
    </button>
    <div class="header-page-title-group">
      <h1 class="header-page-title" id="pageTitle">MEDICAL RECORDS DASHBOARD</h1>
      <span class="header-page-subtitle" id="pageSubtitle">Patient Record Administration & Medical Record Retrieval</span>
    </div>
  </div>

  <div class="header-right">
    <!-- Confidentiality Indicator Tag -->
    <div class="confidential-pill">
      <i data-lucide="lock" style="width: 13px; height: 13px;"></i>
      <span>CONFIDENTIAL RECORDS</span>
    </div>

    <!-- Live Global Search -->
    <div class="header-search-box">
      <i data-lucide="search" class="header-search-icon" style="width: 15px; height: 15px;"></i>
      <input type="text" class="header-search-input" id="globalSearchInput" placeholder="Search patient ID, name, DOB...">
    </div>

    <!-- Notification Bell -->
    <button class="header-action-btn" onclick="window.hospityNavigate('notifications')" title="Notifications">
      <i data-lucide="bell" style="width: 18px; height: 18px;"></i>
      <span class="badge-dot" id="headerNotifBadge"></span>
    </button>

    <!-- User Role Tag -->
    <div class="role-badge-header">
      <i data-lucide="shield-check" style="width: 14px; height: 14px;"></i>
      <span>Role 3: Medical Records Officer</span>
    </div>
  </div>

</header>
