<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\MedTechController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\BillingController;
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
// DEPARTMENT ROLE DASHBOARDS
// =========================================================================

Route::middleware(['auth'])->group(function () {
    // Admin
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Director / Chief Medical Officer
    Route::get('/director', [DirectorController::class, 'dashboard'])->name('director.dashboard');
    
    // Medical Records Officer
    Route::get('/records', [RecordsController::class, 'dashboard'])->name('records.dashboard');
    
    // Doctor Consultation
    Route::get('/doctor', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    
    // Nursing Station
    Route::get('/nurse', [NurseController::class, 'dashboard'])->name('nurse.dashboard');
    
    // Medical Technologist / Laboratory
    Route::get('/medtech', [MedTechController::class, 'dashboard'])->name('medtech.dashboard');
    
    // Pharmacy
    Route::get('/pharmacy', [PharmacyController::class, 'dashboard'])->name('pharmacy.dashboard');
    
    // Billing & Cashier
    Route::get('/billing', [BillingController::class, 'dashboard'])->name('billing.dashboard');
});

