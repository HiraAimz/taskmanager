<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') 
            || $user->can('project.view') 
            || $user->can('manage-projects');
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Must belong to the workspace
        if (! $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists()) {
            return false;
        }

        // Project owner, workspace managers, users with manage-projects permission, or explicitly assigned project members
        if ($project->owner_id === $user->id 
            || $user->hasRole('Manager') 
            || $user->can('manage-projects')
            || $user->can('project.view')) {
            return true;
        }

        return $project->members()->where('users.id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') 
            || $user->hasRole('Manager')
            || $user->can('project.create')
            || $user->can('manage-projects');
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Must belong to workspace
        if (! $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists()) {
            return false;
        }

        if ($user->hasRole('Manager') || $user->can('manage-projects') || $user->can('project.edit')) {
            return true;
        }

        // Project owner or project admin
        if ($project->owner_id === $user->id) {
            return true;
        }

        return $project->members()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Must belong to workspace
        if (! $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists()) {
            return false;
        }

        if ($user->hasRole('Manager') || $user->can('manage-projects') || $user->can('project.delete')) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $this->update($user, $project) 
            || $user->can('project.manage_members');
    }
}
