<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            return redirect()->route('member.dashboard');
        }
        return view('member.register');
    }
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // buat validate email 
        $emailExist = User::where('email', $request->email)->exists();
        if ($emailExist) {
            return back()->withErrors([
                'email' => 'This email already exists',
            ])->withInput();
        }

        $data['role'] = 'member';
        $data['password'] = Hash::make($request->password);

        User::create($data);
        return redirect()->route('member.login');
    }
}
