<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Service\UserAccessHelper;

class SoftwareAccess
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
        // return $next($request);

        if(UserAccessHelper::hasValidSubscription(Auth::user())) {

            if (! Gate::allows('software_access')) {
                return abort(401);
            }

            // dd(UserAccessHelper::hasAccessToModule($request));

            return $next($request);
        }

        return redirect('/');
    }
}
