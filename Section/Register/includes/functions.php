<?php
// includes/functions.php

/**
 * Escape string for secure HTML output
 */
function e(?string $string): string
{
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or get CSRF token
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate submitted CSRF token
 */
function validate_csrf(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format Date nicely (e.g. 15 Aug 2026)
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
 * Format Time nicely (e.g. 02:30 PM)
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
 * Get category badge color styling
 */
function get_category_badge(?string $category): string
{
    switch ($category) {
        case 'Emergency':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        case 'Admitted':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Consultation':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'Inpatient':
            return 'bg-purple-50 text-purple-700 border-purple-200';
        case 'Outpatient':
        default:
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    }
}

/**
 * Get appointment status badge color styling
 */
function get_status_badge(?string $status): string
{
    switch ($status) {
        case 'In Consultation':
            return 'bg-blue-600 text-white shadow-xs';
        case 'Waiting':
            return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'Completed':
            return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'Cancelled':
        case 'No Show':
            return 'bg-rose-100 text-rose-800 border-rose-200';
        case 'Confirmed':
            return 'bg-teal-100 text-teal-800 border-teal-200';
        case 'Scheduled':
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
}

/**
 * Return JSON response and terminate
 */
function json_response(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Base URL helper
 */
function base_url(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Determine base directory relative to server document root
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $base = rtrim($scriptDir, '/\\');
    
    // If in views or api subdirectory, trim up
    $base = preg_replace('/(\/views.*|\/api.*|\/controllers.*)/', '', $base);
    
    $path = ltrim($path, '/');
    return $protocol . $host . ($base ? $base : '') . '/' . $path;
}
