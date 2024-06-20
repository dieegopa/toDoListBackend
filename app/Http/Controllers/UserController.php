<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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
