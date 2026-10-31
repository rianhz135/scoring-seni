<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request for Admin / Dewan panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses panel Admin / Dewan.');
        }

        // 2. Role admin diizinkan
        if (auth()->user()->role === 'admin') {
            return $next($request);
        }

        // 3. User login tapi role-nya bukan admin (misal: juri mencoba masuk admin)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Hanya untuk Admin / Dewan.'
            ], 403);
        }

        return redirect()->route('juri.dashboard')->with('error', 'Akses ditolak: Halaman ini hanya untuk Admin / Dewan.');
    }
}
