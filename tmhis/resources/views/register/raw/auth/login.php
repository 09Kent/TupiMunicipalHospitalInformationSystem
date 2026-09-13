<?php
// Register/views/auth/login.php
// Unified Role-Based Authentication System for Tupi Municipal Hospital Information Management System

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/User.php';

// Handle Logout
if (isset($_GET['logout'])) {
    Session::logout();
    header('Location: ' . base_url('views/auth/login.php'));
    exit;
}

$error = '';
$emailInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailInput = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        $error = 'Security validation failed. Please try again.';
    } elseif (empty($emailInput) || empty($password)) {
        $error = 'Please provide your email/username and password.';
    } else {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT u.*, d.DoctorID, d.SpecialtyID, d.Specialty, d.LicenseNumber, d.ProfileImage, d.Clinic, d.ClinicRoom
                FROM users u
                LEFT JOIN doctors d ON u.UserID = d.UserID
                WHERE (u.Email = :input OR u.Username = :input2) AND u.Status = 'Active'
                LIMIT 1
            ");
            $stmt->execute([':input' => $emailInput, ':input2' => $emailInput]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['PasswordHash'])) {
                // Set Session
                Session::set('user_id', (int)$user['UserID']);
                Session::set('username', $user['Username']);
                Session::set('full_name', $user['Role'] === 'Doctor' ? 'Dr. ' . $user['FirstName'] . ' ' . $user['LastName'] : $user['FirstName'] . ' ' . $user['LastName']);
                Session::set('role', $user['Role']);
                Session::set('email', $user['Email']);

                if ($user['Role'] === 'Doctor') {
                    Session::set('doctor_id', (int)$user['DoctorID']);
                    Session::set('specialty', $user['Specialty']);
                    Session::set('specialty_id', (int)$user['SpecialtyID']);
                    Session::set('license_number', $user['LicenseNumber']);
                    Session::set('profile_image', $user['ProfileImage']);
                    Session::set('clinic', $user['Clinic'] . ' (' . $user['ClinicRoom'] . ')');

                    // Redirect to Doctor Dashboard
                    header('Location: /Section/Doctor/views/dashboard/index.php');
                    exit;
                } else {
                    // Redirect to Registrator Dashboard
                    header('Location: ' . base_url('views/dashboard/index.php'));
                    exit;
                }
            } else {
                $error = 'Invalid credentials. Please check your email/username and password.';
            }
        } catch (Exception $e) {
            $error = 'Authentication error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
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
  </style>
</head>
<body class="h-full antialiased flex items-center justify-center p-4 sm:p-6 md:p-10 bg-slate-900">

  <!-- Reference 3 Split Login Container -->
  <div class="max-w-5xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row min-h-[620px] border border-slate-200">
    
    <!-- LEFT SIDE: Modern Hospital Hero Image + Dark Blue Overlay + Pitch -->
    <div class="md:w-1/2 relative bg-slate-900 flex flex-col justify-between p-8 sm:p-12 text-white overflow-hidden min-h-[280px] md:min-h-full">
      <img src="https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?auto=format&fit=crop&q=80&w=1200" 
           alt="Modern Hospital Center" 
           class="absolute inset-0 w-full h-full object-cover object-center opacity-40 scale-105">

      <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-blue-950/90 to-blue-900/80 backdrop-blur-[2px]"></div>

      <div class="relative z-10 flex items-center gap-3">
        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 ring-4 ring-white/10">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 2v20M2 12h20M12 8l4 4-4 4-4-4 4-4z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-xl font-black tracking-tight font-display">Tupi Municipal Hospital</h2>
          <p class="text-xs text-blue-200 font-semibold tracking-wider uppercase">Hospital Clinical System</p>
        </div>
      </div>

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
      
      <div class="space-y-1.5">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight font-display">Welcome back</h2>
        <p class="text-xs text-slate-500 font-medium">Sign in to your authorized hospital account to continue.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-semibold flex items-center gap-2.5">
          <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
          <span><?= e($error) ?></span>
        </div>
      <?php endif; ?>

      <form action="" method="POST" class="space-y-4 text-xs">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

        <div>
          <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider text-[10px]">Email or Username</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <i data-lucide="mail" class="w-4 h-4"></i>
            </div>
            <input type="text" id="emailField" name="email" value="<?= e($emailInput ?: 'registrator@hospital.com') ?>" required 
                   placeholder="e.g. registrator@hospital.com" 
                   class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px]">Password</label>
            <a href="javascript:void(0)" onclick="alert('Demo password is: password123')" class="text-[11px] font-bold text-blue-600 hover:text-blue-700">Forgot Password?</a>
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <i data-lucide="lock" class="w-4 h-4"></i>
            </div>
            <input type="password" id="passwordField" name="password" value="password123" required 
                   placeholder="••••••••" 
                   class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
          </div>
        </div>

        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
            <input type="checkbox" name="remember" checked 
                   class="w-4 h-4 text-blue-600 rounded-lg border-slate-300 focus:ring-blue-500" />
            <span>Remember Me on this device</span>
          </label>
        </div>

        <button type="submit" 
                class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-xs sm:text-sm shadow-xl shadow-blue-600/25 transition-all flex items-center justify-center gap-2 group">
          <span>Sign In to System</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </form>

      <div class="pt-2 border-t border-slate-100 space-y-2">
        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Quick Demo Roles (1-Click Fill):</span>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 text-[11px]">
          <button type="button" onclick="setCreds('registrator@hospital.com')" class="p-2 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 font-bold text-slate-700 hover:text-blue-700 text-left transition">
            📋 Registrator
          </button>
          <button type="button" onclick="setCreds('cardio@hospital.com')" class="p-2 rounded-xl bg-slate-50 hover:bg-rose-50 border border-slate-200 font-bold text-slate-700 hover:text-rose-700 text-left transition">
            🫀 Cardiologist
          </button>
          <button type="button" onclick="setCreds('neuro@hospital.com')" class="p-2 rounded-xl bg-slate-50 hover:bg-purple-50 border border-slate-200 font-bold text-slate-700 hover:text-purple-700 text-left transition">
            🧠 Neurologist
          </button>
          <button type="button" onclick="setCreds('pedia@hospital.com')" class="p-2 rounded-xl bg-slate-50 hover:bg-pink-50 border border-slate-200 font-bold text-slate-700 hover:text-pink-700 text-left transition">
            👶 Pediatrician
          </button>
          <button type="button" onclick="setCreds('surgeon@hospital.com')" class="p-2 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-slate-200 font-bold text-slate-700 hover:text-indigo-700 text-left transition">
            ✂️ Surgeon
          </button>
          <button type="button" onclick="setCreds('gastro@hospital.com')" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 font-bold text-slate-700 hover:text-amber-700 text-left transition">
            🧪 Gastro
          </button>
        </div>
      </div>

    </div>

  </div>

  <script>
    if (window.lucide) {
      lucide.createIcons();
    }
    function setCreds(email) {
      document.getElementById('emailField').value = email;
      document.getElementById('passwordField').value = 'password123';
    }
  </script>
</body>
</html>
