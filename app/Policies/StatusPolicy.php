<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Status;
use App\Models\User;

class StatusPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Status $status): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Check if user is a member of the status's project workspace
        return $user->workspaces()->where('workspaces.id', $status->project->workspace_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function update(User $user, Status $status): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            // Check if user is a member of the status's project workspace
            return $user->workspaces()->where('workspaces.id', $status->project->workspace_id)->exists();
        }

        return false;
    }

    public function delete(User $user, Status $status): bool
    {
        return $this->update($user, $status);
    }
}
