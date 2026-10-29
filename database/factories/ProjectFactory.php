<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'color' => fake()->hexColor(),
            'icon' => null,
            'owner_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Project $project) {
            if ($project->statuses()->count() === 0) {
                $project->statuses()->createMany([
                    [
                        'name' => 'To Do',
                        'color' => '#6B7280',
                        'type' => \App\Enums\StatusType::Todo,
                        'position' => 0,
                        'is_default' => true,
                    ],
                    [
                        'name' => 'In Progress',
                        'color' => '#3B82F6',
                        'type' => \App\Enums\StatusType::InProgress,
                        'position' => 1,
                        'is_default' => false,
                    ],
                    [
                        'name' => 'In Review',
                        'color' => '#F59E0B',
                        'type' => \App\Enums\StatusType::InProgress,
                        'position' => 2,
                        'is_default' => false,
                    ],
                    [
                        'name' => 'Done',
                        'color' => '#10B981',
                        'type' => \App\Enums\StatusType::Done,
                        'position' => 3,
                        'is_default' => false,
                    ],
                ]);
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('project_user')) {
                $project->members()->syncWithoutDetaching([$project->owner_id => ['role' => 'admin']]);
            }
        });
    }
}
