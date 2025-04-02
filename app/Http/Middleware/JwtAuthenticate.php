<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->input('token') ?? $request->bearerToken();
        $serialKey = $request->input('serial_key');

        if (!$token || !$serialKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token d\'authentification manquant',
            ], 401);
        }

        // Rechercher la clé de série dans la base de données
        $key = \App\Models\SerialKey::where('serial_key', $serialKey)->first();
        if (!$key) {
            return response()->json([
                'status' => 'error',
                'message' => 'Clé de série invalide',
            ], 404);
        }

        // Vérifier le token en cache
        $cachedToken = Cache::get('licence_token_' . $key->id);
        if (!$cachedToken || $cachedToken !== $token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalide ou expiré',
            ], 401);
        }

        return $next($request);
    }
}