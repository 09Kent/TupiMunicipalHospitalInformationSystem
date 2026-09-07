<?php
// Nurse/views/auth/login.php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

if (isset($_GET['logout'])) {
    Session::logout();
}

$pageTitle = 'Nurse Portal Login • Tupi Municipal Hospital';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            display: ['Outfit', 'sans-serif']
          }
        }
      }
    }
  </script>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-100 via-teal-50/30 to-slate-100 font-sans text-slate-800">

  <div class="w-full max-w-md bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xl space-y-6">
    
    <div class="text-center space-y-2">
      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-teal-500/30">
        <i data-lucide="heart-pulse" class="w-7 h-7"></i>
      </div>
      <h1 class="text-2xl font-black text-slate-900 tracking-tight font-display">Tupi Municipal Hospital</h1>
      <p class="text-xs font-bold uppercase tracking-wider text-teal-600">Nurse On Duty Portal</p>
    </div>

    <!-- Active Nurse Demo Card -->
    <div class="p-4 bg-teal-50/60 rounded-2xl border border-teal-100 flex items-center gap-3.5">
      <img src="https://images.unsplash.com/photo-1594824476967-48c8b964ac31?auto=format&fit=crop&q=80&w=150&h=150" 
           alt="Nurse Maria Santos" class="w-12 h-12 rounded-xl object-cover ring-2 ring-teal-500/20">
      <div>
        <p class="text-sm font-bold text-slate-900">Maria Santos</p>
        <p class="text-xs text-teal-700 font-semibold">Nurse on Duty • Day Shift</p>
        <p class="text-[10px] text-slate-400 font-mono mt-0.5">PRC-NUR-2024-08192</p>
      </div>
    </div>

    <form action="<?= nurse_url('views/dashboard/index.php') ?>" method="GET" class="space-y-4">
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Username / Staff ID</label>
        <input type="text" value="maria.santos" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
      </div>
      <div>
        <label class="text-xs font-bold text-slate-700 block mb-1.5">Password</label>
        <input type="password" value="••••••••••••" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
      </div>
      <button type="submit" class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-bold transition shadow-lg shadow-teal-600/25 flex items-center justify-center gap-2">
        <span>Enter Nurse Portal</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </button>
    </form>

    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
      <a href="<?= doctor_url('views/dashboard/index.php') ?>" class="hover:text-blue-600 transition flex items-center gap-1">
        <i data-lucide="stethoscope" class="w-3.5 h-3.5"></i>
        <span>Doctor Portal</span>
      </a>
      <a href="<?= register_url('views/dashboard/index.php') ?>" class="hover:text-indigo-600 transition flex items-center gap-1">
        <i data-lucide="clipboard" class="w-3.5 h-3.5"></i>
        <span>Registrar</span>
      </a>
    </div>

  </div>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
