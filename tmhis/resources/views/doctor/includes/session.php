<?php
// Doctor/includes/session.php

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

if (!class_exists('Session', false)) {
/**
 * Session Helper Functions for Doctor Portal
 */
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

    public static function isDoctor(): bool
    {
        return self::isLoggedIn() && (self::get('role') === 'Doctor' || !empty($_SESSION['doctor_id']));
    }

    public static function getCurrentUser(): ?array
    {
        if (self::isLoggedIn()) {
            return [
                'user_id'    => $_SESSION['user_id'],
                'doctor_id'  => $_SESSION['doctor_id'] ?? null,
                'username'   => $_SESSION['username'] ?? 'cardio',
                'name'       => $_SESSION['full_name'] ?? 'Dr. Daniel Lewis',
                'role'       => $_SESSION['role'] ?? 'Doctor',
                'email'      => $_SESSION['email'] ?? 'cardio@hospital.com',
                'specialty'  => $_SESSION['specialty'] ?? 'Cardiologist',
                'specialty_id' => $_SESSION['specialty_id'] ?? 2,
                'license'    => $_SESSION['license_number'] ?? 'LIC-MED-CARD-101',
                'avatar'     => $_SESSION['profile_image'] ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300'
            ];
        }
        return null;
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
