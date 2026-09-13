<?php
// Doctor/includes/auth.php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Doctor.php';

// Check if user requested doctor switch via GET (for fast demo and testing across specialties)
if (isset($_GET['switch_doctor'])) {
    $switchId = (int)$_GET['switch_doctor'];
    $doctorModel = new Doctor();
    $doc = $doctorModel->findById($switchId);
    if ($doc) {
        Session::set('user_id', $doc['UserID'] ?: 100 + $doc['DoctorID']);
        Session::set('doctor_id', $doc['DoctorID']);
        Session::set('username', strtolower($doc['SpecialtyCode'] ?? 'doctor'));
        Session::set('full_name', 'Dr. ' . $doc['FirstName'] . ' ' . $doc['LastName']);
        Session::set('role', 'Doctor');
        Session::set('email', $doc['Email']);
        Session::set('specialty', $doc['SpecialtyName'] ?: $doc['Specialty']);
        Session::set('specialty_id', $doc['SpecialtyID']);
        Session::set('body_system_id', $doc['BodySystemID']);
        Session::set('license_number', $doc['LicenseNumber']);
        Session::set('profile_image', $doc['ProfileImage']);
        Session::set('clinic', $doc['Clinic'] . ' (' . $doc['ClinicRoom'] . ')');
        
        $cleanUrl = strtok($_SERVER['REQUEST_URI'], '?');
        header('Location: ' . $cleanUrl);
        exit;
    }
}

// Ensure session exists
if (!Session::isLoggedIn() || Session::get('role') !== 'Doctor' || !Session::has('doctor_id')) {
    // If not on login page, initialize default Cardiologist (Dr. Daniel Lewis) for smooth demo experience or redirect
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $isLoginPage = strpos($currentUri, 'login') !== false;
    $isApi = strpos($currentUri, '/api/') !== false;

    if (!$isLoginPage) {
        $doctorModel = new Doctor();
        // Look for Dr. Daniel Lewis (Cardiologist, DoctorID 11) or first available doctor
        $doc = $doctorModel->findById(11);
        if (!$doc) {
            $allDocs = $doctorModel->getAll();
            $doc = $allDocs[0] ?? null;
        }

        if ($doc) {
            Session::set('user_id', $doc['UserID'] ?: 101);
            Session::set('doctor_id', $doc['DoctorID']);
            Session::set('username', 'cardio');
            Session::set('full_name', 'Dr. ' . $doc['FirstName'] . ' ' . $doc['LastName']);
            Session::set('role', 'Doctor');
            Session::set('email', $doc['Email']);
            Session::set('specialty', $doc['SpecialtyName'] ?: $doc['Specialty']);
            Session::set('specialty_id', $doc['SpecialtyID']);
            Session::set('body_system_id', $doc['BodySystemID'] ?? 2);
            Session::set('license_number', $doc['LicenseNumber']);
            Session::set('profile_image', $doc['ProfileImage']);
            Session::set('clinic', $doc['Clinic'] . ' (' . $doc['ClinicRoom'] . ')');
        }
    }
}

function require_doctor_auth(): void
{
    if (!Session::isLoggedIn() || Session::get('role') !== 'Doctor') {
        header('Location: ' . doctor_url('views/auth/login.php'));
        exit;
    }
}
