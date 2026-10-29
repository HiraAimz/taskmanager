<div class="h-full flex flex-col p-4 sm:p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-7xl mx-auto w-full h-[calc(100vh-6.5rem)] flex flex-col space-y-4">
        
        <!-- Header & Workspace Context -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 flex-shrink-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <span>Direct Messages</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Private real-time workspace messaging with eligible team members.
                </p>
            </div>

            <!-- Workspace Switcher Pill -->
            @if($userWorkspaces->count() > 1)
                <div class="flex items-center gap-2 bg-white dark:bg-[#1a1b1e] border border-slate-200/80 dark:border-[#2a2b2f] p-1 rounded-xl shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-400 pl-2">Workspace:</span>
                    <select wire:change="switchWorkspace($event.target.value)" 
                            class="text-xs font-semibold bg-transparent border-none text-slate-900 dark:text-white focus:ring-0 cursor-pointer py-1 pr-6">
                        @foreach($userWorkspaces as $ws)
                            <option value="{{ $ws->id }}" {{ $activeWorkspaceId === $ws->id ? 'selected' : '' }}>
                                {{ $ws->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        @if(session()->has('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-700 dark:text-rose-300 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Main Messenger Canvas -->
        <div class="flex-1 bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] shadow-xs overflow-hidden flex flex-col md:flex-row min-h-0">
            
            <!-- Left Sidebar: Conversations List -->
            <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200/80 dark:border-[#28292e] flex flex-col flex-shrink-0 bg-slate-50/50 dark:bg-[#16171a]">
                <!-- Conversations Header & New Chat button -->
                <div class="p-4 border-b border-slate-200/80 dark:border-[#28292e] flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Conversations</span>
                    <button type="button" 
                            wire:click="$toggle('showNewChatModal')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-2xs hover:opacity-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Chat
                    </button>
                </div>

                <!-- Conversation Items -->
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-[#24252a]">
                    @forelse($conversations as $conv)
                        @php
                            $other = $conv->getOtherUser(auth()->id());
                            $isActive = $activeConversationId === $conv->id;
                            $unreadCount = $conv->messages()->where('sender_id', '!=', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        <button type="button" 
                                wire:click="selectConversation({{ $conv->id }})"
                                class="w-full text-left p-3.5 flex items-start gap-3 transition-colors {{ $isActive ? 'bg-purple-50/80 dark:bg-purple-950/30 border-l-4 border-purple-600' : 'hover:bg-slate-100/60 dark:hover:bg-[#1f2024]' }}">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-xs font-bold shadow-2xs">
                                    {{ strtoupper(substr($other->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-[#16171a]"></span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $other->name ?? 'User' }}
                                    </h4>
                                    @if($conv->latestMessage)
                                        <span class="text-[10px] text-slate-400 flex-shrink-0">
                                            {{ $conv->latestMessage->created_at->diffForHumans(null, true) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate flex items-center gap-1">
                                        @if($conv->latestMessage && !$conv->latestMessage->body && $conv->latestMessage->attachments->count() > 0)
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            Attachment
                                        @else
                                            {{ $conv->latestMessage?->body ?? 'No messages yet' }}
                                        @endif
                                    </p>
                                    @if($unreadCount > 0)
                                        <span class="px-1.5 py-0.5 rounded-full bg-purple-600 text-white text-[10px] font-bold">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400 space-y-3">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <p>No conversations yet in this workspace.</p>
                            <button type="button" 
                                    wire:click="$set('showNewChatModal', true)" 
                                    class="text-purple-600 dark:text-purple-400 font-semibold hover:underline">
                                Start a conversation →
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Area: Message Stream & Chat View -->
            <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-[#1a1b1e]">
                @if($activeConversation)
                    @php
                        $recipient = $activeConversation->getOtherUser(auth()->id());
                    @endphp
                    <!-- Chat Header -->
                    <div class="px-6 py-4 border-b border-slate-200/80 dark:border-[#28292e] flex items-center justify-between flex-shrink-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-xs font-bold shadow-2xs">
                                {{ strtoupper(substr($recipient->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                    {{ $recipient->name ?? 'Workspace Member' }}
                                </h3>
                                <p class="text-[11px] text-slate-400 truncate">
                                    {{ $recipient->email }}
                                </p>
                            </div>
                        </div>

                        <span class="text-2xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-[#25262a] text-slate-600 dark:text-slate-300">
                            {{ $activeConversation->workspace->name }}
                        </span>
                    </div>

                    <!-- Messages Scroll Container with Livewire 3s Polling -->
                    <div wire:poll.3s="markCurrentConversationRead" 
                         class="flex-1 p-6 overflow-y-auto space-y-4"
                         x-data 
                         x-init="$el.scrollTop = $el.scrollHeight"
                         @message-sent.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)">
                        @forelse($messages as $msg)
                            @php
                                $isMe = $msg->sender_id === auth()->id();
                            @endphp
                            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                <div class="max-w-md lg:max-w-lg rounded-2xl px-4 py-2.5 text-xs shadow-2xs {{ $isMe ? 'bg-clickup-gradient text-white rounded-br-xs' : 'bg-slate-100 dark:bg-[#222327] text-slate-800 dark:text-slate-200 rounded-bl-xs' }}">
                                    @if($msg->body)
                                        <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->body }}</p>
                                    @endif

                                    <!-- Message Attachments -->
                                    @if($msg->attachments->count() > 0)
                                        @php
                                            $msgImages = $msg->attachments->filter(fn($a) => str_starts_with($a->file_type ?? '', 'image/'));
                                            $msgFiles = $msg->attachments->reject(fn($a) => str_starts_with($a->file_type ?? '', 'image/'));
                                        @endphp

                                        <!-- Image Previews -->
                                        @if($msgImages->isNotEmpty())
                                            <div class="mt-2 space-y-1.5 {{ $msg->body ? 'border-t border-white/20 pt-2' : '' }}">
                                                @foreach($msgImages as $img)
                                                    <div wire:key="msg-img-{{ $img->id }}" 
                                                         x-data="{ failed: false }"
                                                         class="relative group rounded-xl overflow-hidden border {{ $isMe ? 'border-white/20' : 'border-slate-200 dark:border-[#35363b]' }} shadow-xs">
                                                        <template x-if="!failed">
                                                            <a href="{{ route('message-attachments.preview', $img) }}" target="_blank" class="block overflow-hidden cursor-zoom-in">
                                                                <img src="{{ route('message-attachments.preview', $img) }}" 
                                                                     alt="{{ $img->original_name }}"
                                                                     @error="failed = true"
                                                                     loading="lazy"
                                                                     class="max-h-52 w-full object-cover rounded-xl group-hover:scale-102 transition-transform duration-200">
                                                            </a>
                                                        </template>
                                                        <template x-if="failed">
                                                            <div class="p-2.5 flex items-center gap-2 text-xs {{ $isMe ? 'text-white/80' : 'text-slate-600 dark:text-slate-300' }}">
                                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                <span class="truncate max-w-[140px]">{{ $img->original_name }}</span>
                                                            </div>
                                                        </template>
                                                        <div class="absolute inset-x-0 bottom-0 p-1.5 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-between text-white text-[10px]">
                                                             <span class="truncate px-1 font-medium">{{ $img->original_name }}</span>
                                                             <a href="{{ route('message-attachments.download', $img) }}" title="Download" class="p-1 hover:text-purple-300 transition-colors">
                                                                 <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                             </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Document / Non-Image Files -->
                                        @if($msgFiles->isNotEmpty())
                                            <div class="mt-2 space-y-1 {{ ($msg->body || $msgImages->isNotEmpty()) ? 'border-t border-white/20 pt-2' : '' }}">
                                                @foreach($msgFiles as $att)
                                                    <a href="{{ route('message-attachments.download', $att) }}"
                                                       class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ $isMe ? 'bg-white/15 hover:bg-white/25' : 'bg-white dark:bg-[#2a2b2f] hover:bg-slate-50 dark:hover:bg-[#303136]' }} transition-colors group">
                                                        <svg class="w-3.5 h-3.5 {{ $isMe ? 'text-white/80' : 'text-slate-400' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                        </svg>
                                                        <span class="text-[10px] font-medium {{ $isMe ? 'text-white/90' : 'text-slate-700 dark:text-slate-300' }} truncate max-w-[140px]">
                                                            {{ $att->original_name }}
                                                        </span>
                                                        <span class="text-[9px] {{ $isMe ? 'text-white/60' : 'text-slate-400' }} ml-auto flex-shrink-0">
                                                            {{ $att->file_size_kb }} KB
                                                        </span>
                                                        <svg class="w-3 h-3 {{ $isMe ? 'text-white/70' : 'text-purple-500' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-400 px-1">
                                    <span>{{ $msg->created_at->format('g:i A') }}</span>
                                    @if($isMe)
                                        @if($msg->isRead())
                                            <!-- Double check = read -->
                                            <span class="text-purple-500 font-bold flex items-center" title="Read by recipient">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 12l4 4L19 5M9 12l4 4"/>
                                                </svg>
                                            </span>
                                        @else
                                            <!-- Single check = delivered -->
                                            <span class="flex items-center text-slate-400" title="Delivered">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-center text-xs text-slate-400 space-y-2">
                                <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-slate-600 dark:text-slate-300">Say hello to {{ $recipient->name }}</p>
                                <p class="text-slate-400">This is the beginning of your direct messaging history.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Message Input Form with File Attachment -->
                    <div class="border-t border-slate-200/80 dark:border-[#28292e] bg-slate-50/50 dark:bg-[#16171a]"
                         x-data="{ showAttach: false, files: [] }">

                        <!-- File preview bar (shown when files are selected) -->
                        <div x-show="files.length > 0" x-cloak class="px-4 pt-3 flex flex-wrap gap-1.5">
                            <template x-for="(f, i) in files" :key="i">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-[10px] font-semibold text-purple-700 dark:text-purple-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <span x-text="f.name" class="max-w-[100px] truncate"></span>
                                    <button type="button" @click="files.splice(i,1); $wire.set('messageFiles', Array.from($refs.msgFileInput.files).filter((_,idx) => idx !== i))" class="text-purple-400 hover:text-rose-500 ml-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            </template>
                        </div>

                        <form wire:submit="sendMessage" class="p-4 flex items-center gap-2">
                            <!-- Hidden file input -->
                            <input type="file"
                                   x-ref="msgFileInput"
                                   wire:model="messageFiles"
                                   multiple
                                   class="hidden"
                                   @change="files = Array.from($event.target.files)">

                            <!-- Attach button -->
                            <button type="button"
                                    @click="$refs.msgFileInput.click()"
                                    class="flex-shrink-0 p-2 text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-slate-100 dark:hover:bg-[#25262a] rounded-xl transition-colors"
                                    title="Attach files">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </button>

                            <input type="text" 
                                   wire:model="newMessage"
                                   placeholder="Write a message to {{ $recipient->name }}..."
                                   class="flex-1 text-xs bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-4 py-2.5 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 font-medium transition-all"
                                   autofocus>
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                                <span>Send</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- No Active Chat Selected -->
                    <div class="h-full flex flex-col items-center justify-center text-center p-8 text-xs text-slate-400 space-y-3">
                        <div class="w-16 h-16 rounded-2xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Select a conversation</h3>
                        <p class="max-w-xs text-slate-400">Choose a team member from the left panel or click New Chat to start a private conversation.</p>
                        <button type="button" 
                                wire:click="$set('showNewChatModal', true)" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Start New Conversation
                        </button>
                    </div>
                @endif
            </div>

        </div>

        <!-- New Chat Modal -->
        @if($showNewChatModal)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                 wire:click="$set('showNewChatModal', false)">
                <div class="bg-white dark:bg-[#1e1f23] text-slate-900 dark:text-slate-100 rounded-2xl shadow-2xl border border-slate-200 dark:border-[#2f3036] w-full max-w-md overflow-hidden"
                     wire:click.stop>
                    
                    <div class="p-5 border-b border-slate-100 dark:border-[#28292e] flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Start New Conversation</h3>
                            <p class="text-2xs text-slate-400">Select an eligible workspace collaborator</p>
                        </div>
                        <button type="button" wire:click="$set('showNewChatModal', false)" 
                                class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-[#25262a] rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="p-4 border-b border-slate-100 dark:border-[#28292e]">
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   wire:model.live.debounce.250ms="searchUser"
                                   placeholder="Filter team members by name or email..."
                                   class="w-full text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl pl-9 pr-3 py-2 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Members List -->
                    <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-[#24252a] p-2">
                        @forelse($eligibleUsers as $userItem)
                            <button type="button" 
                                    wire:click="startChatWith({{ $userItem->id }})"
                                    class="w-full p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-[#25262a] flex items-center gap-3 transition-colors text-left">
                                <div class="w-8 h-8 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-xs font-bold shadow-2xs flex-shrink-0">
                                    {{ strtoupper(substr($userItem->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $userItem->name }}</h4>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $userItem->email }}</p>
                                </div>
                                <span class="text-xs text-purple-600 dark:text-purple-400 font-semibold flex items-center gap-1">
                                    Message
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </button>
                        @empty
                            <p class="p-6 text-center text-xs text-slate-400">No matching workspace members found.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        @endif

    </div>
</div>
