<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Priority;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'task_list_id' => TaskList::factory(),
            'status_id' => Status::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(Priority::cases())->value,
            'creator_id' => User::factory(),
            'start_date' => fake()->optional()->dateTimeBetween('-1 month', '+1 month'),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+2 months'),
            'estimated_minutes' => fake()->optional()->numberBetween(15, 480),
            'position' => 0,
        ];
    }
}
