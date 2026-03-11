<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Ensure the authenticated user has admin role.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden. Admin only.',
            ], 403);
        }

        return $next($request);
    }
}
