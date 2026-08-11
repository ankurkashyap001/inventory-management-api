<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || ($user->role !== 'admin' && !$user->is_admin)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Access restricted to administrator accounts only.',
                'code'    => 403,
            ], 403);
        }

        return $next($request);
    }
}