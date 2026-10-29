<?php

declare(strict_types=1);

namespace App\Livewire\Messages;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Workspace;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DirectMessages extends Component
{
    use WithFileUploads;
    public ?int $activeWorkspaceId = null;

    public ?int $activeConversationId = null;

    public string $newMessage = '';

    public string $searchUser = '';

    public bool $showNewChatModal = false;

    /** @var array<\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $messageFiles = [];

    public function mount(?int $conversationId = null): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('message.access'), 
            403
        );

        // Pick user's first workspace as default
        $user = auth()->user();
        $firstWorkspace = $user->hasRole('Admin') 
            ? Workspace::first() 
            : $user->workspaces()->first();

        if ($firstWorkspace) {
            $this->activeWorkspaceId = $firstWorkspace->id;
        }

        if ($conversationId) {
            $conversation = Conversation::find($conversationId);
            if ($conversation && $conversation->hasParticipant(auth()->id())) {
                $this->activeWorkspaceId = $conversation->workspace_id;
                $this->activeConversationId = $conversation->id;
                $this->markCurrentConversationRead();
            }
        }
    }

    public function switchWorkspace(int $workspaceId): void
    {
        $user = auth()->user();
        if (!$user->hasRole('Admin') && !$user->workspaces()->where('workspaces.id', $workspaceId)->exists()) {
            abort(403);
        }

        $this->activeWorkspaceId = $workspaceId;
        $this->activeConversationId = null;
        $this->showNewChatModal = false;
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Verify participant
        if (!$conversation->hasParticipant(auth()->id()) && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $this->activeConversationId = $conversationId;
        $this->showNewChatModal = false;
        $this->markCurrentConversationRead();
    }

    public function startChatWith(int $userId): void
    {
        if (!$this->activeWorkspaceId) {
            return;
        }

        $workspace = Workspace::findOrFail($this->activeWorkspaceId);

        // Security check: Verify recipient is in the same workspace
        $isRecipientInWorkspace = $workspace->users()->where('users.id', $userId)->exists();
        if (!$isRecipientInWorkspace) {
            session()->flash('error', 'User does not belong to this workspace.');
            return;
        }

        // Security check: Verify sender is in the same workspace (or Admin)
        if (!auth()->user()->hasRole('Admin') && !$workspace->users()->where('users.id', auth()->id())->exists()) {
            abort(403);
        }

        $conversation = Conversation::findOrCreateBetween($this->activeWorkspaceId, auth()->id(), $userId);
        $this->activeConversationId = $conversation->id;
        $this->showNewChatModal = false;
        $this->searchUser = '';
        $this->markCurrentConversationRead();
    }

    public function sendMessage(): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('message.send'), 
            403
        );

        $this->validate([
            'newMessage'   => ['required_without:messageFiles', 'nullable', 'string', 'max:2000'],
            'messageFiles' => ['nullable', 'array', 'max:5'],
            'messageFiles.*' => ['file', 'max:10240'],
        ]);

        if (!$this->activeConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->activeConversationId);

        // Security check: Check sender still has membership in workspace
        $workspace = Workspace::findOrFail($conversation->workspace_id);
        if (!auth()->user()->hasRole('Admin') && !$workspace->users()->where('users.id', auth()->id())->exists()) {
            session()->flash('error', 'You no longer belong to this workspace to send messages.');
            return;
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => auth()->id(),
            'body'            => trim($this->newMessage) ?: null,
        ]);

        // Upload attachments
        foreach ($this->messageFiles as $file) {
            $path = $file->store('message-attachments', 'local');

            MessageAttachment::create([
                'message_id'    => $message->id,
                'user_id'       => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'disk_path'     => $path,
                'file_type'     => $file->getMimeType(),
                'file_size'     => $file->getSize(),
            ]);
        }

        $conversation->update(['last_message_at' => now()]);
        $this->newMessage   = '';
        $this->messageFiles = [];

        $this->dispatch('message-sent');
    }

    public function markCurrentConversationRead(): void
    {
        if ($this->activeConversationId) {
            Message::where('conversation_id', $this->activeConversationId)
                ->where('sender_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function render()
    {
        $user = auth()->user();
        $userWorkspaces = $user->hasRole('Admin')
            ? Workspace::all()
            : $user->workspaces()->get();

        $conversations = collect();
        $eligibleUsers = collect();
        $activeConversation = null;
        $messages = collect();

        if ($this->activeWorkspaceId) {
            $workspace = Workspace::find($this->activeWorkspaceId);

            if ($workspace) {
                // Get one-to-one conversations where auth user is a participant in this workspace
                $conversations = Conversation::where('workspace_id', $this->activeWorkspaceId)
                    ->where(function ($q) {
                        $q->where('user_one_id', auth()->id())
                            ->orWhere('user_two_id', auth()->id());
                    })
                    ->with(['userOne', 'userTwo', 'latestMessage'])
                    ->orderByDesc('last_message_at')
                    ->orderByDesc('updated_at')
                    ->get();

                // Eligible recipients: other users in the same workspace
                $eligibleQuery = $workspace->users()->where('users.id', '!=', auth()->id());
                if (trim($this->searchUser)) {
                    $eligibleQuery->where(function ($q) {
                        $q->where('name', 'like', '%' . trim($this->searchUser) . '%')
                            ->orWhere('email', 'like', '%' . trim($this->searchUser) . '%');
                    });
                }
                $eligibleUsers = $eligibleQuery->get();
            }

            if ($this->activeConversationId) {
                $activeConversation = Conversation::with(['userOne', 'userTwo'])
                    ->find($this->activeConversationId);

                if ($activeConversation) {
                    $this->markCurrentConversationRead();
                    $messages = $activeConversation->messages()
                        ->with('sender', 'attachments')
                        ->get();
                }
            }
        }

        return view('livewire.messages.direct-messages', [
            'userWorkspaces' => $userWorkspaces,
            'conversations' => $conversations,
            'eligibleUsers' => $eligibleUsers,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
        ])->layout('components.layouts.app');
    }
}
