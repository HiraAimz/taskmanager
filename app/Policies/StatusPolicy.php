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
        return $user->hasRole('Admin')
            || $status->project->workspace->users->contains($user);
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
            return $status->project->workspace->users->contains($user);
        }

        return false;
    }

    public function delete(User $user, Status $status): bool
    {
        return $this->update($user, $status);
    }
}
