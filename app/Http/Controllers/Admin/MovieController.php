<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movies', ['movies' => $movies]);
    }
    public function create()
    {
        return view('admin.movie-create');
    }
    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movie-edit', ['movie' => $movie]);
    }

    public function store(Request $request)
    {
        $data = $request->except('_token'); //ignore token ke db

        $request->validate([
            'title' => 'required|string',
            'movie' => 'required|url',
            'trailer' => 'required|url',
            'duration' => 'required|string',
            'release_date' => 'required|string',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'small_thumbnail' => 'required|image|mimes:jpeg,jpg,png,webp',
            'large_thumbnail' => 'required|image|mimes:jpeg,jpg,png,webp',
            'short_about' => 'required|string',
            'about' => 'required|string',
            'featured' => 'required'
        ]);
        // gambar object temp
        $smallThumbnail = $request->small_thumbnail;
        $largeThumbnail = $request->large_thumbnail;
        // original name file
        $originalSmallThumbnailName = Str::random(10) . $smallThumbnail->getClientOriginalName();
        $originalLargeThumbnailName = Str::random(10) . $largeThumbnail->getClientOriginalName();

        // simpan file
        $smallThumbnail->storeAs('public/thumbnail', $originalSmallThumbnailName);
        $largeThumbnail->storeAs('public/thumbnail', $originalLargeThumbnailName);

        //ubah object ke namestring
        $data['small_thumbnail'] = $originalSmallThumbnailName;
        $data['large_thumbnail'] = $originalLargeThumbnailName;
        // dd($data);

        Movie::create($data);
        return redirect()->route('admin.movies')->with('success', 'Movie Created');
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('_token');
        $request->validate([
            'title' => 'required|string',
            'movie' => 'required|url',
            'trailer' => 'required|url',
            'duration' => 'required|string',
            'release_date' => 'required|string',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'small_thumbnail' => 'required|image|mimes:jpeg,jpg,png,webp',
            'large_thumbnail' => 'required|image|mimes:jpeg,jpg,png,webp',
            'short_about' => 'required|string',
            'about' => 'required|string',
            'featured' => 'required'
        ]);
        $movie = Movie::find($id);
        if ($request->small_thumbnail) {
            // save new thumbnail
            $smallThumbnail = $request->small_thumbnail;
            $originalSmallThumbnailName = Str::random(10) . $smallThumbnail->getClientOriginalName();
            $smallThumbnail->storeAs('public/thumbnail', $originalSmallThumbnailName);
            $data['small_thumbnail'] = $originalSmallThumbnailName;

            //remove old data
            Storage::delete(['public/thumbnail/' . $movie->small_thumbnail]);
        }
        if ($request->large_thumbnail) {
            // save new thumbnail
            $largeThumbnail = $request->large_thumbnail;
            $originalLargeThumbnailName = Str::random(10) . $largeThumbnail->getClientOriginalName();
            $largeThumbnail->storeAs('public/thumbnail', $originalLargeThumbnailName);
            $data['large_thumbnail'] = $originalLargeThumbnailName;
            //remove old data
            Storage::delete(['public/thumbnail/' . $movie->large_thumbnail]);

            $movie->update($data);
            return redirect()->route('admin.movies')->with('success', 'Movie Updated');
        }
    }

    public function delete($id)
    {
        Movie::find($id)->delete();
        return redirect()->route('admin.movies')->with('success', 'Movie Deleted');
    }
}
