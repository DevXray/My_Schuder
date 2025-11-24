<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // ✅ Check if user is authenticated
        if (!auth()->check()) {
            Log::warning('CheckRole: User not authenticated');
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'redirect' => route('login')
                ], 401);
            }
            
            return redirect()->route('login');
        }

        $user = auth()->user();

        // ✅ Get role name safely with error handling
        try {
            // First check if role relationship exists
            if (!$user->role) {
                Log::error('CheckRole: User has no role', [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id
                ]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User role not found. Please contact administrator.',
                        'redirect' => route('dashboard')
                    ], 403);
                }
                
                return redirect()->route('dashboard')
                    ->with('error', 'Role tidak ditemukan. Hubungi administrator.');
            }
            
            $userRole = $user->getRoleName();
            
        } catch (\Exception $e) {
            Log::error('CheckRole: Error getting role name', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error checking user role',
                    'redirect' => route('dashboard')
                ], 500);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'Terjadi kesalahan saat memeriksa role.');
        }

        // ✅ Debug log
        Log::debug('CheckRole middleware', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'required_roles' => $roles,
            'request_path' => $request->path(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
        ]);

        // ✅ Check if user has required role
        if (!in_array($userRole, $roles)) {
            Log::warning('CheckRole: Access denied', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'required_roles' => $roles,
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only ' . implode(' or ', $roles) . ' can access this page.',
                    'redirect' => route('dashboard'),
                    'debug' => [
                        'your_role' => $userRole,
                        'required' => $roles
                    ]
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}