@extends('layouts.admin')

@section('title', 'Target Penjualan')

@section('content')
{{-- Alert Messages --}}
@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3">
    <i class="fas fa-check-circle text-xl"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-xl"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-white">Target Penjualan</h2>
        <p class="text-sm text-white/50">Pantau progres target harian & bulanan</p>
    </div>
    <a href="{{ route('admin.sales-targets.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Buat Target
    </a>
</div>

{{-- Filter Tabs --}}
<div class="flex gap-2 p-1.5 rounded-xl mb-6 w-fit" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
    <a href="{{ route('admin.sales-targets.index', ['type' => 'daily']) }}" 
       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
       {{ request('type', 'daily') === 'daily' ? 'text-white' : 'text-white/50 hover:text-white hover:bg-white/10' }}"
       style="{{ request('type', 'daily') === 'daily' ? 'background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);' : '' }}">
        <i class="fas fa-sun mr-1.5"></i> Harian
    </a>
    <a href="{{ route('admin.sales-targets.index', ['type' => 'monthly']) }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
       {{ request('type') === 'monthly' ? 'text-white' : 'text-white/50 hover:text-white hover:bg-white/10' }}"
       style="{{ request('type') === 'monthly' ? 'background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);' : '' }}">
        <i class="fas fa-calendar mr-1.5"></i> Bulanan
    </a>
</div>

{{-- Today & This Month Progress Cards --}}
@php
    $currentTarget = request('type', 'daily') === 'monthly' ? ($monthTarget ?? null) : ($todayTarget ?? null);
@endphp
@if(isset($currentTarget) && $currentTarget)
<div class="dark-glass-card rounded-2xl p-5 sm:p-6 mb-6 border border-cyan-500/20">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <div>
            <h3 class="text-white font-bold text-lg">
                {{ $currentTarget->type === 'daily' ? 'Target Hari Ini' : 'Target Bulan Ini' }}
            </h3>
            <p class="text-xs text-white/40">
                {{ $currentTarget->type === 'daily' ? $currentTarget->target_date->format('d M Y') : $currentTarget->target_date->format('M Y') }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-extrabold {{ $currentTarget->is_achieved ? 'text-emerald-400' : 'text-white' }}">
                {{ number_format($currentTarget->progress_percent, 1) }}%
            </p>
            @if($currentTarget->is_achieved)
            <span class="text-xs font-semibold text-emerald-400"><i class="fas fa-trophy mr-1"></i>Tercapai!</span>
            @endif
        </div>
    </div>
    <div class="w-full rounded-full h-4 overflow-hidden mb-3" style="background: rgba(255,255,255,0.08);">
        <div class="h-4 rounded-full transition-all duration-700 flex items-center justify-end pr-2"
             style="width: {{ min($currentTarget->progress_percent, 100) }}%; 
                    background: {{ $currentTarget->is_achieved ? 'linear-gradient(90deg, #10b981 0%, #059669 100%)' : 'linear-gradient(90deg, #0891b2 0%, #14b8a6 100%)' }};
                    box-shadow: 0 2px 8px rgba(6,182,212,0.4);">
        </div>
    </div>
    <div class="flex items-center justify-between text-xs text-white/50">
        <span>Actual: <strong class="text-white">Rp {{ number_format($currentTarget->actual_sales, 0, ',', '.') }}</strong></span>
        <span>Target: <strong class="text-white">Rp {{ number_format($currentTarget->target_amount, 0, ',', '.') }}</strong></span>
    </div>
</div>
@endif

{{-- Targets List --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    @if($targets->count() > 0)
    {{-- Desktop Table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Tipe</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Target</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Actual</th>
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Progres</th>
                    <th class="text-right p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($targets as $target)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 text-white font-semibold">
                        {{ $target->type === 'daily' ? $target->target_date->format('d M Y') : $target->target_date->format('M Y') }}
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold
                            {{ $target->type === 'daily' ? 'bg-cyan-500/15 text-cyan-300' : 'bg-violet-500/15 text-violet-300' }}">
                            {{ $target->type === 'daily' ? 'Harian' : 'Bulanan' }}
                        </span>
                    </td>
                    <td class="p-4 text-white/80 font-semibold">
                        Rp {{ number_format($target->target_amount, 0, ',', '.') }}
                    </td>
                    <td class="p-4 {{ $target->is_achieved ? 'text-emerald-400 font-bold' : 'text-white/80' }}">
                        Rp {{ number_format($target->actual_sales, 0, ',', '.') }}
                    </td>
                    <td class="p-4 w-40">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1);">
                                <div class="h-2 rounded-full transition-all"
                                     style="width: {{ min($target->progress_percent, 100) }}%;
                                            background: {{ $target->is_achieved ? 'linear-gradient(90deg,#10b981,#059669)' : 'linear-gradient(90deg,#0891b2,#14b8a6)' }};"></div>
                            </div>
                            <span class="text-xs font-bold {{ $target->is_achieved ? 'text-emerald-400' : 'text-white/70' }} w-12 text-right">
                                {{ number_format($target->progress_percent, 0) }}%
                            </span>
                            @if($target->is_achieved)
                            <i class="fas fa-trophy text-amber-400 text-xs"></i>
                            @endif
                        </div>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.sales-targets.edit', $target) }}" 
                               class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-all">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <form action="{{ route('admin.sales-targets.destroy', $target) }}" method="POST" 
                                  onsubmit="return confirm('Hapus target ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="sm:hidden divide-y divide-white/5">
        @foreach($targets as $target)
        <div class="p-4">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-bold text-white">
                        {{ $target->type === 'daily' ? $target->target_date->format('d M Y') : $target->target_date->format('M Y') }}
                    </p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                        {{ $target->type === 'daily' ? 'bg-cyan-500/15 text-cyan-300' : 'bg-violet-500/15 text-violet-300' }}">
                        {{ $target->type === 'daily' ? 'Harian' : 'Bulanan' }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    @if($target->is_achieved)
                    <i class="fas fa-trophy text-amber-400"></i>
                    @endif
                    <a href="{{ route('admin.sales-targets.edit', $target) }}" class="text-amber-400 hover:text-amber-300 text-sm">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.sales-targets.destroy', $target) }}" method="POST" 
                          onsubmit="return confirm('Hapus target ini?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs text-white/50 mb-1.5">
                <span>Rp {{ number_format($target->actual_sales, 0, ',', '.') }}</span>
                <span>/ Rp {{ number_format($target->target_amount, 0, ',', '.') }}</span>
            </div>
            <div class="w-full h-2.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1);">
                <div class="h-2.5 rounded-full"
                     style="width: {{ min($target->progress_percent, 100) }}%;
                            background: {{ $target->is_achieved ? 'linear-gradient(90deg,#10b981,#059669)' : 'linear-gradient(90deg,#0891b2,#14b8a6)' }};"></div>
            </div>
            <p class="text-right text-xs mt-1 {{ $target->is_achieved ? 'text-emerald-400 font-bold' : 'text-white/50' }}">
                {{ number_format($target->progress_percent, 0) }}%
            </p>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($targets->hasPages())
    <div class="p-4 border-t border-white/5">
        {{ $targets->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
             style="background: rgba(255,255,255,0.05);">
            <i class="fas fa-bullseye text-2xl text-white/20"></i>
        </div>
        <p class="text-white/50 font-semibold mb-1">Belum ada target</p>
        <p class="text-white/30 text-sm mb-4">Buat target pertama Anda sekarang</p>
        <a href="{{ route('admin.sales-targets.create') }}" class="btn-primary text-sm">
            <i class="fas fa-plus mr-1"></i> Buat Target
        </a>
    </div>
    @endif
</div>
@endsection
