<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

require_once app_path('Services/Doctor/Doctor.php');
require_once app_path('Services/Doctor/Patient.php');
require_once app_path('Services/Doctor/Consultation.php');
require_once app_path('Services/Doctor/Diagnosis.php');
if (file_exists(resource_path('views/doctor/includes/anatomy_model.php'))) {
    require_once resource_path('views/doctor/includes/anatomy_model.php');
}

class DoctorController extends Controller
{
    public function dashboard(Request $request)
    {
        $currentUser = \Session::getCurrentUser();
        $doctorId = (int)($request->query('switch_doctor', session('doctor_id', $currentUser['doctor_id'] ?? 11)));
        
        $doctorModel = new \Doctor();
        $patientModel = new \Patient();
        $consultationModel = new \Consultation();
        $diagnosisModel = new \Diagnosis();

        $metrics = $doctorModel->getDashboardMetrics($doctorId);
        $todayQueue = $patientModel->getTodayQueue($doctorId);
        $activeConsultation = $consultationModel->getActiveConsultation($doctorId);
        $diagnosisStats = $diagnosisModel->getDistributionStats($doctorId);

        return view('doctor.dashboard.index', compact(
            'currentUser',
            'doctorId',
            'metrics',
            'todayQueue',
            'activeConsultation',
            'diagnosisStats'
        ));
    }
}
