<?php


use App\Models\User;
use function Pest\Laravel\actingAs;

it('returns unauthorized when no token is provided', function () {
    $this
        ->getJson('api/v1/user')
        ->assertStatus(401);
});

it('returns user data', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this
        ->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

    actingAs($user, 'sanctum')
        ->getJson('api/v1/user')
        ->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ])
        ->assertJsonFragment([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

it('returns unauthorized when no token is provided when indexing user tags', function () {
    $this
        ->getJson('api/v1/user/tags')
        ->assertStatus(401);
});

it('returns user tags', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->getJson('api/v1/user/tags')
        ->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'name',
                'slug',
            ]
        ]);
});
