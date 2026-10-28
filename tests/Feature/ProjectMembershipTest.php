<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Projects\ProjectEdit;
use App\Livewire\Workspaces\WorkspaceEdit;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectMembershipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_workspace_member_can_be_added_to_project(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Admin');

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $member = User::factory()->create();
        $member->assignRole('Member');
        $workspace->users()->attach($member->id);

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($owner);

        Livewire::test(ProjectEdit::class, ['project' => $project])
            ->set('selectedUserId', $member->id)
            ->set('selectedRole', 'member')
            ->call('addMember')
            ->assertHasNoErrors();

        $this->assertTrue($project->fresh()->hasMember($member));
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);
    }

    public function test_non_workspace_user_cannot_be_added_to_project(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Admin');

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $outsider = User::factory()->create();
        $outsider->assignRole('Member');
        // $outsider is NOT in workspace

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($owner);

        Livewire::test(ProjectEdit::class, ['project' => $project])
            ->set('selectedUserId', $outsider->id)
            ->set('selectedRole', 'member')
            ->call('addMember')
            ->assertSee('User must be a member of the workspace before being added to this project.');

        $this->assertFalse($project->fresh()->hasMember($outsider));
    }

    public function test_project_member_can_view_project_board(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Admin');

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $member = User::factory()->create();
        $member->assignRole('Member');
        $workspace->users()->attach($member->id);

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);

        $project->members()->attach($member->id, ['role' => 'member']);

        $this->actingAs($member)
            ->get(route('projects.board', $project))
            ->assertOk();
    }

    public function test_user_not_in_workspace_cannot_view_project(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Admin');

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $outsider = User::factory()->create();
        $outsider->assignRole('Member');

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($outsider)
            ->get(route('projects.board', $project))
            ->assertForbidden();
    }

    public function test_removing_user_from_workspace_revokes_project_memberships(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Admin');

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $member = User::factory()->create();
        $member->assignRole('Member');
        $workspace->users()->attach($member->id);

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);

        $project->members()->attach($member->id, ['role' => 'member']);
        $this->assertTrue($project->fresh()->hasMember($member));

        $this->actingAs($owner);

        Livewire::test(WorkspaceEdit::class, ['workspace' => $workspace])
            ->call('removeMember', $member->id)
            ->assertHasNoErrors();

        // Must be detached from both workspace and project
        $this->assertFalse($workspace->fresh()->users()->where('users.id', $member->id)->exists());
        $this->assertFalse($project->fresh()->hasMember($member));
    }
}
