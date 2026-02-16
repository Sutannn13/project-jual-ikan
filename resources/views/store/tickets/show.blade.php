@extends('layouts.master')
@section('title', 'Tiket #' . $ticket->ticket_number)
@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white text-sm mb-4 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Ticket Info Card --}}
        <div class="store-glass-card rounded-2xl p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-xs font-mono text-white/50">#{{ $ticket->ticket_number }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ticket->priority_color }}">
                            {{ $ticket->priority_label }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ticket->status_color }}">
                            {{ $ticket->status_label }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white">{{ $ticket->subject }}</h1>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 text-xs text-white/50">
                <span><i class="fas fa-tag mr-1"></i>{{ $ticket->category_label }}</span>
                @if($ticket->order)
                    <a href="{{ route('order.track', $ticket->order) }}" class="hover:text-cyan-300 transition-colors">
                        <i class="fas fa-shopping-bag mr-1"></i>Order #{{ $ticket->order->id }}
                    </a>
                @endif
                <span><i class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                @if($ticket->assignedAdmin)
                    <span><i class="fas fa-user-shield mr-1"></i>{{ $ticket->assignedAdmin->name }}</span>
                @endif
            </div>
        </div>

        {{-- Messages Thread --}}
        <div class="space-y-4 mb-6">
            @foreach($ticket->messages as $msg)
                <div class="flex {{ $msg->is_admin ? 'justify-start' : 'justify-end' }}">
                    <div class="max-w-[85%] {{ $msg->is_admin ? 'store-glass-card' : 'bg-cyan-600/20 border border-cyan-500/30' }} rounded-2xl p-4 sm:p-5">
                        <div class="flex items-center gap-2 mb-2">
                            @if($msg->is_admin)
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center text-white text-xs font-bold">
                                    <i class="fas fa-shield-alt text-[10px]"></i>
                                </div>
                                <span class="text-sm font-semibold text-orange-300">Admin</span>
                            @else
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr($msg->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-sm font-semibold text-cyan-300">{{ $msg->user->name ?? 'Anda' }}</span>
                            @endif
                            <span class="text-[10px] text-white/40 ml-auto">{{ $msg->created_at->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-sm text-white/80 whitespace-pre-wrap">{{ $msg->message }}</p>
                        @if($msg->attachment)
                            <a href="{{ Storage::url($msg->attachment) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg bg-white/10 text-xs text-white/60 hover:text-white hover:bg-white/20 transition-colors">
                                <i class="fas fa-paperclip"></i> Lihat Lampiran
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Reply Form --}}
        @if(!in_array($ticket->status, ['closed', 'resolved']))
            <form action="{{ route('tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data"
                  class="store-glass-card rounded-2xl p-5 sm:p-6">
                @csrf
                <h3 class="text-sm font-semibold text-white mb-3">
                    <i class="fas fa-reply mr-1 text-cyan-300"></i> Balas Tiket
                </h3>
                <textarea name="message" rows="3" required
                          class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition resize-none text-sm"
                          placeholder="Tulis balasan Anda..."></textarea>
                @error('message')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mt-3">
                    <input type="file" name="attachment" accept="image/*,.pdf"
                           class="text-xs text-white/50 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-white/10 file:text-white/60 hover:file:bg-white/20">
                    <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                </div>
            </form>
        @else
            <div class="store-glass-card rounded-2xl p-5 text-center">
                <p class="text-white/50 text-sm">
                    <i class="fas fa-lock mr-1"></i> Tiket ini sudah {{ $ticket->status === 'closed' ? 'ditutup' : 'diselesaikan' }}.
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
