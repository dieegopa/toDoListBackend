<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as HttpCodes;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validation = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = User::query()
            ->where('email', $validation['email'])
            ->first();

        if (!$user || !Hash::check($validation['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], HttpCodes::HTTP_UNAUTHORIZED);
        }

        if ($user->tokens()->exists()) {
            $user->tokens()->delete();
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        $request->user()->tokens()->delete();
        return response()->noContent();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validation = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        /** @var User $user */
        $user = User::query()
            ->create([
                'name' => $validation['name'],
                'email' => $validation['email'],
                'password' => Hash::make($validation['password']),
            ]);

        $user->attachTags(['normal', 'important', 'urgent'], "user-$user->id-todo");

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }
}
