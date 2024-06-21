<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Tags\Tag;

class TagController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
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
