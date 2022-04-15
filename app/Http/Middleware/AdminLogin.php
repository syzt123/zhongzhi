<?php


namespace App\Http\Middleware;

use Closure;

class AdminLogin
{
    public function handle($request, Closure $next)
    {

        if ($request->session()->has('ttl') && $request->session()->get('ttl') > time() && $request->session()->get('admin_login') === true) {
            if ($request->path() == '/') {
                return redirect('admin/');
            }
        } else {
            session(['admin_login' => false]);
            if ($request->path() != '/') {
                return redirect('/');
            }
        }
        return $next($request);
    }
}
