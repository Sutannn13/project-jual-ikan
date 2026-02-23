@extends('layouts.admin')

@section('title', 'Peringatan Kedaluwarsa Stok')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-white">Peringatan Kedaluwarsa Stok</h2>
        <p class="text-sm text-white/50">Pantau stok ikan yang mendekati atau sudah kedaluwarsa</p>
    </div>
    <a href="{{ route('admin.stock-in.index') }}" class="px-4 py-2 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all border border-white/10">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Stok
    </a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="dark-glass-card rounded-xl p-4 border border-red-500/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(239,68,68,0.2);">
                <i class="fas fa-times-circle text-red-400"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-red-400">{{ $expired->count() }}</p>
                <p class="text-xs text-red-400/70 font-semibold">Kadaluwarsa</p>
            </div>
        </div>
    </div>
    <div class="dark-glass-card rounded-xl p-4 border border-orange-500/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(249,115,22,0.2);">
                <i class="fas fa-exclamation-triangle text-orange-400"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-orange-400">{{ $critical->count() }}</p>
                <p class="text-xs text-orange-400/70 font-semibold">Kritis (≤1 hari)</p>
            </div>
        </div>
    </div>
    <div class="dark-glass-card rounded-xl p-4 border border-amber-500/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(245,158,11,0.2);">
                <i class="fas fa-clock text-amber-400"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-amber-400">{{ $warning->count() }}</p>
                <p class="text-xs text-amber-400/70 font-semibold">Perhatian (≤3 hari)</p>
            </div>
        </div>
    </div>
</div>

{{-- Expired Section --}}
@if($expired->count() > 0)
<div class="mb-6">
    <div class="flex items-center gap-2.5 mb-3">
        <div class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></div>
        <h3 class="font-bold text-red-400 text-base">KADALUWARSA — Segera Tangani!</h3>
    </div>
    <div class="dark-glass-card rounded-2xl overflow-hidden border border-red-500/20">
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-red-500/20" style="background: rgba(239,68,68,0.05);">
                        <th class="text-left p-3.5 text-red-400/70 font-semibold text-xs uppercase tracking-wider">Produk</th>
                        <th class="text-left p-3.5 text-red-400/70 font-semibold text-xs uppercase tracking-wider">Qty (Kg)</th>
                        <th class="text-left p-3.5 text-red-400/70 font-semibold text-xs uppercase tracking-wider">Tgl Kedaluwarsa</th>
                        <th class="text-left p-3.5 text-red-400/70 font-semibold text-xs uppercase tracking-wider">Lewat</th>
                        <th class="text-left p-3.5 text-red-400/70 font-semibold text-xs uppercase tracking-wider">Supplier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-500/10">
                    @foreach($expired as $item)
                    <tr class="hover:bg-red-500/5 transition-colors">
                        <td class="p-3.5 font-semibold text-white">{{ $item->produk->nama }}</td>
                        <td class="p-3.5 text-red-300 font-bold">{{ number_format($item->qty, 1) }}</td>
                        <td class="p-3.5 text-red-400 font-semibold">{{ $item->expiry_date->format('d M Y') }}</td>
                        <td class="p-3.5 text-red-400 font-bold">{{ $item->expiry_date->diffForHumans() }}</td>
                        <td class="p-3.5 text-white/50">{{ $item->supplier ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="sm:hidden divide-y divide-red-500/10">
            @foreach($expired as $item)
            <div class="p-4">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <p class="font-bold text-white">{{ $item->produk->nama }}</p>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-red-500/20 text-red-300">{{ number_format($item->qty, 1) }} Kg</span>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="text-red-400 font-semibold"><i class="fas fa-calendar mr-1"></i>{{ $item->expiry_date->format('d M Y') }}</span>
                    <span class="text-red-300">{{ $item->expiry_date->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Critical Section --}}
@if($critical->count() > 0)
<div class="mb-6">
    <div class="flex items-center gap-2.5 mb-3">
        <div class="w-3 h-3 rounded-full bg-orange-500"></div>
        <h3 class="font-bold text-orange-400 text-base">KRITIS — Kedaluwarsa dalam 24 Jam</h3>
    </div>
    <div class="dark-glass-card rounded-2xl overflow-hidden border border-orange-500/20">
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-orange-500/20" style="background: rgba(249,115,22,0.05);">
                        <th class="text-left p-3.5 text-orange-400/70 font-semibold text-xs uppercase tracking-wider">Produk</th>
                        <th class="text-left p-3.5 text-orange-400/70 font-semibold text-xs uppercase tracking-wider">Qty (Kg)</th>
                        <th class="text-left p-3.5 text-orange-400/70 font-semibold text-xs uppercase tracking-wider">Tgl Kedaluwarsa</th>
                        <th class="text-left p-3.5 text-orange-400/70 font-semibold text-xs uppercase tracking-wider">Sisa Waktu</th>
                        <th class="text-left p-3.5 text-orange-400/70 font-semibold text-xs uppercase tracking-wider">Supplier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-500/10">
                    @foreach($critical as $item)
                    <tr class="hover:bg-orange-500/5 transition-colors">
                        <td class="p-3.5 font-semibold text-white">{{ $item->produk->nama }}</td>
                        <td class="p-3.5 text-orange-300 font-bold">{{ number_format($item->qty, 1) }}</td>
                        <td class="p-3.5 text-orange-400 font-semibold">{{ $item->expiry_date->format('d M Y') }}</td>
                        <td class="p-3.5 text-orange-300">{{ $item->expiry_date->diffForHumans() }}</td>
                        <td class="p-3.5 text-white/50">{{ $item->supplier ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="sm:hidden divide-y divide-orange-500/10">
            @foreach($critical as $item)
            <div class="p-4">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <p class="font-bold text-white">{{ $item->produk->nama }}</p>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-orange-500/20 text-orange-300">{{ number_format($item->qty, 1) }} Kg</span>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="text-orange-400 font-semibold"><i class="fas fa-calendar mr-1"></i>{{ $item->expiry_date->format('d M Y') }}</span>
                    <span class="text-orange-300">{{ $item->expiry_date->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Warning Section --}}
@if($warning->count() > 0)
<div class="mb-6">
    <div class="flex items-center gap-2.5 mb-3">
        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
        <h3 class="font-bold text-amber-400 text-base">PERHATIAN — Kedaluwarsa dalam 3 Hari</h3>
    </div>
    <div class="dark-glass-card rounded-2xl overflow-hidden border border-amber-500/20">
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-amber-500/20" style="background: rgba(245,158,11,0.05);">
                        <th class="text-left p-3.5 text-amber-400/70 font-semibold text-xs uppercase tracking-wider">Produk</th>
                        <th class="text-left p-3.5 text-amber-400/70 font-semibold text-xs uppercase tracking-wider">Qty (Kg)</th>
                        <th class="text-left p-3.5 text-amber-400/70 font-semibold text-xs uppercase tracking-wider">Tgl Kedaluwarsa</th>
                        <th class="text-left p-3.5 text-amber-400/70 font-semibold text-xs uppercase tracking-wider">Sisa Waktu</th>
                        <th class="text-left p-3.5 text-amber-400/70 font-semibold text-xs uppercase tracking-wider">Supplier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-500/10">
                    @foreach($warning as $item)
                    <tr class="hover:bg-amber-500/5 transition-colors">
                        <td class="p-3.5 font-semibold text-white">{{ $item->produk->nama }}</td>
                        <td class="p-3.5 text-amber-300 font-bold">{{ number_format($item->qty, 1) }}</td>
                        <td class="p-3.5 text-amber-400 font-semibold">{{ $item->expiry_date->format('d M Y') }}</td>
                        <td class="p-3.5 text-amber-300">{{ $item->expiry_date->diffForHumans() }}</td>
                        <td class="p-3.5 text-white/50">{{ $item->supplier ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="sm:hidden divide-y divide-amber-500/10">
            @foreach($warning as $item)
            <div class="p-4">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <p class="font-bold text-white">{{ $item->produk->nama }}</p>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300">{{ number_format($item->qty, 1) }} Kg</span>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="text-amber-400 font-semibold"><i class="fas fa-calendar mr-1"></i>{{ $item->expiry_date->format('d M Y') }}</span>
                    <span class="text-amber-300">{{ $item->expiry_date->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Empty State --}}
@if($expired->count() === 0 && $critical->count() === 0 && $warning->count() === 0)
<div class="dark-glass-card rounded-2xl text-center py-20">
    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5"
         style="background: rgba(16,185,129,0.1);">
        <i class="fas fa-check-circle text-3xl text-emerald-400"></i>
    </div>
    <h3 class="text-xl font-bold text-white mb-2">Semua Stok Aman!</h3>
    <p class="text-white/50">Tidak ada stok yang mendekati atau sudah kedaluwarsa (dalam 3 hari ke depan)</p>
</div>
@endif
@endsection
