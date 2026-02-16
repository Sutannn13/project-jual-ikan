@extends('layouts.master')
@section('title', 'Tiket Support')
@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Tiket Support</h1>
                <p class="text-white/60 mt-1">Kelola pertanyaan & masalah Anda</p>
            </div>
            <a href="{{ route('tickets.create') }}"
               class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm">
                <i class="fas fa-plus"></i> Buat Tiket Baru
            </a>
        </div>

        @if($tickets->count())
            <div class="space-y-4">
                @foreach($tickets as $ticket)
                    <a href="{{ route('tickets.show', $ticket) }}" class="block store-glass-card rounded-2xl p-5 sm:p-6 hover:bg-white/10 transition-all duration-300 group">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="text-xs font-mono text-white/50">#{{ $ticket->ticket_number }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ticket->priority_color }}">
                                        {{ $ticket->priority_label }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ticket->status_color }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </div>
                                <h3 class="font-semibold text-white group-hover:text-cyan-300 transition-colors truncate">
                                    {{ $ticket->subject }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-white/50">
                                    <span><i class="fas fa-tag mr-1"></i>{{ $ticket->category_label }}</span>
                                    @if($ticket->order)
                                        <span><i class="fas fa-shopping-bag mr-1"></i>Order #{{ $ticket->order->id }}</span>
                                    @endif
                                    <span><i class="fas fa-clock mr-1"></i>{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                                @if($ticket->latestMessage)
                                    <p class="mt-2 text-sm text-white/40 truncate">
                                        {{ $ticket->latestMessage->is_admin ? '🛡️ Admin: ' : '' }}{{ Str::limit($ticket->latestMessage->message, 80) }}
                                    </p>
                                @endif
                            </div>
                            <i class="fas fa-chevron-right text-white/30 group-hover:text-cyan-300 transition-colors mt-1"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="store-glass-card rounded-2xl p-12 text-center">
                <div class="w-24 h-24 rounded-full bg-teal-500/20 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-headset text-3xl text-teal-300"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Belum Ada Tiket</h3>
                <p class="text-white/60 mb-6">Anda belum pernah membuat tiket support. Buat tiket jika butuh bantuan.</p>
                <a href="{{ route('tickets.create') }}" class="btn-primary inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold">
                    <i class="fas fa-plus"></i> Buat Tiket Pertama
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
