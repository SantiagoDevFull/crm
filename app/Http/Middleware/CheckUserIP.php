<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Support\Str;

class CheckUserIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user_id = $request->user()->id;
        $clientIP = $request->ip();

        $user_groups = UserGroup::where('user_id', $user_id)->get();

        $groups = Group::whereIn('id', $user_groups->pluck('group_id'))->get();

        $hasEmptyIp = $groups->contains(function ($group) {
            return empty($group->ip);
        });

        if (!$hasEmptyIp) {

            $group = $groups->first(function ($group) use ($clientIP) {
                return Str::contains($group->ip, $clientIP);
            });

            if (!$group) {
                abort(403, 'Acceso denegado.');
            }
        }

        return $next($request);
    }
}
