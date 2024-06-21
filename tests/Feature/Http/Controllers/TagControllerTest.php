<?php

use App\Models\User;
use Spatie\Tags\Tag;
use function Pest\Laravel\actingAs;

it('returns forbidden when user is not authenticated', function () {
    $this
        ->postJson('/api/v1/tags', ['name' => 'tag'])
        ->assertStatus(401);
});

it('returns precondition failed when name is not provided', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson('/api/v1/tags')
        ->assertStatus(422);
});

it('creates a new tag', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson('/api/v1/tags', [
            'name' => 'tag',
        ])
        ->assertStatus(200);
});

it('does not creates a new tag when the name is duplicated', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson('/api/v1/tags', [
            'name' => 'tag',
        ])
        ->assertStatus(200);

    actingAs($user, 'sanctum')
        ->postJson('/api/v1/tags', [
            'name' => 'tag',
        ])
        ->assertStatus(200);

    $newTags = Tag::containing('tag')->get();

    $this->assertCount(1, $newTags);
});
