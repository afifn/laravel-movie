<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $movie = Movie::orderBy('created_at', 'ASC')
            ->get();

        $movie_featured = Movie::orderBy('created_at', 'ASC')
            ->where('featured', '1')
            ->limit(2)
            ->get();
        return view('member.dashboard', ['movies' => $movie, 'movie_featured' => $movie_featured]);
    }
    public function subscription()
    {
        return view('member.subscription');
    }
}
