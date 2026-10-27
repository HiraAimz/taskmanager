<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;

class UpdateProject
{
    public function handle(Project $project, array $data): Project
    {
        $project->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'color' => $data['color'] ?? $project->color,
            'icon' => $data['icon'] ?? $project->icon,
        ]);

        return $project->fresh();
    }
}
