<?php
// Pharmacy/includes/session.php

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

if (!class_exists('Session', false)) {
class Session
{
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function isPharmacist(): bool
    {
        return self::isLoggedIn() && (self::get('role') === 'Pharmacist' || !empty($_SESSION['pharmacist_id']));
    }

    public static function getCurrentUser(): array
    {
        return [
            'user_id'       => $_SESSION['user_id'] ?? 8,
            'pharmacist_id' => $_SESSION['pharmacist_id'] ?? 'RPH-2026-042',
            'username'      => $_SESSION['username'] ?? 'maria.santos',
            'name'          => $_SESSION['full_name'] ?? 'Maria Santos, RPh',
            'role'          => 'Pharmacist',
            'title'         => 'Registered Pharmacist / Pharmacy-In-Charge',
            'email'         => $_SESSION['email'] ?? 'maria.santos@tupimunicipal.gov.ph',
            'department'    => 'Pharmacy & Drug Dispensing Unit',
            'shift'         => 'Day Shift (08:00 AM – 04:00 PM)',
            'license_no'    => 'PRC-RPH-0071834',
            'avatar'        => 'https://images.unsplash.com/photo-1594824476967-48c8b964ac31?auto=format&fit=crop&q=80&w=150&h=150',
            'initials'      => 'MS'
        ];
    }
}
} // end class_exists guard
