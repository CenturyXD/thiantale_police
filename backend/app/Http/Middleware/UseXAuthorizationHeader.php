<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UseXAuthorizationHeader
{
    /**
     * Allow custom auth headers while keeping Sanctum authentication unchanged.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenHeader = $request->headers->get('Authorization-x')
            ?? $request->headers->get('X-Authorization');

        if ($tokenHeader) {
            $request->headers->set('Authorization', $tokenHeader);
            $request->server->set('HTTP_AUTHORIZATION', $tokenHeader);
        }

        return $next($request);
    }
}
