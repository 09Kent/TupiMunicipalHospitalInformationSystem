<?php
// Med_Tech/includes/footer.php
?>

  <!-- Toast Notification Container -->
  <div id="toastContainer" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

  <!-- AOS Animation Script -->
  <script>
    AOS.init({
      duration: 450,
      easing: 'ease-out-cubic',
      once: true,
      offset: 20
    });
  </script>

  <!-- Lucide Icons Rendering -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) {
        lucide.createIcons();
      }
    });
  </script>

  <!-- Core JavaScript Interactivity & Medical Technologist LIS Logic -->
  <script>
    // Live Digital Clock
    setInterval(() => {
      const clockEl = document.getElementById('liveClockDisplay');
      if (clockEl) {
        const now = new Date();
        clockEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
      }
    }, 1000);

    // Toast Notification System
    function showToast(message, type = 'success') {
      const container = document.getElementById('toastContainer');
      if (!container) return;

      const toast = document.createElement('div');
      toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-xs font-semibold transform transition-all duration-300 translate-y-3 opacity-0 ${
        type === 'success' ? 'bg-emerald-950/90 text-emerald-100 border-emerald-700/60 backdrop-blur-md' :
        type === 'error' ? 'bg-rose-950/90 text-rose-100 border-rose-700/60 backdrop-blur-md' :
        type === 'warning' ? 'bg-amber-950/90 text-amber-100 border-amber-700/60 backdrop-blur-md' :
        'bg-slate-900/90 text-slate-100 border-slate-700/60 backdrop-blur-md'
      }`;

      let icon = 'check-circle';
      if (type === 'error') icon = 'alert-circle';
      if (type === 'warning') icon = 'alert-triangle';
      if (type === 'info') icon = 'info';

      toast.innerHTML = `
        <span class="w-5 h-5 flex items-center justify-center shrink-0">
          <i data-lucide="${icon}" class="w-4 h-4"></i>
        </span>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white ml-2">×</button>
      `;

      container.appendChild(toast);
      if (window.lucide) lucide.createIcons();

      // Trigger animation
      setTimeout(() => {
        toast.classList.remove('translate-y-3', 'opacity-0');
      }, 20);

      // Auto dismiss
      setTimeout(() => {
        toast.classList.add('translate-y-3', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
      }, 4000);
    }

    // Sidebar Collapse Toggle (Desktop)
    function toggleSidebarCollapse() {
      const sidebar = document.getElementById('sidebar');
      const icon = document.getElementById('sidebarCollapseIcon');
      if (!sidebar) return;

      if (sidebar.classList.contains('sidebar-expanded')) {
        sidebar.classList.remove('sidebar-expanded');
        sidebar.classList.add('sidebar-collapsed');
        if (icon) icon.setAttribute('data-lucide', 'panel-left-open');
      } else {
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.classList.add('sidebar-expanded');
        if (icon) icon.setAttribute('data-lucide', 'panel-left-close');
      }
      if (window.lucide) lucide.createIcons();
    }

    // Sidebar Mobile Toggle
    function toggleSidebarMobile() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      if (!sidebar || !overlay) return;

      if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
      }
    }

    // Toggle Profile Popover
    function toggleProfileDropdown() {
      const dd = document.getElementById('profileDropdown');
      if (dd) dd.classList.toggle('hidden');
    }

    // Toggle Notifications Dropdown
    function toggleNotificationDropdown() {
      const dd = document.getElementById('notificationDropdown');
      if (dd) dd.classList.toggle('hidden');
    }

    function markAllNotificationsRead() {
      showToast('All notifications marked as read', 'info');
      const dd = document.getElementById('notificationDropdown');
      if (dd) dd.classList.add('hidden');
    }

    function handleNotificationClick(targetTab) {
      if (targetTab) switchMainTab(targetTab);
      const dd = document.getElementById('notificationDropdown');
      if (dd) dd.classList.add('hidden');
    }

    // Close dropdowns on outside click
    document.addEventListener('click', (e) => {
      const profileDd = document.getElementById('profileDropdown');
      const notifDd = document.getElementById('notificationDropdown');
      
      if (profileDd && !e.target.closest('#sidebar') && !profileDd.classList.contains('hidden')) {
        profileDd.classList.add('hidden');
      }
      if (notifDd && !e.target.closest('#topbar') && !notifDd.classList.contains('hidden')) {
        notifDd.classList.add('hidden');
      }
    });

    // Main Tab Switcher (SPA Zero-Reload Navigation)
    function switchMainTab(tabId) {
      const tabs = ['dashboard', 'requests', 'samples', 'results', 'catalog', 'reports', 'ranges', 'notifications'];
      
      tabs.forEach(t => {
        const sec = document.getElementById(`tab-section-${t}`);
        const nav = document.getElementById(`nav-${t}`);
        if (sec) {
          if (t === tabId) {
            sec.classList.remove('hidden');
            sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
          } else {
            sec.classList.add('hidden');
          }
        }
        if (nav) {
          if (t === tabId) {
            nav.classList.add('bg-slate-100/90', 'text-slate-900', 'font-bold', 'border', 'border-slate-200/70', 'shadow-xs');
            nav.classList.remove('text-slate-600');
          } else {
            nav.classList.remove('bg-slate-100/90', 'text-slate-900', 'font-bold', 'border', 'border-slate-200/70', 'shadow-xs');
            nav.classList.add('text-slate-600');
          }
        }
      });

      // Update Topbar Title
      const titleEl = document.getElementById('topbarPageTitle');
      if (titleEl) {
        const titles = {
          dashboard: 'Laboratory Dashboard',
          requests: 'Laboratory Test Requests Queue',
          samples: 'Specimen & Sample Tracking',
          results: 'Laboratory Test Results Recording',
          catalog: 'Laboratory Test Catalog',
          reports: 'Diagnostic Laboratory Reports',
          ranges: 'Clinical Reference Ranges',
          notifications: 'Department Alerts & Notifications'
        };
        titleEl.textContent = titles[tabId] || 'Laboratory Dashboard';
      }

      // Close mobile sidebar if open
      if (window.innerWidth < 1024) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar && overlay) {
          sidebar.classList.add('-translate-x-full');
          overlay.classList.add('hidden');
        }
      }

      if (window.lucide) lucide.createIcons();
    }

    // Centerpiece Specimen Visualizer Switcher
    let currentSampleIdx = 0;
    const visualizerSamples = [
      {
        sample_id: 'SMP-2026-00124',
        patient_name: 'Juan Dela Cruz',
        patient_id: 'P-2026-0001',
        test: 'Complete Blood Count (CBC)',
        sample_type: 'Whole Blood (EDTA)',
        status: 'Processing',
        cells: '4.82 M',
        rack: 'RACK-HEM-A04',
        color: '#6366f1'
      },
      {
        sample_id: 'SMP-2026-00127',
        patient_name: 'Eduardo Dizon',
        patient_id: 'P-2026-0005',
        test: 'Kidney Function Panel',
        sample_type: 'Serum (SST)',
        status: 'Processing',
        cells: '—',
        rack: 'COBAS-C501-12',
        color: '#3b82f6'
      },
      {
        sample_id: 'SMP-2026-00129',
        patient_name: 'Mark Anthony Ramos',
        patient_id: 'P-2026-0012',
        test: 'Dengue Duo Rapid Test',
        sample_type: 'Serum (SST)',
        status: 'Completed',
        cells: '—',
        rack: 'ARCHIVE-SER-08',
        color: '#10b981'
      },
      {
        sample_id: 'SMP-2026-00130',
        patient_name: 'Crisanto Valenzuela',
        patient_id: 'P-2026-0006',
        test: 'Lipid Profile Panel',
        sample_type: 'Serum (SST)',
        status: 'For Verification',
        cells: '—',
        rack: 'CHEM-VERIFY-TRAY',
        color: '#8b5cf6'
      }
    ];

    function cycleVisualizerSample() {
      currentSampleIdx = (currentSampleIdx + 1) % visualizerSamples.length;
      const s = visualizerSamples[currentSampleIdx];

      const patientNameEl = document.getElementById('visualizerPatientName');
      const patientIdEl = document.getElementById('visualizerPatientId');
      const sampleIdEl = document.getElementById('visualizerSampleId');
      const testNameEl = document.getElementById('visualizerTestName');
      const specimenEl = document.getElementById('visualizerSpecimenType');
      const cellCountEl = document.getElementById('visualizerCellCount');
      const rackEl = document.getElementById('visualizerRack');
      const glowEl = document.getElementById('ambientGlow1');

      if (patientNameEl) patientNameEl.textContent = s.patient_name;
      if (patientIdEl) patientIdEl.textContent = `(${s.patient_id})`;
      if (sampleIdEl) sampleIdEl.textContent = s.sample_id;
      if (testNameEl) testNameEl.textContent = s.test;
      if (specimenEl) specimenEl.textContent = s.sample_type;
      if (cellCountEl) cellCountEl.textContent = s.cells;
      if (rackEl) rackEl.textContent = s.rack;
      if (glowEl) glowEl.style.backgroundColor = s.color;

      showToast(`Analyzer telemetry shifted to ${s.sample_id} (${s.patient_name})`, 'info');
    }

    // Global Search Handler
    function handleGlobalSearch(e) {
      const q = (e.target.value || '').toLowerCase().trim();
      const rows = document.querySelectorAll('.searchable-row');
      rows.forEach(row => {
        const text = (row.textContent || '').toLowerCase();
        if (text.includes(q)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Shortcut '/' key for global search
    document.addEventListener('keydown', (e) => {
      if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
        e.preventDefault();
        const searchInput = document.getElementById('globalSearchInput');
        if (searchInput) searchInput.focus();
      }
    });

    // Filter Request Queue Table
    function filterRequestTable(filterVal, btn) {
      const rows = document.querySelectorAll('#requestsTableBody tr');
      
      // Update button styling
      const pillContainer = document.getElementById('queueFilterPills');
      if (pillContainer) {
        pillContainer.querySelectorAll('button').forEach(b => {
          b.className = 'px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition';
        });
      }
      if (btn) {
        btn.className = 'px-3 py-1.5 rounded-xl bg-slate-900 text-white transition active:scale-95';
      }

      rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const priority = row.getAttribute('data-priority');
        
        if (filterVal === 'all') {
          row.style.display = '';
        } else if (filterVal === 'STAT' || filterVal === 'Urgent') {
          if (priority === filterVal) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        } else {
          if (status === filterVal) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      });
    }

    // Trigger Print for Laboratory Report
    function triggerPrintLabReport() {
      window.print();
    }
  </script>
  <script src="../includes/paginator.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.TablePaginator) {
        TablePaginator.paginateHtmlTable('#medtechRequestsTable', { pageSize: 8, recordLabel: 'requests' });
        TablePaginator.paginateHtmlTable('#medtechResultsTable', { pageSize: 8, recordLabel: 'results' });
        TablePaginator.paginateHtmlTable('#medtechCatalogTable', { pageSize: 8, recordLabel: 'tests' });
        TablePaginator.paginateHtmlTable('#medtechRangesTable', { pageSize: 8, recordLabel: 'parameters' });
      }
    });
  </script>
</body>
</html>
