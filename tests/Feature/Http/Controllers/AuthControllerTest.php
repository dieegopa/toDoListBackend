<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

it('returns precondition failed when no credentials are provided', function () {
    $this
        ->postJson('api/v1/login')
        ->assertStatus(422);
});

it('returns unauthorized when no user found', function () {
    /** @var User $user */
    $user = User::factory()->make();

    $this
        ->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => 'random_password',
        ])
        ->assertStatus(401);
});

it('returns unauthorized when invalid credentials are provided', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this
        ->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => 'random_password',
        ])
        ->assertStatus(401);
});

it('logins a user', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this
        ->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'token'
        ]);
});

it('logs out a user', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this
        ->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

    actingAs($user, 'sanctum')
        ->postJson('api/v1/logout')
        ->assertStatus(204);
});

it('returns precondition failed when no data is provided when registering', function () {
    $this
        ->postJson('api/v1/register')
        ->assertStatus(422);
});

it('returns precondition failed when invalid data is provided when registering', function () {
    $this
        ->postJson('api/v1/register', [
            'name' => 'John Doe',
            'email' => 'invalid_email',
            'password' => '1234567',
        ])
        ->assertStatus(422);
});

it('returns precondition failed when email is already taken', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this
        ->postJson('api/v1/register', [
            'name' => 'John Doe',
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertStatus(422);
});

it('registers a user', function () {
    /** @var User $user */
    $user = User::factory()->make();

    $this
        ->postJson('api/v1/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'token'
        ]);

    /** @var User $user */
    $user = User::query()
        ->where('email', $user->email)
        ->first();

    $this->assertCount(3, $user->tags()->get());
});

