<?php
// ============================================
// app/Http/Middleware/CheckRole.php
// ============================================
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Jika user tidak punya role
        if (!$user->role) {
            abort(403, 'Unauthorized: No role assigned');
        }

        // Check if user has required role
        if (!in_array($user->role->name, $roles)) {
            // Jika AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya ' . implode(' atau ', $roles) . ' yang bisa mengakses halaman ini.'
                ], 403);
            }
            
            // Jika web request, return with SweetAlert message
            return redirect()->back()->with('error', 'Hanya ' . implode(' atau ', $roles) . ' yang bisa mengakses halaman ini.');
        }

        return $next($request);
    }
}

// ============================================
// app/Http/Kernel.php (UPDATE)
// Register the middleware
// ============================================
// Tambahkan di protected $middlewareAliases:
/*
protected $middlewareAliases = [
    // ... existing middlewares
    'role' => \App\Http\Middleware\CheckRole::class,
];
*/