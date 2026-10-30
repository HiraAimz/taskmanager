<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\CommentAttachment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Livewire\Comments\CommentList;
use App\Livewire\Messages\DirectMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class AttachmentsInCommentsAndMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;
    protected Project $project;
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Storage::fake('local');

        $this->user = User::factory()->create();
        $this->user->assignRole('Admin');
        $this->workspace = Workspace::factory()->create(['owner_id' => $this->user->id]);
        $this->workspace->users()->attach($this->user->id, ['role' => 'owner']);

        $this->project = Project::factory()->create([
            'workspace_id' => $this->workspace->id,
            'owner_id' => $this->user->id,
        ]);

        $status = Status::factory()->create(['project_id' => $this->project->id]);

        $this->task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status_id' => $status->id,
            'assignee_id' => $this->user->id,
        ]);
    }

    public function test_user_can_add_comment_with_attachment(): void
    {
        $this->actingAs($this->user);

        $fakeFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        Livewire::test(CommentList::class, ['task' => $this->task])
            ->set('newComment', 'Here is the specification document')
            ->set('commentFiles', [$fakeFile])
            ->call('addComment')
            ->assertHasNoErrors();

        $comment = Comment::where('commentable_id', $this->task->id)->first();
        $this->assertNotNull($comment);
        $this->assertEquals('Here is the specification document', $comment->body);

        $attachment = CommentAttachment::where('comment_id', $comment->id)->first();
        $this->assertNotNull($attachment);
        $this->assertEquals('document.pdf', $attachment->original_name);
        Storage::disk('local')->assertExists($attachment->disk_path);
    }

    public function test_user_can_add_comment_with_only_attachment_and_no_text(): void
    {
        $this->actingAs($this->user);

        $fakeImage = UploadedFile::fake()->image('screenshot.png');

        Livewire::test(CommentList::class, ['task' => $this->task])
            ->set('commentFiles', [$fakeImage])
            ->call('addComment')
            ->assertHasNoErrors();

        $attachment = CommentAttachment::where('original_name', 'screenshot.png')->first();
        $this->assertNotNull($attachment);
        Storage::disk('local')->assertExists($attachment->disk_path);

        $this->get(route('comment-attachments.preview', $attachment))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=screenshot.png');
        $this->get(route('comment-attachments.preview', $attachment).'?thumbnail=1')
            ->assertOk();
    }

    public function test_user_can_download_comment_attachment(): void
    {
        $this->actingAs($this->user);

        $fakeFile = UploadedFile::fake()->create('contract.pdf', 300);
        $path = $fakeFile->store('comment-attachments', 'local');

        $comment = Comment::create([
            'commentable_type' => Task::class,
            'commentable_id' => $this->task->id,
            'user_id' => $this->user->id,
            'body' => 'See attached file',
        ]);

        $attachment = CommentAttachment::create([
            'comment_id' => $comment->id,
            'user_id' => $this->user->id,
            'original_name' => 'contract.pdf',
            'disk_path' => $path,
            'file_type' => 'application/pdf',
            'file_size' => 300 * 1024,
        ]);

        $response = $this->get(route('comment-attachments.download', $attachment));
        $response->assertOk();
    }

    public function test_user_can_send_direct_message_with_attachment(): void
    {
        $otherUser = User::factory()->create();
        $this->workspace->users()->attach($otherUser->id, ['role' => 'member']);

        $conversation = Conversation::findOrCreateBetween($this->workspace->id, $this->user->id, $otherUser->id);

        $this->actingAs($this->user);

        $fakeFile = UploadedFile::fake()->create('project_notes.docx', 120);

        Livewire::test(DirectMessages::class, ['conversationId' => $conversation->id])
            ->set('newMessage', 'Please review these notes')
            ->set('messageFiles', [$fakeFile])
            ->call('sendMessage')
            ->assertHasNoErrors();

        $message = Message::where('conversation_id', $conversation->id)->first();
        $this->assertNotNull($message);
        $this->assertEquals('Please review these notes', $message->body);

        $attachment = MessageAttachment::where('message_id', $message->id)->first();
        $this->assertNotNull($attachment);
        $this->assertEquals('project_notes.docx', $attachment->original_name);
        Storage::disk('local')->assertExists($attachment->disk_path);
    }

    public function test_user_can_send_direct_message_with_attachment_only(): void
    {
        $otherUser = User::factory()->create();
        $this->workspace->users()->attach($otherUser->id, ['role' => 'member']);

        $conversation = Conversation::findOrCreateBetween($this->workspace->id, $this->user->id, $otherUser->id);

        $this->actingAs($this->user);

        $fakeImage = UploadedFile::fake()->image('design.png');

        Livewire::test(DirectMessages::class, ['conversationId' => $conversation->id])
            ->set('messageFiles', [$fakeImage])
            ->call('sendMessage')
            ->assertHasNoErrors();

        $attachment = MessageAttachment::where('original_name', 'design.png')->first();
        $this->assertNotNull($attachment);
        Storage::disk('local')->assertExists($attachment->disk_path);

        $this->get(route('message-attachments.preview', $attachment))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=design.png');
        $this->get(route('message-attachments.preview', $attachment).'?thumbnail=1')
            ->assertOk();
    }

    public function test_conversation_participants_can_download_message_attachment(): void
    {
        $otherUser = User::factory()->create();
        $this->workspace->users()->attach($otherUser->id, ['role' => 'member']);

        $conversation = Conversation::findOrCreateBetween($this->workspace->id, $this->user->id, $otherUser->id);

        $fakeFile = UploadedFile::fake()->create('asset.zip', 1000);
        $path = $fakeFile->store('message-attachments', 'local');

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user->id,
            'body' => null,
        ]);

        $attachment = MessageAttachment::create([
            'message_id' => $message->id,
            'user_id' => $this->user->id,
            'original_name' => 'asset.zip',
            'disk_path' => $path,
            'file_type' => 'application/zip',
            'file_size' => 1000 * 1024,
        ]);

        // Sender can download
        $this->actingAs($this->user);
        $this->get(route('message-attachments.download', $attachment))->assertOk();

        // Recipient can download
        $this->actingAs($otherUser);
        $this->get(route('message-attachments.download', $attachment))->assertOk();

        // Unrelated user cannot download (403)
        $outsider = User::factory()->create();
        $this->actingAs($outsider);
        $this->get(route('message-attachments.download', $attachment))->assertStatus(403);
    }
}
