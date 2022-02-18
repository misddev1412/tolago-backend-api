<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $role, $permission = null)
    {

        if(!Auth::guard('api')->user()->hasRole($role)) {
            return response()->json(Response::generateResponse(HttpStatusCode::FORBIDDEN, '', ''));
        }

        if($permission !== null && !Auth::guard('api')->user()->can($permission)) {
            return response()->json(Response::generateResponse(HttpStatusCode::FORBIDDEN, '', ''));
        }

        return $next($request);
    }

}
