<?php
// api/reports/index.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Report.php';

try {
    $reportModel = new Report();

    $kpis = $reportModel->getDashboardKPIs();
    $trend = $reportModel->getRegistrationTrend();
    $systems = $reportModel->getBodySystemDistribution();
    $categories = $reportModel->getPatientCategories();
    $consultations = $reportModel->getConsultationStats();
    $workload = $reportModel->getDoctorWorkload();

    json_response([
        'success'             => true,
        'kpis'                => $kpis,
        'registration_trend'  => $trend,
        'body_systems'        => $systems,
        'patient_categories'  => $categories,
        'consultation_stats'  => $consultations,
        'doctor_workload'     => $workload
    ]);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
