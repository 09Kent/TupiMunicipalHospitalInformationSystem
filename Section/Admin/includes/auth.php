<?php
// Section/Admin/includes/auth.php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/Database.php';

function require_admin_auth(): void
{
    if (!Session::isLoggedIn() || Session::get('role') !== 'Admin') {
        // Redirect to unified login page
        header('Location: /Section/Doctor/views/auth/login.php');
        exit;
    }
}
