<?php
// Doctor/includes/header.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Doctor Portal | Tupi Municipal Hospital Information Management System';
$activeMenu = $activeMenu ?? 'dashboard';
$currentUser = Session::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#eff6ff',
              100: '#dbeafe',
              200: '#bfdbfe',
              300: '#93c5fd',
              400: '#60a5fa',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
              800: '#1e40af',
              900: '#1e3a8a',
              950: '#172554'
            },
            medical: {
              cardio: '#ef4444',
              neuro: '#8b5cf6',
              gastro: '#f59e0b',
              pulmo: '#06b6d4',
              ortho: '#10b981',
              pedia: '#ec4899',
              general: '#3b82f6'
            }
          },
          fontFamily: {
            sans: ['Inter', 'Outfit', 'system-ui', 'sans-serif'],
            display: ['Outfit', 'Inter', 'sans-serif']
          },
          boxShadow: {
            'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
            'card-hover': '0 12px 30px -4px rgba(37, 99, 235, 0.12)',
            'glow': '0 0 25px rgba(59, 130, 246, 0.25)'
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
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    /* Specialty Anatomy Glowing Animations */
    @keyframes pulse-glow {
      0%, 100% {
        filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.8)) drop-shadow(0 0 16px rgba(239, 68, 68, 0.4));
        transform: scale(1);
      }
      50% {
        filter: drop-shadow(0 0 16px rgba(239, 68, 68, 1)) drop-shadow(0 0 28px rgba(239, 68, 68, 0.7));
        transform: scale(1.04);
      }
    }
    @keyframes neuro-pulse {
      0%, 100% {
        filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.8));
        opacity: 0.9;
      }
      50% {
        filter: drop-shadow(0 0 20px rgba(139, 92, 246, 1));
        opacity: 1;
      }
    }
    @keyframes pulmo-breath {
      0%, 100% {
        transform: scale(1);
        filter: drop-shadow(0 0 8px rgba(6, 182, 212, 0.8));
      }
      50% {
        transform: scale(1.03);
        filter: drop-shadow(0 0 18px rgba(6, 182, 212, 1));
      }
    }
    @keyframes gastro-flow {
      0%, 100% {
        filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.8));
      }
      50% {
        filter: drop-shadow(0 0 18px rgba(245, 158, 11, 1));
      }
    }
    .animate-heart-beat {
      animation: pulse-glow 1.5s infinite ease-in-out;
      transform-origin: center;
    }
    .animate-neuro-glow {
      animation: neuro-pulse 2s infinite ease-in-out;
      transform-origin: center;
    }
    .animate-pulmo-breath {
      animation: pulmo-breath 2.5s infinite ease-in-out;
      transform-origin: center;
    }
    .animate-gastro-glow {
      animation: gastro-flow 2s infinite ease-in-out;
      transform-origin: center;
    }

    /* Print styling */
    @media print {
      body * {
        visibility: hidden;
      }
      #printableArea, #printableArea * {
        visibility: visible;
      }
      #printableArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
        background: white !important;
        box-shadow: none !important;
      }
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body class="h-full font-sans text-slate-800 antialiased flex bg-slate-50 selection:bg-blue-600 selection:text-white">
