<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StatusType;
use App\Models\Project;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
            'type' => fake()->randomElement(StatusType::cases())->value,
            'position' => 0,
            'is_default' => false,
        ];
    }
}
