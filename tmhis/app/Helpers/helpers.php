<?php

if (!function_exists('format_date')) {
    function format_date(?string $dateString, string $format = 'd M Y'): string
    {
        if (!$dateString || $dateString === '0000-00-00' || $dateString === '0000-00-00 00:00:00') return '—';
        try {
            $date = new DateTime($dateString);
            return $date->format($format);
        } catch (Exception $e) {
            return $dateString;
        }
    }
}

if (!function_exists('format_time')) {
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
}

if (!function_exists('get_category_badge')) {
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
}

if (!function_exists('get_status_badge')) {
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
}

if (!function_exists('calculate_age')) {
    function calculate_age(?string $dob): int
    {
        if (!$dob || $dob === '0000-00-00') return 0;
        try {
            $birthDate = new DateTime($dob);
            $today = new DateTime('today');
            return $birthDate->diff($today)->y;
        } catch (Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        return url('/register/' . ltrim($path, '/'));
    }
}

if (!function_exists('doctor_url')) {
    function doctor_url(string $path = ''): string
    {
        return url('/doctor/' . ltrim($path, '/'));
    }
}
