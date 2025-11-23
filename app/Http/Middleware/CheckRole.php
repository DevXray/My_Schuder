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

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            // If AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Hanya ' . implode(' atau ', $roles) . ' yang dapat mengakses halaman ini.',
                    'redirect' => route('dashboard')
                ], 403);
            }

            // For web request, redirect with error
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized. Hanya ' . implode(' atau ', $roles) . ' yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}