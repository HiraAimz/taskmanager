<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Attachments\AttachmentUpload;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AttachmentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('local');
    }

    public function test_user_can_upload_image_attachment_to_task(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Member');

        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user->id);

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $user->id,
        ]);

        $status = Status::factory()->create(['project_id' => $project->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'creator_id' => $user->id,
        ]);

        $file = UploadedFile::fake()->image('screenshot.png');

        $this->actingAs($user);

        Livewire::test(AttachmentUpload::class, ['task' => $task])
            ->set('files', [$file])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('attachments', [
            'attachable_type' => Task::class,
            'attachable_id' => $task->id,
            'user_id' => $user->id,
            'original_name' => 'screenshot.png',
        ]);

        $attachment = Attachment::first();
        $this->assertNotNull($attachment);
        $this->assertNotEmpty($attachment->disk_path);

        // Verify accessors
        $this->assertEquals('screenshot.png', $attachment->file_name);
        $this->assertEquals($attachment->disk_path, $attachment->file_path);
        $this->assertNotEmpty($attachment->file_type);

        // Verify file stored in storage
        Storage::disk('local')->assertExists($attachment->disk_path);
    }

    public function test_user_can_download_and_delete_attachment(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Member');

        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user->id);

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $user->id,
        ]);

        $status = Status::factory()->create(['project_id' => $project->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'creator_id' => $user->id,
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($user);

        Livewire::test(AttachmentUpload::class, ['task' => $task])
            ->set('files', [$file])
            ->assertHasNoErrors();

        $attachment = Attachment::first();

        // Download route check
        $response = $this->get(route('attachments.download', $attachment));
        $response->assertOk();

        // Delete attachment
        Livewire::test(AttachmentUpload::class, ['task' => $task])
            ->call('deleteAttachment', $attachment->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('local')->assertMissing($attachment->disk_path);
    }
}
