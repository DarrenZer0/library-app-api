<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import Log facade

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Log role information for debugging
        Log::info('User role: ' . ($request->user() ? $request->user()->role : 'No user authenticated'));

        // Check if the user is authenticated and has the right role
        if ($request->user() && $request->user()->role === $role) {
            return $next($request);
        }

        // If the role does not match, return unauthorized response
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}