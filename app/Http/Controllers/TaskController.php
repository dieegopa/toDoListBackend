<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json($user->tasks()->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'due_date' => $task->due_date,
                'tag' => $task->tag->name,
            ];
        }));

    }

    public function store(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string',
            'due_date' => 'required|date',
            'tag_id' => 'required|exists:tags,id',
        ]);

        /** @var User $user */
        $user = $request->user();

        $task = new Task();
        $task->name = $request->input('name');
        $task->due_date = $request->input('due_date');
        $task->tag_id = $request->input('tag_id');
        $task->user_id = $user->id;
        $task->save();

        return response()->noContent();
    }

    public function show(Task $task, Request $request): JsonResponse
    {

        /** @var User $user */
        $user = $request->user();

        if ($task->user_id !== $user->id) {
            return response()->json([], 403);
        }

        return response()->json([
            'id' => $task->id,
            'name' => $task->name,
            'due_date' => $task->due_date,
            'tag' => $task->tag->name,
        ]);
    }

    public function update(Task $task, Request $request): Response|JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'due_date' => 'required|date',
            'tag_id' => 'required|exists:tags,id',
        ]);

        /** @var User $user */
        $user = $request->user();

        if ($task->user_id !== $user->id) {
            return response()->json([], 403);
        }

        $task->name = $request->input('name');
        $task->due_date = $request->input('due_date');
        $task->tag_id = $request->input('tag_id');
        $task->save();

        return response()->noContent();
    }

    public function destroy(Task $task, Request $request): Response|JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($task->user_id !== $user->id) {
            return response()->json([], 403);
        }

        $task->delete();

        return response()->noContent();
    }
}
