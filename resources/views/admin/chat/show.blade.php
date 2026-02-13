@extends('layouts.admin')

@section('title', 'Chat - ' . $user->name)

@section('content')
<div class="flex flex-col h-full sm:h-auto sm:max-w-3xl sm:mx-auto -mx-1 -my-2 sm:m-0 sm:mt-6 relative">
    {{-- Chat Header --}}
    <div class="dark-glass-card rounded-none sm:rounded-t-2xl p-3 sm:p-5 border-b border-white/5 flex-shrink-0 z-10">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.chat.index') }}" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white/50 hover:bg-cyan-500/15 hover:text-cyan-400 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);">
                <i class="fas fa-user text-ocean-600"></i>
            </div>
            <div class="min-w-0">
                <h1 class="font-bold text-white truncate">{{ $user->name }}</h1>
                <p class="text-xs text-white/40 truncate">
                    {{ $user->email }}
                </p>
            </div>
        </div>
    </div>

    {{-- Chat Messages --}}
    <div id="chat-messages" class="flex-1 p-4 space-y-4 overflow-y-auto sm:h-[calc(100vh-300px)] sm:min-h-[500px] border-x border-white/5 scroll-smooth custom-scrollbar"
         style="background: rgba(0,0,0,0.15);">
        
        @if($messages->isEmpty())
        <div class="text-center py-12">
            <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center"
                 style="background: linear-gradient(135deg, rgba(6,182,212,0.1) 0%, rgba(20,184,166,0.05) 100%);">
                <i class="fas fa-comments text-3xl text-ocean-400"></i>
            </div>
            <p class="text-white/40 font-medium">Belum ada pesan</p>
        </div>
        @endif

        @foreach($messages as $msg)
        <div class="flex {{ $msg->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[85%] sm:max-w-[70%] {{ $msg->sender_id === Auth::id() 
                ? 'bg-gradient-to-br from-ocean-500 to-teal-500 text-white rounded-2xl rounded-br-md shadow-lg shadow-cyan-500/10' 
                : 'bg-white/10 text-white rounded-2xl rounded-bl-md border border-white/5' }} px-4 py-3 relative group">
                <p class="text-sm leading-relaxed break-words">{{ $msg->message }}</p>
                <div class="flex items-center justify-end gap-1 mt-1.5 opacity-70">
                    <span class="text-[10px] {{ $msg->sender_id === Auth::id() ? 'text-white/90' : 'text-white/50' }}">
                        {{ $msg->created_at->format('H:i') }}
                    </span>
                    @if($msg->sender_id === Auth::id())
                        <i class="fas fa-check-double text-[10px] {{ $msg->is_read ?? false ? 'text-blue-200' : 'text-white/50' }}"></i>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Chat Input --}}
    <div class="dark-glass-card rounded-none sm:rounded-b-2xl p-3 sm:p-5 border-t border-white/5 flex-shrink-0 z-10" x-data="adminChat()">
        <form @submit.prevent="sendMessage" class="flex items-end gap-2 sm:gap-3">
            <div class="flex-1 bg-white/5 rounded-xl border border-white/10 focus-within:border-cyan-500/50 focus-within:ring-1 focus-within:ring-cyan-500/20 transition-all">
                <textarea x-ref="messageInput" 
                          x-model="message"
                          @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                          placeholder="Ketik balasan Anda..." 
                          rows="1"
                          class="w-full bg-transparent text-white text-sm px-4 py-3 focus:outline-none resize-none max-h-32 custom-scrollbar"
                          style="min-height: 48px;"></textarea>
            </div>
            <button type="submit" :disabled="!message.trim() || sending" 
                    class="btn-primary w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-cyan-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95">
                <i class="fas" :class="sending ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function adminChat() {
    return {
        message: '',
        sending: false,
        lastId: {{ $messages->last()?->id ?? 0 }},
        pollInterval: null,

        init() {
            this.scrollToBottom();
            this.startPolling();
        },

        async sendMessage() {
            if (!this.message.trim() || this.sending) return;

            this.sending = true;
            const msg = this.message;
            this.message = '';

            try {
                const response = await fetch('{{ route("admin.chat.send", $user) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: msg }),
                });

                const data = await response.json();
                if (data.success) {
                    this.appendMessage(data.message);
                    this.lastId = data.message.id;
                }
            } catch (error) {
                console.error('Send failed:', error);
                this.message = msg;
            } finally {
                this.sending = false;
                this.$refs.messageInput.focus();
            }
        },

        startPolling() {
            this.pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(`{{ route("admin.chat.poll", $user) }}?last_id=${this.lastId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });

                    const data = await response.json();
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            if (msg.id > this.lastId) {
                                this.appendMessage(msg);
                                this.lastId = msg.id;
                            }
                        });
                    }
                } catch (error) {
                    console.error('Poll failed:', error);
                }
            }, 3000);
        },

        appendMessage(msg) {
            const container = document.getElementById('chat-messages');
            const emptyState = container.querySelector('.text-center.py-12');
            if (emptyState) emptyState.remove();

            const isMine = msg.is_mine;
            const div = document.createElement('div');
            div.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;
            
            const bubbleClass = isMine 
                    ? 'bg-gradient-to-br from-ocean-500 to-teal-500 text-white rounded-2xl rounded-br-md shadow-lg shadow-cyan-500/10' 
                    : 'bg-white/10 text-white rounded-2xl rounded-bl-md border border-white/5';

            div.innerHTML = `
                <div class="max-w-[85%] sm:max-w-[70%] ${bubbleClass} px-4 py-3 relative group">
                    <p class="text-sm leading-relaxed break-words">${this.escapeHtml(msg.message)}</p>
                    <div class="flex items-center justify-end gap-1 mt-1.5 opacity-70">
                        <p class="text-[10px] ${isMine ? 'text-white/90' : 'text-white/50'} text-right">${msg.created_at}</p>
                        ${isMine ? '<i class="fas fa-check-double text-[10px] text-white/50"></i>' : ''}
                    </div>
                </div>
            `;
            container.appendChild(div);
            this.scrollToBottom();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('chat-messages');
                container.scrollTop = container.scrollHeight;
            });
        },

        escapeHtml(text) {
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return text.replace(/[&<>"']/g, m => map[m]);
        },

        destroy() {
            if (this.pollInterval) clearInterval(this.pollInterval);
        }
    };
}
</script>
@endpush
@endsection
