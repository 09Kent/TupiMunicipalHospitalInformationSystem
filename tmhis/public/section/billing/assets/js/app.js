/**
 * TMHIS Healthcare Billing & Cashier Dashboard Application Controller
 * Handles reactivity, state management, validation, modal workflows, printing, and UI transitions
 */

// Application State cloned from BILLING_DATA
const AppState = {
  activeView: 'dashboard',
  sidebarCollapsed: false,
  searchTerm: '',
  chargeFilter: 'all',
  paymentFilter: 'all',
  accountFilter: 'all',
  invoiceFilter: 'all',
  receiptFilter: 'all',
  
  // Data references
  currentUser: { ...BILLING_DATA.currentUser },
  metrics: { ...BILLING_DATA.metrics },
  patients: [ ...BILLING_DATA.patients ],
  charges: [ ...BILLING_DATA.charges ],
  bills: [ ...BILLING_DATA.bills ],
  invoices: [ ...BILLING_DATA.invoices ],
  payments: [ ...BILLING_DATA.payments ],
  officialReceipts: [ ...BILLING_DATA.officialReceipts ],
  recentActivities: [ ...BILLING_DATA.recentActivities ],
  notifications: [ ...BILLING_DATA.notifications ],
  settings: { ...BILLING_DATA.settings },

  // Currently selected items for active flows
  selectedPatientForBill: 'P-2026-001',
  activeInvoice: null,
  activeReceipt: null,
  chargeToEdit: null,
  chargeToDelete: null,
  paymentToEdit: null,

  // Table pagination state (strictly 8 records per page)
  pageSize: 8,
  pages: {
    charges: 1,
    payments: 1,
    invoices: 1,
    receipts: 1,
    accounts: 1
  }
};

// Currency Formatter for Philippine Peso (₱)
function formatPeso(amount) {
  const num = parseFloat(amount) || 0;
  return '₱' + num.toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

// Convert numbers to Philippine English Words for Official Receipts
function numberToWords(amount) {
  const num = Math.floor(amount);
  if (num === 0) return "Zero Pesos Only";

  const ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", 
                "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
  const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
  const thousands = ["", "Thousand", "Million", "Billion"];

  function convertGroup(n) {
    let result = "";
    if (n >= 100) {
      result += ones[Math.floor(n / 100)] + " Hundred ";
      n %= 100;
    }
    if (n >= 20) {
      result += tens[Math.floor(n / 10)] + " ";
      n %= 10;
    }
    if (n > 0) {
      result += ones[n] + " ";
    }
    return result.trim();
  }

  let wordResult = "";
  let thousandIdx = 0;
  let remaining = num;

  while (remaining > 0) {
    const chunk = remaining % 1000;
    if (chunk !== 0) {
      const groupStr = convertGroup(chunk);
      wordResult = groupStr + (thousands[thousandIdx] ? " " + thousands[thousandIdx] + " " : " ") + wordResult;
    }
    remaining = Math.floor(remaining / 1000);
    thousandIdx++;
  }

  return wordResult.trim() + " Pesos Only";
}

// Recompute Metrics
function recalculateMetrics() {
  let chargesTotal = 0;
  AppState.charges.forEach(c => {
    if (c.status !== 'Cancelled') chargesTotal += c.total;
  });

  let paymentsTotal = 0;
  AppState.payments.forEach(p => {
    if (p.status === 'Completed') paymentsTotal += p.amount;
  });

  let outstandingTotal = 0;
  let pendingCount = 0;
  AppState.bills.forEach(b => {
    if (b.status !== 'PAID') {
      outstandingTotal += b.outstandingBalance;
      pendingCount++;
    }
  });

  AppState.metrics.chargesToday = chargesTotal;
  AppState.metrics.paymentsToday = paymentsTotal;
  AppState.metrics.outstandingBalance = outstandingTotal;
  AppState.metrics.pendingBills = pendingCount;
  AppState.metrics.targetPercentage = Math.min(100, Math.round((paymentsTotal / AppState.metrics.dailyCollectionTarget) * 100));

  renderKPIs();
}

// Show Toast Alert
function showToast(message, type = 'success') {
  const toastContainer = document.getElementById('toast-container');
  if (!toastContainer) return;

  const toastId = 'toast-' + Date.now();
  const iconName = type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info';
  const bgColor = type === 'success' ? 'bg-emerald-600 text-white' : type === 'error' ? 'bg-rose-600 text-white' : 'bg-slate-800 text-white';

  const toastEl = document.createElement('div');
  toastEl.id = toastId;
  toastEl.className = `flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg transform transition-all duration-300 translate-y-2 opacity-0 ${bgColor} text-sm font-medium`;
  toastEl.innerHTML = `
    <i data-lucide="${iconName}" class="w-5 h-5 flex-shrink-0"></i>
    <span>${message}</span>
    <button onclick="document.getElementById('${toastId}').remove()" class="ml-auto opacity-75 hover:opacity-100">
      <i data-lucide="x" class="w-4 h-4"></i>
    </button>
  `;

  toastContainer.appendChild(toastEl);
  lucide.createIcons();

  setTimeout(() => {
    toastEl.classList.remove('translate-y-2', 'opacity-0');
    toastEl.classList.add('translate-y-0', 'opacity-100');
  }, 10);

  setTimeout(() => {
    toastEl.classList.add('opacity-0', 'translate-y-2');
    setTimeout(() => toastEl.remove(), 300);
  }, 4000);
}

// Add an Activity Log
function addActivityLog(type, title, description, icon, badgeColor) {
  const newActivity = {
    id: Date.now(),
    type: type,
    title: title,
    description: description,
    time: "Just now",
    icon: icon,
    badgeColor: badgeColor
  };
  AppState.recentActivities.unshift(newActivity);
  renderActivities();
}

// Reusable 8-Record Pagination Controller & UI Builder
function renderBillingPaginationHelper(tableKey, totalItems, onPageChange) {
  let container = document.getElementById(`${tableKey}-pagination-container`);
  if (!container) {
    const tbody = document.getElementById(`${tableKey}-tbody`);
    if (tbody && tbody.closest('table')) {
      const table = tbody.closest('table');
      container = document.createElement('div');
      container.id = `${tableKey}-pagination-container`;
      container.className = 'tmhis-billing-pagination-wrap';
      table.parentNode.insertBefore(container, table.nextSibling);
    }
  }
  if (!container) return;

  const pageSize = AppState.pageSize || 8;
  const currentPage = AppState.pages[tableKey] || 1;
  const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
  if (currentPage > totalPages) AppState.pages[tableKey] = totalPages;

  const startRecord = totalItems === 0 ? 0 : (currentPage - 1) * pageSize + 1;
  const endRecord = Math.min(startRecord + pageSize - 1, totalItems);

  let html = `
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3 px-4 bg-white border-t border-slate-200 text-xs font-medium text-slate-600 rounded-b-2xl select-none">
      <div class="text-slate-500 font-medium">
        ${totalItems === 0 
          ? 'Showing <span class="font-bold text-slate-800">0</span> records' 
          : `Showing <span class="font-bold text-slate-900">${startRecord}–${endRecord}</span> of <span class="font-bold text-slate-900">${totalItems}</span> records`}
      </div>
      <div class="flex items-center gap-1.5">
  `;

  // Previous button
  if (currentPage <= 1) {
    html += `<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</span>`;
  } else {
    html += `<button type="button" onclick="setBillingPage('${tableKey}', ${currentPage - 1})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</button>`;
  }

  // Page numeric buttons
  const startP = Math.max(1, currentPage - 2);
  const endP = Math.min(totalPages, currentPage + 2);

  if (startP > 1) {
    html += `<button type="button" onclick="setBillingPage('${tableKey}', 1)" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">1</button>`;
    if (startP > 2) html += `<span class="px-1 text-slate-400">...</span>`;
  }

  for (let p = startP; p <= endP; p++) {
    if (p === currentPage) {
      html += `<span class="w-8 h-8 rounded-lg flex items-center justify-center font-extrabold bg-brand-600 text-white shadow-xs">${p}</span>`;
    } else {
      html += `<button type="button" onclick="setBillingPage('${tableKey}', ${p})" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">${p}</button>`;
    }
  }

  if (endP < totalPages) {
    if (endP < totalPages - 1) html += `<span class="px-1 text-slate-400">...</span>`;
    html += `<button type="button" onclick="setBillingPage('${tableKey}', ${totalPages})" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">${totalPages}</button>`;
  }

  // Next button
  if (currentPage >= totalPages) {
    html += `<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></span>`;
  } else {
    html += `<button type="button" onclick="setBillingPage('${tableKey}', ${currentPage + 1})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></button>`;
  }

  html += `</div></div>`;
  container.innerHTML = html;
  if (window.lucide) lucide.createIcons();
}

function setBillingPage(tableKey, page) {
  AppState.pages[tableKey] = page;
  if (tableKey === 'charges') renderChargesTable();
  else if (tableKey === 'payments') renderPaymentsTable();
  else if (tableKey === 'invoices') renderInvoicesTable();
  else if (tableKey === 'receipts') renderReceiptsTable();
  else if (tableKey === 'accounts') renderAccountsTables();
}

// Navigation View Switcher
function switchView(viewName) {
  AppState.activeView = viewName;

  // Update navigation links
  document.querySelectorAll('.nav-link').forEach(link => {
    if (link.getAttribute('data-view') === viewName) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  // Update section visibility
  document.querySelectorAll('.view-section').forEach(section => {
    if (section.id === `view-${viewName}`) {
      section.classList.remove('hidden');
      section.classList.add('animate-stagger-1');
    } else {
      section.classList.add('hidden');
      section.classList.remove('animate-stagger-1');
    }
  });

  // Update breadcrumb / header title if needed
  const pageTitleMap = {
    'dashboard': 'BILLING DASHBOARD',
    'charges': 'PATIENT SERVICE CHARGES',
    'billing': 'PATIENT BILL PROCESSING',
    'payments': 'PAYMENT PROCESSING',
    'accounts': 'PATIENT ACCOUNTS & BALANCES',
    'invoices': 'PATIENT INVOICES',
    'receipts': 'OFFICIAL RECEIPTS',
    'notifications': 'BILLING NOTIFICATIONS',
    'settings': 'CASHIER WORKSTATION SETTINGS'
  };

  const pageSubMap = {
    'dashboard': 'Patient Charges, Bill Processing & Payment Management',
    'charges': 'Patient Service Charges & Charge Computation',
    'billing': 'Compute Total Patient Bills, Apply Discounts & Issue Invoices',
    'payments': 'Record Cash, Card, GCash, Bank Payments & Generate Official Receipts',
    'accounts': 'Track Account Ledgers, Aging Balances & Settle Outstanding Bills',
    'invoices': 'Hospital Invoices, Medical Billing Statements & Print Layouts',
    'receipts': 'BIR-Compliant Official Receipts with Amount in Words',
    'notifications': 'Real-time Payment and Billing Alerts',
    'settings': 'Cashier Station, Receipt Printer & Billing Policy Configuration'
  };

  const titleEl = document.getElementById('page-title');
  const subEl = document.getElementById('page-subtitle');
  if (titleEl) titleEl.innerText = pageTitleMap[viewName] || 'BILLING DASHBOARD';
  if (subEl) subEl.innerText = pageSubMap[viewName] || '';

  // Render view-specific content
  if (viewName === 'dashboard') renderDashboard();
  else if (viewName === 'charges') renderChargesTable();
  else if (viewName === 'billing') renderBillProcessing();
  else if (viewName === 'payments') renderPaymentsTable();
  else if (viewName === 'accounts') renderAccountsTables();
  else if (viewName === 'invoices') renderInvoicesTable();
  else if (viewName === 'receipts') renderReceiptsTable();
  else if (viewName === 'notifications') renderNotificationsView();

  window.scrollTo({ top: 0, behavior: 'smooth' });
  lucide.createIcons();
}

// Sidebar Collapse / Expand Toggle
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('main-content');
  const toggleIcon = document.getElementById('sidebar-toggle-icon');
  AppState.sidebarCollapsed = !AppState.sidebarCollapsed;

  if (AppState.sidebarCollapsed) {
    sidebar.classList.add('collapsed', 'w-[72px]');
    sidebar.classList.remove('w-[250px]');
    mainContent.classList.add('ml-[72px]');
    mainContent.classList.remove('ml-[250px]');
    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.sidebar-logo-text').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.sidebar-badge').forEach(el => el.classList.add('hidden'));
    if (toggleIcon) toggleIcon.setAttribute('data-lucide', 'chevron-right');
  } else {
    sidebar.classList.remove('collapsed', 'w-[72px]');
    sidebar.classList.add('w-[250px]');
    mainContent.classList.remove('ml-[72px]');
    mainContent.classList.add('ml-[250px]');
    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
    document.querySelectorAll('.sidebar-logo-text').forEach(el => el.classList.remove('hidden'));
    document.querySelectorAll('.sidebar-badge').forEach(el => el.classList.remove('hidden'));
    if (toggleIcon) toggleIcon.setAttribute('data-lucide', 'chevron-left');
  }
  lucide.createIcons();
}

// Render Summary KPIs
function renderKPIs() {
  const c1 = document.getElementById('kpi-charges-today');
  const c2 = document.getElementById('kpi-payments-today');
  const c3 = document.getElementById('kpi-outstanding');
  const c4 = document.getElementById('kpi-pending-bills');

  if (c1) c1.innerText = formatPeso(AppState.metrics.chargesToday);
  if (c2) c2.innerText = formatPeso(AppState.metrics.paymentsToday);
  if (c3) c3.innerText = formatPeso(AppState.metrics.outstandingBalance);
  if (c4) c4.innerText = AppState.metrics.pendingBills;

  // Collection Target Ring & Stats
  const targetPctEl = document.getElementById('target-pct-text');
  const targetRing = document.getElementById('target-ring-circle');
  if (targetPctEl) targetPctEl.innerText = `${AppState.metrics.targetPercentage}%`;
  if (targetRing) {
    const circumference = 2 * Math.PI * 40; // r=40
    const offset = circumference - (AppState.metrics.targetPercentage / 100) * circumference;
    targetRing.style.strokeDasharray = `${circumference}`;
    targetRing.style.strokeDashoffset = `${offset}`;
  }

  const cashEl = document.getElementById('stat-cash');
  const cardEl = document.getElementById('stat-card');
  const gcashEl = document.getElementById('stat-gcash');
  const bankEl = document.getElementById('stat-bank');

  if (cashEl) cashEl.innerText = formatPeso(AppState.metrics.paymentBreakdown.cash);
  if (cardEl) cardEl.innerText = formatPeso(AppState.metrics.paymentBreakdown.card);
  if (gcashEl) gcashEl.innerText = formatPeso(AppState.metrics.paymentBreakdown.gcash);
  if (bankEl) bankEl.innerText = formatPeso(AppState.metrics.paymentBreakdown.bankTransfer);
}

// Render Patient Billing Queue on Dashboard
function renderDashboardQueue() {
  const queueTbody = document.getElementById('dashboard-queue-tbody');
  if (!queueTbody) return;

  const queueItems = AppState.bills.slice(0, 8);
  let html = '';

  queueItems.forEach(b => {
    const statusClass = b.status === 'PAID' ? 'badge-paid' : b.status === 'PARTIALLY PAID' ? 'badge-partially-paid' : b.status === 'OVERDUE' ? 'badge-overdue' : 'badge-unpaid';
    html += `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="px-4 py-3">
          <div class="font-semibold text-slate-800">${b.patientName}</div>
          <div class="text-xs text-slate-400 font-mono">${b.patientId}</div>
        </td>
        <td class="px-4 py-3 font-mono text-xs font-semibold text-slate-600">${b.invoiceId}</td>
        <td class="px-4 py-3 font-medium text-slate-800">${formatPeso(b.totalBill)}</td>
        <td class="px-4 py-3 font-semibold ${b.outstandingBalance > 0 ? 'text-rose-600' : 'text-emerald-600'}">
          ${formatPeso(b.outstandingBalance)}
        </td>
        <td class="px-4 py-3">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${statusClass}">
            ${b.status}
          </span>
        </td>
        <td class="px-4 py-3 text-right">
          <button onclick="openPatientBill('${b.patientId}')" class="px-3 py-1 text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors inline-flex items-center gap-1">
            <i data-lucide="calculator" class="w-3.5 h-3.5"></i> Bill
          </button>
          ${b.outstandingBalance > 0 ? `
            <button onclick="openPaymentModalForBill('${b.billId}')" class="ml-1 px-3 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors inline-flex items-center gap-1">
              <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Pay
            </button>
          ` : `
            <button onclick="viewInvoice('${b.invoiceId}')" class="ml-1 px-3 py-1 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors inline-flex items-center gap-1">
              <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Invoice
            </button>
          `}
        </td>
      </tr>
    `;
  });

  queueTbody.innerHTML = html;
}

// Render Recent Activities on Dashboard
function renderActivities() {
  const container = document.getElementById('recent-activities-container');
  if (!container) return;

  let html = '';
  AppState.recentActivities.slice(0, 5).forEach(act => {
    html += `
      <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-colors border border-transparent hover:border-slate-100">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 ${act.badgeColor}">
          <i data-lucide="${act.icon === 'CreditCard' ? 'credit-card' : act.icon === 'FileText' ? 'file-text' : act.icon === 'Tag' ? 'tag' : act.icon === 'Receipt' ? 'receipt' : 'plus-circle'}" class="w-4 h-4"></i>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between">
            <h4 class="text-xs font-semibold text-slate-800 truncate">${act.title}</h4>
            <span class="text-[11px] text-slate-400 font-medium">${act.time}</span>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">${act.description}</p>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
}

// Full Dashboard Render
function renderDashboard() {
  recalculateMetrics();
  renderDashboardQueue();
  renderActivities();
}

// Render Charges Management Table
function renderChargesTable() {
  const tbody = document.getElementById('charges-tbody');
  if (!tbody) return;

  const search = AppState.searchTerm.toLowerCase();
  const filter = AppState.chargeFilter;

  const filtered = AppState.charges.filter(c => {
    const matchSearch = c.patientName.toLowerCase().includes(search) || 
                        c.patientId.toLowerCase().includes(search) || 
                        c.serviceName.toLowerCase().includes(search) || 
                        c.id.toLowerCase().includes(search);
    const matchFilter = filter === 'all' || c.status.toLowerCase() === filter.toLowerCase();
    return matchSearch && matchFilter;
  });

  if (filtered.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="10" class="text-center py-10 text-slate-400">
          <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
          No patient charges found matching your criteria.
        </td>
      </tr>
    `;
    lucide.createIcons();
    return;
  }

  const pageSize = AppState.pageSize || 8;
  const currentPage = AppState.pages.charges || 1;
  const totalItems = filtered.length;
  const start = (currentPage - 1) * pageSize;
  const paginated = filtered.slice(start, start + pageSize);

  let html = '';
  paginated.forEach(c => {
    const badgeClass = c.status === 'Paid' ? 'badge-paid' : c.status === 'Included in Bill' ? 'badge-included' : c.status === 'Cancelled' ? 'badge-cancelled' : 'badge-unbilled';
    html += `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="font-mono text-xs font-semibold text-slate-700">${c.id}</td>
        <td>
          <div class="font-semibold text-slate-800">${c.patientName}</div>
          <div class="text-xs text-slate-400 font-mono">${c.patientId}</div>
        </td>
        <td class="max-w-[220px]">
          <div class="font-medium text-slate-800 truncate" title="${c.serviceName}">${c.serviceName}</div>
          <div class="text-xs text-slate-400">${c.category}</div>
        </td>
        <td>
          <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">
            ${c.department}
          </span>
        </td>
        <td class="text-center font-medium">${c.quantity}</td>
        <td class="font-medium">${formatPeso(c.unitPrice)}</td>
        <td class="font-semibold text-slate-900">${formatPeso(c.total)}</td>
        <td class="text-xs text-slate-500">${c.date}</td>
        <td>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">
            ${c.status}
          </span>
        </td>
        <td class="text-right whitespace-nowrap">
          <button onclick="viewChargeDetails('${c.id}')" title="View Patient Charges" class="p-1.5 text-slate-600 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors">
            <i data-lucide="eye" class="w-4 h-4"></i>
          </button>
          <button onclick="openEditChargeModal('${c.id}')" title="Edit Charge" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
          </button>
          <button onclick="openDeleteChargeModal('${c.id}')" title="Delete Charge" class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = html;
  renderBillingPaginationHelper('charges', totalItems);
  lucide.createIcons();
}

// Bill Processing View Interactive Computation
function renderBillProcessing() {
  const patientSelect = document.getElementById('bill-patient-select');
  if (!patientSelect) return;

  // Populate patient select if empty
  if (patientSelect.options.length === 0) {
    let options = '';
    AppState.patients.forEach(p => {
      options += `<option value="${p.id}" ${p.id === AppState.selectedPatientForBill ? 'selected' : ''}>${p.name} (${p.id}) - ${p.room}</option>`;
    });
    patientSelect.innerHTML = options;
  }

  const selectedId = patientSelect.value || AppState.selectedPatientForBill;
  const patient = AppState.patients.find(p => p.id === selectedId) || AppState.patients[0];
  
  // Find or construct bill for patient
  let bill = AppState.bills.find(b => b.patientId === selectedId);
  if (!bill) {
    // Generate a default bill structure from unbilled charges
    const patientCharges = AppState.charges.filter(c => c.patientId === selectedId);
    let subtotal = 0;
    const items = patientCharges.map(c => {
      subtotal += c.total;
      return { service: c.serviceName, department: c.department, quantity: c.quantity, unitPrice: c.unitPrice, amount: c.total };
    });

    bill = {
      billId: "BILL-2026-" + Math.floor(100 + Math.random() * 900),
      patientId: patient.id,
      patientName: patient.name,
      invoiceId: "INV-2026-" + Math.floor(100 + Math.random() * 900),
      date: "Aug 28, 2026",
      items: items.length > 0 ? items : [
        { service: "Attending Specialist Consultation", department: "Internal Medicine", quantity: 1, unitPrice: 500, amount: 500 }
      ],
      subtotal: subtotal || 500,
      discountType: "None",
      discountRate: 0,
      discountAmount: 0,
      discountReason: "",
      authorizedBy: "",
      totalBill: subtotal || 500,
      amountPaid: 0,
      outstandingBalance: subtotal || 500,
      status: "UNPAID",
      dueDate: "Aug 30, 2026"
    };
    AppState.bills.push(bill);
  }

  // Populate Patient Header Details
  document.getElementById('bill-patient-name').innerText = patient.name;
  document.getElementById('bill-patient-id').innerText = patient.id;
  document.getElementById('bill-patient-age-gender').innerText = `${patient.age} yrs old • ${patient.gender}`;
  document.getElementById('bill-patient-room').innerText = patient.room;
  document.getElementById('bill-patient-physician').innerText = patient.physician;
  document.getElementById('bill-id-badge').innerText = bill.billId;

  // Render Charges Table
  const itemsTbody = document.getElementById('bill-items-tbody');
  let itemsHtml = '';
  bill.items.forEach(item => {
    itemsHtml += `
      <tr class="border-b border-slate-100 hover:bg-slate-50">
        <td class="py-3 px-4">
          <div class="font-semibold text-slate-800">${item.service}</div>
          <div class="text-xs text-slate-400">${item.department}</div>
        </td>
        <td class="py-3 px-4 text-center font-medium">${item.quantity}</td>
        <td class="py-3 px-4 text-right font-medium">${formatPeso(item.unitPrice)}</td>
        <td class="py-3 px-4 text-right font-semibold text-slate-900">${formatPeso(item.amount)}</td>
      </tr>
    `;
  });
  itemsTbody.innerHTML = itemsHtml;

  // Compute live amounts
  const subtotal = bill.items.reduce((acc, curr) => acc + curr.amount, 0);
  bill.subtotal = subtotal;
  const discountAmt = bill.discountAmount || 0;
  const totalBill = Math.max(0, subtotal - discountAmt);
  bill.totalBill = totalBill;
  const outstanding = Math.max(0, totalBill - bill.amountPaid);
  bill.outstandingBalance = outstanding;

  document.getElementById('bill-subtotal').innerText = formatPeso(subtotal);
  document.getElementById('bill-discount').innerText = `- ${formatPeso(discountAmt)}`;
  document.getElementById('bill-discount-label').innerText = bill.discountType !== 'None' ? `(${bill.discountType})` : '';
  document.getElementById('bill-total').innerText = formatPeso(totalBill);
  document.getElementById('bill-paid').innerText = formatPeso(bill.amountPaid);
  document.getElementById('bill-outstanding').innerText = formatPeso(outstanding);

  const statusBadge = document.getElementById('bill-status-badge');
  const badgeClass = bill.status === 'PAID' ? 'badge-paid' : bill.status === 'PARTIALLY PAID' ? 'badge-partially-paid' : bill.status === 'OVERDUE' ? 'badge-overdue' : 'badge-unpaid';
  statusBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}`;
  statusBadge.innerText = bill.status;

  lucide.createIcons();
}

// Select different patient in Bill Processing
function onPatientBillChange() {
  const patientSelect = document.getElementById('bill-patient-select');
  AppState.selectedPatientForBill = patientSelect.value;
  renderBillProcessing();
}

// Open Patient Bill for specific patient from any button
function openPatientBill(patientId) {
  AppState.selectedPatientForBill = patientId;
  switchView('billing');
  const patientSelect = document.getElementById('bill-patient-select');
  if (patientSelect) patientSelect.value = patientId;
  renderBillProcessing();
}

// Render Payments Table
function renderPaymentsTable() {
  const tbody = document.getElementById('payments-tbody');
  if (!tbody) return;

  const search = AppState.searchTerm.toLowerCase();
  const filter = AppState.paymentFilter;

  const filtered = AppState.payments.filter(p => {
    const matchSearch = p.patientName.toLowerCase().includes(search) ||
                        p.patientId.toLowerCase().includes(search) ||
                        p.invoiceId.toLowerCase().includes(search) ||
                        p.paymentId.toLowerCase().includes(search) ||
                        p.referenceNo.toLowerCase().includes(search);
    const matchFilter = filter === 'all' || p.method.toLowerCase().includes(filter.toLowerCase());
    return matchSearch && matchFilter;
  });

  if (filtered.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="9" class="text-center py-10 text-slate-400">
          <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
          No payment records found.
        </td>
      </tr>
    `;
    lucide.createIcons();
    return;
  }

  const pageSize = AppState.pageSize || 8;
  const currentPage = AppState.pages.payments || 1;
  const totalItems = filtered.length;
  const start = (currentPage - 1) * pageSize;
  const paginated = filtered.slice(start, start + pageSize);

  let html = '';
  paginated.forEach(p => {
    html += `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="font-mono text-xs font-semibold text-slate-700">${p.paymentId}</td>
        <td>
          <div class="font-semibold text-slate-800">${p.patientName}</div>
          <div class="text-xs text-slate-400 font-mono">${p.patientId}</div>
        </td>
        <td class="font-mono text-xs font-medium text-slate-600">${p.invoiceId}</td>
        <td class="font-semibold text-emerald-700 text-base">${formatPeso(p.amount)}</td>
        <td>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
            <i data-lucide="${p.method === 'Cash' ? 'banknote' : p.method.includes('Card') ? 'credit-card' : p.method === 'GCash' ? 'smartphone' : 'building-2'}" class="w-3.5 h-3.5 text-slate-500"></i>
            ${p.method}
          </span>
          <div class="text-[11px] text-slate-400 font-mono mt-0.5">${p.referenceNo}</div>
        </td>
        <td class="text-xs text-slate-500">${p.date}</td>
        <td class="text-xs text-slate-700 font-medium">${p.cashier}</td>
        <td>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold badge-paid">
            ${p.status}
          </span>
        </td>
        <td class="text-right whitespace-nowrap">
          <button onclick="openReceiptForPayment('${p.paymentId}')" title="Generate / View Official Receipt" class="px-2.5 py-1 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors inline-flex items-center gap-1">
            <i data-lucide="receipt" class="w-3.5 h-3.5"></i> Receipt
          </button>
          <button onclick="openEditPaymentModal('${p.paymentId}')" title="Update Payment Record" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
          </button>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = html;
  renderBillingPaginationHelper('payments', totalItems);
  lucide.createIcons();
}

// Render Accounts and Outstanding Balance Tables
function renderAccountsTables() {
  const tbody = document.getElementById('accounts-tbody');
  const outstandingTbody = document.getElementById('outstanding-tbody');
  if (!tbody || !outstandingTbody) return;

  const search = AppState.searchTerm.toLowerCase();
  
  // All accounts table
  const allAccounts = AppState.bills.filter(b => b.patientName.toLowerCase().includes(search) || b.patientId.toLowerCase().includes(search) || b.invoiceId.toLowerCase().includes(search));
  const accPageSize = AppState.pageSize || 8;
  const accCurrentPage = AppState.pages.accounts || 1;
  const accTotalItems = allAccounts.length;
  const accStart = (accCurrentPage - 1) * accPageSize;
  const accPaginated = allAccounts.slice(accStart, accStart + accPageSize);

  let accountsHtml = '';
  accPaginated.forEach(b => {
    const badgeClass = b.status === 'PAID' ? 'badge-paid' : b.status === 'PARTIALLY PAID' ? 'badge-partially-paid' : b.status === 'OVERDUE' ? 'badge-overdue' : 'badge-unpaid';
    accountsHtml += `
      <tr class="hover:bg-slate-50 transition-colors">
        <td>
          <div class="font-semibold text-slate-800">${b.patientName}</div>
          <div class="text-xs text-slate-400 font-mono">${b.patientId}</div>
        </td>
        <td class="font-medium">${formatPeso(b.subtotal)}</td>
        <td class="font-medium text-purple-600">${formatPeso(b.discountAmount)}</td>
        <td class="font-semibold text-slate-900">${formatPeso(b.totalBill)}</td>
        <td class="font-medium text-emerald-600">${formatPeso(b.amountPaid)}</td>
        <td class="font-bold ${b.outstandingBalance > 0 ? 'text-rose-600' : 'text-emerald-600'}">${formatPeso(b.outstandingBalance)}</td>
        <td>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">
            ${b.status}
          </span>
        </td>
        <td class="text-right">
          <button onclick="openPatientBill('${b.patientId}')" class="px-2.5 py-1 text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg">View</button>
        </td>
      </tr>
    `;
  });
  tbody.innerHTML = accountsHtml;
  renderBillingPaginationHelper('accounts', accTotalItems);

  // Outstanding Accounts Table
  let outHtml = '';
  const outstandingList = AppState.bills.filter(b => b.outstandingBalance > 0);
  outstandingList.forEach(b => {
    if (b.patientName.toLowerCase().includes(search) || b.patientId.toLowerCase().includes(search) || b.invoiceId.toLowerCase().includes(search)) {
      const badgeClass = b.status === 'PARTIALLY PAID' ? 'badge-partially-paid' : b.status === 'OVERDUE' ? 'badge-overdue' : 'badge-unpaid';
      outHtml += `
        <tr class="hover:bg-slate-50 transition-colors">
          <td>
            <div class="font-semibold text-slate-800">${b.patientName}</div>
            <div class="text-xs text-slate-400 font-mono">${b.patientId}</div>
          </td>
          <td class="font-mono text-xs font-medium text-slate-600">${b.invoiceId}</td>
          <td class="font-medium">${formatPeso(b.totalBill)}</td>
          <td class="font-medium text-emerald-600">${formatPeso(b.amountPaid)}</td>
          <td class="font-bold text-rose-600 text-base">${formatPeso(b.outstandingBalance)}</td>
          <td class="text-xs font-medium text-slate-600">${b.dueDate}</td>
          <td>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">
              ${b.status}
            </span>
          </td>
          <td class="text-right whitespace-nowrap">
            <button onclick="openPatientBill('${b.patientId}')" class="px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">Account</button>
            <button onclick="openPaymentModalForBill('${b.billId}')" class="ml-1 px-3 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg inline-flex items-center gap-1 font-semibold">
              <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Pay
            </button>
          </td>
        </tr>
      `;
    }
  });
  outstandingTbody.innerHTML = outHtml;
  lucide.createIcons();
}

// Render Invoices Table
function renderInvoicesTable() {
  const tbody = document.getElementById('invoices-tbody');
  if (!tbody) return;

  const search = AppState.searchTerm.toLowerCase();
  const filtered = AppState.invoices.filter(inv => {
    return inv.patientName.toLowerCase().includes(search) || 
           inv.patientId.toLowerCase().includes(search) || 
           inv.invoiceId.toLowerCase().includes(search);
  });

  const pageSize = AppState.pageSize || 8;
  const currentPage = AppState.pages.invoices || 1;
  const totalItems = filtered.length;
  const start = (currentPage - 1) * pageSize;
  const paginated = filtered.slice(start, start + pageSize);

  let html = '';
  paginated.forEach(inv => {
    const badgeClass = inv.status === 'PAID' ? 'badge-paid' : inv.status === 'PARTIALLY PAID' ? 'badge-partially-paid' : inv.status === 'OVERDUE' ? 'badge-overdue' : 'badge-unpaid';
    html += `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="font-mono text-xs font-semibold text-slate-800">${inv.invoiceId}</td>
        <td>
          <div class="font-semibold text-slate-800">${inv.patientName}</div>
          <div class="text-xs text-slate-400 font-mono">${inv.patientId}</div>
        </td>
        <td class="text-xs text-slate-500">${inv.date}</td>
        <td class="font-semibold text-slate-900">${formatPeso(inv.total)}</td>
        <td class="font-medium text-emerald-600">${formatPeso(inv.paid)}</td>
        <td class="font-bold ${inv.balance > 0 ? 'text-rose-600' : 'text-emerald-600'}">${formatPeso(inv.balance)}</td>
        <td>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">
            ${inv.status}
          </span>
        </td>
        <td class="text-xs text-slate-600">${inv.cashier}</td>
        <td class="text-right whitespace-nowrap">
          <button onclick="viewInvoice('${inv.invoiceId}')" title="View & Print Invoice" class="px-3 py-1 text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors inline-flex items-center gap-1">
            <i data-lucide="eye" class="w-3.5 h-3.5"></i> View
          </button>
          <button onclick="printInvoiceDirect('${inv.invoiceId}')" title="Direct Print" class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
          </button>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = html;
  renderBillingPaginationHelper('invoices', totalItems);
  lucide.createIcons();
}

// Render Official Receipts Table
function renderReceiptsTable() {
  const tbody = document.getElementById('receipts-tbody');
  if (!tbody) return;

  const search = AppState.searchTerm.toLowerCase();
  const filtered = AppState.officialReceipts.filter(r => {
    return r.receivedFrom.toLowerCase().includes(search) || 
           r.orNumber.toLowerCase().includes(search) || 
           r.invoiceId.toLowerCase().includes(search);
  });

  const rPageSize = AppState.pageSize || 8;
  const rCurrentPage = AppState.pages.receipts || 1;
  const rTotalItems = filtered.length;
  const rStart = (rCurrentPage - 1) * rPageSize;
  const rPaginated = filtered.slice(rStart, rStart + rPageSize);

  let html = '';
  rPaginated.forEach(r => {
    html += `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="font-mono text-xs font-bold text-indigo-900">${r.orNumber}</td>
        <td class="text-xs text-slate-500">${r.date}</td>
        <td>
          <div class="font-semibold text-slate-800">${r.receivedFrom}</div>
          <div class="text-xs text-slate-400 font-mono">${r.patientId}</div>
        </td>
        <td class="font-mono text-xs text-slate-600">${r.invoiceId}</td>
        <td class="font-bold text-emerald-700 text-base">${formatPeso(r.amount)}</td>
        <td>
          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
            ${r.paymentMethod}
          </span>
        </td>
        <td class="text-xs text-slate-700">${r.cashier}</td>
        <td>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold badge-paid">
            ${r.status}
          </span>
        </td>
        <td class="text-right whitespace-nowrap">
          <button onclick="viewReceipt('${r.orNumber}')" title="View & Print Official Receipt" class="px-3 py-1 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors inline-flex items-center gap-1">
            <i data-lucide="eye" class="w-3.5 h-3.5"></i> View OR
          </button>
          <button onclick="printReceiptDirect('${r.orNumber}')" title="Direct Print Receipt" class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
          </button>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = html;
  lucide.createIcons();
}

// Render Notifications View
function renderNotificationsView() {
  const container = document.getElementById('all-notifications-list');
  if (!container) return;

  let html = '';
  AppState.notifications.forEach(n => {
    html += `
      <div class="p-4 hospity-card flex items-start gap-4 ${n.unread ? 'border-sky-200 bg-sky-50/30' : ''}">
        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center flex-shrink-0">
          <i data-lucide="${n.type === 'payment' ? 'check-circle' : n.type === 'alert' ? 'alert-triangle' : 'bell'}" class="w-5 h-5"></i>
        </div>
        <div class="flex-1">
          <div class="flex items-center justify-between">
            <h4 class="font-semibold text-slate-800 text-sm">${n.title}</h4>
            <span class="text-xs text-slate-400">${n.time}</span>
          </div>
          <p class="text-sm text-slate-600 mt-1">${n.message}</p>
        </div>
      </div>
    `;
  });
  container.innerHTML = html;
  lucide.createIcons();
}

// =======================================================
// MODAL FLOWS & ACTIONS
// =======================================================

// 1. Create Charge Entry Modal
function openCreateChargeModal() {
  const modal = document.getElementById('modal-create-charge');
  if (!modal) return;

  // Populate patient dropdown
  const patientSelect = document.getElementById('charge-patient-select');
  let opts = '<option value="">-- Select Patient --</option>';
  AppState.patients.forEach(p => {
    opts += `<option value="${p.id}">${p.name} (${p.id})</option>`;
  });
  patientSelect.innerHTML = opts;

  // Reset inputs
  document.getElementById('charge-category').value = 'Laboratory';
  document.getElementById('charge-service-name').value = '';
  document.getElementById('charge-department').value = 'Laboratory';
  document.getElementById('charge-qty').value = '1';
  document.getElementById('charge-unit-price').value = '500';
  document.getElementById('charge-total-preview').innerText = '₱500';
  document.getElementById('charge-notes').value = '';

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  lucide.createIcons();
}

function updateChargeTotalCalculation() {
  const qty = parseFloat(document.getElementById('charge-qty').value) || 0;
  const unitPrice = parseFloat(document.getElementById('charge-unit-price').value) || 0;
  const total = qty * unitPrice;
  document.getElementById('charge-total-preview').innerText = formatPeso(total);
}

function saveNewCharge(event) {
  event.preventDefault();
  const patientId = document.getElementById('charge-patient-select').value;
  if (!patientId) {
    alert('Please select a patient.');
    return;
  }

  const patient = AppState.patients.find(p => p.id === patientId);
  const category = document.getElementById('charge-category').value;
  const serviceName = document.getElementById('charge-service-name').value;
  const department = document.getElementById('charge-department').value;
  const qty = parseInt(document.getElementById('charge-qty').value) || 1;
  const unitPrice = parseFloat(document.getElementById('charge-unit-price').value) || 0;
  const notes = document.getElementById('charge-notes').value;
  const total = qty * unitPrice;

  const newCharge = {
    id: `CHG-2026-${String(AppState.charges.length + 1).padStart(3, '0')}`,
    patientId: patient.id,
    patientName: patient.name,
    category: category,
    serviceName: serviceName,
    department: department,
    quantity: qty,
    unitPrice: unitPrice,
    total: total,
    date: "Aug 28, 2026",
    status: "Unbilled",
    notes: notes
  };

  AppState.charges.unshift(newCharge);
  closeModal('modal-create-charge');
  showToast(`Charge of ${formatPeso(total)} added for ${patient.name}`);
  addActivityLog('charge', 'New patient charge recorded', `${serviceName} (${formatPeso(total)}) added to ${patient.name}.`, 'PlusCircle', 'text-sky-600 bg-sky-50');
  
  recalculateMetrics();
  renderChargesTable();
}

// 2. Edit Charge Modal
function openEditChargeModal(chargeId) {
  const charge = AppState.charges.find(c => c.id === chargeId);
  if (!charge) return;

  AppState.chargeToEdit = charge;
  const modal = document.getElementById('modal-edit-charge');

  document.getElementById('edit-charge-id').value = charge.id;
  document.getElementById('edit-charge-patient').innerText = `${charge.patientName} (${charge.patientId})`;
  document.getElementById('edit-charge-service-name').value = charge.serviceName;
  document.getElementById('edit-charge-qty').value = charge.quantity;
  document.getElementById('edit-charge-unit-price').value = charge.unitPrice;
  document.getElementById('edit-charge-total-preview').innerText = formatPeso(charge.total);
  document.getElementById('edit-charge-notes').value = charge.notes || '';
  document.getElementById('edit-charge-updated-by').innerText = `${AppState.currentUser.name} • Aug 28, 2026`;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function updateEditChargeTotalCalculation() {
  const qty = parseFloat(document.getElementById('edit-charge-qty').value) || 0;
  const unitPrice = parseFloat(document.getElementById('edit-charge-unit-price').value) || 0;
  const total = qty * unitPrice;
  document.getElementById('edit-charge-total-preview').innerText = formatPeso(total);
}

function saveEditedCharge(event) {
  event.preventDefault();
  if (!AppState.chargeToEdit) return;

  const charge = AppState.chargeToEdit;
  const serviceName = document.getElementById('edit-charge-service-name').value;
  const qty = parseInt(document.getElementById('edit-charge-qty').value) || 1;
  const unitPrice = parseFloat(document.getElementById('edit-charge-unit-price').value) || 0;
  const notes = document.getElementById('edit-charge-notes').value;

  charge.serviceName = serviceName;
  charge.quantity = qty;
  charge.unitPrice = unitPrice;
  charge.total = qty * unitPrice;
  charge.notes = notes;

  closeModal('modal-edit-charge');
  showToast(`Charge ${charge.id} updated successfully`);
  recalculateMetrics();
  renderChargesTable();
}

// 3. Delete Charge Modal
function openDeleteChargeModal(chargeId) {
  const charge = AppState.charges.find(c => c.id === chargeId);
  if (!charge) return;

  if (charge.status === 'Paid' || charge.status === 'Included in Bill') {
    alert(`Cannot delete charge ${charge.id} because it is already '${charge.status}'. Please adjust invoice or permissions.`);
    return;
  }

  AppState.chargeToDelete = charge;
  const modal = document.getElementById('modal-delete-charge');
  document.getElementById('delete-charge-name').innerText = charge.serviceName;
  document.getElementById('delete-charge-amount').innerText = formatPeso(charge.total);
  document.getElementById('delete-charge-patient').innerText = `${charge.patientName} (${charge.patientId})`;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function confirmDeleteCharge() {
  if (!AppState.chargeToDelete) return;
  const id = AppState.chargeToDelete.id;
  AppState.charges = AppState.charges.filter(c => c.id !== id);
  closeModal('modal-delete-charge');
  showToast(`Charge ${id} deleted successfully.`, 'error');
  recalculateMetrics();
  renderChargesTable();
}

// 4. View Charge Breakdown Modal
function viewChargeDetails(chargeId) {
  const charge = AppState.charges.find(c => c.id === chargeId);
  if (!charge) return;

  const patient = AppState.patients.find(p => p.id === charge.patientId) || AppState.patients[0];
  const patientCharges = AppState.charges.filter(c => c.patientId === patient.id);

  const modal = document.getElementById('modal-view-patient-charges');
  document.getElementById('view-patient-name').innerText = patient.name;
  document.getElementById('view-patient-id').innerText = patient.id;
  document.getElementById('view-patient-room').innerText = patient.room;
  document.getElementById('view-patient-physician').innerText = patient.physician;

  let subtotal = 0;
  let rowsHtml = '';
  patientCharges.forEach(c => {
    subtotal += c.total;
    const bClass = c.status === 'Paid' ? 'badge-paid' : c.status === 'Included in Bill' ? 'badge-included' : 'badge-unbilled';
    rowsHtml += `
      <tr class="border-b border-slate-100 hover:bg-slate-50">
        <td class="py-2.5 px-3 font-medium text-slate-800">${c.serviceName}</td>
        <td class="py-2.5 px-3 text-slate-600 text-xs">${c.department}</td>
        <td class="py-2.5 px-3 text-center font-medium">${c.quantity}</td>
        <td class="py-2.5 px-3 text-right font-medium">${formatPeso(c.unitPrice)}</td>
        <td class="py-2.5 px-3 text-right font-semibold text-slate-900">${formatPeso(c.total)}</td>
        <td class="py-2.5 px-3 text-xs text-slate-400">${c.date}</td>
        <td class="py-2.5 px-3 text-center">
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold ${bClass}">${c.status}</span>
        </td>
      </tr>
    `;
  });

  document.getElementById('view-charges-tbody').innerHTML = rowsHtml;
  document.getElementById('view-charges-subtotal').innerText = formatPeso(subtotal);
  
  const bill = AppState.bills.find(b => b.patientId === patient.id);
  const discount = bill ? bill.discountAmount : 0;
  const total = Math.max(0, subtotal - discount);
  const paid = bill ? bill.amountPaid : 0;
  const balance = Math.max(0, total - paid);

  document.getElementById('view-charges-discount').innerText = formatPeso(discount);
  document.getElementById('view-charges-total').innerText = formatPeso(total);
  document.getElementById('view-charges-paid').innerText = formatPeso(paid);
  document.getElementById('view-charges-balance').innerText = formatPeso(balance);

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  lucide.createIcons();
}

// 5. Apply Discount Modal
function openApplyDiscountModal() {
  const selectedId = AppState.selectedPatientForBill;
  const bill = AppState.bills.find(b => b.patientId === selectedId);
  if (!bill) return;

  const modal = document.getElementById('modal-apply-discount');
  document.getElementById('discount-original-amount').innerText = formatPeso(bill.subtotal);
  document.getElementById('discount-type').value = 'Senior Citizen';
  document.getElementById('discount-mode').value = 'percentage';
  document.getElementById('discount-rate-value').value = '20';
  document.getElementById('discount-reason').value = 'Senior Citizen Republic Act 9994';
  document.getElementById('discount-authorized-by').value = 'Dr. Roberto Mendoza, MD';

  updateDiscountLivePreview();

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function updateDiscountLivePreview() {
  const selectedId = AppState.selectedPatientForBill;
  const bill = AppState.bills.find(b => b.patientId === selectedId);
  if (!bill) return;

  const mode = document.getElementById('discount-mode').value;
  const val = parseFloat(document.getElementById('discount-rate-value').value) || 0;
  let discountAmt = 0;

  if (mode === 'percentage') {
    discountAmt = (bill.subtotal * val) / 100;
  } else {
    discountAmt = val;
  }

  discountAmt = Math.min(bill.subtotal, discountAmt);
  const finalAmount = Math.max(0, bill.subtotal - discountAmt);

  document.getElementById('discount-calculated-amount').innerText = `- ${formatPeso(discountAmt)}`;
  document.getElementById('discount-final-amount').innerText = formatPeso(finalAmount);
}

function saveDiscount(event) {
  event.preventDefault();
  const selectedId = AppState.selectedPatientForBill;
  const bill = AppState.bills.find(b => b.patientId === selectedId);
  if (!bill) return;

  const discType = document.getElementById('discount-type').value;
  const mode = document.getElementById('discount-mode').value;
  const val = parseFloat(document.getElementById('discount-rate-value').value) || 0;
  const reason = document.getElementById('discount-reason').value;
  const authBy = document.getElementById('discount-authorized-by').value;

  let discountAmt = 0;
  if (mode === 'percentage') {
    discountAmt = (bill.subtotal * val) / 100;
  } else {
    discountAmt = val;
  }
  discountAmt = Math.min(bill.subtotal, discountAmt);

  bill.discountType = discType;
  bill.discountAmount = discountAmt;
  bill.discountReason = reason;
  bill.authorizedBy = authBy;
  bill.totalBill = Math.max(0, bill.subtotal - discountAmt);
  bill.outstandingBalance = Math.max(0, bill.totalBill - bill.amountPaid);

  closeModal('modal-apply-discount');
  showToast(`Discount of ${formatPeso(discountAmt)} applied to bill`);
  addActivityLog('discount', 'Discount applied', `${discType} discount (${formatPeso(discountAmt)}) applied to ${bill.patientName}.`, 'Tag', 'text-purple-600 bg-purple-50');

  renderBillProcessing();
  recalculateMetrics();
}

// 6. Record Payment Modal
function openPaymentModalForBill(billId) {
  const bill = AppState.bills.find(b => b.billId === billId);
  if (!bill) return;

  const modal = document.getElementById('modal-create-payment');
  document.getElementById('payment-bill-id').value = bill.billId;
  document.getElementById('payment-patient-name').innerText = `${bill.patientName} (${bill.patientId})`;
  document.getElementById('payment-invoice-num').innerText = bill.invoiceId;
  document.getElementById('payment-total-bill').innerText = formatPeso(bill.totalBill);
  document.getElementById('payment-already-paid').innerText = formatPeso(bill.amountPaid);
  document.getElementById('payment-outstanding').innerText = formatPeso(bill.outstandingBalance);

  document.getElementById('payment-input-amount').value = bill.outstandingBalance;
  document.getElementById('payment-method-select').value = 'Cash';
  document.getElementById('payment-ref-no').value = 'CSH-' + Math.floor(1000 + Math.random() * 9000);
  document.getElementById('payment-notes').value = 'Full settlement of bill';
  document.getElementById('payment-warning').classList.add('hidden');

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function onPaymentAmountInput() {
  const billId = document.getElementById('payment-bill-id').value;
  const bill = AppState.bills.find(b => b.billId === billId);
  if (!bill) return;

  const entered = parseFloat(document.getElementById('payment-input-amount').value) || 0;
  const warningEl = document.getElementById('payment-warning');

  if (entered > bill.outstandingBalance) {
    warningEl.classList.remove('hidden');
    warningEl.innerText = `Warning: Payment amount (₱${entered}) exceeds the outstanding balance (₱${bill.outstandingBalance}).`;
  } else {
    warningEl.classList.add('hidden');
  }
}

function processPaymentSubmit(event) {
  event.preventDefault();
  const billId = document.getElementById('payment-bill-id').value;
  const bill = AppState.bills.find(b => b.billId === billId);
  if (!bill) return;

  const amount = parseFloat(document.getElementById('payment-input-amount').value) || 0;
  if (amount <= 0) {
    alert('Please enter a valid payment amount.');
    return;
  }

  if (amount > bill.outstandingBalance) {
    if (!confirm('Payment amount exceeds outstanding balance. Do you wish to proceed with change/credit?')) {
      return;
    }
  }

  const method = document.getElementById('payment-method-select').value;
  const refNo = document.getElementById('payment-ref-no').value;
  const notes = document.getElementById('payment-notes').value;

  // Create payment record
  const newPayId = `PAY-2026-${String(AppState.payments.length + 1).padStart(3, '0')}`;
  const paymentObj = {
    paymentId: newPayId,
    patientId: bill.patientId,
    patientName: bill.patientName,
    invoiceId: bill.invoiceId,
    amount: amount,
    method: method,
    referenceNo: refNo,
    date: "Aug 28, 2026 " + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    cashier: AppState.currentUser.name,
    status: "Completed",
    notes: notes
  };
  AppState.payments.unshift(paymentObj);

  // Update Bill & Invoices
  bill.amountPaid += amount;
  bill.outstandingBalance = Math.max(0, bill.totalBill - bill.amountPaid);
  bill.status = bill.outstandingBalance === 0 ? 'PAID' : 'PARTIALLY PAID';

  const inv = AppState.invoices.find(i => i.invoiceId === bill.invoiceId);
  if (inv) {
    inv.paid = bill.amountPaid;
    inv.balance = bill.outstandingBalance;
    inv.status = bill.status;
  }

  // Create BIR-Compliant Official Receipt
  const newOrNum = `OR-2026-${String(AppState.officialReceipts.length + 1).padStart(3, '0')}`;
  const orObj = {
    orNumber: newOrNum,
    date: "August 28, 2026",
    receivedFrom: bill.patientName,
    patientId: bill.patientId,
    invoiceId: bill.invoiceId,
    amount: amount,
    paymentMethod: method,
    cashier: AppState.currentUser.name,
    amountInWords: numberToWords(amount),
    status: "Valid"
  };
  AppState.officialReceipts.unshift(orObj);

  closeModal('modal-create-payment');
  showToast(`Payment of ${formatPeso(amount)} recorded! Receipt ${newOrNum} issued.`);
  addActivityLog('payment', 'Payment received', `${formatPeso(amount)} received from ${bill.patientName} via ${method}.`, 'CreditCard', 'text-emerald-600 bg-emerald-50');

  recalculateMetrics();
  renderPaymentsTable();
  if (AppState.activeView === 'billing') renderBillProcessing();

  // Ask to view/print receipt
  setTimeout(() => {
    if (confirm(`Payment successful! Would you like to view/print Official Receipt ${newOrNum}?`)) {
      viewReceipt(newOrNum);
    }
  }, 300);
}

// 7. Edit Payment Record Modal
function openEditPaymentModal(paymentId) {
  const pay = AppState.payments.find(p => p.paymentId === paymentId);
  if (!pay) return;

  AppState.paymentToEdit = pay;
  const modal = document.getElementById('modal-edit-payment');

  document.getElementById('edit-payment-id').innerText = pay.paymentId;
  document.getElementById('edit-payment-patient').innerText = `${pay.patientName} (${pay.patientId})`;
  document.getElementById('edit-payment-amount').innerText = formatPeso(pay.amount);
  document.getElementById('edit-payment-method').value = pay.method;
  document.getElementById('edit-payment-ref-no').value = pay.referenceNo;
  document.getElementById('edit-payment-notes').value = pay.notes || '';
  document.getElementById('edit-payment-updated-by').innerText = `${AppState.currentUser.name} • Aug 28, 2026`;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function saveEditedPayment(event) {
  event.preventDefault();
  if (!AppState.paymentToEdit) return;

  const pay = AppState.paymentToEdit;
  pay.method = document.getElementById('edit-payment-method').value;
  pay.referenceNo = document.getElementById('edit-payment-ref-no').value;
  pay.notes = document.getElementById('edit-payment-notes').value;

  closeModal('modal-edit-payment');
  showToast(`Payment record ${pay.paymentId} updated successfully`);
  renderPaymentsTable();
}

// 8. Generate & View Invoice
function viewInvoice(invoiceId) {
  let inv = AppState.invoices.find(i => i.invoiceId === invoiceId);
  if (!inv) {
    inv = AppState.invoices[0];
  }

  const patient = AppState.patients.find(p => p.id === inv.patientId) || AppState.patients[0];
  const bill = AppState.bills.find(b => b.invoiceId === inv.invoiceId) || AppState.bills[0];

  AppState.activeInvoice = { inv, patient, bill };
  const modal = document.getElementById('modal-view-invoice');

  // Populate Invoice Preview
  document.getElementById('inv-preview-hospital-name').innerText = AppState.settings.hospitalName;
  document.getElementById('inv-preview-hospital-address').innerText = AppState.settings.hospitalAddress;
  document.getElementById('inv-preview-hospital-contact').innerText = AppState.settings.hospitalContact;
  
  document.getElementById('inv-preview-num').innerText = inv.invoiceId;
  document.getElementById('inv-preview-date').innerText = inv.date;
  document.getElementById('inv-preview-patient-name').innerText = patient.name;
  document.getElementById('inv-preview-patient-id').innerText = patient.id;
  document.getElementById('inv-preview-room').innerText = patient.room;
  document.getElementById('inv-preview-physician').innerText = patient.physician;

  let itemsHtml = '';
  bill.items.forEach(it => {
    itemsHtml += `
      <tr class="border-b border-slate-100">
        <td class="py-2.5 px-3">
          <div class="font-medium text-slate-800">${it.service}</div>
          <div class="text-xs text-slate-400">${it.department}</div>
        </td>
        <td class="py-2.5 px-3 text-center">${it.quantity}</td>
        <td class="py-2.5 px-3 text-right">${formatPeso(it.unitPrice)}</td>
        <td class="py-2.5 px-3 text-right font-semibold text-slate-900">${formatPeso(it.amount)}</td>
      </tr>
    `;
  });
  document.getElementById('inv-preview-items-tbody').innerHTML = itemsHtml;

  document.getElementById('inv-preview-subtotal').innerText = formatPeso(bill.subtotal);
  document.getElementById('inv-preview-discount').innerText = `- ${formatPeso(bill.discountAmount)}`;
  document.getElementById('inv-preview-total').innerText = formatPeso(bill.totalBill);
  document.getElementById('inv-preview-paid').innerText = formatPeso(inv.paid);
  document.getElementById('inv-preview-balance').innerText = formatPeso(inv.balance);
  document.getElementById('inv-preview-status').innerText = inv.status;
  document.getElementById('inv-preview-cashier').innerText = inv.cashier;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  lucide.createIcons();
}

function printInvoiceDirect(invoiceId) {
  viewInvoice(invoiceId);
  setTimeout(() => {
    printCurrentInvoice();
  }, 200);
}

function printCurrentInvoice() {
  if (!AppState.activeInvoice) return;
  const { inv, patient, bill } = AppState.activeInvoice;

  let itemsHtml = '';
  bill.items.forEach(it => {
    itemsHtml += `
      <tr>
        <td><strong>${it.service}</strong><br><small>${it.department}</small></td>
        <td style="text-align:center;">${it.quantity}</td>
        <td style="text-align:right;">${formatPeso(it.unitPrice)}</td>
        <td style="text-align:right;"><strong>${formatPeso(it.amount)}</strong></td>
      </tr>
    `;
  });

  const printArea = document.getElementById('printable-area');
  printArea.innerHTML = `
    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; color: #111;">
      <div style="border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <h2 style="margin: 0; font-size: 20px; color: #0284c7; font-weight: 800;">TMHIS HEALTHCARE CENTER</h2>
          <p style="margin: 3px 0 0; font-size: 12px; color: #555;">${AppState.settings.hospitalAddress}</p>
          <p style="margin: 2px 0 0; font-size: 12px; color: #555;">${AppState.settings.hospitalContact} | TIN: ${AppState.settings.hospitalTin}</p>
        </div>
        <div style="text-align: right;">
          <h3 style="margin: 0; font-size: 18px; font-weight: bold; letter-spacing: 1px;">PATIENT INVOICE</h3>
          <p style="margin: 4px 0 0; font-family: monospace; font-size: 14px; font-weight: bold;">${inv.invoiceId}</p>
          <p style="margin: 2px 0 0; font-size: 12px;">Date: ${inv.date}</p>
        </div>
      </div>

      <div style="background-color: #f8fafc; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
        <div>
          <p style="margin: 0 0 4px;"><strong>Patient Name:</strong> ${patient.name}</p>
          <p style="margin: 0 0 4px;"><strong>Patient ID:</strong> ${patient.id}</p>
          <p style="margin: 0;"><strong>Room / Bed:</strong> ${patient.room}</p>
        </div>
        <div>
          <p style="margin: 0 0 4px;"><strong>Attending Physician:</strong> ${patient.physician}</p>
          <p style="margin: 0 0 4px;"><strong>Payment Status:</strong> <strong>${inv.status}</strong></p>
          <p style="margin: 0;"><strong>Cashier:</strong> ${inv.cashier}</p>
        </div>
      </div>

      <table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px;">
        <thead>
          <tr style="border-top: 1.5px solid #000; border-bottom: 1.5px solid #000;">
            <th style="text-align: left; padding: 8px 4px;">SERVICE / DESCRIPTION</th>
            <th style="text-align: center; padding: 8px 4px; width: 60px;">QTY</th>
            <th style="text-align: right; padding: 8px 4px; width: 100px;">UNIT PRICE</th>
            <th style="text-align: right; padding: 8px 4px; width: 110px;">AMOUNT</th>
          </tr>
        </thead>
        <tbody>
          ${itemsHtml}
        </tbody>
      </table>

      <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
        <div style="width: 280px; font-size: 13px;">
          <div style="display: flex; justify-content: space-between; padding: 3px 0;">
            <span>Subtotal:</span>
            <span>${formatPeso(bill.subtotal)}</span>
          </div>
          <div style="display: flex; justify-content: space-between; padding: 3px 0; color: #7e22ce;">
            <span>Discount:</span>
            <span>- ${formatPeso(bill.discountAmount)}</span>
          </div>
          <div style="display: flex; justify-content: space-between; padding: 6px 0; border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; font-weight: bold; font-size: 15px;">
            <span>TOTAL BILL:</span>
            <span>${formatPeso(bill.totalBill)}</span>
          </div>
          <div style="display: flex; justify-content: space-between; padding: 3px 0; margin-top: 4px;">
            <span>Amount Paid:</span>
            <span style="color: #059669; font-weight: bold;">${formatPeso(inv.paid)}</span>
          </div>
          <div style="display: flex; justify-content: space-between; padding: 3px 0; font-weight: bold;">
            <span>Outstanding Balance:</span>
            <span style="color: ${inv.balance > 0 ? '#e11d48' : '#059669'};">${formatPeso(inv.balance)}</span>
          </div>
        </div>
      </div>

      <div style="margin-top: 40px; border-top: 1px solid #cbd5e1; padding-top: 16px; display: flex; justify-content: space-between; font-size: 12px; color: #555;">
        <div>
          <p style="margin: 0;">This document serves as an official billing statement.</p>
          <p style="margin: 2px 0 0;">Thank you for trusting TMHIS Healthcare Center.</p>
        </div>
        <div style="text-align: right;">
          <p style="margin: 0; border-bottom: 1px solid #000; padding-bottom: 2px; width: 160px; font-weight: bold; text-align: center;">${inv.cashier}</p>
          <p style="margin: 2px 0 0; text-align: center;">Authorized Cashier Signature</p>
        </div>
      </div>
    </div>
  `;

  window.print();
}

// 9. View & Print Official Receipt (OR)
function viewReceipt(orNumber) {
  let receipt = AppState.officialReceipts.find(r => r.orNumber === orNumber);
  if (!receipt) {
    receipt = AppState.officialReceipts[0];
  }

  AppState.activeReceipt = receipt;
  const modal = document.getElementById('modal-view-receipt');

  document.getElementById('or-preview-num').innerText = receipt.orNumber;
  document.getElementById('or-preview-date').innerText = receipt.date;
  document.getElementById('or-preview-received-from').innerText = receipt.receivedFrom;
  document.getElementById('or-preview-invoice-num').innerText = receipt.invoiceId;
  document.getElementById('or-preview-amount').innerText = formatPeso(receipt.amount);
  document.getElementById('or-preview-payment-method').innerText = receipt.paymentMethod;
  document.getElementById('or-preview-cashier').innerText = receipt.cashier;
  document.getElementById('or-preview-amount-words').innerText = receipt.amountInWords || numberToWords(receipt.amount);

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  lucide.createIcons();
}

function openReceiptForPayment(paymentId) {
  const pay = AppState.payments.find(p => p.paymentId === paymentId);
  if (!pay) return;

  const receipt = AppState.officialReceipts.find(r => r.invoiceId === pay.invoiceId && r.amount === pay.amount) || AppState.officialReceipts[0];
  viewReceipt(receipt.orNumber);
}

function printReceiptDirect(orNumber) {
  viewReceipt(orNumber);
  setTimeout(() => {
    printCurrentReceipt();
  }, 200);
}

function printCurrentReceipt() {
  if (!AppState.activeReceipt) return;
  const r = AppState.activeReceipt;

  const printArea = document.getElementById('printable-area');
  printArea.innerHTML = `
    <div style="font-family: Arial, sans-serif; padding: 24px; max-width: 650px; margin: 0 auto; border: 2px solid #0f172a; border-radius: 8px; color: #111;">
      <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 16px;">
        <h2 style="margin: 0; font-size: 22px; font-weight: 800; color: #0284c7;">TMHIS HEALTHCARE MEDICAL CENTER</h2>
        <p style="margin: 4px 0 0; font-size: 12px; color: #555;">${AppState.settings.hospitalAddress}</p>
        <p style="margin: 2px 0 0; font-size: 12px; color: #555;">TIN: ${AppState.settings.hospitalTin} | Tel: ${AppState.settings.hospitalContact}</p>
        <div style="display: inline-block; background-color: #0f172a; color: #fff; padding: 4px 16px; border-radius: 4px; font-size: 14px; font-weight: bold; margin-top: 10px; letter-spacing: 1px;">
          OFFICIAL RECEIPT
        </div>
      </div>

      <div style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 13px;">
        <div><strong>OR Number:</strong> <span style="font-family: monospace; font-weight: bold; font-size: 15px; color: #0369a1;">${r.orNumber}</span></div>
        <div><strong>Date:</strong> ${r.date}</div>
      </div>

      <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; margin-bottom: 16px; font-size: 13px; line-height: 1.8;">
        <div><strong>Received From:</strong> ${r.receivedFrom}</div>
        <div><strong>Settlement For Invoice:</strong> <span style="font-family: monospace;">${r.invoiceId}</span></div>
        <div><strong>Payment Method:</strong> ${r.paymentMethod}</div>
        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #cbd5e1;">
          <strong>Amount Received:</strong> <span style="font-size: 18px; font-weight: bold; color: #059669;">${formatPeso(r.amount)}</span>
        </div>
        <div><strong>Amount in Words:</strong> <em>${r.amountInWords}</em></div>
      </div>

      <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: flex-end; font-size: 12px;">
        <div>
          <p style="margin: 0; color: #64748b;">BIR Permit / POS Counter #03</p>
          <p style="margin: 2px 0 0; color: #64748b;">Official Hospital Receipt</p>
        </div>
        <div style="text-align: center;">
          <div style="border-bottom: 1.5px solid #000; width: 180px; padding-bottom: 4px; font-weight: bold;">${r.cashier}</div>
          <div style="margin-top: 4px; color: #334155;">Collecting Cashier</div>
        </div>
      </div>
    </div>
  `;

  window.print();
}

// 10. User Profile Dropdown & Modal
function toggleProfileDropdown() {
  const menu = document.getElementById('profile-dropdown-menu');
  if (menu) menu.classList.toggle('hidden');
}

function openProfileModal() {
  const menu = document.getElementById('profile-dropdown-menu');
  if (menu) menu.classList.add('hidden');
  const modal = document.getElementById('modal-user-profile');
  if (modal) {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }
}

// Close any open modal
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
}

// Global Filter and Search Listener
function onGlobalSearch(val) {
  AppState.searchTerm = val;
  if (AppState.activeView === 'charges') renderChargesTable();
  else if (AppState.activeView === 'payments') renderPaymentsTable();
  else if (AppState.activeView === 'accounts') renderAccountsTables();
  else if (AppState.activeView === 'invoices') renderInvoicesTable();
  else if (AppState.activeView === 'receipts') renderReceiptsTable();
}

// App Initialization
document.addEventListener('DOMContentLoaded', () => {
  // Initialize Lucide Icons
  lucide.createIcons();

  // Render initial dashboard
  renderDashboard();

  // Close modals on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-wrapper:not(.hidden)').forEach(modal => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      });
      const profileMenu = document.getElementById('profile-dropdown-menu');
      if (profileMenu) profileMenu.classList.add('hidden');
    }
  });

  // Global search input listener
  const searchInput = document.getElementById('global-search-input');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      onGlobalSearch(e.target.value);
    });
  }
});
