<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        Log::info("Role: ". $role);  // Debug statement

        if ( $request->user()->role !== $role) {

            return redirect()->route('resident.home');
            Log::info("Role: ". $role);  // Debug statement

        } else {
            Log::info("Role: ". $role);  // Debug statement
            return redirect()->route('admin.home');

        }

        return $next($request);
    }
}
