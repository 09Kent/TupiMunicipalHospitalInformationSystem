<?php
// Doctor/index.php
// Main entry point for Doctor Portal

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

if (Session::isLoggedIn()) {
    $role = Session::get('role');
    if ($role === 'Doctor') {
        header('Location: ' . doctor_url('views/dashboard/index.php'));
        exit;
    } else {
        header('Location: ' . register_url('views/dashboard/index.php'));
        exit;
    }
} else {
    header('Location: ' . doctor_url('views/dashboard/index.php'));
    exit;
}
