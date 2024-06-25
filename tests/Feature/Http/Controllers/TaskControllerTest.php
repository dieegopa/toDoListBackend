<?php

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use function Pest\Laravel\actingAs;

it('returns forbidden when user is not authenticated and index tasks', function () {
    $this
        ->getJson('/api/v1/tasks')
        ->assertStatus(401);
});

it('returns all tasks for authenticated user', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $tasks = Task::factory(3)->create([
        'user_id' => $user->id,
    ]);

    Task::factory(4)->create();

    actingAs($user)
        ->getJson('/api/v1/tasks')
        ->assertJson($tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'due_date' => $task->due_date->toISOString(),
                'tag' => $task->tag->name,
            ];
        })->toArray());
});

it('returns forbidden when user is not authenticated and store task', function () {
    $this
        ->postJson('/api/v1/tasks')
        ->assertStatus(401);
});

it('returns validation error when request is invalid', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user)
        ->postJson('/api/v1/tasks')
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'due_date',
            'tag_id',
        ]);
});

it('stores task for authenticated user', function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Task $task */
    $task = Task::factory()->make();

    actingAs($user)
        ->postJson('/api/v1/tasks', [
            'name' => $task->name,
            'due_date' => $task->due_date->toISOString(),
            'tag_id' => $task->tag->id,
        ])
        ->assertStatus(204);

    $this->assertDatabaseHas('tasks', [
        'name' => $task->name,
        'due_date' => $task->due_date->toDateTimeString(),
        'user_id' => $user->id,
        'tag_id' => $task->tag->id,
    ]);
});

it('returns forbidden when user is not authenticated and show task', function () {
    $task = Task::factory()->create();

    $this
        ->getJson("/api/v1/tasks/{$task->id}")
        ->assertStatus(401);
});

it('returns not found when task does not exist for user', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user)
        ->getJson('/api/v1/tasks/1')
        ->assertStatus(404);
});

it('returns forbidden when user is not the owner of the task in show', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $task = Task::factory()->create();

    actingAs($user)
        ->getJson("/api/v1/tasks/{$task->id}")
        ->assertStatus(403);
});

it('returns task for authenticated user', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->getJson("/api/v1/tasks/{$task->id}")
        ->assertJson([
            'id' => $task->id,
            'name' => $task->name,
            'due_date' => $task->due_date->toISOString(),
            'tag' => $task->tag->name,
        ]);
});

it('returns forbidden when user is not authenticated and update task', function () {
    $task = Task::factory()->create();

    $this
        ->patchJson("/api/v1/tasks/{$task->id}")
        ->assertStatus(401);
});

it('returns not found when task does not exist for user in update', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user)
        ->patchJson('/api/v1/tasks/1')
        ->assertStatus(404);
});

it('returns forbidden when user is not the owner of the task in update', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $task = Task::factory()->create();

    actingAs($user)
        ->patchJson("/api/v1/tasks/{$task->id}", [
            'name' => 'Updated Name',
            'due_date' => Carbon::now()->addDays(4),
            'tag_id' => $task->tag->id
        ])
        ->assertStatus(403);
});

it('returns validation error when request is invalid in update', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->patchJson("/api/v1/tasks/{$task->id}")
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'due_date',
            'tag_id',
        ]);
});

it('updates task for authenticated user', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    $newTask = Task::factory()->make();

    actingAs($user)
        ->patchJson("/api/v1/tasks/{$task->id}", [
            'name' => $newTask->name,
            'due_date' => $newTask->due_date->toISOString(),
            'tag_id' => $newTask->tag->id,
        ])
        ->assertStatus(204);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => $newTask->name,
        'due_date' => $newTask->due_date->toDateTimeString(),
        'tag_id' => $newTask->tag->id,
    ]);
});

it('returns forbidden when user is not authenticated and destroy task', function () {
    $task = Task::factory()->create();

    $this
        ->deleteJson("/api/v1/tasks/{$task->id}")
        ->assertStatus(401);
});

it('returns not found when task does not exist', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user)
        ->deleteJson('/api/v1/tasks/1')
        ->assertStatus(404);
});

it('returns forbidden when user is not the owner of the task', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $task = Task::factory()->create();

    actingAs($user)
        ->deleteJson("/api/v1/tasks/{$task->id}")
        ->assertStatus(403);
});

it('deletes task for authenticated user', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->deleteJson("/api/v1/tasks/{$task->id}")
        ->assertStatus(204);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});
