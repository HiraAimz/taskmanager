<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->can('workspace.view') || $user->can('manage-workspaces');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return $user->workspaces()->where('workspaces.id', $workspace->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin') 
            || $user->can('workspace.create') 
            || $user->can('manage-workspaces');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        if ($user->hasRole('Admin') || $workspace->owner_id === $user->id) {
            return true;
        }

        return ($user->can('workspace.edit') || $user->can('manage-workspaces'))
            && $user->workspaces()->where('workspaces.id', $workspace->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        if ($user->hasRole('Admin') || $workspace->owner_id === $user->id) {
            return true;
        }

        return $user->can('workspace.delete')
            && $user->workspaces()->where('workspaces.id', $workspace->id)->exists();
    }

    /**
     * Determine whether the user can add members to the workspace.
     */
    public function addMember(User $user, Workspace $workspace): bool
    {
        if ($user->hasRole('Admin') || $workspace->owner_id === $user->id) {
            return true;
        }

        return ($user->can('workspace.manage_members') || $user->can('manage-workspaces'))
            && $user->workspaces()->where('workspaces.id', $workspace->id)->exists();
    }

    /**
     * Determine whether the user can remove members from the workspace.
     */
    public function removeMember(User $user, Workspace $workspace): bool
    {
        return $this->addMember($user, $workspace);
    }
}
