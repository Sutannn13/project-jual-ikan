@extends('layouts.admin')

@section('title', 'Chat Pelanggan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white">Chat Pelanggan</h1>
    <p class="text-white/50 mt-1">Kelola percakapan dengan pelanggan Anda</p>
</div>

@if($conversations->count() > 0)
<div class="dark-glass-card rounded-2xl overflow-hidden">
    <div class="divide-y divide-white/5">
        @foreach($conversations as $conv)
        @php
            $customer = $customers[$conv->customer_id] ?? null;
            $unread = $unreadCounts[$conv->customer_id] ?? 0;
            $lastMsg = $conv->last_message ?? null;
        @endphp
        @if($customer)
        <a href="{{ route('admin.chat.show', $customer) }}" 
           class="flex items-center gap-4 p-5 hover:bg-white/5 transition-colors group">
            {{-- Avatar --}}
            <div class="relative flex-shrink-0">
                <div class="w-12 h-12 rounded-full flex items-center justify-center"
                     style="background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);">
                    <i class="fas fa-user text-ocean-600"></i>
                </div>
                @if($unread > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center animate-pulse">
                    {{ $unread }}
                </span>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-white truncate {{ $unread > 0 ? '' : '' }}">{{ $customer->name }}</h3>
                    <span class="text-xs text-white/30 flex-shrink-0 ml-2">
                        {{ $lastMsg ? $lastMsg->created_at->diffForHumans() : '' }}
                    </span>
                </div>
                <p class="text-sm text-white/50 truncate">
                    @if($lastMsg)
                        @if($lastMsg->sender_id === Auth::id())
                            <span class="text-white/40">Anda: </span>
                        @endif
                        {{ $lastMsg->message }}
                    @else
                        <span class="text-white/30 italic">Belum ada pesan</span>
                    @endif
                </p>
            </div>

            {{-- Arrow --}}
            <i class="fas fa-chevron-right text-white/20 group-hover:text-cyan-400 transition-colors"></i>
        </a>
        @endif
        @endforeach
    </div>
</div>
@else
<div class="dark-glass-card rounded-2xl p-12 text-center">
    <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center"
         style="background: linear-gradient(135deg, rgba(6,182,212,0.1) 0%, rgba(20,184,166,0.05) 100%);">
        <i class="fas fa-comments text-3xl text-ocean-400"></i>
    </div>
    <h3 class="text-lg font-bold text-white/70 mb-2">Belum Ada Percakapan</h3>
    <p class="text-white/40 text-sm">Pelanggan belum memulai percakapan apapun.</p>
</div>
@endif
@endsection
