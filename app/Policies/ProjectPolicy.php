<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Check if user is a member of the project's workspace
        return $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            // Check if user is a member of the project's workspace
            return $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists();
        }

        return false;
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            // Check if user is a member of the project's workspace
            return $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists();
        }

        return false;
    }
}
