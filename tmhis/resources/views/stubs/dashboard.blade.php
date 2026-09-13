<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $section }} | Tupi Municipal Hospital</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'sans-serif'],
          }
        }
      }
    }
  </script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex items-center justify-center font-sans">
  <div class="max-w-lg w-full mx-4">
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl text-center space-y-6">
      
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center mx-auto shadow-md shadow-blue-500/25">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
        </svg>
      </div>
      
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $section }}</h1>
        <p class="text-sm text-slate-500 mt-1">Tupi Municipal Hospital Information Management System</p>
      </div>
      
      <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-left">
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </div>
          <div>
            <div class="text-sm font-bold text-amber-900">Module Migration In Progress</div>
            <p class="text-xs text-amber-700 mt-1">
              The <strong>{{ $section }}</strong> module is currently being migrated to Laravel. 
              This dashboard will be fully functional once the migration is complete.
            </p>
          </div>
        </div>
      </div>
      
      <div class="flex items-center justify-center gap-3 pt-2 text-xs text-slate-400">
        <span>Logged in as: <strong class="text-slate-600">{{ Auth::user()->FirstName ?? 'User' }} {{ Auth::user()->LastName ?? '' }}</strong></span>
        <span>•</span>
        <span>Role: <strong class="text-blue-600">{{ $role }}</strong></span>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
        <a href="{{ route('landing') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-2">
          ← Back to Home
        </a>
        <a href="{{ route('logout') }}" class="px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition flex items-center gap-2">
          Sign Out
        </a>
      </div>
      
    </div>
  </div>
</body>
</html>
