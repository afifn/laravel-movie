<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\UserPremium;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MovieController extends Controller
{
    public function show($id)
    {
        $movie = Movie::find($id);
        return view('member.detail', ['movie' => $movie]);
    }

    public function watch($id)
    {
        $userId = auth()->user()->id;
        $movie = Movie::find($id);

        $userPremium = UserPremium::where('user_id', $userId)->first();
        if ($userPremium) {
            $endOfSubscription = $userPremium->end_of_subscription;
            /* convert string date ke carbon */
            $date = Carbon::createFromFormat('Y-m-d', $endOfSubscription);

            $isValidateSubscription = $date->greaterThan(now());
            if ($isValidateSubscription) {
                return view('member.watching', ['movie' => $movie]);
            }
        }
        return redirect()->route('member.pricing');
    }
}
