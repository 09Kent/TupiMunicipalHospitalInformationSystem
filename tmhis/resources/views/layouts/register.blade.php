<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Tupi Municipal Hospital Information Management System')</title>
  <meta name="description" content="Tupi Municipal Hospital Information Management System - Registration Portal" />

  <!-- Google Fonts (Plus Jakarta Sans & Inter) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <!-- Tailwind CSS CDN with Custom Blue Healthcare Palette -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#EFF6FF',
              100: '#DBEAFE',
              200: '#BFDBFE',
              300: '#93C5FD',
              400: '#60A5FA',
              500: '#3B82F6',
              600: '#2563EB',
              700: '#1D4ED8',
              800: '#1E40AF',
              900: '#1E3A8A',
            },
            medical: {
              teal: '#0D9488',
              cyan: '#0284C7',
              emerald: '#10B981',
              slate: '#0F172A'
            }
          },
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Custom Module CSS -->
  <link rel="stylesheet" href="{{ asset('section/register/css/styles.css') }}" />
  
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
    }
  </style>
  @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col font-sans">

  <!-- TOP EMERGENCY ADVISORY (No Print) -->
  <header class="no-print bg-slate-900 text-white text-xs py-1.5 px-4 border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-rose-500 text-white font-bold text-[10px]">!</span>
        <span class="font-medium text-slate-300 text-[11px]">
          <strong class="text-white">Clinical Triage:</strong> In emergency or critical distress, route immediately to Acute ER.
        </span>
      </div>
      <div class="flex items-center gap-4 text-slate-400 text-[11px]">
        <span class="flex items-center gap-1"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i> HIPAA Compliant</span>
        <span class="flex items-center gap-1"><i data-lucide="database" class="w-3.5 h-3.5 text-blue-400"></i> MySQL Connected</span>
        <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5 text-slate-300"></i> {{ date('D, d M Y') }}</span>
      </div>
    </div>
  </header>

  <!-- MAIN TOP NAVBAR (No Print) -->
  <nav class="no-print sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-2xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-18 gap-4">
        
        <!-- Brand Logo -->
        <a href="{{ route('register.dashboard') }}" class="flex items-center gap-3 shrink-0 group">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform duration-200">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-lg font-black text-slate-900 tracking-tight">Tupi Municipal Hospital</span>
              <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">EHR</span>
            </div>
            <p class="text-[11px] text-slate-400 font-medium">Registrator & Pre-Consultation Portal</p>
          </div>
        </a>

        <!-- Primary Navigation Links -->
        <div class="hidden lg:flex items-center gap-1">
          
          <a href="{{ route('register.dashboard') }}" 
             class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('register.dashboard') ? 'bg-blue-50 text-blue-700 font-extrabold shadow-2xs' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">
            <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('register.dashboard') ? 'text-blue-600' : 'text-slate-400' }}"></i>
            <span>Dashboard</span>
          </a>

          <a href="{{ route('register.patients.index') }}" 
             class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('register.patients.*') ? 'bg-blue-50 text-blue-700 font-extrabold shadow-2xs' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">
            <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('register.patients.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
            <span>Patients</span>
          </a>

          <a href="{{ route('register.appointments.index') }}" 
             class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('register.appointments.*') ? 'bg-blue-50 text-blue-700 font-extrabold shadow-2xs' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">
            <i data-lucide="calendar" class="w-4 h-4 {{ request()->routeIs('register.appointments.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
            <span>Appointments</span>
          </a>

          <a href="{{ route('register.queue.index') }}" 
             class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('register.queue.*') ? 'bg-blue-50 text-blue-700 font-extrabold shadow-2xs' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">
            <i data-lucide="list-ordered" class="w-4 h-4 {{ request()->routeIs('register.queue.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
            <span>Live Queue</span>
          </a>

          <a href="{{ route('register.registration.index') }}" 
             class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('register.registration.*') ? 'bg-blue-50 text-blue-700 font-extrabold shadow-2xs' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">
            <i data-lucide="user-plus" class="w-4 h-4 {{ request()->routeIs('register.registration.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
            <span>New Intake</span>
          </a>

          <a href="{{ route('register.reports.index') }}" 
             class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('register.reports.*') ? 'bg-blue-50 text-blue-700 font-extrabold shadow-2xs' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">
            <i data-lucide="bar-chart-3" class="w-4 h-4 {{ request()->routeIs('register.reports.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
            <span>Reports</span>
          </a>

        </div>

        <!-- Right User Actions -->
        <div class="flex items-center gap-3">
          
          <!-- Primary CTA: Intake -->
          <a href="{{ route('register.registration.index') }}" 
             class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 transition-all hover:shadow-lg hover:shadow-blue-600/30">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Patient Intake</span>
          </a>

          <!-- User Avatar Menu -->
          <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-black text-xs border border-blue-200/60 shadow-2xs">
              {{ substr(session('full_name') ?: (Auth::user()->FirstName ?? 'S'), 0, 1) }}
            </div>
            <div class="hidden md:block text-left">
              <span class="block text-xs font-bold text-slate-800 leading-tight">{{ session('full_name') ?: (Auth::user()->Username ?? 'Registrator') }}</span>
              <span class="block text-[10px] font-semibold text-blue-600 uppercase tracking-wider">{{ Auth::user()->Role ?? 'Registrator' }}</span>
            </div>
            <a href="{{ route('logout') }}" title="Sign Out" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
              <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
          </div>

        </div>

      </div>
    </div>
  </nav>

  <!-- Page Content Body -->
  <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
  </main>

  <!-- CLINICAL FOOTER (No Print) -->
  <footer class="no-print mt-14 bg-white border-t border-slate-200 py-8 px-4 sm:px-6 lg:px-8 text-slate-500 text-xs">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
      
      <div class="flex items-center gap-3">
        <div class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
          +
        </div>
        <div>
          <span class="font-bold text-slate-900">Tupi Municipal Hospital Information Management System</span>
          <p class="text-[11px] text-slate-400">Accredited Clinical Registrator & Pre-Consultation System (Laravel & MySQL)</p>
        </div>
      </div>

      <!-- Regulatory & Safety Notice -->
      <div class="text-center md:text-right text-[11px] text-slate-400 max-w-md">
        <p>This digital clinical platform is intended for patient registration, queue triage, and consultation preparation. Final clinical diagnoses are conducted by licensed healthcare professionals.</p>
        <p class="mt-1 font-medium text-slate-500">© 2026 Tupi Municipal Hospital. Database: <code>MedicalRegistrationDB</code>.</p>
      </div>

    </div>
  </footer>

  <!-- Toast Notifications Container -->
  <div id="toast-container" class="fixed top-20 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

  <!-- Initialize Lucide Icons & Global Toast -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) {
        window.lucide.createIcons();
      }
    });

    window.showToast = function(message, type = 'success') {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      const isSuccess = type === 'success';
      toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-xs font-bold transition-all duration-300 transform translate-x-10 opacity-0 ${
        isSuccess 
          ? 'bg-white text-slate-800 border-emerald-200 ring-2 ring-emerald-500/20' 
          : 'bg-white text-slate-800 border-rose-200 ring-2 ring-rose-500/20'
      }`;

      toast.innerHTML = `
        <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 ${isSuccess ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div>
          <div class="${isSuccess ? 'text-emerald-950 font-extrabold' : 'text-rose-950 font-extrabold'}">${isSuccess ? 'Success' : 'Notice'}</div>
          <div class="text-[11px] text-slate-600 font-medium">${message}</div>
        </div>
      `;

      container.appendChild(toast);
      requestAnimationFrame(() => {
        toast.classList.remove('translate-x-10', 'opacity-0');
      });

      setTimeout(() => {
        toast.classList.add('translate-x-10', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    };
  </script>

  @stack('scripts')
</body>
</html>
