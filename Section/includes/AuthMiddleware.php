<?php
// Section/includes/AuthMiddleware.php
// Centralized Authentication and Role-Based Access Control (RBAC) Guard

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthMiddleware
{
    public static function requireRole(array|string $allowedRoles): void
    {
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
        $userRole = $_SESSION['role'] ?? '';

        if (!$isLoggedIn || !in_array($userRole, $allowedRoles, true)) {
            // Check if API or AJAX
            if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Access Denied: You do not have permission to access this resource.'
                ]);
                exit;
            }

            // Redirect to unified login
            header('Location: /Section/Doctor/views/auth/login.php');
            exit;
        }
    }
}
