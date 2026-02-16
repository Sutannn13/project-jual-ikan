@extends('layouts.admin')
@section('title', 'Support Tickets')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-headset text-rose-400"></i> Support Tickets
            </h1>
            <p class="text-sm text-white/50">Kelola tiket bantuan dari customer</p>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-2">
        <a href="{{ route('admin.tickets.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all
                  {{ !request('status') ? 'text-white shadow-lg' : 'bg-white/5 backdrop-blur text-white/60 border border-white/10 hover:bg-white/10' }}"
           @if(!request('status')) style="background: linear-gradient(135deg, #e11d48, #f43f5e); box-shadow: 0 4px 15px rgba(225,29,72,0.3);" @endif>
            Semua <span class="ml-1 text-xs opacity-75">({{ array_sum($statusCounts) }})</span>
        </a>
        @php
            $statusFilters = [
                'open'             => ['label' => 'Open',     'color' => '#0ea5e9', 'shadow' => 'rgba(14,165,233,0.3)'],
                'in_progress'      => ['label' => 'Proses',   'color' => '#f59e0b', 'shadow' => 'rgba(245,158,11,0.3)'],
                'waiting_customer' => ['label' => 'Tunggu Customer', 'color' => '#8b5cf6', 'shadow' => 'rgba(139,92,246,0.3)'],
                'resolved'         => ['label' => 'Resolved', 'color' => '#10b981', 'shadow' => 'rgba(16,185,129,0.3)'],
                'closed'           => ['label' => 'Closed',   'color' => '#6b7280', 'shadow' => 'rgba(107,114,128,0.3)'],
            ];
        @endphp
        @foreach($statusFilters as $key => $filter)
            <a href="{{ route('admin.tickets.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all
                      {{ request('status') === $key ? 'text-white shadow-lg' : 'bg-white/5 backdrop-blur text-white/60 border border-white/10 hover:bg-white/10' }}"
               @if(request('status') === $key) style="background: {{ $filter['color'] }}; box-shadow: 0 4px 15px {{ $filter['shadow'] }};" @endif>
                {{ $filter['label'] }}
                @if($statusCounts[$key] > 0)
                    <span class="ml-1 text-xs opacity-75">({{ $statusCounts[$key] }})</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Tickets List --}}
    <div class="dark-glass-card rounded-2xl overflow-hidden">
        @if($tickets->count())
            {{-- Mobile --}}
            <div class="sm:hidden divide-y divide-white/5">
                @foreach($tickets as $ticket)
                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="block p-4 hover:bg-white/5 transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-mono text-white/40">#{{ $ticket->ticket_number }}</span>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ticket->priority_color }}">
                                    {{ $ticket->priority_label }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ticket->status_color }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </div>
                        </div>
                        <p class="font-semibold text-white text-sm truncate">{{ $ticket->subject }}</p>
                        <div class="flex items-center justify-between mt-2 text-xs text-white/40">
                            <span>{{ $ticket->user->name ?? '-' }}</span>
                            <span>{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-white/5 text-white/40 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 text-left font-semibold">Tiket</th>
                            <th class="px-6 py-4 text-left font-semibold">Customer</th>
                            <th class="px-6 py-4 text-left font-semibold">Kategori</th>
                            <th class="px-6 py-4 text-left font-semibold">Prioritas</th>
                            <th class="px-6 py-4 text-left font-semibold">Status</th>
                            <th class="px-6 py-4 text-left font-semibold">Tanggal</th>
                            <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-white/5 transition {{ in_array($ticket->status, ['open']) ? 'bg-sky-500/5' : '' }}">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $ticket->subject }}</p>
                                    <p class="text-xs text-white/40 mt-0.5">#{{ $ticket->ticket_number }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-white">{{ $ticket->user->name ?? '-' }}</p>
                                    <p class="text-xs text-white/40">{{ $ticket->user->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-white/60">{{ $ticket->category_label }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $ticket->priority_color }}">
                                        {{ $ticket->priority_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $ticket->status_color }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-white/50">
                                    {{ $ticket->created_at->format('d/m/Y') }}<br>
                                    <span class="text-white/30">{{ $ticket->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.tickets.show', $ticket) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 text-xs text-white/70 hover:bg-white/20 hover:text-white transition">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $tickets->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center">
                <i class="fas fa-headset text-4xl text-white/20 mb-4"></i>
                <p class="text-white/40">Tidak ada tiket ditemukan.</p>
            </div>
        @endif
    </div>
</div>
@endsection
