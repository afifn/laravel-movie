<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class ApiMovieController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $movies = Movie::where('title', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'ASC')
            ->paginate(10);
        return response()->json([
            'error' => false,
            'message' => 'movie fetched success',
            'data' => $movies
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json([
                'error' => true,
                'message' => 'Movie not found',
            ], 400);
        } else {

            return response()->json([
                'error' => false,
                'message' => 'Movie fetced success',
                'data' => $movie,
            ], 200);
        }
    }
}
