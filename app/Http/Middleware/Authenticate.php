<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use Closure;
use Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    public function handle($request, Closure $next, ...$guards)
    {
        
        if (Auth::guard('api')->guest()) {
            return response()->json(Response::generateResponse(HttpStatusCode::UNAUTHORIZED, '', ''), HttpStatusCode::UNAUTHORIZED);
        }
        $request->setUserResolver(function () {
            return Auth::guard('api')->user();
        });
    
        // other checks
    
        return $next($request);
    }
    
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            //return generateResponse 
            return response()->json([]);

        }
    }
}
