<?php


namespace App\Http\Controllers\Admin;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginController
{
    public function index()
    {
        $pass = Cache::pull('pass');
        return view('admin.login.index',compact('pass'));
    }

    public function login(Request $request)
    {
        if (!$request->isMethod('post')) {
            return redirect('/');
        } else {
            if ($request->name == 'admin' && md5($request->password) == md5('12345678')) {
                session(['admin_login' => true, 'ttl' => Carbon::now()->addHours(1)->timestamp]);
                return redirect('admin/');
            } else {
                Cache::put('pass','账号密码错误！');
                return redirect('/');
            }
        }

    }

    public function loginout(Request $request)
    {

        session(['admin_login' => false, 'ttl' => 0]);
        return redirect('/');
    }
}
