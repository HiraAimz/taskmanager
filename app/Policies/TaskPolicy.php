<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
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

        // Must be member of workspace
        if (! $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists()) {
            return false;
        }

        // Project member, project owner, manager, or direct assignee/creator
        return $task->project->owner_id === $user->id
            || $task->project->members()->where('users.id', $user->id)->exists()
            || $task->creator_id === $user->id
            || $task->assignee_id === $user->id
            || $task->assignees()->where('users.id', $user->id)->exists()
            || $user->hasRole('Manager')
            || $user->can('manage-projects');
    }

    public function create(User $user, ?Project $project = null): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($project && ! $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists()) {
            return false;
        }

        return $user->can('task.create') 
            || $user->can('create-task') 
            || $user->hasRole('Manager');
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Must belong to the project workspace
        if (! $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists()) {
            return false;
        }

        // Project owners and workspace managers can edit any task
        if ($task->project->owner_id === $user->id || $user->hasRole('Manager')) {
            return true;
        }

        // Users with general project management or full task editing permissions
        if ($user->can('manage-projects')) {
            return true;
        }

        // Creator, primary assignee, or collaborator can edit their tasks
        if ($task->creator_id === $user->id 
            || $task->assignee_id === $user->id 
            || $task->assignees()->where('users.id', $user->id)->exists()) {
            return $user->can('task.edit') || $user->can('edit-task') || $user->hasRole('Member');
        }

        return false;
    }

    public function changeStatus(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Must belong to workspace
        if (! $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists()) {
            return false;
        }

        // Project owner, workspace manager, or users with task status change permission
        if ($task->project->owner_id === $user->id 
            || $user->hasRole('Manager') 
            || $user->can('manage-projects')
            || $user->can('task.change_status')) {
            return true;
        }

        // If user is creator, assigned to task (either primary or collaborator), or a project member with edit-task permission
        if ($task->creator_id === $user->id 
            || $task->assignee_id === $user->id 
            || $task->assignees()->where('users.id', $user->id)->exists()
            || ($task->project->members()->where('users.id', $user->id)->exists() && ($user->can('task.change_status') || $user->can('edit-task') || $user->can('task.edit')))) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Must belong to workspace
        if (! $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists()) {
            return false;
        }

        if ($task->project->owner_id === $user->id 
            || $user->hasRole('Manager') 
            || $user->can('manage-projects')
            || $user->can('task.delete')
            || $user->can('delete-task')) {
            return true;
        }

        return $task->creator_id === $user->id;
    }

    public function comment(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists()
            && ($user->can('comment.create') || $user->can('comment-task') || $user->hasRole(['Manager', 'Member']));
    }

    public function assign(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return $user->workspaces()->where('workspaces.id', $task->project->workspace_id)->exists()
            && ($user->can('task.assign') || $user->can('assign-task') || $user->hasRole(['Manager']) || $task->creator_id === $user->id);
    }
}
