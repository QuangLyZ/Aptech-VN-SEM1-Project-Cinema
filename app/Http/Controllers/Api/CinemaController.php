<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Cinema::query()->latest('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $cinema = Cinema::create($payload);

        return response()->json($cinema, 201);
    }

    public function show(Cinema $cinema): JsonResponse
    {
        return response()->json($cinema);
    }

    public function update(Request $request, Cinema $cinema): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $cinema->update($payload);

        return response()->json($cinema->fresh());
    }

    public function destroy(Cinema $cinema): JsonResponse
    {
        $cinema->delete();

        return response()->json(status: 204);
    }
}
