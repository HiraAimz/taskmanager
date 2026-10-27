<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Models\User;
use App\Models\Workspace;

class CreateWorkspace
{
    /**
     * Create a new workspace with the given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $owner): Workspace
    {
        // Create the workspace
        $workspace = Workspace::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'owner_id' => $owner->id,
        ]);

        // Attach the owner as a member of the workspace
        $workspace->users()->attach($owner->id);

        return $workspace;
    }
}
