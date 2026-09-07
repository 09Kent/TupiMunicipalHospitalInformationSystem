<?php
// Med_Tech/includes/functions.php

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
 * Format Date nicely (e.g. 28 Aug 2026)
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
 * Get Lab Request status badge styling
 */
function get_request_status_badge(?string $status): string
{
    switch ($status) {
        case 'Pending':
            return 'bg-amber-50 text-amber-700 border border-amber-200/80';
        case 'Received':
            return 'bg-sky-50 text-sky-700 border border-sky-200/80';
        case 'Processing':
            return 'bg-indigo-50 text-indigo-700 border border-indigo-200/80';
        case 'Completed':
            return 'bg-emerald-50 text-emerald-700 border border-emerald-200/80';
        case 'Cancelled':
            return 'bg-rose-50 text-rose-700 border border-rose-200/80';
        default:
            return 'bg-slate-100 text-slate-700 border border-slate-200';
    }
}

/**
 * Get Sample Processing status badge styling
 */
function get_sample_status_badge(?string $status): string
{
    switch ($status) {
        case 'Collected':
            return 'bg-amber-50 text-amber-700 border border-amber-200/80';
        case 'Received':
            return 'bg-blue-50 text-blue-700 border border-blue-200/80';
        case 'Processing':
            return 'bg-indigo-50 text-indigo-700 border border-indigo-200/80';
        case 'For Verification':
            return 'bg-purple-50 text-purple-700 border border-purple-200/80';
        case 'Completed':
            return 'bg-emerald-50 text-emerald-700 border border-emerald-200/80';
        case 'Rejected':
            return 'bg-rose-50 text-rose-700 border border-rose-200/80';
        default:
            return 'bg-slate-100 text-slate-700 border border-slate-200';
    }
}

/**
 * Get Priority badge styling
 */
function get_priority_badge(?string $priority): string
{
    switch ($priority) {
        case 'STAT':
            return 'bg-rose-50 text-rose-700 border border-rose-200 font-black animate-pulse';
        case 'Urgent':
            return 'bg-amber-50 text-amber-700 border border-amber-200 font-bold';
        case 'Routine':
        default:
            return 'bg-slate-100 text-slate-600 border border-slate-200 font-semibold';
    }
}

/**
 * Get Result Flag badge styling
 */
function get_flag_badge(?string $flag): string
{
    switch ($flag) {
        case 'Critical':
            return 'bg-red-50 text-red-700 border border-red-300 font-black animate-pulse';
        case 'High':
            return 'bg-amber-50 text-amber-700 border border-amber-300 font-bold';
        case 'Low':
            return 'bg-blue-50 text-blue-700 border border-blue-300 font-bold';
        case 'Normal':
        default:
            return 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium';
    }
}

/**
 * Automatic flag evaluator against standard laboratory reference ranges
 */
function evaluate_result_flag(float $value, float $min, float $max, float $critLow = 0, float $critHigh = 999999): string
{
    if ($value < $critLow || $value > $critHigh) {
        return 'Critical';
    }
    if ($value < $min) {
        return 'Low';
    }
    if ($value > $max) {
        return 'High';
    }
    return 'Normal';
}
