<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Role 9: BILLING / CASHIER STAFF INTERFACE
 * 
 * Features:
 * - Patient Service Charge Computation & Management (CRUD)
 * - Interactive Patient Bill Processing & Live Formulas
 * - Senior Citizen / PWD / Employee Discount Applicator
 * - Payment Records & Validation (Cash, Card, GCash, Bank Transfer)
 * - BIR-Compliant Official Receipt Generation with Amount in Words
 * - Itemized Medical Billing Invoices with Print Layouts
 * - Collapsible Sidebar & Soft Healthcare Aesthetics
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Billing & Cashier Dashboard | Tupi Municipal Hospital Information Management System</title>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              200: '#bae6fd',
              500: '#0ea5e9',
              600: '#0284c7',
              700: '#0369a1',
              800: '#075985',
              900: '#0c4a6e',
            }
          }
        }
      }
    }
  </script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Custom Styles -->
  <link rel="stylesheet" href="/section/billing/assets/css/styles.css">
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex">

  <!-- ========================================================
       LEFT VERTICAL SIDEBAR (COLLAPSIBLE 250px -> 72px)
       ======================================================== -->
  <aside id="sidebar" class="w-[250px] bg-white border-r border-slate-200/80 fixed inset-y-0 left-0 z-40 flex flex-col justify-between select-none">
    
    <!-- Sidebar Top Header / Hospital Brand -->
    <div>
      <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100">
        <div class="flex items-center gap-3 cursor-pointer" onclick="switchView('dashboard')">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-700 to-brand-500 flex items-center justify-center text-white font-black text-lg shadow-sm shadow-brand-500/30">
            H
          </div>
          <div class="sidebar-logo-text flex flex-col">
            <span class="font-bold text-base tracking-tight text-slate-900 font-heading">Tupi Municipal Hospital</span>
            <span class="text-[10px] font-semibold text-brand-600 uppercase tracking-widest">Billing / Cashier</span>
          </div>
        </div>
        <button id="sidebar-toggle-btn" onclick="toggleSidebar()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Toggle Sidebar">
          <i id="sidebar-toggle-icon" data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>
      </div>

      <!-- Navigation Section -->
      <div class="py-4 px-3 space-y-6 overflow-y-auto max-h-[calc(100vh-160px)]">
        <!-- Main Navigation -->
        <div>
          <div class="sidebar-text px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
            Main
          </div>
          <nav class="space-y-1">
            <!-- Dashboard -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('dashboard')" data-view="dashboard" class="nav-link active flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Dashboard</span>
              </a>
              <span class="sidebar-tooltip">Dashboard</span>
            </div>

            <!-- Patient Charges -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('charges')" data-view="charges" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="receipt-text" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Patient Charges</span>
              </a>
              <span class="sidebar-tooltip">Patient Charges</span>
            </div>

            <!-- Bill Processing -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('billing')" data-view="billing" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="calculator" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Bill Processing</span>
              </a>
              <span class="sidebar-tooltip">Bill Processing</span>
            </div>

            <!-- Payments -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('payments')" data-view="payments" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="credit-card" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Payments</span>
              </a>
              <span class="sidebar-tooltip">Payments</span>
            </div>

            <!-- Patient Accounts -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('accounts')" data-view="accounts" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="wallet-cards" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Patient Accounts</span>
              </a>
              <span class="sidebar-tooltip">Patient Accounts</span>
            </div>

            <!-- Invoices -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('invoices')" data-view="invoices" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="file-text" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Invoices</span>
              </a>
              <span class="sidebar-tooltip">Invoices</span>
            </div>

            <!-- Official Receipts -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('receipts')" data-view="receipts" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="receipt" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Official Receipts</span>
              </a>
              <span class="sidebar-tooltip">Official Receipts</span>
            </div>
          </nav>
        </div>

        <!-- Other Navigation -->
        <div>
          <div class="sidebar-text px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
            Other
          </div>
          <nav class="space-y-1">
            <!-- Notifications -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('notifications')" data-view="notifications" class="nav-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <div class="flex items-center gap-3">
                  <i data-lucide="bell" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                  <span class="sidebar-text">Notifications</span>
                </div>
                <span class="sidebar-badge px-1.5 py-0.5 text-[10px] font-bold bg-rose-500 text-white rounded-full">2</span>
              </a>
              <span class="sidebar-tooltip">Notifications</span>
            </div>

            <!-- Settings -->
            <div class="sidebar-item relative">
              <a href="javascript:void(0)" onclick="switchView('settings')" data-view="settings" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition-all group">
                <i data-lucide="settings" class="w-5 h-5 flex-shrink-0 text-slate-500 group-hover:text-brand-600"></i>
                <span class="sidebar-text">Settings</span>
              </a>
              <span class="sidebar-tooltip">Settings</span>
            </div>
          </nav>
        </div>
      </div>
    </div>

    <!-- Sidebar Bottom / User Profile Card -->
    <div class="p-3 border-t border-slate-100 relative">
      <div onclick="toggleProfileDropdown()" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors group">
        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=256&auto=format&fit=crop" alt="Maria Santos" class="w-9 h-9 rounded-full object-cover ring-2 ring-brand-100 flex-shrink-0">
        <div class="sidebar-text flex-1 min-w-0">
          <p class="text-xs font-bold text-slate-800 truncate">Maria Santos</p>
          <p class="text-[11px] text-slate-500 truncate">Cashier Staff</p>
        </div>
        <i data-lucide="more-vertical" class="sidebar-text w-4 h-4 text-slate-400 group-hover:text-slate-600"></i>
      </div>

      <!-- Profile Dropdown Menu -->
      <div id="profile-dropdown-menu" class="hidden absolute bottom-16 left-3 right-3 bg-white rounded-xl shadow-xl border border-slate-200/80 p-1.5 space-y-0.5 z-50 animate-pop">
        <a href="javascript:void(0)" onclick="openProfileModal()" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
          <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i> Profile
        </a>
        <a href="javascript:void(0)" onclick="switchView('payments'); toggleProfileDropdown();" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
          <i data-lucide="history" class="w-3.5 h-3.5 text-slate-400"></i> Transaction History
        </a>
        <a href="javascript:void(0)" onclick="switchView('notifications'); toggleProfileDropdown();" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
          <i data-lucide="bell" class="w-3.5 h-3.5 text-slate-400"></i> Notifications
        </a>
        <div class="my-1 border-t border-slate-100"></div>
        <a href="javascript:void(0)" onclick="showToast('Logged out successfully', 'info'); toggleProfileDropdown();" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 rounded-lg">
          <i data-lucide="log-out" class="w-3.5 h-3.5 text-rose-500"></i> Logout
        </a>
      </div>
    </div>
  </aside>

  <!-- ========================================================
       MAIN CONTENT CONTAINER
       ======================================================== -->
  <main id="main-content" class="flex-1 ml-[250px] min-h-screen flex flex-col transition-all duration-300">
    
    <!-- Top Sticky Header -->
    <header id="top-nav" class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-6 sm:px-8 flex items-center justify-between">
      <!-- Title & Subtitle -->
      <div>
        <h1 id="page-title" class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight font-heading">
          BILLING DASHBOARD
        </h1>
        <p id="page-subtitle" class="text-xs sm:text-sm text-slate-500 font-medium">
          Patient Charges, Bill Processing & Payment Management
        </p>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-3 sm:gap-4">
        <!-- Global Search Bar -->
        <div class="relative hidden md:block w-72 lg:w-80">
          <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input 
            type="text" 
            id="global-search-input" 
            placeholder="Search patient, invoice, or transaction..."
            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
          >
        </div>

        <!-- Filter Action -->
        <button onclick="showToast('Showing all active billing filters')" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl border border-slate-200 transition-colors" title="Filter Records">
          <i data-lucide="filter" class="w-4 h-4"></i>
        </button>

        <!-- Notification Bell -->
        <button onclick="switchView('notifications')" class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl border border-slate-200 transition-colors" title="Notifications">
          <i data-lucide="bell" class="w-4 h-4"></i>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
        </button>

        <!-- Quick Primary Action -->
        <button onclick="openCreateChargeModal()" class="btn-primary px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
          <i data-lucide="plus" class="w-4 h-4"></i>
          <span>Add Charge</span>
        </button>
      </div>
    </header>

    <!-- Page Body Container -->
    <div class="p-6 sm:p-8 space-y-6 flex-1 w-full">

      <!-- ====================================================
           VIEW: DASHBOARD (OVERVIEW)
           ==================================================== -->
      <section id="view-dashboard" class="view-section space-y-8">
        
        <!-- 4 Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          
          <!-- Card 1: Patient Charges Today -->
          <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 group-hover:scale-110 transition-transform">
                <i data-lucide="receipt-text" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200/60">+12.4% vs yesterday</span>
            </div>
            <div class="mt-4">
              <div id="kpi-charges-today" class="text-3xl font-black text-slate-900 tracking-tight font-heading">₱128,450</div>
              <p class="text-xs font-bold text-slate-700 mt-1">Patient Charges Today</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">Total service charges recorded today</p>
            </div>
          </div>

          <!-- Card 2: Payments Today -->
          <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                <i data-lucide="credit-card" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">78% of daily target</span>
            </div>
            <div class="mt-4">
              <div id="kpi-payments-today" class="text-3xl font-black text-emerald-600 tracking-tight font-heading">₱96,200</div>
              <p class="text-xs font-bold text-slate-700 mt-1">Payments Received</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">Total cashier settlements today</p>
            </div>
          </div>

          <!-- Card 3: Outstanding Balance -->
          <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                <i data-lucide="wallet-cards" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200/60">Active in-hospital</span>
            </div>
            <div class="mt-4">
              <div id="kpi-outstanding" class="text-3xl font-black text-rose-600 tracking-tight font-heading">₱32,250</div>
              <p class="text-xs font-bold text-slate-700 mt-1">Outstanding Balance</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">Total unpaid patient balance</p>
            </div>
          </div>

          <!-- Card 4: Pending Bills -->
          <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
              <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                <i data-lucide="calculator" class="w-5 h-5"></i>
              </div>
              <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200/60">Ready for cashier</span>
            </div>
            <div class="mt-4">
              <div id="kpi-pending-bills" class="text-3xl font-black text-slate-900 tracking-tight font-heading">18</div>
              <p class="text-xs font-bold text-slate-700 mt-1">Pending Bills</p>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">Patients awaiting payment settlement</p>
            </div>
          </div>

        </div>

        <!-- Main Middle Section: Patient Billing Queue + Collection Target & Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Patient Billing Queue (2 Cols) -->
          <div class="lg:col-span-2 hospity-card p-6 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h2 class="text-base font-bold text-slate-900 font-heading">PATIENT BILLING QUEUE</h2>
                  <p class="text-xs text-slate-400">Patients with active billing statements ready for processing</p>
                </div>
                <button onclick="switchView('billing')" class="text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                  View All Bills <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
              </div>

              <!-- Queue Table -->
              <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left hospity-table">
                  <thead>
                    <tr>
                      <th>Patient</th>
                      <th>Invoice #</th>
                      <th>Total Bill</th>
                      <th>Balance</th>
                      <th>Status</th>
                      <th class="text-right">Action</th>
                    </tr>
                  </thead>
                  <tbody id="dashboard-queue-tbody">
                    <!-- Populated dynamically -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Queue Footer Info -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
              <span>Showing high-priority patients for today's discharge and outpatient settlement</span>
              <button onclick="openCreateChargeModal()" class="font-semibold text-brand-600 hover:underline inline-flex items-center gap-1">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Add Charge to Patient
              </button>
            </div>
          </div>

          <!-- Right Column: Today's Collection Visualization -->
          <div class="hospity-card p-6 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h2 class="text-base font-bold text-slate-900 font-heading">TODAY'S COLLECTION</h2>
                  <p class="text-xs text-slate-400">Cashier Counter Collection Target</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                  <i data-lucide="pie-chart" class="w-4 h-4"></i>
                </div>
              </div>

              <!-- Circular Progress & Amount -->
              <div class="flex items-center justify-center py-4">
                <div class="relative w-36 h-36 flex items-center justify-center">
                  <svg class="w-full h-full transform -rotate-90">
                    <circle cx="72" cy="72" r="54" stroke="#f1f5f9" stroke-width="12" fill="transparent"/>
                    <circle id="target-ring-circle" cx="72" cy="72" r="54" stroke="#0284c7" stroke-width="12" stroke-linecap="round" fill="transparent" stroke-dasharray="339.29" stroke-dashoffset="74.6" class="progress-ring-circle"/>
                  </svg>
                  <div class="absolute text-center">
                    <span id="target-pct-text" class="text-2xl font-black text-slate-900 font-heading">78%</span>
                    <span class="block text-[11px] font-semibold text-slate-400 uppercase">Target</span>
                  </div>
                </div>
              </div>

              <!-- Payment Channels Breakdown -->
              <div class="space-y-2.5 mt-2">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                    <span class="font-medium text-slate-700">Cash</span>
                  </div>
                  <span id="stat-cash" class="font-bold text-slate-900">₱42,000</span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-sky-500"></div>
                    <span class="font-medium text-slate-700">Card (Credit/Debit)</span>
                  </div>
                  <span id="stat-card" class="font-bold text-slate-900">₱18,500</span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                    <span class="font-medium text-slate-700">GCash E-Wallet</span>
                  </div>
                  <span id="stat-gcash" class="font-bold text-slate-900">₱21,200</span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-purple-500"></div>
                    <span class="font-medium text-slate-700">Bank Transfer</span>
                  </div>
                  <span id="stat-bank" class="font-bold text-slate-900">₱14,500</span>
                </div>
              </div>
            </div>

            <!-- Target Subtitle -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
              <span>Daily Target: <strong class="text-slate-800">₱123,000</strong></span>
              <span class="text-emerald-600 font-semibold">On Track</span>
            </div>
          </div>

        </div>

        <!-- Bottom Row: Payment Summary & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          
          <!-- Module Quick Access -->
          <div class="hospity-card p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-base font-bold text-slate-900 font-heading">BILLING & CASHIER WORKFLOW</h2>
              <span class="text-xs font-semibold text-brand-600">Quick Navigation</span>
            </div>
            
            <div class="grid grid-cols-2 gap-3.5">
              <div onclick="openCreateChargeModal()" class="p-4 rounded-2xl bg-sky-50/60 hover:bg-sky-50 border border-sky-100 cursor-pointer transition-all hover:scale-[1.01]">
                <div class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center mb-2.5 shadow-sm shadow-sky-500/20">
                  <i data-lucide="plus" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">1. Create Charge</h3>
                <p class="text-xs text-slate-500 mt-1">Record lab, pharmacy, or consultation charges</p>
              </div>

              <div onclick="switchView('billing')" class="p-4 rounded-2xl bg-indigo-50/60 hover:bg-indigo-50 border border-indigo-100 cursor-pointer transition-all hover:scale-[1.01]">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-2.5 shadow-sm shadow-indigo-500/20">
                  <i data-lucide="calculator" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">2. Compute Bill</h3>
                <p class="text-xs text-slate-500 mt-1">Apply Senior/PWD discount & generate invoice</p>
              </div>

              <div onclick="switchView('payments')" class="p-4 rounded-2xl bg-emerald-50/60 hover:bg-emerald-50 border border-emerald-100 cursor-pointer transition-all hover:scale-[1.01]">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-2.5 shadow-sm shadow-emerald-500/20">
                  <i data-lucide="credit-card" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">3. Process Payment</h3>
                <p class="text-xs text-slate-500 mt-1">Accept Cash, Card, GCash, or Bank Transfer</p>
              </div>

              <div onclick="switchView('receipts')" class="p-4 rounded-2xl bg-purple-50/60 hover:bg-purple-50 border border-purple-100 cursor-pointer transition-all hover:scale-[1.01]">
                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center mb-2.5 shadow-sm shadow-purple-500/20">
                  <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">4. Official Receipts</h3>
                <p class="text-xs text-slate-500 mt-1">Generate & print BIR-compliant receipts</p>
              </div>
            </div>
          </div>

          <!-- Recent Billing Activity -->
          <div class="hospity-card p-6 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h2 class="text-base font-bold text-slate-900 font-heading">RECENT BILLING ACTIVITY</h2>
                  <p class="text-xs text-slate-400">Live cashier and transaction audit log</p>
                </div>
                <button onclick="showToast('Activity log refreshed')" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                  <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
              </div>

              <!-- Activities List -->
              <div id="recent-activities-container" class="space-y-2">
                <!-- Dynamic Activity list -->
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-right">
              <span class="text-xs text-slate-400">Logged by station <strong class="text-slate-700">Counter 3</strong></span>
            </div>
          </div>

        </div>

      </section>

      <!-- ====================================================
           VIEW: PATIENT CHARGES (MODULE 1)
           ==================================================== -->
      <section id="view-charges" class="view-section hidden space-y-6">
        
        <!-- Action & Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <!-- Filters -->
          <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <button onclick="AppState.chargeFilter='all'; renderChargesTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-900 text-white transition-colors">All Charges</button>
            <button onclick="AppState.chargeFilter='unbilled'; renderChargesTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">Unbilled</button>
            <button onclick="AppState.chargeFilter='included in bill'; renderChargesTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">Included in Bill</button>
            <button onclick="AppState.chargeFilter='paid'; renderChargesTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">Paid</button>
          </div>

          <!-- Add Charge Button -->
          <button onclick="openCreateChargeModal()" class="btn-primary px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm whitespace-nowrap">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Create Charge Entry</span>
          </button>
        </div>

        <!-- Table Card -->
        <div class="hospity-card p-6 overflow-hidden">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-base font-bold text-slate-900 font-heading">PATIENT SERVICE CHARGES</h2>
              <p class="text-xs text-slate-400">Charge computation, clinical service entries & patient billing tracking</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">Auto-calculation enabled</span>
          </div>

          <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left hospity-table">
              <thead>
                <tr>
                  <th>Charge ID</th>
                  <th>Patient</th>
                  <th>Service</th>
                  <th>Department</th>
                  <th class="text-center">Qty</th>
                  <th>Unit Price</th>
                  <th>Total</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody id="charges-tbody">
                <!-- Dynamically rendered -->
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- ====================================================
           VIEW: BILL PROCESSING (COMPUTE BILL & DISCOUNT)
           ==================================================== -->
      <section id="view-billing" class="view-section hidden space-y-6">
        
        <!-- Header Selector -->
        <div class="hospity-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Bill Processing Console</span>
            <h2 class="text-xl font-bold text-slate-900 font-heading mt-0.5">COMPUTE TOTAL PATIENT BILL</h2>
            <p class="text-xs text-slate-400">Select a patient to compute charges, apply discounts & generate official invoices</p>
          </div>

          <div class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-600 uppercase">Patient:</label>
            <select id="bill-patient-select" onchange="onPatientBillChange()" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 min-w-[280px]">
              <!-- Populated in JS -->
            </select>
          </div>
        </div>

        <!-- Major Bill Layout (Patient Details + Bill Breakdown) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Left 2 Cols: Patient Info & Service Charges -->
          <div class="lg:col-span-2 hospity-card p-6 space-y-6">
            
            <!-- Patient Info Box -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-wrap items-center justify-between gap-4">
              <div>
                <div class="flex items-center gap-2">
                  <h3 id="bill-patient-name" class="text-base font-bold text-slate-900">Juan Dela Cruz</h3>
                  <span id="bill-id-badge" class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-brand-50 text-brand-700">BILL-2026-001</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                  <span>ID: <strong id="bill-patient-id" class="text-slate-800 font-mono">P-2026-001</strong></span>
                  <span>•</span>
                  <span id="bill-patient-age-gender">45 yrs old • Male</span>
                </div>
              </div>

              <div class="text-right text-xs">
                <div class="text-slate-500">Room: <strong id="bill-patient-room" class="text-slate-800">Room 304 - Semi-Private</strong></div>
                <div class="text-slate-500 mt-0.5">Attending: <strong id="bill-patient-physician" class="text-slate-800">Dr. Roberto Mendoza, MD</strong></div>
              </div>
            </div>

            <!-- Charges Table -->
            <div>
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Service Charges Breakdown</h4>
                <button onclick="openCreateChargeModal()" class="text-xs font-semibold text-brand-600 hover:underline inline-flex items-center gap-1">
                  <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Additional Service
                </button>
              </div>

              <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left hospity-table">
                  <thead>
                    <tr>
                      <th>Service / Item</th>
                      <th class="text-center">Qty</th>
                      <th class="text-right">Unit Price</th>
                      <th class="text-right">Amount</th>
                    </tr>
                  </thead>
                  <tbody id="bill-items-tbody">
                    <!-- Populated dynamically -->
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <!-- Right 1 Col: Calculation Summary & Actions -->
          <div class="hospity-card p-6 flex flex-col justify-between space-y-6">
            <div>
              <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 font-heading uppercase tracking-wide">BILL SUMMARY</h3>
                <span id="bill-status-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold badge-partially-paid">
                  PARTIALLY PAID
                </span>
              </div>

              <!-- Mathematical Computation List -->
              <div class="space-y-3.5 py-4 text-sm">
                <!-- Subtotal -->
                <div class="flex items-center justify-between text-slate-600">
                  <span>Subtotal:</span>
                  <span id="bill-subtotal" class="font-semibold text-slate-800">₱2,300</span>
                </div>

                <!-- Discount -->
                <div class="flex items-center justify-between text-purple-700">
                  <div class="flex items-center gap-1">
                    <span>Discount:</span>
                    <span id="bill-discount-label" class="text-xs font-medium text-purple-500">(Senior Citizen)</span>
                  </div>
                  <span id="bill-discount" class="font-bold">- ₱200</span>
                </div>

                <!-- Total Bill Line -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-slate-900 font-extrabold text-lg font-heading">
                  <span>TOTAL BILL:</span>
                  <span id="bill-total" class="text-brand-700">₱2,100</span>
                </div>

                <!-- Amount Paid -->
                <div class="flex items-center justify-between text-emerald-600 text-sm">
                  <span>Amount Paid:</span>
                  <span id="bill-paid" class="font-bold">₱1,000</span>
                </div>

                <!-- Outstanding Balance -->
                <div class="pt-3 border-t border-dashed border-slate-200 flex items-center justify-between text-rose-600 font-bold text-base">
                  <span>Outstanding Balance:</span>
                  <span id="bill-outstanding" class="text-xl font-black">₱1,100</span>
                </div>
              </div>

              <!-- Apply Discount Trigger -->
              <button onclick="openApplyDiscountModal()" class="w-full py-2.5 px-4 rounded-xl border border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-bold flex items-center justify-center gap-2 transition-colors">
                <i data-lucide="tag" class="w-4 h-4"></i>
                <span>Apply Senior / PWD / Employee Discount</span>
              </button>
            </div>

            <!-- Bill Processing Actions -->
            <div class="space-y-2.5 pt-4 border-t border-slate-100">
              <button onclick="viewInvoice(AppState.bills.find(b => b.patientId === AppState.selectedPatientForBill)?.invoiceId || 'INV-2026-001')" class="btn-primary w-full py-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-sm">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Generate & View Invoice</span>
              </button>

              <button onclick="openPaymentModalForBill(AppState.bills.find(b => b.patientId === AppState.selectedPatientForBill)?.billId || 'BILL-2026-001')" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition-all">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                <span>Process Payment for this Bill</span>
              </button>
            </div>
          </div>

        </div>

      </section>

      <!-- ====================================================
           VIEW: PAYMENTS (MODULE 2)
           ==================================================== -->
      <section id="view-payments" class="view-section hidden space-y-6">
        
        <!-- Filter and Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <button onclick="AppState.paymentFilter='all'; renderPaymentsTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-900 text-white transition-colors">All Methods</button>
            <button onclick="AppState.paymentFilter='cash'; renderPaymentsTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">Cash</button>
            <button onclick="AppState.paymentFilter='card'; renderPaymentsTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">Credit/Debit Card</button>
            <button onclick="AppState.paymentFilter='gcash'; renderPaymentsTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">GCash</button>
            <button onclick="AppState.paymentFilter='bank'; renderPaymentsTable();" class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">Bank Transfer</button>
          </div>

          <button onclick="openPaymentModalForBill('BILL-2026-001')" class="btn-primary px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm whitespace-nowrap">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Create Payment Record</span>
          </button>
        </div>

        <!-- Payments Table Card -->
        <div class="hospity-card p-6 overflow-hidden">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-base font-bold text-slate-900 font-heading">PAYMENT RECORDS</h2>
              <p class="text-xs text-slate-400">Cashier settlement records, digital receipts & official transaction register</p>
            </div>
            <span class="text-xs font-semibold text-emerald-600">Active Register</span>
          </div>

          <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left hospity-table">
              <thead>
                <tr>
                  <th>Payment ID</th>
                  <th>Patient</th>
                  <th>Invoice #</th>
                  <th>Amount</th>
                  <th>Method & Ref</th>
                  <th>Date</th>
                  <th>Cashier</th>
                  <th>Status</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody id="payments-tbody">
                <!-- Populated dynamically -->
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- ====================================================
           VIEW: PATIENT ACCOUNTS & OUTSTANDING BALANCES
           ==================================================== -->
      <section id="view-accounts" class="view-section hidden space-y-8">
        
        <!-- Outstanding Accounts Aging Table -->
        <div class="hospity-card p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                <h2 class="text-base font-bold text-slate-900 font-heading">OUTSTANDING ACCOUNTS</h2>
              </div>
              <p class="text-xs text-slate-400">Unsettled patient balances requiring collection or payment follow-up</p>
            </div>
            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
              Total Unpaid: ₱32,250
            </span>
          </div>

          <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left hospity-table">
              <thead>
                <tr>
                  <th>Patient</th>
                  <th>Invoice #</th>
                  <th>Total Bill</th>
                  <th>Amount Paid</th>
                  <th>Outstanding Balance</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody id="outstanding-tbody">
                <!-- Populated dynamically -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- All Patient Accounts Ledger -->
        <div class="hospity-card p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-slate-900 font-heading">PATIENT ACCOUNTS LEDGER</h2>
              <p class="text-xs text-slate-400">Complete historical patient billing accounts and statements</p>
            </div>
          </div>

          <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left hospity-table">
              <thead>
                <tr>
                  <th>Patient</th>
                  <th>Total Charges</th>
                  <th>Discount</th>
                  <th>Total Bill</th>
                  <th>Amount Paid</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody id="accounts-tbody">
                <!-- Populated dynamically -->
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- ====================================================
           VIEW: INVOICES (MODULE 3)
           ==================================================== -->
      <section id="view-invoices" class="view-section hidden space-y-6">
        
        <div class="hospity-card p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-base font-bold text-slate-900 font-heading">PATIENT INVOICES</h2>
              <p class="text-xs text-slate-400">Official medical billing invoices, itemized statements & printer-ready layouts</p>
            </div>
            <button onclick="viewInvoice('INV-2026-001')" class="btn-secondary px-3.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5">
              <i data-lucide="printer" class="w-4 h-4"></i>
              <span>Print Preview</span>
            </button>
          </div>

          <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left hospity-table">
              <thead>
                <tr>
                  <th>Invoice ID</th>
                  <th>Patient</th>
                  <th>Date</th>
                  <th>Total Bill</th>
                  <th>Paid</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Cashier</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody id="invoices-tbody">
                <!-- Populated dynamically -->
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- ====================================================
           VIEW: OFFICIAL RECEIPTS (MODULE 3)
           ==================================================== -->
      <section id="view-receipts" class="view-section hidden space-y-6">
        
        <div class="hospity-card p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-base font-bold text-slate-900 font-heading">OFFICIAL RECEIPTS (BIR-COMPLIANT)</h2>
              <p class="text-xs text-slate-400">Numbered cashier receipts with spelled-out amounts in Philippine Pesos</p>
            </div>
            <button onclick="viewReceipt('OR-2026-001')" class="btn-secondary px-3.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5">
              <i data-lucide="receipt" class="w-4 h-4"></i>
              <span>Preview Sample OR</span>
            </button>
          </div>

          <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left hospity-table">
              <thead>
                <tr>
                  <th>OR Number</th>
                  <th>Date</th>
                  <th>Received From</th>
                  <th>Invoice #</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Cashier</th>
                  <th>Status</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody id="receipts-tbody">
                <!-- Populated dynamically -->
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- ====================================================
           VIEW: NOTIFICATIONS
           ==================================================== -->
      <section id="view-notifications" class="view-section hidden space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-bold text-slate-900 font-heading">BILLING NOTIFICATIONS</h2>
            <p class="text-xs text-slate-400">Real-time alerts for payments, charges, invoices, and targets</p>
          </div>
          <button onclick="showToast('All notifications marked as read')" class="btn-secondary px-3.5 py-1.5 rounded-xl text-xs font-semibold">
            Mark all as read
          </button>
        </div>

        <div id="all-notifications-list" class="space-y-3">
          <!-- Populated in JS -->
        </div>
      </section>

      <!-- ====================================================
           VIEW: SETTINGS
           ==================================================== -->
      <section id="view-settings" class="view-section hidden space-y-6">
        
        <div class="hospity-card p-6 space-y-6 max-w-4xl">
          <div>
            <h2 class="text-base font-bold text-slate-900 font-heading">CASHIER WORKSTATION CONFIGURATION</h2>
            <p class="text-xs text-slate-400">Configure POS devices, thermal receipt printers, discount policies & tax rules</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100 text-sm">
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Assigned Station</label>
              <input type="text" value="Counter 3 - Main Hospital Lobby" readonly class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Cashier Shift</label>
              <input type="text" value="Morning Shift (7:00 AM - 3:00 PM)" readonly class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Receipt Printer (POS)</label>
              <select class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
                <option selected>Epson TM-T88VI Thermal POS Printer (Counter 3)</option>
                <option>Star Micronics TSP143III (Network)</option>
                <option>Direct PDF Generator</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Invoice Statement Printer</label>
              <select class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
                <option selected>HP LaserJet Enterprise M507 (Billing Office)</option>
                <option>Canon imageRUNNER ADVANCE</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Senior Citizen / PWD Statutory Discount</label>
              <input type="text" value="20% (Republic Act 9994 / RA 10754)" readonly class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Daily Target Quota</label>
              <input type="text" value="₱123,000" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button onclick="showToast('Workstation settings saved successfully')" class="btn-primary px-5 py-2.5 rounded-xl text-xs font-bold">
              Save Configuration
            </button>
          </div>
        </div>

      </section>

    </div>
  </main>

  <!-- ========================================================
       MODAL 1: CREATE CHARGE ENTRY
       ======================================================== -->
  <div id="modal-create-charge" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">CREATE CHARGE ENTRY</h3>
            <p class="text-xs text-slate-400">Record a patient service, procedure, or medical charge</p>
          </div>
        </div>
        <button onclick="closeModal('modal-create-charge')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <form onsubmit="saveNewCharge(event)" class="mt-4 space-y-4 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Select Patient *</label>
          <select id="charge-patient-select" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
            <!-- Populated via JS -->
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Service Category</label>
            <select id="charge-category" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
              <option value="Consultation">Consultation</option>
              <option value="Laboratory" selected>Laboratory</option>
              <option value="Pharmacy">Pharmacy</option>
              <option value="Room">Room Accommodation</option>
              <option value="Procedure">Procedure / Surgery</option>
              <option value="Medical Supplies">Medical Supplies</option>
              <option value="Other">Other Services</option>
            </select>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Department</label>
            <input type="text" id="charge-department" value="Laboratory" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Service / Item Name *</label>
          <input type="text" id="charge-service-name" placeholder="e.g. Complete Blood Count (CBC) or Specialist Consult" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Quantity</label>
            <input type="number" id="charge-qty" value="1" min="1" oninput="updateChargeTotalCalculation()" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Unit Price (₱)</label>
            <input type="number" id="charge-unit-price" value="500" min="0" step="10" oninput="updateChargeTotalCalculation()" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
          </div>
        </div>

        <!-- Automatic Calculation Preview -->
        <div class="p-3 bg-sky-50/70 border border-sky-100 rounded-xl flex items-center justify-between">
          <span class="font-bold text-sky-900">Total Computed Charge:</span>
          <span id="charge-total-preview" class="text-base font-black text-sky-700">₱500</span>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Clinical Notes / Reference</label>
          <textarea id="charge-notes" rows="2" placeholder="Optional charge reason, physician order reference..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800"></textarea>
        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeModal('modal-create-charge')" class="btn-secondary px-4 py-2 rounded-xl font-bold">
            Cancel
          </button>
          <button type="submit" class="btn-primary px-5 py-2 rounded-xl font-bold">
            Add Charge
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================
       MODAL 2: EDIT CHARGE ENTRY
       ======================================================== -->
  <div id="modal-edit-charge" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <h3 class="text-base font-bold text-slate-900 font-heading">UPDATE CHARGE ENTRY</h3>
        <button onclick="closeModal('modal-edit-charge')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <form onsubmit="saveEditedCharge(event)" class="mt-4 space-y-4 text-xs">
        <input type="hidden" id="edit-charge-id">
        <div>
          <label class="block font-bold text-slate-400 mb-1">Patient</label>
          <div id="edit-charge-patient" class="font-bold text-slate-800">Juan Dela Cruz (P-2026-001)</div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Service Name *</label>
          <input type="text" id="edit-charge-service-name" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Quantity</label>
            <input type="number" id="edit-charge-qty" min="1" oninput="updateEditChargeTotalCalculation()" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Unit Price (₱)</label>
            <input type="number" id="edit-charge-unit-price" min="0" oninput="updateEditChargeTotalCalculation()" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
          </div>
        </div>

        <div class="p-3 bg-amber-50/70 border border-amber-100 rounded-xl flex items-center justify-between">
          <span class="font-bold text-amber-900">Recalculated Total:</span>
          <span id="edit-charge-total-preview" class="text-base font-black text-amber-700">₱0</span>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Notes</label>
          <textarea id="edit-charge-notes" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800"></textarea>
        </div>

        <div class="text-[11px] text-slate-400">
          Last Updated: <span id="edit-charge-updated-by" class="font-semibold text-slate-600">Maria Santos • Aug 28, 2026</span>
        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeModal('modal-edit-charge')" class="btn-secondary px-4 py-2 rounded-xl font-bold">
            Cancel
          </button>
          <button type="submit" class="btn-primary px-5 py-2 rounded-xl font-bold">
            Update Charge
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================
       MODAL 3: DELETE CHARGE CONFIRMATION
       ======================================================== -->
  <div id="modal-delete-charge" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center">
      <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
      </div>
      <h3 class="text-base font-bold text-slate-900 font-heading">Delete Charge?</h3>
      <p class="text-xs text-slate-500 mt-1">Are you sure you want to remove this patient service charge?</p>
      
      <div class="my-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-left">
        <div class="text-slate-400">Patient: <strong id="delete-charge-patient" class="text-slate-800"></strong></div>
        <div class="text-slate-400 mt-1">Charge: <strong id="delete-charge-name" class="text-slate-800"></strong></div>
        <div class="text-slate-400 mt-1">Amount: <strong id="delete-charge-amount" class="text-rose-600 font-bold"></strong></div>
      </div>

      <div class="flex items-center gap-2.5">
        <button onclick="closeModal('modal-delete-charge')" class="btn-secondary flex-1 py-2 rounded-xl text-xs font-bold">
          Cancel
        </button>
        <button onclick="confirmDeleteCharge()" class="flex-1 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
          Delete Charge
        </button>
      </div>
    </div>
  </div>

  <!-- ========================================================
       MODAL 4: VIEW PATIENT CHARGES BREAKDOWN
       ======================================================== -->
  <div id="modal-view-patient-charges" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 relative">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
          <h3 class="text-base font-bold text-slate-900 font-heading">VIEW PATIENT SERVICE CHARGES</h3>
          <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
            <span id="view-patient-name" class="font-bold text-slate-800">Juan Dela Cruz</span>
            <span>•</span>
            <span id="view-patient-id" class="font-mono">P-2026-001</span>
          </div>
        </div>
        <button onclick="closeModal('modal-view-patient-charges')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div class="mt-4 space-y-4 text-xs">
        <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
          <div>Room: <strong id="view-patient-room" class="text-slate-800">Room 304</strong></div>
          <div>Physician: <strong id="view-patient-physician" class="text-slate-800">Dr. Roberto Mendoza</strong></div>
        </div>

        <div class="max-h-60 overflow-y-auto rounded-xl border border-slate-100">
          <table class="w-full text-left hospity-table">
            <thead>
              <tr>
                <th>Service</th>
                <th>Dept</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
                <th>Date</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody id="view-charges-tbody">
              <!-- Dynamically rendered -->
            </tbody>
          </table>
        </div>

        <!-- Summary Totals -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2">
          <div class="flex justify-between text-slate-600">
            <span>Subtotal:</span>
            <span id="view-charges-subtotal" class="font-semibold text-slate-800">₱0</span>
          </div>
          <div class="flex justify-between text-purple-700">
            <span>Discount:</span>
            <span id="view-charges-discount" class="font-bold">- ₱0</span>
          </div>
          <div class="flex justify-between text-slate-900 font-bold border-t border-slate-200 pt-2 text-sm">
            <span>Total Bill:</span>
            <span id="view-charges-total">₱0</span>
          </div>
          <div class="flex justify-between text-emerald-600">
            <span>Amount Paid:</span>
            <span id="view-charges-paid" class="font-bold">₱0</span>
          </div>
          <div class="flex justify-between text-rose-600 font-bold text-sm">
            <span>Outstanding Balance:</span>
            <span id="view-charges-balance">₱0</span>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button onclick="closeModal('modal-view-patient-charges')" class="btn-secondary px-5 py-2 rounded-xl font-bold">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================
       MODAL 5: APPLY DISCOUNT ENTRY
       ======================================================== -->
  <div id="modal-apply-discount" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center">
            <i data-lucide="tag" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">APPLY DISCOUNT ENTRY</h3>
            <p class="text-xs text-slate-400">Senior Citizen, PWD or Employee Discount</p>
          </div>
        </div>
        <button onclick="closeModal('modal-apply-discount')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <form onsubmit="saveDiscount(event)" class="mt-4 space-y-4 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Discount Type *</label>
          <select id="discount-type" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
            <option value="Senior Citizen">Senior Citizen (20%) - RA 9994</option>
            <option value="PWD">Person with Disability (20%) - RA 10754</option>
            <option value="Hospital Employee">Hospital Employee (15%)</option>
            <option value="Other">Special Charity / Other</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Calculation Mode</label>
            <select id="discount-mode" onchange="updateDiscountLivePreview()" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
              <option value="percentage">Percentage (%)</option>
              <option value="fixed">Fixed Amount (₱)</option>
            </select>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Discount Value</label>
            <input type="number" id="discount-rate-value" value="20" min="0" oninput="updateDiscountLivePreview()" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Reason / ID Card Number *</label>
          <input type="text" id="discount-reason" placeholder="e.g. Senior Citizen OSCA ID #12345" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Authorized By *</label>
          <input type="text" id="discount-authorized-by" value="Dr. Roberto Mendoza, MD" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
        </div>

        <!-- Live Preview -->
        <div class="p-3.5 bg-purple-50 rounded-xl border border-purple-100 space-y-1.5">
          <div class="flex justify-between text-slate-600">
            <span>Original Bill:</span>
            <span id="discount-original-amount" class="font-semibold text-slate-900">₱2,300</span>
          </div>
          <div class="flex justify-between text-purple-700 font-bold">
            <span>Discount Applied:</span>
            <span id="discount-calculated-amount">- ₱200</span>
          </div>
          <div class="flex justify-between text-slate-900 font-extrabold border-t border-purple-200 pt-1.5 text-sm">
            <span>Final Amount:</span>
            <span id="discount-final-amount" class="text-purple-900">₱2,100</span>
          </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeModal('modal-apply-discount')" class="btn-secondary px-4 py-2 rounded-xl font-bold">
            Cancel
          </button>
          <button type="submit" class="btn-primary px-5 py-2 rounded-xl font-bold">
            Apply Discount
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================
       MODAL 6: CREATE PAYMENT RECORD
       ======================================================== -->
  <div id="modal-create-payment" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
            <i data-lucide="credit-card" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">RECORD PAYMENT</h3>
            <p class="text-xs text-slate-400">Process cashier collection & generate official receipt</p>
          </div>
        </div>
        <button onclick="closeModal('modal-create-payment')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <form onsubmit="processPaymentSubmit(event)" class="mt-4 space-y-4 text-xs">
        <input type="hidden" id="payment-bill-id">

        <!-- Patient and Invoice Info Box -->
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1.5">
          <div class="flex justify-between">
            <span class="text-slate-500">Patient:</span>
            <strong id="payment-patient-name" class="text-slate-800">Juan Dela Cruz (P-2026-001)</strong>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Invoice Number:</span>
            <span id="payment-invoice-num" class="font-mono font-bold text-slate-700">INV-2026-001</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Total Bill:</span>
            <span id="payment-total-bill" class="font-semibold text-slate-800">₱2,100</span>
          </div>
          <div class="flex justify-between text-emerald-600">
            <span>Amount Already Paid:</span>
            <span id="payment-already-paid" class="font-bold">₱1,000</span>
          </div>
          <div class="flex justify-between text-rose-600 font-bold border-t border-slate-200 pt-1">
            <span>Outstanding Balance:</span>
            <span id="payment-outstanding" class="text-sm font-black">₱1,100</span>
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Payment Amount (₱) *</label>
          <input type="number" id="payment-input-amount" step="0.01" min="1" oninput="onPaymentAmountInput()" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
          <p id="payment-warning" class="hidden text-[11px] font-semibold text-rose-600 mt-1"></p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Payment Method *</label>
            <select id="payment-method-select" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
              <option value="Cash" selected>Cash</option>
              <option value="Credit/Debit Card">Credit/Debit Card</option>
              <option value="GCash">GCash E-Wallet</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Reference / Slip Number</label>
            <input type="text" id="payment-ref-no" placeholder="e.g. CSH-9912 or POS-8812" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Notes / Cashier Remarks</label>
          <input type="text" id="payment-notes" placeholder="Full settlement or partial deposit" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeModal('modal-create-payment')" class="btn-secondary px-4 py-2 rounded-xl font-bold">
            Cancel
          </button>
          <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-all shadow-sm">
            Process Payment
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================
       MODAL 7: EDIT PAYMENT RECORD
       ======================================================== -->
  <div id="modal-edit-payment" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <h3 class="text-base font-bold text-slate-900 font-heading">UPDATE PAYMENT RECORD</h3>
        <button onclick="closeModal('modal-edit-payment')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <form onsubmit="saveEditedPayment(event)" class="mt-4 space-y-4 text-xs">
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
          <div class="flex justify-between">
            <span class="text-slate-400">Payment ID:</span>
            <span id="edit-payment-id" class="font-mono font-bold text-slate-700"></span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Patient:</span>
            <span id="edit-payment-patient" class="font-bold text-slate-800"></span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Amount (Locked):</span>
            <span id="edit-payment-amount" class="font-bold text-emerald-600"></span>
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Payment Method</label>
          <select id="edit-payment-method" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
            <option value="Cash">Cash</option>
            <option value="Credit/Debit Card">Credit/Debit Card</option>
            <option value="GCash">GCash E-Wallet</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Reference Number</label>
          <input type="text" id="edit-payment-ref-no" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Notes</label>
          <textarea id="edit-payment-notes" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800"></textarea>
        </div>

        <div class="text-[11px] text-slate-400">
          Audit: <span id="edit-payment-updated-by" class="font-semibold text-slate-600"></span>
        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeModal('modal-edit-payment')" class="btn-secondary px-4 py-2 rounded-xl font-bold">
            Cancel
          </button>
          <button type="submit" class="btn-primary px-5 py-2 rounded-xl font-bold">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================
       MODAL 8: VIEW & PRINT INVOICE
       ======================================================== -->
  <div id="modal-view-invoice" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-3xl w-full p-8 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto">
      
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hospital Invoice Document</span>
        <div class="flex items-center gap-2">
          <button onclick="printCurrentInvoice()" class="btn-primary px-4 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Print Invoice</span>
          </button>
          <button onclick="closeModal('modal-view-invoice')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>
      </div>

      <!-- Professional Medical Billing Document Preview -->
      <div class="mt-6 border border-slate-200 rounded-2xl p-6 sm:p-8 bg-white shadow-sm space-y-6 text-slate-800">
        
        <!-- Hospital Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b-2 border-slate-900 gap-4">
          <div>
            <h2 id="inv-preview-hospital-name" class="text-xl font-black text-brand-700 tracking-tight font-heading">TMHIS HEALTHCARE MEDICAL CENTER</h2>
            <p id="inv-preview-hospital-address" class="text-xs text-slate-500 mt-1">108 Health Boulevard, Diliman, Quezon City, Philippines</p>
            <p id="inv-preview-hospital-contact" class="text-xs text-slate-500">(02) 8888-HOSP | billing@tupimunicipal.gov.ph</p>
          </div>
          <div class="text-left sm:text-right">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">PATIENT INVOICE</div>
            <div id="inv-preview-num" class="text-base font-mono font-black text-slate-900">INV-2026-001</div>
            <div id="inv-preview-date" class="text-xs text-slate-500">Aug 28, 2026</div>
          </div>
        </div>

        <!-- Patient Metadata Box -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
          <div>
            <p class="text-slate-500">Patient Name: <strong id="inv-preview-patient-name" class="text-slate-900">Juan Dela Cruz</strong></p>
            <p class="text-slate-500 mt-1">Patient ID: <strong id="inv-preview-patient-id" class="font-mono text-slate-800">P-2026-001</strong></p>
            <p class="text-slate-500 mt-1">Room / Bed: <strong id="inv-preview-room" class="text-slate-800">Room 304 - Semi-Private</strong></p>
          </div>
          <div>
            <p class="text-slate-500">Attending Physician: <strong id="inv-preview-physician" class="text-slate-900">Dr. Roberto Mendoza, MD</strong></p>
            <p class="text-slate-500 mt-1">Payment Status: <strong id="inv-preview-status" class="text-brand-700 font-bold">PARTIALLY PAID</strong></p>
            <p class="text-slate-500 mt-1">Processing Cashier: <strong id="inv-preview-cashier" class="text-slate-800">Maria Santos</strong></p>
          </div>
        </div>

        <!-- Service Charges Line Items -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="border-y-2 border-slate-900 text-slate-700 uppercase font-bold">
                <th class="py-2.5 px-3">Service / Description</th>
                <th class="py-2.5 px-3 text-center">Qty</th>
                <th class="py-2.5 px-3 text-right">Unit Price</th>
                <th class="py-2.5 px-3 text-right">Amount</th>
              </tr>
            </thead>
            <tbody id="inv-preview-items-tbody">
              <!-- Populated dynamically -->
            </tbody>
          </table>
        </div>

        <!-- Financial Totals -->
        <div class="flex justify-end pt-4">
          <div class="w-64 space-y-2 text-xs">
            <div class="flex justify-between text-slate-600">
              <span>Subtotal:</span>
              <span id="inv-preview-subtotal" class="font-semibold text-slate-900">₱2,300</span>
            </div>
            <div class="flex justify-between text-purple-700">
              <span>Discount:</span>
              <span id="inv-preview-discount" class="font-bold">- ₱200</span>
            </div>
            <div class="flex justify-between text-slate-900 font-extrabold text-sm border-y-2 border-slate-900 py-1.5 font-heading">
              <span>TOTAL BILL:</span>
              <span id="inv-preview-total">₱2,100</span>
            </div>
            <div class="flex justify-between text-emerald-600 font-bold">
              <span>Amount Paid:</span>
              <span id="inv-preview-paid">₱1,000</span>
            </div>
            <div class="flex justify-between text-rose-600 font-bold">
              <span>Outstanding Balance:</span>
              <span id="inv-preview-balance" class="text-sm">₱1,100</span>
            </div>
          </div>
        </div>

        <!-- Invoice Sign-off -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
          <div>
            <p>This statement is an official hospital invoice document.</p>
            <p class="text-[11px] text-slate-400">Keep this document for HMO / PhilHealth reimbursement.</p>
          </div>
          <div class="text-center">
            <div class="border-b border-slate-900 w-44 pb-1 font-bold text-slate-800">Maria Santos</div>
            <div class="text-[11px] text-slate-400 mt-1">Authorized Billing Officer</div>
          </div>
        </div>

      </div>

      <div class="mt-6 flex justify-end">
        <button onclick="closeModal('modal-view-invoice')" class="btn-secondary px-6 py-2 rounded-xl text-xs font-bold">
          Close Preview
        </button>
      </div>
    </div>
  </div>

  <!-- ========================================================
       MODAL 9: VIEW & PRINT OFFICIAL RECEIPT (OR)
       ======================================================== -->
  <div id="modal-view-receipt" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-xl w-full p-8 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto">
      
      <!-- Header Action -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Official Receipt Document</span>
        <div class="flex items-center gap-2">
          <button onclick="printCurrentReceipt()" class="btn-primary px-4 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Print Official Receipt</span>
          </button>
          <button onclick="closeModal('modal-view-receipt')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>
      </div>

      <!-- Clean Official Receipt Preview -->
      <div class="mt-6 border-2 border-slate-900 rounded-2xl p-6 sm:p-8 bg-white text-slate-800 space-y-6">
        
        <!-- Header -->
        <div class="text-center border-b-2 border-slate-900 pb-4">
          <h2 class="text-lg font-black text-brand-700 tracking-tight font-heading">TMHIS HEALTHCARE MEDICAL CENTER</h2>
          <p class="text-xs text-slate-500">108 Health Boulevard, Diliman, Quezon City, Philippines</p>
          <p class="text-[11px] text-slate-400">TIN: 009-842-119-000 VAT • Tel: (02) 8888-HOSP</p>
          <div class="inline-block bg-slate-900 text-white font-bold text-xs uppercase px-4 py-1 rounded-full mt-3 tracking-wider">
            OFFICIAL RECEIPT
          </div>
        </div>

        <!-- OR Details -->
        <div class="flex justify-between items-center text-xs">
          <div>OR Number: <strong id="or-preview-num" class="font-mono text-sm font-bold text-slate-900">OR-2026-001</strong></div>
          <div>Date: <strong id="or-preview-date" class="text-slate-700">August 28, 2026</strong></div>
        </div>

        <!-- Receipt Body Details -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2 leading-relaxed">
          <div>Received From: <strong id="or-preview-received-from" class="text-slate-900">Juan Dela Cruz</strong></div>
          <div>Settlement For Invoice: <strong id="or-preview-invoice-num" class="font-mono text-slate-800">INV-2026-001</strong></div>
          <div>Payment Method: <strong id="or-preview-payment-method" class="text-slate-800">Cash</strong></div>
          
          <div class="pt-2 border-t border-dashed border-slate-300 flex items-center justify-between text-emerald-700 font-bold">
            <span class="text-slate-700">Amount Received:</span>
            <span id="or-preview-amount" class="text-base font-black">₱1,000</span>
          </div>

          <div class="pt-1">
            <span class="text-slate-500">Amount in Words:</span>
            <div id="or-preview-amount-words" class="italic font-bold text-slate-800 mt-0.5">One Thousand Pesos Only</div>
          </div>
        </div>

        <!-- Footer Cashier Signature -->
        <div class="pt-6 border-t border-slate-200 flex items-end justify-between text-xs text-slate-500">
          <div>
            <p>BIR Permit No: 2026-CSH-0988</p>
            <p class="text-[11px] text-slate-400">Valid Hospital Official Receipt</p>
          </div>
          <div class="text-center">
            <div id="or-preview-cashier" class="border-b border-slate-900 w-40 pb-1 font-bold text-slate-800">Maria Santos</div>
            <div class="text-[11px] text-slate-400 mt-1">Collecting Cashier</div>
          </div>
        </div>

      </div>

      <div class="mt-6 flex justify-end">
        <button onclick="closeModal('modal-view-receipt')" class="btn-secondary px-6 py-2 rounded-xl text-xs font-bold">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- ========================================================
       MODAL 10: USER PROFILE MODAL
       ======================================================== -->
  <div id="modal-user-profile" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center">
      <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=256&auto=format&fit=crop" alt="Maria Santos" class="w-16 h-16 rounded-full object-cover ring-4 ring-brand-100 mx-auto shadow-md">
      <h3 class="text-base font-bold text-slate-900 mt-3 font-heading">Maria Santos</h3>
      <p class="text-xs font-semibold text-brand-600">Billing / Cashier Staff</p>
      <p class="text-[11px] text-slate-400 font-mono mt-0.5">EMP-CSH-2026-089</p>

      <div class="my-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-left space-y-1.5">
        <div>Department: <strong class="text-slate-800">Cashier & Accounts Division</strong></div>
        <div>Shift: <strong class="text-slate-800">Morning Shift (7AM - 3PM)</strong></div>
        <div>Station: <strong class="text-slate-800">Counter 3 - Main Lobby</strong></div>
        <div>Permissions: <strong class="text-emerald-600">Charges, Bills, OR & POS</strong></div>
      </div>

      <button onclick="closeModal('modal-user-profile')" class="btn-primary w-full py-2 rounded-xl text-xs font-bold">
        Close Profile
      </button>
    </div>
  </div>

  <!-- ========================================================
       TOAST CONTAINER & HIDDEN PRINT CONTAINER
       ======================================================== -->
  <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 max-w-md pointer-events-auto"></div>
  <div id="printable-area" class="hidden"></div>

  <!-- Application Logic Scripts -->
  <script src="/section/billing/assets/js/data.js"></script>
  <script src="/section/billing/assets/js/app.js"></script>
</body>
</html>
