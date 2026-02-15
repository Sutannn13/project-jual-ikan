@extends('layouts.master')

@section('title', 'Chat dengan Admin')

@section('content')
<section class="py-4 sm:py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Chat Header --}}
        <div class="store-glass-card rounded-t-2xl rounded-b-none p-4 sm:p-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white/60 hover:bg-white/20 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div class="w-11 h-11 rounded-full flex items-center justify-center"
                     style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 12px rgba(6,182,212,0.3);">
                    <i class="fas fa-headset text-white"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white">{{ $admin->name }}</h1>
                    <p class="text-xs text-emerald-400 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span> Admin FishMarket
                    </p>
                </div>
            </div>
        </div>

        {{-- Chat Messages --}}
        <div id="chat-messages" class="p-4 sm:p-6 space-y-4 overflow-y-auto"
             style="height: calc(100vh - 340px); min-height: 400px; background: rgba(255,255,255,0.03); border-left: 1px solid rgba(255,255,255,0.1); border-right: 1px solid rgba(255,255,255,0.1);">
            
            @if($messages->isEmpty())
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center"
                     style="background: rgba(6,182,212,0.12); border: 1px solid rgba(6,182,212,0.2);">
                    <i class="fas fa-comments text-3xl text-cyan-400"></i>
                </div>
                <p class="text-white/60 font-medium">Mulai percakapan dengan admin</p>
                <p class="text-white/40 text-sm mt-1">Tanyakan tentang produk, pesanan, atau apa saja!</p>
            </div>
            @endif

            @foreach($messages as $msg)
            <div class="flex {{ $msg->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] sm:max-w-[70%] {{ $msg->sender_id === Auth::id() 
                    ? 'bg-gradient-to-br from-cyan-500 to-teal-500 text-white rounded-2xl rounded-br-md' 
                    : 'rounded-2xl rounded-bl-md text-white/90 px-4 py-3' }} px-4 py-3" style="{{ $msg->sender_id !== Auth::id() ? 'background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);' : '' }}">
                    <p class="text-sm leading-relaxed break-words">{{ $msg->message }}</p>
                    <p class="text-[10px] mt-1.5 {{ $msg->sender_id === Auth::id() ? 'text-white/60' : 'text-white/40' }} text-right">
                        {{ $msg->created_at->format('H:i') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Chat Input --}}
        <div class="store-glass-card rounded-b-2xl rounded-t-none p-4 sm:p-5 border-t border-white/10" x-data="chatInput()">
            <form @submit.prevent="sendMessage" class="flex items-end gap-3">
                <div class="flex-1 relative">
                    <textarea x-ref="messageInput" 
                              x-model="message"
                              @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                              placeholder="Ketik pesan Anda..." 
                              rows="1"
                              class="w-full px-4 py-3 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none placeholder-white/40 resize-none max-h-32"
                              style="min-height: 44px;"></textarea>
                </div>
                <button type="submit" :disabled="!message.trim() || sending" 
                        class="btn-primary px-5 py-3 rounded-xl flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas" :class="sending ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                </button>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
function chatInput() {
    return {
        message: '',
        sending: false,
        lastId: {{ $messages->last()?->id ?? 0 }},
        pollInterval: null,
        echoConnected: false,

        init() {
            this.scrollToBottom();
            this.listenWebSocket();
            this.startPolling();
        },

        listenWebSocket() {
            if (typeof window.Echo === 'undefined') {
                console.log('[Chat] Echo not available, using polling only');
                return;
            }

            try {
                const userId = {{ Auth::id() }};
                window.Echo.private(`chat.${userId}`)
                    .listen('.message.sent', (e) => {
                        console.log('[Chat] WebSocket message received:', e);
                        if (e.messageData && e.messageData.id > this.lastId) {
                            this.appendMessage({
                                id: e.messageData.id,
                                message: e.messageData.message,
                                is_mine: false,
                                created_at: e.messageData.created_at
                            });
                            this.lastId = e.messageData.id;
                        }
                    })
                    .subscribed(() => {
                        console.log('[Chat] WebSocket connected');
                        this.echoConnected = true;
                    })
                    .error((error) => {
                        console.warn('[Chat] WebSocket error, falling back to polling:', error);
                        this.echoConnected = false;
                    });
            } catch (e) {
                console.warn('[Chat] WebSocket setup failed:', e);
            }
        },

        async sendMessage() {
            if (!this.message.trim() || this.sending) return;

            this.sending = true;
            const msg = this.message;
            this.message = '';

            try {
                const response = await fetch('{{ route("chat.send") }}', {
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
            // Use longer interval when WebSocket is connected (fallback safety net)
            this.pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(`{{ route("chat.poll") }}?last_id=${this.lastId}`, {
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
            }, this.echoConnected ? 15000 : 3000); // 15s with WS, 3s without
        },

        appendMessage(msg) {
            const container = document.getElementById('chat-messages');
            const emptyState = container.querySelector('.text-center.py-12');
            if (emptyState) emptyState.remove();

            // Prevent duplicate messages
            if (container.querySelector(`[data-msg-id="${msg.id}"]`)) return;

            const isMine = msg.is_mine;
            const div = document.createElement('div');
            div.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;
            div.setAttribute('data-msg-id', msg.id);
            div.innerHTML = `
                <div class="max-w-[80%] sm:max-w-[70%] ${isMine 
                    ? 'bg-gradient-to-br from-cyan-500 to-teal-500 text-white rounded-2xl rounded-br-md' 
                    : 'rounded-2xl rounded-bl-md text-white/90'} px-4 py-3" style="${!isMine ? 'background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);' : ''}">
                    <p class="text-sm leading-relaxed break-words">${this.escapeHtml(msg.message)}</p>
                    <p class="text-[10px] mt-1.5 ${isMine ? 'text-white/60' : 'text-white/40'} text-right">${msg.created_at}</p>
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
            if (typeof window.Echo !== 'undefined') {
                window.Echo.leave(`chat.{{ Auth::id() }}`);
            }
        }
    };
}
</script>
@endpush
@endsection
