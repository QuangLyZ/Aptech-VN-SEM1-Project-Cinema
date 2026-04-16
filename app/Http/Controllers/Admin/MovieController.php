<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::latest()->paginate(10);
        return view('admin.movies.index', [
            'movies' => $movies,
            'activeTab' => 'management',
            'pageTitle' => 'Quản lý Phim'
        ]);
    }

    public function create()
    {
        return view('admin.movies.create', [
            'activeTab' => 'management',
            'pageTitle' => 'Thêm Phim Mới'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'actors' => 'nullable|string',
            'age_limit' => 'nullable|integer',
            'trailer_link' => 'nullable|url'
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('posters', 'public');
            $validated['poster'] = '/storage/' . $path;
        }

        Movie::create($validated);
        return redirect()->route('admin.movies.index')->with('success', 'Thêm phim thành công!');
    }

    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', [
            'movie' => $movie,
            'activeTab' => 'management',
            'pageTitle' => 'Chỉnh Sửa Phim'
        ]);
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'actors' => 'nullable|string',
            'age_limit' => 'nullable|integer',
            'trailer_link' => 'nullable|url'
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('posters', 'public');
            $validated['poster'] = '/storage/' . $path;
        }

        $movie->update($validated);
        return redirect()->route('admin.movies.index')->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();
        return redirect()->route('admin.movies.index')->with('success', 'Xóa phim thành công!');
    }

    public function list()
    {
        $today = today();

        $movies = \App\Models\Movie::whereHas('showtimes', function ($q) use ($today) {
                $q->whereBetween('start_time', [
                    $today->copy()->startOfDay(),
                    $today->copy()->addDays(4)->endOfDay(),
                ]);
            })
            ->with(['showtimes' => function ($q) use ($today) {
                $q->whereBetween('start_time', [
                        $today->copy()->startOfDay(),
                        $today->copy()->addDays(4)->endOfDay(),
                    ])
                    ->orderBy('start_time')
                    ->with('room.cinema');
            }])
            ->get();

        $movies->each(function ($movie) {
            $movie->cinema_ids = $movie->showtimes
                ->map(fn($s) => optional($s->room)->cinema_id)
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $movie->dates_map = $movie->showtimes
                ->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->toDateString())
                ->map(fn($group) => $group->map(fn($s) => [
                    'id'   => $s->id,
                    'time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
                ])->values())
                ->toArray();
        });

        $cinemas = \App\Models\Cinema::orderBy('name')->get();

        $dates = collect(range(0, 4))->map(fn($i) => [
            'value' => $today->copy()->addDays($i)->toDateString(),
            'label' => $i === 0 ? 'Hôm nay' : $today->copy()->addDays($i)->format('D'),
            'day'   => $today->copy()->addDays($i)->format('d'),
            'month' => $today->copy()->addDays($i)->format('M'),
        ]);

        return view('movies.index', compact('movies', 'cinemas', 'dates'));
    }
}
