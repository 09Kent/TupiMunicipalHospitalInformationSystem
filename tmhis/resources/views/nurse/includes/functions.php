<?php
// Nurse/includes/functions.php

/**
 * Escape string for HTML
 */
function e(?string $string): string
{
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or retrieve CSRF token
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validate_csrf(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format Date nicely (e.g. 16 Aug 2026)
 */
function format_date(?string $dateString, string $format = 'd M Y'): string
{
    if (!$dateString || $dateString === '0000-00-00') return '—';
    try {
        $date = new DateTime($dateString);
        return $date->format($format);
    } catch (Exception $e) {
        return $dateString;
    }
}

/**
 * Format Time nicely (e.g. 09:30 AM)
 */
function format_time(?string $timeString): string
{
    if (!$timeString) return '—';
    try {
        $time = new DateTime($timeString);
        return $time->format('h:i A');
    } catch (Exception $e) {
        return $timeString;
    }
}

/**
 * Get patient status badge styling
 */
function get_patient_status_badge(?string $status): string
{
    switch ($status) {
        case 'Critical':
            return 'bg-red-50 text-red-700 border border-red-200';
        case 'Needs Attention':
            return 'bg-amber-50 text-amber-700 border border-amber-200';
        case 'Under Observation':
            return 'bg-blue-50 text-blue-700 border border-blue-200';
        case 'For Discharge':
            return 'bg-purple-50 text-purple-700 border border-purple-200';
        case 'Stable':
        default:
            return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
    }
}

/**
 * Get vital sign status color
 */
function get_vital_status_class(?string $status): string
{
    switch ($status) {
        case 'Critical':
            return 'text-red-600';
        case 'Warning':
            return 'text-amber-600';
        case 'Normal':
        default:
            return 'text-emerald-600';
    }
}

/**
 * Get queue status badge
 */
function get_queue_badge(?string $status): string
{
    switch ($status) {
        case 'Called':
            return 'bg-blue-100 text-blue-800 border border-blue-200 font-semibold';
        case 'In Progress':
            return 'bg-indigo-100 text-indigo-800 border border-indigo-200 font-semibold';
        case 'Completed':
            return 'bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold';
        case 'Cancelled':
            return 'bg-rose-100 text-rose-800 border border-rose-200 font-semibold';
        case 'Waiting':
        default:
            return 'bg-amber-100 text-amber-800 border border-amber-200 font-semibold';
    }
}

/**
 * Get task status badge
 */
function get_task_badge(?string $status): string
{
    switch ($status) {
        case 'In Progress':
            return 'bg-blue-600 text-white shadow-sm font-semibold';
        case 'Completed':
            return 'bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold';
        case 'Cancelled':
            return 'bg-rose-100 text-rose-800 border border-rose-200 font-semibold';
        case 'Pending':
        default:
            return 'bg-amber-100 text-amber-800 border border-amber-200 font-semibold';
    }
}

/**
 * Priority badge styling
 */
function get_priority_badge(?string $priority): string
{
    switch ($priority) {
        case 'Urgent':
        case 'High':
            return 'bg-rose-100 text-rose-700 border border-rose-200 font-bold';
        case 'Medium':
            return 'bg-amber-100 text-amber-700 border border-amber-200';
        case 'Low':
        default:
            return 'bg-slate-100 text-slate-600 border border-slate-200';
    }
}

/**
 * JSON response helper
 */
function json_response(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Nurse portal base URL
 */
function nurse_url(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $prefix = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/main') === 0 || strpos($_SERVER['REQUEST_URI'] ?? '', '/main') === 0) ? '/main' : '';
    
    $path = ltrim($path, '/');
    return $protocol . $host . $prefix . '/Section/Nurse/' . $path;
}

/**
 * Doctor portal URL helper
 */
function doctor_url(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $prefix = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/main') === 0 || strpos($_SERVER['REQUEST_URI'] ?? '', '/main') === 0) ? '/main' : '';
    
    $path = ltrim($path, '/');
    return $protocol . $host . $prefix . '/Section/Doctor/' . $path;
}

/**
 * Register portal URL helper
 */
function register_url(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $prefix = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/main') === 0 || strpos($_SERVER['REQUEST_URI'] ?? '', '/main') === 0) ? '/main' : '';
    
    $path = ltrim($path, '/');
    return $protocol . $host . $prefix . '/Section/Register/' . $path;
}
