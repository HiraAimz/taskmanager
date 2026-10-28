<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Messages\DirectMessages;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DirectMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_workspace_members_can_access_direct_messages_and_chat(): void
    {
        $workspace = Workspace::factory()->create();

        $user1 = User::factory()->create();
        $user1->assignRole('Member');
        $workspace->users()->attach($user1->id);

        $user2 = User::factory()->create();
        $user2->assignRole('Member');
        $workspace->users()->attach($user2->id);

        $this->actingAs($user1);

        // Access route
        $this->get(route('messages.index'))
            ->assertOk();

        // Start conversation and send message
        Livewire::test(DirectMessages::class)
            ->set('activeWorkspaceId', $workspace->id)
            ->call('startChatWith', $user2->id)
            ->set('newMessage', 'Hello from User 1!')
            ->call('sendMessage')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('conversations', [
            'workspace_id' => $workspace->id,
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $user1->id,
            'body' => 'Hello from User 1!',
        ]);

        // User 2 logs in and marks read
        $this->actingAs($user2);

        $conversation = Conversation::first();

        Livewire::test(DirectMessages::class, ['conversationId' => $conversation->id])
            ->assertSee('Hello from User 1!');

        $message = $conversation->messages()->first();
        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_cannot_message_user_outside_of_shared_workspace(): void
    {
        $workspace = Workspace::factory()->create();

        $user1 = User::factory()->create();
        $user1->assignRole('Member');
        $workspace->users()->attach($user1->id);

        $outsider = User::factory()->create();
        $outsider->assignRole('Member');
        // $outsider is not in $workspace

        $this->actingAs($user1);

        Livewire::test(DirectMessages::class)
            ->set('activeWorkspaceId', $workspace->id)
            ->call('startChatWith', $outsider->id)
            ->assertSee('User does not belong to this workspace.');

        $this->assertDatabaseCount('conversations', 0);
    }
}
