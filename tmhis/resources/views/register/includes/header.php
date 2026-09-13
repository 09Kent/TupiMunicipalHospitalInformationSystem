<?php
// includes/header.php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

$currentUser = Session::getCurrentUser() ?: [
    'name' => 'Sarah Jenkins',
    'role' => 'Registrator',
    'email' => 'sarah.jenkins@tupimunicipal.gov.ph'
];

$pageTitle = $pageTitle ?? 'Tupi Municipal Hospital Information Management System';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle) ?></title>
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

  <!-- Chart.js for Reports & Analytics -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>" />
</head>
<body class="bg-medical-mesh min-h-screen text-slate-800 flex flex-col justify-between selection:bg-blue-600 selection:text-white">
