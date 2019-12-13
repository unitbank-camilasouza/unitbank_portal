<?php
// Author: Davi Mendes Pimentel
// last modified date: 12/12/2019

namespace App\Http\Middleware;

use Closure;
use App\Admins;

class IpAdminAccess
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
        // verifies if the ip address exists on database
        if(! Admins::ipExists($request->ip()))
            return redirect('/');

        return $next($request);
    }
}
