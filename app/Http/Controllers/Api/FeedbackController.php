<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Feedback::query()->latest('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'context' => ['required', 'string'],
        ]);

        $feedback = Feedback::create([
            ...$payload,
            'created_at' => now(),
        ]);

        return response()->json($feedback, 201);
    }

    public function show(Feedback $feedback): JsonResponse
    {
        return response()->json($feedback);
    }

    public function destroy(Feedback $feedback): JsonResponse
    {
        $feedback->delete();

        return response()->json(status: 204);
    }
}
