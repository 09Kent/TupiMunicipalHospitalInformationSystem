<?php
// Nurse/includes/session.php

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

    public static function isNurse(): bool
    {
        return self::isLoggedIn() && (self::get('role') === 'Nurse' || !empty($_SESSION['nurse_id']));
    }

    public static function getCurrentUser(): ?array
    {
        // Return demo nurse user for development
        return [
            'user_id'    => $_SESSION['user_id'] ?? 1,
            'nurse_id'   => $_SESSION['nurse_id'] ?? 1,
            'username'   => $_SESSION['username'] ?? 'maria.santos',
            'name'       => $_SESSION['full_name'] ?? 'Maria Santos',
            'role'       => $_SESSION['role'] ?? 'Nurse',
            'email'      => $_SESSION['email'] ?? 'maria.santos@aurahealth.ph',
            'department' => $_SESSION['department'] ?? 'General Ward',
            'shift'      => $_SESSION['shift'] ?? 'Day Shift (6:00 AM – 2:00 PM)',
            'license'    => $_SESSION['license_number'] ?? 'PRC-NUR-2024-08192',
            'avatar'     => $_SESSION['profile_image'] ?? 'https://images.unsplash.com/photo-1594824476967-48c8b964ac31?auto=format&fit=crop&q=80&w=300&h=300'
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
} // end class_exists guard
