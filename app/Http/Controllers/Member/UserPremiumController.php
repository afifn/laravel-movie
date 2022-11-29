<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\UserPremium;
use Illuminate\Http\Request;

class UserPremiumController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;
        $userPremium = UserPremium::with('package')->where('user_id', $userId)->first();

        return view('member.subscription', ['user_premium' => $userPremium]);
    }
}
