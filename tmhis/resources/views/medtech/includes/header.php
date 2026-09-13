<?php
// Med_Tech/includes/header.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Medical Technologist Portal | Tupi Municipal Hospital Information Management System';
$activeMenu = $activeMenu ?? 'dashboard';
$currentUser = Session::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="Tupi Municipal Hospital Laboratory Portal — Process lab test requests, track specimen tubes, record multi-parameter results, and manage catalog reference ranges.">
  
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
            lab: {
              50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac',
              400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d',
              800: '#166534', 900: '#14532d'
            },
            tech: {
              50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
              400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
              800: '#3730a3', 900: '#312e81'
            }
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            display: ['Outfit', 'Inter', 'sans-serif'],
            mono: ['JetBrains Mono', 'Fira Code', 'Courier New', 'monospace']
          },
          boxShadow: {
            'card': '0 2px 12px -2px rgba(0, 0, 0, 0.04), 0 1px 3px 0 rgba(0, 0, 0, 0.02)',
            'card-hover': '0 12px 28px -4px rgba(99, 102, 241, 0.09), 0 4px 10px -2px rgba(0, 0, 0, 0.03)',
            'glow': '0 0 25px rgba(99, 102, 241, 0.25)'
          }
        }
      }
    }
  </script>

  <!-- Google Fonts: Inter & Outfit -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
      -webkit-font-smoothing: antialiased;
    }
    
    .font-display {
      font-family: 'Outfit', sans-serif;
    }

    /* Custom Slim Scrollbars */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    /* Sidebar Transitions */
    .sidebar-expanded {
      width: 250px !important;
    }
    .sidebar-collapsed {
      width: 72px !important;
    }
    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .sidebar-header-text,
    .sidebar-collapsed .sidebar-badge,
    .sidebar-collapsed .sidebar-profile-info {
      display: none !important;
    }
    .sidebar-collapsed .sidebar-item {
      justify-content: center !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    .sidebar-collapsed .sidebar-logo-container {
      justify-content: center !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    .sidebar-collapsed .sidebar-section-title {
      display: none !important;
    }

    /* Print Stylesheet for Laboratory Reports */
    @media print {
      body {
        background-color: #ffffff !important;
        color: #000000 !important;
      }
      #sidebar, #topbar, #sidebarOverlay, .no-print, button, .action-buttons {
        display: none !important;
      }
      .printable-report-container {
        display: block !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 20px !important;
        background: white !important;
        border: none !important;
        box-shadow: none !important;
      }
      .print-shadow-none {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
      }
    }
  </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-50 text-slate-800">
