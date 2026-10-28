<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Check if user is a member of the task's project workspace
        return $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists();
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create tasks
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            // Check if user is a member of the task's project workspace
            return $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists();
        }

        // Members can edit their own tasks or tasks assigned to them
        return $task->creator_id === $user->id
            || $task->assignees()->where('users.id', $user->id)->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            // Check if user is a member of the task's project workspace
            return $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists();
        }

        return $task->creator_id === $user->id;
    }

    public function changeStatus(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function comment(User $user, Task $task): bool
    {
        // Check if user is a member of the task's project workspace
        return $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists();
    }
}
