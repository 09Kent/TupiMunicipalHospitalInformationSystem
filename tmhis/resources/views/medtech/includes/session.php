<?php
// Med_Tech/includes/session.php

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
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
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

    public static function isMedTech(): bool
    {
        return self::isLoggedIn() && (self::get('role') === 'Medical Technologist' || !empty($_SESSION['medtech_id']));
    }

    public static function getCurrentUser(): array
    {
        return [
            'user_id'    => $_SESSION['user_id'] ?? 7,
            'medtech_id' => $_SESSION['medtech_id'] ?? 'RMT-2026-088',
            'username'   => $_SESSION['username'] ?? 'robert.santos',
            'name'       => $_SESSION['full_name'] ?? 'Robert Santos, RMT',
            'role'       => 'Medical Technologist',
            'title'      => 'Senior Medical Technologist / Clinical Analyst',
            'email'      => $_SESSION['email'] ?? 'robert.santos@tupimunicipal.gov.ph',
            'department' => 'Clinical Pathology & Diagnostic Laboratory',
            'shift'      => 'Morning Lab Shift (07:00 AM – 03:00 PM)',
            'license_no' => 'PRC-MLS-0084920',
            'avatar'     => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=150&auto=format&fit=crop&q=80',
            'initials'   => 'RS'
        ];
    }
}
} // end class_exists guard
