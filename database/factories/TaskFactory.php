<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Tags\Tag;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'due_date' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'user_id' => User::factory(),
            'tag_id' => Tag::query()->create(['name' => fake()->word])
        ];
    }
}
