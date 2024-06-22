<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecureRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // verifie si l'utilisateur est authentifie et a un bon bearer token
        if (!$request->bearerToken() || !Auth::guard('sanctum')->check()) {
            return response()->json([
                'error' => 'Vous n\'etes pas autorise a acceder a cette ressource sans un token valide',
                'status' => 401,
            ], 401);
        }
        return $next($request);
    }
}
