<?php
// includes/auth.php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

// Auto-login fallback for testing convenience if session is empty
if (!Session::isLoggedIn()) {
    // Check if on login page or API request
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $isApi = strpos($currentUri, '/api/') !== false;
    $isLoginPage = strpos($currentUri, 'login') !== false;

    if (!$isLoginPage) {
        if ($isApi) {
            // For APIs, check session or allow registrator default
            // Set session for seamless demo experience
            Session::set('user_id', 1);
            Session::set('username', 'registrator');
            Session::set('full_name', 'Sarah Jenkins');
            Session::set('role', 'Registrator');
            Session::set('email', 'sarah.jenkins@tupimunicipal.gov.ph');
        } else {
            // Default login session so user directly experiences the dashboard
            Session::set('user_id', 1);
            Session::set('username', 'registrator');
            Session::set('full_name', 'Sarah Jenkins');
            Session::set('role', 'Registrator');
            Session::set('email', 'sarah.jenkins@tupimunicipal.gov.ph');
        }
    }
}

function require_auth(): void
{
    if (!Session::isLoggedIn()) {
        header('Location: ' . base_url('views/auth/login.php'));
        exit;
    }
}
