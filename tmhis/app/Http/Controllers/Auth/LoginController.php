<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = trim($request->input('email', ''));
        $password = $request->input('password', '');

        if (empty($input) || empty($password)) {
            return back()->withInput()->with('error', 'Please provide your email/username and password.');
        }

        // Match user by Email or Username (case-insensitive)
        $user = User::where(function ($query) use ($input) {
            $query->where('Email', $input)
                  ->orWhere('Username', $input);
        })->where('Status', 'Active')->first();

        if ($user && (Hash::check($password, $user->PasswordHash) || password_verify($password, $user->PasswordHash))) {
            Auth::login($user, $request->boolean('remember'));

            // Populate legacy session variables for 100% preservation across views & JS
            session([
                'user_id' => (int)$user->UserID,
                'username' => $user->Username,
                'full_name' => $user->Role === 'Doctor' ? 'Dr. ' . $user->FirstName . ' ' . $user->LastName : $user->FirstName . ' ' . $user->LastName,
                'role' => $user->Role,
                'email' => $user->Email,
            ]);

            // If doctor, load doctor-specific session variables
            $doctor = Doctor::where('UserID', $user->UserID)->first();
            if ($doctor) {
                $specialty = Specialty::find($doctor->SpecialtyID);
                session([
                    'doctor_id' => (int)$doctor->DoctorID,
                    'specialty' => $specialty->SpecialtyName ?? ($doctor->Specialty ?: 'General Practitioner'),
                    'specialty_id' => (int)$doctor->SpecialtyID,
                    'body_system_id' => (int)($specialty->BodySystemID ?? 0),
                    'license_number' => $doctor->LicenseNumber,
                    'profile_image' => $doctor->ProfileImage,
                    'clinic' => ($doctor->Clinic ?? '') . ($doctor->ClinicRoom ? ' (' . $doctor->ClinicRoom . ')' : ''),
                ]);
            }

            return $this->redirectUser($user);
        }

        return back()->withInput()->with('error', 'Invalid credentials. Please check your email and password.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function redirectUser(User $user)
    {
        $username = strtolower(trim($user->Username ?? ''));
        $role = strtolower(trim($user->Role ?? ''));

        if ($role === 'admin' || $username === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if (in_array($role, ['chief', 'director']) || $username === 'director') {
            return redirect()->route('director.dashboard');
        }
        if ($role === 'records' || $username === 'records' || $username === 'records_officer') {
            return redirect()->route('records.dashboard');
        }
        if (in_array($role, ['register', 'registrator']) || str_contains($username, 'registrator') || str_contains($username, 'register') || $username === 'register1') {
            return redirect()->route('register.dashboard');
        }
        if ($role === 'doctor' || Doctor::where('UserID', $user->UserID)->exists()) {
            return redirect()->route('doctor.dashboard');
        }
        if ($role === 'nurse' || str_contains($username, 'nurse') || $username === 'staff.reyes') {
            return redirect()->route('nurse.dashboard');
        }
        if ($role === 'medtech' || str_contains($username, 'medtech') || $username === 'medtech2') {
            return redirect()->route('medtech.dashboard');
        }
        if (in_array($role, ['pharmacist', 'pharmacy']) || str_contains($username, 'pharmacist')) {
            return redirect()->route('pharmacy.dashboard');
        }
        if (in_array($role, ['billing', 'cashier', 'accountant']) || in_array($username, ['cashier', 'billing', 'accountant'])) {
            return redirect()->route('billing.dashboard');
        }

        return redirect()->route('doctor.dashboard');
    }
}
