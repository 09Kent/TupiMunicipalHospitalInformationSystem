<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Register\PatientRegistration;
use App\Services\Register\SymptomClassifier;
use App\Services\Register\Symptom;
use App\Services\Register\Doctor;
use App\Services\Register\Patient;
use App\Services\Register\Queue;
use App\Services\Register\Appointment;
use App\Services\Register\BodySystem;
use App\Services\Register\BodyLocation;
use Illuminate\Support\Facades\Auth;

class RegisterApiController extends Controller
{
    public function classify(Request $request)
    {
        $complaint = $request->input('complaint', '');
        $symptoms = $request->input('symptoms', []);
        $locationCode = $request->input('bodyLocation');

        if (empty(trim($complaint)) && empty($symptoms)) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint description or symptoms are required.'
            ], 400);
        }

        try {
            $classifier = new SymptomClassifier();
            $result = $classifier->classifyComplaint($complaint, (array)$symptoms, $locationCode);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Classification error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitRegistration(Request $request)
    {
        $data = $request->isJson() ? $request->json()->all() : $request->all();

        $firstName = $data['personal']['firstName'] ?? $data['firstName'] ?? '';
        $lastName  = $data['personal']['lastName'] ?? $data['lastName'] ?? '';
        $dob       = $data['personal']['dob'] ?? $data['dob'] ?? '';
        $gender    = $data['personal']['gender'] ?? $data['gender'] ?? '';
        $phone     = $data['personal']['phone'] ?? $data['phone'] ?? '';
        $email     = $data['personal']['email'] ?? $data['email'] ?? '';
        $address   = $data['personal']['address'] ?? $data['address'] ?? '';
        $complaint = $data['complaint'] ?? '';

        $errors = [];
        if (empty(trim($firstName))) $errors['firstName'] = 'First name is required.';
        if (empty(trim($lastName))) $errors['lastName'] = 'Last name is required.';
        if (empty($dob)) $errors['dob'] = 'Date of birth is required.';
        if (empty($gender)) $errors['gender'] = 'Gender is required.';
        if (empty(trim($phone))) $errors['phone'] = 'Contact number is required.';
        if (empty(trim($address))) $errors['address'] = 'Address is required.';
        if (empty(trim($complaint))) $errors['complaint'] = 'Chief complaint description is required.';

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed on server.',
                'errors'  => $errors
            ], 422);
        }

        $userId = session('user_id') ?: (Auth::id() ?: 1);

        try {
            $regService = new PatientRegistration();
            $result = $regService->registerCompletePatient($data, (int)$userId);
            return response()->json($result, $result['success'] ? 201 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server registration error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSymptoms(Request $request)
    {
        try {
            $symptomService = new Symptom();
            $bodySystemService = new BodySystem();
            $bodyLocationService = new BodyLocation();

            $symptoms = $symptomService->getAll();
            $systems = $bodySystemService->getAll();
            $locations = $bodyLocationService->getAll();

            return response()->json([
                'success'   => true,
                'symptoms'  => $symptoms,
                'systems'   => $systems,
                'locations' => $locations,
                'data'      => $symptoms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving symptoms metadata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDoctors(Request $request)
    {
        $specialtyId = (int)$request->query('specialty_id', 0);
        $doctorService = new Doctor();
        $doctors = $specialtyId > 0 ? $doctorService->getBySpecialty($specialtyId) : $doctorService->getAll();

        return response()->json([
            'success' => true,
            'data' => $doctors
        ]);
    }

    public function getPatients(Request $request)
    {
        $search = trim($request->query('search', ''));
        $patientService = new Patient();
        $patients = $patientService->getPaginated(10, 0, $search);

        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    public function getQueue(Request $request)
    {
        $queueService = new Queue();
        return response()->json([
            'success' => true,
            'now_serving' => $queueService->getNowServing(),
            'today_queue' => $queueService->getTodayQueue(),
            'stats' => $queueService->getQueueStats()
        ]);
    }

    public function callQueueNext(Request $request)
    {
        $queueId = (int)$request->input('queue_id');
        $queueService = new Queue();
        $result = $queueService->updateStatus($queueId, 'In Consultation');

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Patient called into consultation.' : 'Failed to update queue status.'
        ]);
    }
}
