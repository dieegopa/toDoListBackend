<?php

namespace App\Http\Controllers;

use App\OpenApi\RequestBodies\TagRequestBody;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;
use Spatie\Tags\Tag;

class TagController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(path: '/api/v1/tags', summary: 'Create a new tag', security: [['bearerAuth' => [],],], tags: ['Tags'])]
    #[OA\Response(response: '200', description: 'Tag created')]
    #[OA\RequestBody(
        description: 'Tag data',
        required: true,
        content: [
            new OA\JsonContent(
                ref: TagRequestBody::class,
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validate = $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        $tag = Tag::findOrCreate([
            'name' => $validate['name'],
            'type' => 'user-todo'
        ]);

        return response()->json($tag);
    }

}
