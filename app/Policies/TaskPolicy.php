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
        return $user->hasRole('Admin')
            || $task->project->workspace->users->contains($user);
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
            return $task->project->workspace->users->contains($user);
        }

        // Members can edit their own tasks or tasks assigned to them
        return $task->creator_id === $user->id
            || $task->assignees->contains($user);
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            return $task->project->workspace->users->contains($user);
        }

        return $task->creator_id === $user->id;
    }

    public function changeStatus(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function comment(User $user, Task $task): bool
    {
        return $task->project->workspace->users->contains($user);
    }
}
