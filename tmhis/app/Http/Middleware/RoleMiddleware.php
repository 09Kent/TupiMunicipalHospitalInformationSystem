<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = strtolower(trim($user->Role ?? ''));
        $username = strtolower(trim($user->Username ?? ''));

        // Normalize requested roles
        $allowed = array_map(fn($r) => strtolower(trim($r)), $roles);

        // Check direct role match
        $hasAccess = in_array($userRole, $allowed, true);

        // Check role aliases
        if (!$hasAccess) {
            foreach ($allowed as $role) {
                if ($role === 'admin' && ($userRole === 'admin' || $username === 'admin')) {
                    $hasAccess = true;
                    break;
                }
                if (in_array($role, ['chief', 'director']) && ($userRole === 'chief' || $userRole === 'director' || $username === 'director')) {
                    $hasAccess = true;
                    break;
                }
                if ($role === 'records' && ($userRole === 'records' || $username === 'records' || $username === 'records_officer')) {
                    $hasAccess = true;
                    break;
                }
                if (in_array($role, ['register', 'registrator']) && ($userRole === 'register' || $userRole === 'registrator' || str_contains($username, 'registrator') || str_contains($username, 'register'))) {
                    $hasAccess = true;
                    break;
                }
                if ($role === 'doctor' && ($userRole === 'doctor' || !empty($user->doctor))) {
                    $hasAccess = true;
                    break;
                }
                if ($role === 'nurse' && ($userRole === 'nurse' || str_contains($username, 'nurse') || $username === 'staff.reyes')) {
                    $hasAccess = true;
                    break;
                }
                if ($role === 'medtech' && ($userRole === 'medtech' || str_contains($username, 'medtech'))) {
                    $hasAccess = true;
                    break;
                }
                if (in_array($role, ['pharmacist', 'pharmacy']) && ($userRole === 'pharmacist' || str_contains($username, 'pharmacist'))) {
                    $hasAccess = true;
                    break;
                }
                if (in_array($role, ['billing', 'cashier', 'accountant']) && ($userRole === 'billing' || in_array($username, ['cashier', 'billing', 'accountant']))) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        if (!$hasAccess) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied: You do not have permission to access this resource.'
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Access Denied: Insufficient permissions.');
        }

        return $next($request);
    }
}
