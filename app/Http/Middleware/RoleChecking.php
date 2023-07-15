<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleChecking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        $result=true;
        $owner=request()->user()->owner;
        foreach($roles as $role){
            $result&=$owner->hasRole($role);
        }
        $result|=$owner->hasRole(config('roles.admin'));
        $result|=$owner->hasRole(config('roles.principal'));
        if($result)
        return $next($request);
        else
        return response()->json([
            'message'=>'User does not have the right roles.'
        ],403);
        
    }
}
