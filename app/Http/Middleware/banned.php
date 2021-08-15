<?php

namespace App\Http\Middleware;

use Closure;

class banned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

            if($user->stateId == "banned"){
            return $next($request);
        }
        return back()->withStatus('you are banned please contact the support to know the reason');
    }
}
