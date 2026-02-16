@extends('layouts.admin')
@section('title', 'Tiket #' . $ticket->ticket_number)
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.tickets.index') }}" class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-white/60 hover:bg-white/20 hover:text-white transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-white truncate">{{ $ticket->subject }}</h1>
            <p class="text-sm text-white/50">#{{ $ticket->ticket_number }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Messages Column --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Chat Thread --}}
            <div class="dark-glass-card rounded-2xl p-5 sm:p-6 space-y-4 max-h-[500px] overflow-y-auto" id="chatThread">
                @foreach($ticket->messages as $msg)
                    <div class="flex {{ $msg->is_admin ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[85%] {{ $msg->is_admin ? 'bg-orange-500/10 border border-orange-500/20' : 'bg-white/10 border border-white/10' }} rounded-2xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                @if($msg->is_admin)
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center text-[10px] text-white font-bold">
                                        <i class="fas fa-shield-alt text-[8px]"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-orange-300">{{ $msg->user->name ?? 'Admin' }}</span>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center text-[10px] text-white font-bold">
                                        {{ substr($msg->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-semibold text-cyan-300">{{ $msg->user->name ?? 'Customer' }}</span>
                                @endif
                                <span class="text-[10px] text-white/30 ml-auto">{{ $msg->created_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="text-sm text-white/80 whitespace-pre-wrap">{{ $msg->message }}</p>
                            @if($msg->attachment)
                                <a href="{{ Storage::url($msg->attachment) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 rounded-lg bg-white/10 text-xs text-white/50 hover:text-white hover:bg-white/15 transition">
                                    <i class="fas fa-paperclip"></i> Lampiran
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Admin Reply Form --}}
            <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data"
                  class="dark-glass-card rounded-2xl p-5 sm:p-6">
                @csrf
                <h3 class="text-sm font-semibold text-white mb-3">
                    <i class="fas fa-reply mr-1 text-orange-400"></i> Balas sebagai Admin
                </h3>
                <textarea name="message" rows="3" required
                          class="w-full px-4 py-3 bg-white/10 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition resize-none text-sm"
                          placeholder="Tulis balasan..."></textarea>
                @error('message')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mt-3">
                    <input type="file" name="attachment" accept="image/*,.pdf"
                           class="text-xs text-white/40 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white/50 hover:file:bg-white/15">
                    <div class="flex items-center gap-2 sm:ml-auto">
                        <select name="status" class="px-3 py-2 bg-white/10 border border-white/10 rounded-xl text-xs text-white focus:ring-2 focus:ring-orange-500 transition">
                            <option value="" class="bg-gray-800">Set status...</option>
                            <option value="in_progress" class="bg-gray-800" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_customer" class="bg-gray-800" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Tunggu Customer</option>
                            <option value="resolved" class="bg-gray-800">Resolved</option>
                            <option value="closed" class="bg-gray-800">Closed</option>
                        </select>
                        <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold text-white transition-all"
                                style="background: linear-gradient(135deg, #ea580c, #f97316); box-shadow: 0 4px 12px rgba(234,88,12,0.3);">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-4">
            {{-- Ticket Details --}}
            <div class="dark-glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-white mb-4">Detail Tiket</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-white/40">Status</span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $ticket->status_color }}">
                            {{ $ticket->status_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-white/40">Prioritas</span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $ticket->priority_color }}">
                            {{ $ticket->priority_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-white/40">Kategori</span>
                        <span class="text-xs text-white/70">{{ $ticket->category_label }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-white/40">Dibuat</span>
                        <span class="text-xs text-white/70">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($ticket->closed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-white/40">Ditutup</span>
                            <span class="text-xs text-white/70">{{ $ticket->closed_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Quick Status Update --}}
                <div class="mt-4 pt-4 border-t border-white/10">
                    <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="flex-1 px-3 py-2 bg-white/10 border border-white/10 rounded-xl text-xs text-white focus:ring-2 focus:ring-orange-500 transition">
                            <option value="open" class="bg-gray-800" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" class="bg-gray-800" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_customer" class="bg-gray-800" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Tunggu Customer</option>
                            <option value="resolved" class="bg-gray-800" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" class="bg-gray-800" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-xl bg-white/10 text-xs text-white hover:bg-white/20 transition">
                            Update
                        </button>
                    </form>
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="dark-glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-white mb-3">Customer</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $ticket->user->name ?? '-' }}</p>
                        <p class="text-xs text-white/40">{{ $ticket->user->email ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- Related Order --}}
            @if($ticket->order)
                <div class="dark-glass-card rounded-2xl p-5">
                    <h3 class="text-sm font-bold text-white mb-3">Pesanan Terkait</h3>
                    <a href="{{ route('admin.orders.show', $ticket->order) }}" class="block hover:bg-white/5 -mx-2 px-2 py-2 rounded-lg transition">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold text-white">Order #{{ $ticket->order->id }}</span>
                            @php
                                $orderStatusColor = match($ticket->order->status) {
                                    'pending' => 'bg-amber-500/15 text-amber-400',
                                    'waiting_payment' => 'bg-orange-500/15 text-orange-400',
                                    'paid' => 'bg-cyan-500/15 text-cyan-400',
                                    'confirmed' => 'bg-blue-500/15 text-blue-400',
                                    'out_for_delivery' => 'bg-indigo-500/15 text-indigo-400',
                                    'completed' => 'bg-emerald-500/15 text-emerald-400',
                                    'cancelled' => 'bg-red-500/15 text-red-400',
                                    default => 'bg-white/15 text-white/60',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $orderStatusColor }}">
                                {{ ucfirst($ticket->order->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-white/40">Rp {{ number_format($ticket->order->total, 0, ',', '.') }}</p>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll chat to bottom
    document.addEventListener('DOMContentLoaded', function() {
        const thread = document.getElementById('chatThread');
        if (thread) thread.scrollTop = thread.scrollHeight;
    });
</script>
@endpush
@endsection
