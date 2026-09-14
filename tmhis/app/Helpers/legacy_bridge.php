<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Global Legacy Compatibility Bridge
 */

namespace TMHIS {
    use PDO;

    if (!class_exists('TMHIS\Database', false)) {
        class Database
        {
            public static function getConnection(): PDO
            {
                return \Database::getConnection();
            }
        }
    }
}

namespace {

    if (!class_exists('Database', false)) {
        class Database
        {
            private static ?PDO $instance = null;

            public static function getConnection(): PDO
            {
                try {
                    return \Illuminate\Support\Facades\DB::connection()->getPdo();
                } catch (\Throwable $e) {
                    if (self::$instance === null) {
                        $host = function_exists('env') ? env('DB_HOST', 'db') : (getenv('DB_HOST') ?: 'db');
                        $port = function_exists('env') ? env('DB_PORT', 3306) : (getenv('DB_PORT') ?: 3306);
                        $db   = function_exists('env') ? env('DB_DATABASE', 'MedicalRegistrationDB') : (getenv('DB_DATABASE') ?: 'MedicalRegistrationDB');
                        $user = function_exists('env') ? env('DB_USERNAME', 'root') : (getenv('DB_USERNAME') ?: 'root');
                        $pass = function_exists('env') ? env('DB_PASSWORD', 'root_password') : (getenv('DB_PASSWORD') ?: 'root_password');
                        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
                        self::$instance = new PDO($dsn, $user, $pass, [
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES   => false,
                        ]);
                    }
                    return self::$instance;
                }
            }
        }
    }

    if (!class_exists('Session', false)) {
        class Session
        {
            public static function set(string $key, mixed $value): void
            {
                session([$key => $value]);
                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION[$key] = $value;
                }
            }

            public static function get(string $key, mixed $default = null): mixed
            {
                return session($key, $_SESSION[$key] ?? $default);
            }

            public static function has(string $key): bool
            {
                return session()->has($key) || isset($_SESSION[$key]);
            }

            public static function remove(string $key): void
            {
                session()->forget($key);
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
                return \Illuminate\Support\Facades\Auth::check() || !empty(session('user_id'));
            }

            public static function isAdmin(): bool
            {
                $role = strtolower((string)session('role', ''));
                return self::isLoggedIn() && ($role === 'admin' || $role === 'system administrator');
            }

            public static function isDoctor(): bool
            {
                $role = strtolower((string)session('role', ''));
                return self::isLoggedIn() && ($role === 'doctor' || !empty(session('doctor_id')));
            }

            public static function getCurrentUser(): ?array
            {
                $u = \Illuminate\Support\Facades\Auth::user();
                $role = session('role', $u->Role ?? 'Staff');
                $fullName = session('full_name', $u ? ($u->FirstName . ' ' . $u->LastName) : 'Hospital Staff');

                return [
                    'user_id'       => (int)session('user_id', $u->UserID ?? 1),
                    'doctor_id'     => (int)session('doctor_id', 11),
                    'username'      => (string)session('username', $u->Username ?? 'staff'),
                    'name'          => (string)$fullName,
                    'full_name'     => (string)$fullName,
                    'role'          => (string)$role,
                    'email'         => (string)session('email', $u->Email ?? 'staff@tupihospital.gov.ph'),
                    'specialty'     => (string)session('specialty', 'Cardiologist'),
                    'specialty_id'  => (int)session('specialty_id', 2),
                    'license'       => (string)session('license_number', 'LIC-MED-CARD-101'),
                    'license_number'=> (string)session('license_number', 'LIC-MED-CARD-101'),
                    'license_no'    => (string)session('license_no', session('license_number', 'PRC-0084920')),
                    'title'         => (string)session('title', 'Clinical Officer / Medical Staff'),
                    'department'    => (string)session('department', 'Clinical Services'),
                    'shift'         => (string)session('shift', 'Regular Shift (08:00 AM – 05:00 PM)'),
                    'initials'      => (string)session('initials', 'TM'),
                    'nurse_id'      => (string)session('nurse_id', 'RN-2026-001'),
                    'medtech_id'    => (string)session('medtech_id', 'RMT-2026-088'),
                    'pharmacist_id' => (string)session('pharmacist_id', 'RPH-2026-042'),
                    'avatar'        => (string)session('profile_image', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300&h=300'),
                    'clinic'        => (string)session('clinic', 'Cardiology Outpatient Clinic (Room 204)'),
                ];
            }
        }
    }
}
