<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Use relationship-aware role name getter (works with role_id foreign key)
        $userRole = method_exists($user, 'getRoleName') ? $user->getRoleName() : ($user->role ?? null);

        // Debug log to help trace why access may be denied
        try {
            \Log::debug('CheckRole middleware', [
                'user_id' => $user->id ?? null,
                'user_role_computed' => $userRole,
                'required_roles' => $roles,
                'request_path' => $request->path(),
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors in middleware
        }

        // Check if user has required role
        if (!in_array($userRole, $roles)) {
            // If AJAX request, return JSON with debug info
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Hanya ' . implode(' atau ', $roles) . ' yang dapat mengakses halaman ini.',
                    'redirect' => route('dashboard'),
                    'debug' => ['your_role' => $userRole, 'required' => $roles]
                ], 403);
            }

            // For web request, redirect with error
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized. Hanya ' . implode(' atau ', $roles) . ' yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}