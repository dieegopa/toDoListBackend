<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\OpenApi\RequestBodies\LoginRequestBody;
use App\OpenApi\RequestBodies\RegisterRequestBody;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as HttpCodes;


#[OA\Info(version: '0.1', title: 'Todo API')]
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(path: '/api/v1/login', summary: 'Login user', tags: ['Auth'])]
    #[OA\Response(response: '200', description: 'User logged in')]
    #[OA\RequestBody(
        description: 'User credentials',
        required: true,
        content: [
            new OA\JsonContent(
                ref: LoginRequestBody::class,
            )
        ]
    )]
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
    #[OA\Post(path: '/api/v1/logout', summary: 'Logout user', tags: ['Auth'])]
    #[OA\Response(response: '204', description: 'User logged out')]
    public function logout(Request $request): Response
    {
        $request->user()->tokens()->delete();
        return response()->noContent();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(path: '/api/v1/register', summary: 'Register user', tags: ['Auth'])]
    #[OA\Response(response: '200', description: 'User registered')]
    #[OA\RequestBody(
        description: 'User credentials',
        required: true,
        content: [
            new OA\JsonContent(
                ref: RegisterRequestBody::class,
            )
        ]
    )]
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
