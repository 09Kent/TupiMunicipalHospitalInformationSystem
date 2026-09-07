<?php
// index.php
// Entry point for Tupi Municipal Hospital Information Management System

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// If logged in, redirect to Dashboard; otherwise to Dashboard (which sets default session) or Login
if (Session::isLoggedIn()) {
    header('Location: ' . base_url('views/dashboard/index.php'));
    exit;
} else {
    header('Location: ' . base_url('views/dashboard/index.php'));
    exit;
}
