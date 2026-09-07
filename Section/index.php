<?php
// Section/index.php
// Tupi Municipal Hospital Information Management System - Gateway Router

session_start();
require_once __DIR__ . '/includes/helpers.php';

if (isset($_SESSION['role'])) {
    $url = role_dashboard_url($_SESSION['role']);
    header('Location: ' . $url);
    exit;
}

// Redirect to unified login page
header('Location: ' . section_url('Doctor/views/auth/login.php'));
exit;
