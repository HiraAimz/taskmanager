<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\RoleCreate;
use App\Livewire\Admin\RoleEdit;
use App\Livewire\Admin\RoleIndex;
use App\Livewire\Admin\UserEdit;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_roles_list(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->get('/admin/roles')
            ->assertOk();
    }

    public function test_non_admin_cannot_view_roles_list(): void
    {
        $member = User::factory()->create();
        $member->assignRole('Member');

        $this->actingAs($member)
            ->get('/admin/roles')
            ->assertForbidden();
    }

    public function test_admin_can_create_custom_role_with_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        Livewire::test(RoleCreate::class)
            ->set('name', 'QA Tester')
            ->set('selectedPermissions', ['task.view', 'task.edit', 'task.change_status'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.roles'));

        $this->assertDatabaseHas('roles', ['name' => 'QA Tester']);
        $role = Role::findByName('QA Tester');
        $this->assertTrue($role->hasPermissionTo('task.view'));
        $this->assertTrue($role->hasPermissionTo('task.edit'));
        $this->assertTrue($role->hasPermissionTo('task.change_status'));
    }

    public function test_admin_cannot_delete_core_admin_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $adminRole = Role::findByName('Admin');

        Livewire::test(RoleIndex::class)
            ->call('deleteRole', $adminRole->id)
            ->assertSee('The core Admin role cannot be deleted.');

        $this->assertDatabaseHas('roles', ['name' => 'Admin']);
    }

    public function test_admin_cannot_delete_role_with_assigned_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $customRole = Role::create(['name' => 'Auditor']);
        $user = User::factory()->create();
        $user->assignRole($customRole);

        $this->actingAs($admin);

        Livewire::test(RoleIndex::class)
            ->call('deleteRole', $customRole->id)
            ->assertSee("Cannot delete role 'Auditor' because it is assigned to 1 user(s).");

        $this->assertDatabaseHas('roles', ['name' => 'Auditor']);
    }

    public function test_admin_can_delete_unassigned_custom_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $customRole = Role::create(['name' => 'TemporaryRole']);

        $this->actingAs($admin);

        Livewire::test(RoleIndex::class)
            ->call('deleteRole', $customRole->id)
            ->assertSee("Role 'TemporaryRole' deleted successfully.");

        $this->assertDatabaseMissing('roles', ['name' => 'TemporaryRole']);
    }

    public function test_admin_cannot_demote_the_last_administrator(): void
    {
        // Single admin in the system
        $admin = User::role('Admin')->first();
        if (!$admin) {
            $admin = User::factory()->create();
            $admin->assignRole('Admin');
        }

        // Ensure no other admins exist
        User::role('Admin')->where('id', '!=', $admin->id)->each(fn ($u) => $u->removeRole('Admin'));

        $this->actingAs($admin);

        Livewire::test(UserEdit::class, ['user' => $admin])
            ->set('role', 'Member')
            ->call('updateRole')
            ->assertSee('Cannot remove the Admin role from the last remaining administrator.');

        $this->assertTrue($admin->fresh()->hasRole('Admin'));
    }
}
