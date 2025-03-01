<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(path: '/api/v1/user', summary: 'Get user', security: [['bearerAuth' => [],],], tags: ['User'])]
    #[OA\Response(response: '200', description: 'User data')]
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(path: '/api/v1/user/tags', summary: 'Get user tags', security: [['bearerAuth' => [],],], tags: ['User'])]
    #[OA\Response(response: '200', description: 'User tags')]
    public function indexTags(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json($user->tags->map(function ($tag) {
            return [
                'name' => ucfirst($tag->name),
                'slug' => $tag->slug,
            ];
        }));
    }

}
