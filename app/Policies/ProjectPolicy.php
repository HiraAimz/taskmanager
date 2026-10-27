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
        return $user->hasRole('Admin')
            || $project->workspace->users->contains($user);
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
            return $project->workspace->users->contains($user);
        }

        return false;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('Admin')
            || ($user->hasRole('Manager') && $project->workspace->users->contains($user));
    }
}
