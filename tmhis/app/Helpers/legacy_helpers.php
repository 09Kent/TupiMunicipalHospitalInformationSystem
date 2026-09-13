<?php
/**
 * Tupi Municipal Hospital Information Management System
 * System-Wide Core Helpers & Dynamic URL Resolution
 */

if (!defined('TMHIS_SYSTEM_NAME')) {
    define('TMHIS_SYSTEM_NAME', 'Tupi Municipal Hospital Information Management System');
}

/**
 * Escape HTML output securely
 */
if (!function_exists('e')) {
    function e(?string $str): string {
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Returns the absolute base web URL for the application root (e.g. http://localhost/main or http://localhost:8000)
 */
function app_root_url(string $path = ''): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Check if script is under /main/
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $subDir = '';
    if (strpos($script, '/main/') === 0 || $script === '/main') {
        $subDir = '/main';
    }
    
    $path = ltrim($path, '/');
    return $protocol . $host . $subDir . ($path ? '/' . $path : '');
}

/**
 * Returns the URL to a specific Section module or sub-path
 */
function section_url(string $path = ''): string {
    return app_root_url('Section/' . ltrim($path, '/'));
}

/**
 * Returns the direct URL to the official hospital logo asset
 */
function hospital_logo_url(): string {
    return app_root_url('assets/logo.png');
}

/**
 * Role to dashboard URL mapping
 */
function role_dashboard_url(string $role): string {
    switch (strtolower(trim($role))) {
        case 'admin':
        case 'system administrator':
            return section_url('Admin/index.php');
        case 'chief':
        case 'director':
        case 'hospital chief':
        case 'hospital chief / medical director':
            return section_url('Chef_Medical_officer/index.php');
        case 'records':
        case 'medical records officer':
            return section_url('Medical_Officer/index.php');
        case 'register':
        case 'registrator':
        case 'admitting / registration staff':
            return section_url('Register/views/dashboard/index.php');
        case 'doctor':
        case 'attending physician':
            return section_url('Doctor/views/dashboard/index.php');
        case 'nurse':
        case 'nurse on duty':
            return section_url('Nurse/index.php');
        case 'medtech':
        case 'medical technologist':
            return section_url('Med_Tech/index.php');
        case 'pharmacist':
        case 'pharmacy aide':
        case 'pharmacist / pharmacy aide':
            return section_url('Pharmacy/index.php');
        case 'billing':
        case 'cashier':
        case 'billing / cashier staff':
        case 'accountant':
            return section_url('Accountant/index.php');
        default:
            return section_url('Doctor/views/dashboard/index.php');
    }
}
