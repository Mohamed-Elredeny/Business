<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class admin
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

        if($user->type == 1){
            return $next($request);
        }
        return back()->withStatus('you are not allowed to enter this page');

    }
}
