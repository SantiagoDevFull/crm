<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Group;

class CheckUserIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientIP = $request->ip();

        $userWithIP = Group::where('ip', 'like', '%' . $clientIP . '%')->exists();

        if (!$userWithIP) {
            abort(403, 'Acceso denegado.');
        }

        return $next($request);
    }
}
