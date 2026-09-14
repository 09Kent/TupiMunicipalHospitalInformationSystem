<?php
// Pharmacy/includes/functions.php

/**
 * Escape string for HTML output
 */
if (!function_exists('e')) {
function e(?string $string): string
{
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}
}

/**
 * Generate or retrieve CSRF token
 */
if (!function_exists('csrf_token')) {
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
}

/**
 * Format Date nicely (e.g. 28 Aug 2026)
 */
if (!function_exists('format_date')) {
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
}

/**
 * Get Prescription status badge class
 */
if (!function_exists('get_rx_status_badge')) {
function get_rx_status_badge(?string $status): string
{
    return match($status) {
        'Pending'              => 'bg-amber-50 text-amber-700 border border-amber-200/80',
        'Under Verification'   => 'bg-blue-50 text-blue-700 border border-blue-200/80',
        'Verified'             => 'bg-indigo-50 text-indigo-700 border border-indigo-200/80',
        'Ready for Dispensing' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
        'Dispensed'            => 'bg-teal-50 text-teal-700 border border-teal-200/80',
        'Rejected'             => 'bg-rose-50 text-rose-700 border border-rose-200/80',
        default                => 'bg-slate-100 text-slate-700 border border-slate-200',
    };
}
}

/**
 * Get Inventory status badge class
 */
if (!function_exists('get_inventory_status_badge')) {
function get_inventory_status_badge(?string $status): string
{
    return match($status) {
        'In Stock'      => 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
        'Low Stock'     => 'bg-amber-50 text-amber-700 border border-amber-200/80',
        'Out of Stock'  => 'bg-rose-50 text-rose-700 border border-rose-200/80',
        'Expiring Soon' => 'bg-orange-50 text-orange-700 border border-orange-200/80',
        'Expired'       => 'bg-red-50 text-red-700 border border-red-200/80',
        default         => 'bg-slate-100 text-slate-700 border border-slate-200',
    };
}
}

/**
 * Get Priority badge class
 */
if (!function_exists('get_priority_badge')) {
function get_priority_badge(?string $priority): string
{
    return match($priority) {
        'STAT'    => 'bg-rose-50 text-rose-700 border border-rose-200 font-black animate-pulse',
        'Urgent'  => 'bg-amber-50 text-amber-700 border border-amber-200 font-bold',
        'Routine' => 'bg-slate-100 text-slate-600 border border-slate-200 font-semibold',
        default   => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}
}

/**
 * Days until expiry
 */
if (!function_exists('days_until_expiry')) {
function days_until_expiry(string $expiryDate): int
{
    try {
        $expiry = new DateTime($expiryDate);
        $today  = new DateTime();
        return (int)$today->diff($expiry)->days * ($expiry > $today ? 1 : -1);
    } catch (Exception $e) {
        return 999;
    }
}
}
