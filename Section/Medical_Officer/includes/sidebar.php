<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Sidebar Component (User Role 3: Medical Records Officer)
 */
?>
<aside class="sidebar" id="sidebar">
  
  <!-- Brand / Logo Header -->
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <div class="brand-icon-box">
        <i data-lucide="cross" style="width: 22px; height: 22px; stroke-width: 2.5;"></i>
      </div>
      <div class="brand-info">
        <span class="brand-title">Tupi Municipal Hospital</span>
        <span class="brand-subtitle">HEALTHCARE SYSTEM</span>
      </div>
    </div>
    <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
      <i data-lucide="chevron-left" style="width: 18px; height: 18px;"></i>
    </button>
  </div>

  <!-- Navigation Menu -->
  <nav class="sidebar-nav">
    
    <!-- MAIN Section -->
    <div class="nav-section">
      <div class="nav-section-title">MAIN</div>
      
      <a href="#" class="nav-item active" data-view="dashboard" data-tooltip="Dashboard">
        <div class="nav-item-icon">
          <i data-lucide="layout-dashboard"></i>
        </div>
        <span class="nav-item-label">Dashboard</span>
      </a>

      <a href="#" class="nav-item" data-view="patients" data-tooltip="Patient Records">
        <div class="nav-item-icon">
          <i data-lucide="folder-open"></i>
        </div>
        <span class="nav-item-label">Patient Records</span>
      </a>

      <a href="#" class="nav-item" data-view="verification" data-tooltip="Record Verification">
        <div class="nav-item-icon">
          <i data-lucide="file-check"></i>
        </div>
        <span class="nav-item-label">Record Verification</span>
        <span class="nav-badge" id="sidebarPendingBadge">8</span>
      </a>

      <a href="#" class="nav-item" data-view="requests" data-tooltip="Medical Record Requests">
        <div class="nav-item-icon">
          <i data-lucide="file-clock"></i>
        </div>
        <span class="nav-item-label">Medical Record Requests</span>
        <span class="nav-badge" style="background:#0284c7;">5</span>
      </a>

      <a href="#" class="nav-item" data-view="history" data-tooltip="Patient History">
        <div class="nav-item-icon">
          <i data-lucide="history"></i>
        </div>
        <span class="nav-item-label">Patient History</span>
      </a>

      <a href="#" class="nav-item" data-view="summaries" data-tooltip="Record Summaries">
        <div class="nav-item-icon">
          <i data-lucide="file-text"></i>
        </div>
        <span class="nav-item-label">Record Summaries</span>
      </a>
    </div>

    <!-- OTHER Section -->
    <div class="nav-section">
      <div class="nav-section-title">OTHER</div>

      <a href="#" class="nav-item" data-view="notifications" data-tooltip="Notifications">
        <div class="nav-item-icon">
          <i data-lucide="bell"></i>
        </div>
        <span class="nav-item-label">Notifications</span>
      </a>

      <a href="#" class="nav-item" data-view="audit" data-tooltip="Audit Trail">
        <div class="nav-item-icon">
          <i data-lucide="shield-check"></i>
        </div>
        <span class="nav-item-label">Audit Trail</span>
      </a>

      <a href="#" class="nav-item" data-view="settings" data-tooltip="Settings">
        <div class="nav-item-icon">
          <i data-lucide="settings"></i>
        </div>
        <span class="nav-item-label">Settings</span>
      </a>
    </div>

  </nav>

  <!-- Sidebar User Profile Footer -->
  <div class="sidebar-footer">
    <div class="user-profile-card">
      <div class="user-avatar">
        MV
      </div>
      <div class="user-info-text">
        <span class="user-name"><?= htmlspecialchars($currentOfficer['name'] ?? 'Mark Valenzuela') ?></span>
        <span class="user-role-badge">Medical Records Officer</span>
      </div>
    </div>
  </div>

</aside>
