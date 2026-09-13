<?php
// Pharmacy/includes/header.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Pharmacist Portal | Tupi Municipal Hospital Information Management System';
$activeMenu = $activeMenu ?? 'dashboard';
$currentUser = Session::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="Tupi Municipal Hospital Pharmacy Portal – Process electronic prescriptions, verify medicines, dispense to patients, and manage pharmacy inventory.">
  
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
            pharma: {
              50:  '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
              400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
              800: '#075985', 900: '#0c4a6e'
            },
            rx: {
              50:  '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd',
              400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9',
              800: '#5b21b6', 900: '#4c1d95'
            }
          },
          fontFamily: {
            sans:    ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            display: ['Outfit', 'Inter', 'sans-serif'],
            mono:    ['JetBrains Mono', 'Fira Code', 'Courier New', 'monospace']
          },
          boxShadow: {
            'card':       '0 2px 12px -2px rgba(0, 0, 0, 0.04), 0 1px 3px 0 rgba(0, 0, 0, 0.02)',
            'card-hover': '0 12px 28px -4px rgba(14, 165, 233, 0.10), 0 4px 10px -2px rgba(0, 0, 0, 0.03)',
            'glow':       '0 0 25px rgba(14, 165, 233, 0.25)'
          }
        }
      }
    }
  </script>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
      -webkit-font-smoothing: antialiased;
    }
    .font-display { font-family: 'Outfit', sans-serif; }
    .font-mono    { font-family: 'JetBrains Mono', monospace; }

    /* ─── Slim Scrollbar ─────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* ─── Sidebar Collapse ───────────────────────────── */
    .sidebar-expanded  { width: 256px !important; }
    .sidebar-collapsed { width: 72px  !important; }
    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .sidebar-header-text,
    .sidebar-collapsed .sidebar-badge,
    .sidebar-collapsed .sidebar-profile-info,
    .sidebar-collapsed .sidebar-section-title { display: none !important; }
    .sidebar-collapsed .sidebar-item { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
    .sidebar-collapsed .sidebar-logo-container { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }

    /* ─── Animations ─────────────────────────────────── */
    @keyframes float-pill {
      0%, 100% { transform: translateY(0px) rotate(-15deg); }
      50%       { transform: translateY(-10px) rotate(-15deg); }
    }
    @keyframes float-bottle {
      0%, 100% { transform: translateY(0px) rotate(5deg); }
      50%       { transform: translateY(-8px) rotate(5deg); }
    }
    @keyframes scan-line {
      0%   { top: 10%;  opacity: 0.8; }
      50%  { top: 88%;  opacity: 0.4; }
      100% { top: 10%;  opacity: 0.8; }
    }
    @keyframes stock-pulse {
      0%, 100% { opacity: 1; transform: scaleX(1); }
      50%       { opacity: 0.7; transform: scaleX(0.97); }
    }
    @keyframes alert-throb {
      0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); }
      50%       { box-shadow: 0 0 0 6px rgba(251, 191, 36, 0); }
    }
    @keyframes badge-bounce {
      0%, 100% { transform: scale(1); }
      50%       { transform: scale(1.18); }
    }
    .animate-float-pill    { animation: float-pill    3s infinite ease-in-out; }
    .animate-float-bottle  { animation: float-bottle  4s infinite ease-in-out 0.5s; }
    .animate-scan-line     { animation: scan-line     2.5s infinite ease-in-out; }
    .animate-stock-pulse   { animation: stock-pulse   2s infinite ease-in-out; }
    .animate-alert-throb   { animation: alert-throb   2s infinite ease-in-out; }
    .animate-badge-bounce  { animation: badge-bounce  2s infinite ease-in-out; }

    /* ─── Modal / Drawer ─────────────────────────────── */
    .modal-backdrop  { transition: opacity 0.3s ease, backdrop-filter 0.3s ease; }
    .modal-content   { transition: opacity 0.3s ease, transform 0.3s ease; }
    .drawer-panel    { transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
    .drawer-panel.open { transform: translateX(0) !important; }

    /* ─── Table Row Hover ────────────────────────────── */
    .rx-table-row:hover { background-color: #f8fafc; }

    /* ─── Progress bar animation ─────────────────────── */
    .stock-bar { transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1); }

    /* ─── Print ──────────────────────────────────────── */
    @media print {
      #sidebar, #topbar, #sidebarOverlay, .no-print, button { display: none !important; }
    }
  </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-50 text-slate-800">
