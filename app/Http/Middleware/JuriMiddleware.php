<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JuriMiddleware
{
    /**
     * Handle an incoming request for Juri panel.
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

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses panel Juri.');
        }

        // 2. Role juri dan admin (admin boleh masuk untuk supervisi/testing) diizinkan
        if (in_array(auth()->user()->role, ['juri', 'admin'])) {
            return $next($request);
        }

        // 3. Jika bukan juri atau admin
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Hak akses tidak valid.'
            ], 403);
        }

        return redirect()->route('login')->with('error', 'Akses juri tidak valid.');
    }
}
