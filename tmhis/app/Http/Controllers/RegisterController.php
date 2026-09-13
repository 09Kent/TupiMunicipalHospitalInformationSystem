<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Register\Report;
use App\Services\Register\Patient;
use App\Services\Register\Appointment;
use App\Services\Register\Queue;
use App\Services\Register\Doctor;
use App\Services\Register\BodyLocation;
use App\Services\Register\BodySystem;
use App\Services\Register\Symptom;
use App\Services\Register\SymptomClassifier;
use App\Services\Register\PatientRegistration;

class RegisterController extends Controller
{
    protected Report $reportService;
    protected Patient $patientService;
    protected Appointment $appointmentService;
    protected Queue $queueService;
    protected Doctor $doctorService;

    public function __construct()
    {
        $this->reportService = new Report();
        $this->patientService = new Patient();
        $this->appointmentService = new Appointment();
        $this->queueService = new Queue();
        $this->doctorService = new Doctor();
    }

    public function dashboard()
    {
        $kpis = $this->reportService->getDashboardKPIs();
        $recentPatients = $this->patientService->getPaginated(6, 0);
        $todayAppointments = $this->appointmentService->getTodayAppointments();
        $nowServing = $this->queueService->getNowServing();
        $todayQueue = $this->queueService->getTodayQueue();

        $user = [
            'name' => session('full_name', auth()->user()->FirstName ?? 'Registrator'),
        ];

        return view('register.dashboard', compact(
            'kpis',
            'recentPatients',
            'todayAppointments',
            'nowServing',
            'todayQueue',
            'user'
        ));
    }

    public function patients(Request $request)
    {
        $search = trim($request->query('search', ''));
        $category = trim($request->query('category', ''));
        $status = trim($request->query('status', ''));
        $page = max(1, (int)$request->query('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $patients = $this->patientService->getPaginated($limit, $offset, $search, $category, $status);
        $totalPatients = $this->patientService->countTotal($search, $category, $status);
        $totalPages = max(1, ceil($totalPatients / $limit));

        return view('register.patients.index', compact(
            'patients',
            'totalPatients',
            'totalPages',
            'page',
            'search',
            'category',
            'status',
            'limit'
        ));
    }

    public function viewPatient($id)
    {
        $patient = $this->patientService->getFullProfile((int)$id);
        if (!$patient) {
            abort(404, 'Patient record not found.');
        }

        return view('register.patients.view', compact('patient'));
    }

    public function editPatient($id)
    {
        $patient = $this->patientService->getFullProfile((int)$id);
        if (!$patient) {
            abort(404, 'Patient record not found.');
        }

        return view('register.patients.edit', compact('patient'));
    }

    public function updatePatient(Request $request, $id)
    {
        $data = $request->all();
        $updated = $this->patientService->update((int)$id, $data);

        if ($updated) {
            return redirect()->route('register.patients.view', $id)->with('success', 'Patient record updated successfully.');
        }

        return back()->with('error', 'Failed to update patient record.');
    }

    public function appointments(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        $status = $request->query('status', '');
        $doctorId = (int)$request->query('doctor_id', 0);

        $appointments = $this->appointmentService->getAppointments($date, $status, $doctorId);
        $doctors = $this->doctorService->getAll();
        $summary = $this->appointmentService->getDailySummary($date);

        return view('register.appointments.index', compact(
            'appointments',
            'doctors',
            'summary',
            'date',
            'status',
            'doctorId'
        ));
    }

    public function queue(Request $request)
    {
        $nowServing = $this->queueService->getNowServing();
        $todayQueue = $this->queueService->getTodayQueue();
        $queueStats = $this->queueService->getQueueStats();
        $doctors = $this->doctorService->getAll();

        return view('register.queue.index', compact(
            'nowServing',
            'todayQueue',
            'queueStats',
            'doctors'
        ));
    }

    public function registration()
    {
        $bodyLocations = (new BodyLocation())->getAll();
        $bodySystems = (new BodySystem())->getAll();
        $doctors = $this->doctorService->getAll();

        return view('register.registration.index', compact(
            'bodyLocations',
            'bodySystems',
            'doctors'
        ));
    }

    public function reports(Request $request)
    {
        $kpis = $this->reportService->getDashboardKPIs();
        $trend = $this->reportService->getRegistrationTrend();
        $demographics = $this->reportService->getDemographicBreakdown();
        $consultationTrends = $this->reportService->getConsultationTrends();
        $specialtyDist = $this->reportService->getSpecialtyDistribution();

        return view('register.reports.index', compact(
            'kpis',
            'trend',
            'demographics',
            'consultationTrends',
            'specialtyDist'
        ));
    }
}
