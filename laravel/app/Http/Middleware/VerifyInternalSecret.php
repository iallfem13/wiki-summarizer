<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyInternalSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->header('X-Internal-Secret');
        if ($secret !== config('auth.service_secret')) {
            return response()->json(['Unknown'], 401);
        }

        return $next($request);
    }
}
