<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveMiddleware
{
    /**
     * Ensure the authenticated user has active status.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->status !== 'active') {
            return response()->json([
                'message' => 'Forbidden. Active account only.',
            ], 403);
        }

        return $next($request);
    }
}
