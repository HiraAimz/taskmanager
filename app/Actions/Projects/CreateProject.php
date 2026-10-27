<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Enums\StatusType;
use App\Models\Project;
use App\Models\User;

class CreateProject
{
    public function handle(array $data, User $owner): Project
    {
        $project = Project::create([
            'workspace_id' => $data['workspace_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'color' => $data['color'] ?? '#3B82F6',
            'icon' => $data['icon'] ?? null,
            'owner_id' => $owner->id,
        ]);

        // Create default statuses
        $project->statuses()->create([
            'name' => 'To Do',
            'color' => '#6B7280',
            'type' => StatusType::Todo,
            'position' => 0,
            'is_default' => true,
        ]);

        $project->statuses()->create([
            'name' => 'In Progress',
            'color' => '#3B82F6',
            'type' => StatusType::InProgress,
            'position' => 1,
            'is_default' => false,
        ]);

        $project->statuses()->create([
            'name' => 'In Review',
            'color' => '#F59E0B',
            'type' => StatusType::InProgress,
            'position' => 2,
            'is_default' => false,
        ]);

        $project->statuses()->create([
            'name' => 'Done',
            'color' => '#10B981',
            'type' => StatusType::Done,
            'position' => 3,
            'is_default' => false,
        ]);

        return $project;
    }
}
