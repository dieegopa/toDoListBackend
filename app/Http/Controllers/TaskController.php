<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\OpenApi\RequestBodies\CreateTaskRequestBody;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(path: '/api/v1/tasks', summary: 'Get user tasks', security: [['bearerAuth' => [],],], tags: ['Tasks'])]
    #[OA\Response(response: '200', description: 'User tasks')]
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

    /**
     * @param Request $request
     * @return Response
     */
    #[OA\Post(path: '/api/v1/tasks', summary: 'Create a new task', security: [['bearerAuth' => [],],], tags: ['Tasks'])]
    #[OA\Response(response: '204', description: 'Task created')]
    #[OA\RequestBody(
        description: 'Task data',
        required: true,
        content: [
            new OA\JsonContent(
                ref: CreateTaskRequestBody::class,
            )
        ]
    )]
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

    /**
     * @param Task $task
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(path: '/api/v1/tasks/{task}', summary: 'Get task', security: [['bearerAuth' => [],],], tags: ['Tasks'])]
    #[OA\Response(response: '200', description: 'Task data')]
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

    /**
     * @param Task $task
     * @param Request $request
     * @return Response|JsonResponse
     */
    #[OA\Put(path: '/api/v1/tasks/{task}', summary: 'Update task', security: [['bearerAuth' => [],],], tags: ['Tasks'])]
    #[OA\Response(response: '204', description: 'Task updated')]
    #[OA\RequestBody(
        description: 'Task data',
        required: true,
        content: [
            new OA\JsonContent(
                ref: CreateTaskRequestBody::class,
            )
        ]
    )]
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

    /**
     * @param Task $task
     * @param Request $request
     * @return Response|JsonResponse
     */
    #[OA\Delete(path: '/api/v1/tasks/{task}', summary: 'Delete task', security: [['bearerAuth' => [],],], tags: ['Tasks'])]
    #[OA\Response(response: '204', description: 'Task deleted')]
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
