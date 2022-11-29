<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            return redirect()->route('member.dashboard');
        }
        return view('member.login');
    }
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credential = $request->only('email', 'password');
        $credential['role'] = 'member';

        if (Auth::attempt($credential)) {
            $request->session()->regenerate();

            return redirect()->route('member.dashboard');
        }
        return back()->withErrors([
            'credentials' => 'Your credentials are wrong!'
        ])->withInput();
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect('/');
    }
}
