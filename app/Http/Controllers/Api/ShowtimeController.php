<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function index(): JsonResponse
    {
        $showtimes = Showtime::query()
            ->with('movie')
            ->latest('id')
            ->get();

        return response()->json($showtimes);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'subtitle_id' => ['nullable', 'integer', 'exists:subtitles,id'],
            'start_time' => ['required', 'date'],
        ]);

        $showtime = Showtime::create($payload)->load('movie');

        return response()->json($showtime, 201);
    }

    public function show(Showtime $showtime): JsonResponse
    {
        return response()->json($showtime->load('movie'));
    }

    public function update(Request $request, Showtime $showtime): JsonResponse
    {
        $payload = $request->validate([
            'movie_id' => ['sometimes', 'required', 'integer', 'exists:movies,id'],
            'room_id' => ['sometimes', 'required', 'integer', 'exists:rooms,id'],
            'subtitle_id' => ['nullable', 'integer', 'exists:subtitles,id'],
            'start_time' => ['sometimes', 'required', 'date'],
        ]);

        $showtime->update($payload);

        return response()->json($showtime->fresh()->load('movie'));
    }

    public function destroy(Showtime $showtime): JsonResponse
    {
        $showtime->delete();

        return response()->json(status: 204);
    }
}
