<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Api\RegisterApiController;

// Public Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Authentication
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout.post');

// =========================================================================
// ROLE 4: REGISTRATION STAFF (Admitting / Pre-Consultation)
// =========================================================================
Route::middleware(['auth', 'role:Register,Registrator,Admin'])->prefix('register')->name('register.')->group(function () {
    Route::get('/', [RegisterController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [RegisterController::class, 'dashboard'])->name('dashboard.index');
    
    // Patients
    Route::get('/patients', [RegisterController::class, 'patients'])->name('patients.index');
    Route::get('/patients/{id}', [RegisterController::class, 'viewPatient'])->name('patients.view');
    Route::get('/patients/{id}/edit', [RegisterController::class, 'editPatient'])->name('patients.edit');
    Route::post('/patients/{id}/update', [RegisterController::class, 'updatePatient'])->name('patients.update');
    
    // Appointments
    Route::get('/appointments', [RegisterController::class, 'appointments'])->name('appointments.index');
    
    // Live Queue
    Route::get('/queue', [RegisterController::class, 'queue'])->name('queue.index');
    
    // Patient Intake
    Route::get('/intake', [RegisterController::class, 'registration'])->name('registration.index');
    Route::get('/registration', [RegisterController::class, 'registration'])->name('registration.alt');
    
    // Reports
    Route::get('/reports', [RegisterController::class, 'reports'])->name('reports.index');
});

// Register API Routes
Route::prefix('api/register')->name('api.register.')->group(function () {
    Route::post('/classify', [RegisterApiController::class, 'classify'])->name('classify');
    Route::post('/submit', [RegisterApiController::class, 'submitRegistration'])->name('submit');
    Route::get('/symptoms', [RegisterApiController::class, 'getSymptoms'])->name('symptoms');
    Route::get('/doctors', [RegisterApiController::class, 'getDoctors'])->name('doctors');
    Route::get('/patients', [RegisterApiController::class, 'getPatients'])->name('patients');
    Route::get('/queue', [RegisterApiController::class, 'getQueue'])->name('queue');
    Route::post('/queue/call', [RegisterApiController::class, 'callQueueNext'])->name('queue.call');
});

// =========================================================================
// STUB ROUTES for remaining role dashboards (to be fully migrated)
// These ensure login redirects work without crashing.
// =========================================================================

Route::middleware(['auth'])->group(function () {
    // Admin
    Route::get('/admin', fn() => view('stubs.dashboard', ['role' => 'Admin', 'section' => 'Administration']))->name('admin.dashboard');
    
    // Director / Chief Medical Officer
    Route::get('/director', fn() => view('stubs.dashboard', ['role' => 'Director', 'section' => 'Chief Medical Director']))->name('director.dashboard');
    
    // Medical Records
    Route::get('/records', fn() => view('stubs.dashboard', ['role' => 'Records', 'section' => 'Medical Records']))->name('records.dashboard');
    
    // Doctor
    Route::get('/doctor', fn() => view('stubs.dashboard', ['role' => 'Doctor', 'section' => 'Doctor Consultation']))->name('doctor.dashboard');
    
    // Nurse
    Route::get('/nurse', fn() => view('stubs.dashboard', ['role' => 'Nurse', 'section' => 'Nursing Station']))->name('nurse.dashboard');
    
    // MedTech
    Route::get('/medtech', fn() => view('stubs.dashboard', ['role' => 'MedTech', 'section' => 'Medical Technology / Laboratory']))->name('medtech.dashboard');
    
    // Pharmacy
    Route::get('/pharmacy', fn() => view('stubs.dashboard', ['role' => 'Pharmacist', 'section' => 'Pharmacy']))->name('pharmacy.dashboard');
    
    // Billing / Cashier / Accountant
    Route::get('/billing', fn() => view('stubs.dashboard', ['role' => 'Billing', 'section' => 'Billing & Cashier']))->name('billing.dashboard');
});

