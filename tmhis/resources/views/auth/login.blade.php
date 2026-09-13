<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Tupi Municipal Hospital Information Management System</title>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif; }
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-6px); }
      40%, 80% { transform: translateX(6px); }
    }
    .animate-shake {
      animation: shake 0.4s ease-in-out;
    }
  </style>
</head>
<body class="h-full antialiased flex items-center justify-center p-4 sm:p-6 md:p-10 bg-slate-900">

  <!-- Reference 3 Split Login Container -->
  <div class="max-w-5xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row min-h-[620px] border border-slate-200">
    
    <!-- LEFT SIDE: Modern Hospital Building Hero + Dark Blue Gradient Overlay + Welcome Text -->
    <div class="md:w-1/2 relative bg-slate-900 flex flex-col justify-between p-8 sm:p-12 text-white overflow-hidden min-h-[280px] md:min-h-full">
      
      <!-- Modern Hospital Building Background Image -->
      <img src="https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?auto=format&fit=crop&q=80&w=1200" 
           alt="Modern Hospital Center" 
           class="absolute inset-0 w-full h-full object-cover object-center scale-105 transition-transform duration-1000 hover:scale-100 opacity-40">

      <!-- Dark Blue Multi-layer Overlay -->
      <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-blue-950/90 to-blue-900/80 backdrop-blur-[2px]"></div>

      <!-- Top Branding -->
      <div class="relative z-10 flex items-center gap-3">
        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 ring-4 ring-white/10">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-xl font-black tracking-tight font-display">Tupi Municipal Hospital</h2>
          <p class="text-xs text-blue-200 font-semibold tracking-wider uppercase">Information Management System</p>
        </div>
      </div>

      <!-- Center Welcome Pitch -->
      <div class="relative z-10 my-auto py-8 space-y-3">
        <span class="px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
          Unified Healthcare Portal
        </span>
        <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white leading-tight font-display">
          Better Healthcare<br/>Starts Here.
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-sm font-medium">
          Comprehensive Electronic Medical Records, real-time pre-consultation routing, electronic prescriptions, and diagnostic collaboration.
        </p>
      </div>

      <!-- Bottom Trust Metrics -->
      <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
        <div>
          <span class="block font-black text-base text-white font-display">15+</span>
          <span class="text-[11px] text-slate-400">Specialty Clinics</span>
        </div>
        <div>
          <span class="block font-black text-base text-white font-display">100%</span>
          <span class="text-[11px] text-slate-400">HIPAA Compliant</span>
        </div>
        <div>
          <span class="block font-black text-base text-white font-display">Live</span>
          <span class="text-[11px] text-emerald-400 font-bold">Auto-Routing</span>
        </div>
      </div>

    </div>

    <!-- RIGHT SIDE: White Login Panel -->
    <div class="md:w-1/2 p-8 sm:p-12 flex flex-col justify-center bg-white space-y-6">
      
      <!-- Panel Header -->
      <div class="space-y-1.5">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight font-display">Welcome back</h2>
        <p class="text-xs text-slate-500 font-medium">Sign in to your authorized hospital account to continue.</p>
      </div>

      @if (Auth::check())
        <div class="p-3.5 bg-blue-50 border border-blue-200/80 rounded-2xl text-blue-900 text-xs flex items-center justify-between gap-2">
          <div class="flex items-center gap-2 min-w-0">
            <i data-lucide="user-check" class="w-4 h-4 text-blue-600 shrink-0"></i>
            <span class="truncate">Logged in as: <strong>{{ session('full_name') ?: Auth::user()->Username }}</strong> ({{ Auth::user()->Role }})</span>
          </div>
          <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-bold underline whitespace-nowrap text-[11px] shrink-0">Continue to Portal &rarr;</a>
        </div>
      @endif

      <!-- Error Alert -->
      @if (session('error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-semibold flex items-center gap-2.5 animate-shake">
          <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      <!-- Login Form -->
      <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <!-- Email / Username Input -->
        <div>
          <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider text-[10px]">Email or Username</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <i data-lucide="mail" class="w-4 h-4"></i>
            </div>
            <input type="text" id="emailField" name="email" value="{{ old('email') }}" required 
                   placeholder="Enter username or email" 
                   class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
          </div>
        </div>

        <!-- Password Input -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px]">Password</label>
            <a href="javascript:void(0)" onclick="alert('Demo password is: password123')" class="text-[11px] font-bold text-blue-600 hover:text-blue-700">Forgot Password?</a>
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <i data-lucide="lock" class="w-4 h-4"></i>
            </div>
            <input type="password" id="passwordField" name="password" value="" required 
                   placeholder="••••••••" 
                   class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
          </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
            <input type="checkbox" name="remember" checked 
                   class="w-4 h-4 text-blue-600 rounded-lg border-slate-300 focus:ring-blue-500" />
            <span>Remember Me on this device</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-xs sm:text-sm shadow-xl shadow-blue-600/25 transition-all flex items-center justify-center gap-2 group">
          <span>Sign In to System</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </form>

      <!-- Quick Demo Account Switcher — All 9 FDD Roles -->
      <div class="pt-2 border-t border-slate-100 space-y-2">
        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Quick Demo Login (1-Click — All 9 Roles):</span>
        <div class="grid grid-cols-3 gap-1.5 text-[10px]">
          <button type="button" onclick="setCreds('admin')" class="p-2 rounded-xl bg-slate-50 hover:bg-red-50 border border-slate-200 font-bold text-slate-700 hover:text-red-700 text-left transition truncate" title="System Administrator">
            🛡️ Admin
          </button>
          <button type="button" onclick="setCreds('director')" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 font-bold text-slate-700 hover:text-amber-700 text-left transition truncate" title="Hospital Chief / Medical Director">
            👔 Chief
          </button>
          <button type="button" onclick="setCreds('records')" class="p-2 rounded-xl bg-slate-50 hover:bg-teal-50 border border-slate-200 font-bold text-slate-700 hover:text-teal-700 text-left transition truncate" title="Medical Records Officer">
            📁 Records
          </button>
          <button type="button" onclick="setCreds('registrator')" class="p-2 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 font-bold text-slate-700 hover:text-blue-700 text-left transition truncate" title="Admitting / Registration Staff">
            📋 Register
          </button>
          <button type="button" onclick="setCreds('cardio')" class="p-2 rounded-xl bg-slate-50 hover:bg-rose-50 border border-slate-200 font-bold text-slate-700 hover:text-rose-700 text-left transition truncate" title="Attending Physician">
            🩺 Doctor
          </button>
          <button type="button" onclick="setCreds('nurse')" class="p-2 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 font-bold text-slate-700 hover:text-emerald-700 text-left transition truncate" title="Nurse on Duty">
            💉 Nurse
          </button>
          <button type="button" onclick="setCreds('medtech')" class="p-2 rounded-xl bg-slate-50 hover:bg-purple-50 border border-slate-200 font-bold text-slate-700 hover:text-purple-700 text-left transition truncate" title="Medical Technologist">
            🔬 MedTech
          </button>
          <button type="button" onclick="setCreds('pharmacist')" class="p-2 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-slate-200 font-bold text-slate-700 hover:text-indigo-700 text-left transition truncate" title="Pharmacist / Pharmacy Aide">
            💊 Pharmacist
          </button>
          <button type="button" onclick="setCreds('cashier')" class="p-2 rounded-xl bg-slate-50 hover:bg-orange-50 border border-slate-200 font-bold text-slate-700 hover:text-orange-700 text-left transition truncate" title="Billing / Cashier Staff">
            💰 Billing
          </button>
        </div>
        <p class="text-[9px] text-slate-400 font-medium">All demo accounts use password: <code class="bg-slate-100 px-1 py-0.5 rounded font-bold text-slate-600">password123</code></p>
      </div>

    </div>

  </div>

  <script>
    if (window.lucide) {
      lucide.createIcons();
    }
    function setCreds(username) {
      document.getElementById('emailField').value = username;
      document.getElementById('passwordField').value = 'password123';
    }
  </script>
</body>
</html>
