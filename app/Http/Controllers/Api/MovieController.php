<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Movie::query()->latest('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'release_date' => ['nullable', 'date'],
            'director' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'poster' => ['nullable', 'string', 'max:255'],
            'actors' => ['nullable', 'string'],
            'age_limit' => ['nullable', 'integer', 'min:0'],
            'trailer_link' => ['nullable', 'string', 'max:255'],
        ]);

        $movie = Movie::create($payload);

        return response()->json($movie, 201);
    }

    public function show(Movie $movie): JsonResponse
    {
        return response()->json($movie);
    }

    public function update(Request $request, Movie $movie): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'release_date' => ['nullable', 'date'],
            'director' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'poster' => ['nullable', 'string', 'max:255'],
            'actors' => ['nullable', 'string'],
            'age_limit' => ['nullable', 'integer', 'min:0'],
            'trailer_link' => ['nullable', 'string', 'max:255'],
        ]);

        $movie->update($payload);

        return response()->json($movie->fresh());
    }

    public function destroy(Movie $movie): JsonResponse
    {
        $movie->delete();

        return response()->json(status: 204);
    }
}
