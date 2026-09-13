<?php
// Nurse/includes/header.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Nurse Portal | Tupi Municipal Hospital Information Management System';
$activeMenu = $activeMenu ?? 'dashboard';
$currentUser = Session::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="Tupi Municipal Hospital Information Management System Nurse On Duty Portal — Patient vital signs recording, status monitoring, queue assistance, task coordination and department notifications.">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
              400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
              800: '#1e40af', 900: '#1e3a8a', 950: '#172554'
            },
            nurse: {
              50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4',
              400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e',
              800: '#115e59', 900: '#134e4a'
            }
          },
          fontFamily: {
            sans: ['Inter', 'Outfit', 'system-ui', 'sans-serif'],
            display: ['Outfit', 'Inter', 'sans-serif']
          },
          boxShadow: {
            'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
            'card-hover': '0 12px 30px -4px rgba(13, 148, 136, 0.12)',
            'glow': '0 0 25px rgba(20, 184, 166, 0.25)'
          }
        }
      }
    }
  </script>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- AOS Animate On Scroll -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Heart pulse animation */
    @keyframes heartbeat {
      0%, 100% { transform: scale(1); }
      10% { transform: scale(1.12); }
      20% { transform: scale(1); }
      30% { transform: scale(1.08); }
      40% { transform: scale(1); }
    }
    .animate-heartbeat {
      animation: heartbeat 1.5s infinite ease-in-out;
      transform-origin: center;
    }

    /* Breathing animation */
    @keyframes breathe {
      0%, 100% { transform: scaleY(1); opacity: 0.7; }
      50% { transform: scaleY(1.08); opacity: 1; }
    }
    .animate-breathe {
      animation: breathe 3s infinite ease-in-out;
      transform-origin: center bottom;
    }

    /* Pulse ring */
    @keyframes pulse-ring {
      0% { transform: scale(0.8); opacity: 1; }
      80%, 100% { transform: scale(2.2); opacity: 0; }
    }
    .animate-pulse-ring {
      animation: pulse-ring 1.8s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }

    /* Vital glow */
    @keyframes vital-glow {
      0%, 100% { filter: drop-shadow(0 0 6px rgba(20, 184, 166, 0.6)); }
      50% { filter: drop-shadow(0 0 16px rgba(20, 184, 166, 1)); }
    }
    .animate-vital-glow {
      animation: vital-glow 2s infinite ease-in-out;
    }

    /* ECG line animation */
    @keyframes ecg-draw {
      0% { stroke-dashoffset: 600; }
      100% { stroke-dashoffset: 0; }
    }
    .ecg-line {
      stroke-dasharray: 600;
      animation: ecg-draw 2s linear infinite;
    }

    /* Notification badge bounce */
    @keyframes badge-bounce {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.2); }
    }
    .animate-badge-bounce {
      animation: badge-bounce 2s infinite ease-in-out;
    }

    /* Smooth fade-in for modals */
    .modal-backdrop {
      transition: opacity 0.3s ease, backdrop-filter 0.3s ease;
    }
    .modal-content {
      transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .modal-backdrop.active {
      opacity: 1;
      backdrop-filter: blur(8px);
    }
    .modal-content.active {
      opacity: 1;
      transform: scale(1) translateY(0);
    }

    /* Number counter animation */
    .counter-value {
      transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Card entrance stagger via CSS */
    [data-aos] {
      transition-property: transform, opacity !important;
    }

    /* Print styling */
    @media print {
      body * { visibility: hidden; }
      #printableArea, #printableArea * { visibility: visible; }
      #printableArea {
        position: absolute; left: 0; top: 0; width: 100%;
        margin: 0; padding: 20px; background: white !important; box-shadow: none !important;
      }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body class="h-full font-sans text-slate-800 antialiased flex bg-slate-50 selection:bg-teal-600 selection:text-white">
