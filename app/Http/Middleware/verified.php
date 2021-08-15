<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class verified
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

        if($user->email_verified_at == null){
            return $this->returnError('your email is not verified',422);
        }
        return $next($request);
    }
}
