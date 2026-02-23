@extends('layouts.admin')

@section('title', 'Laporan Margin Keuntungan')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-white">Laporan Margin Keuntungan</h2>
        <p class="text-sm text-white/50">Analisis profitabilitas per produk</p>
    </div>
    <a href="{{ route('admin.reports.profit-margin.export') }}" 
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:scale-105"
       style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="dark-glass-card rounded-xl p-4">
        <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Total Omset</p>
        <p class="text-lg font-extrabold text-white">Rp {{ number_format($products->sum('total_revenue'), 0, ',', '.') }}</p>
    </div>
    <div class="dark-glass-card rounded-xl p-4">
        <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Total Modal</p>
        <p class="text-lg font-extrabold text-amber-400">Rp {{ number_format($products->sum('total_cost'), 0, ',', '.') }}</p>
    </div>
    <div class="dark-glass-card rounded-xl p-4">
        <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Total Profit</p>
        @php $totalProfit = $products->sum('total_revenue') - $products->sum('total_cost'); @endphp
        <p class="text-lg font-extrabold {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
            Rp {{ number_format($totalProfit, 0, ',', '.') }}
        </p>
    </div>
    <div class="dark-glass-card rounded-xl p-4">
        <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Rata-rata Margin</p>
        @php
            $totalRev = $products->sum('total_revenue');
            $avgMargin = $totalRev > 0 ? ($totalProfit / $totalRev) * 100 : 0;
        @endphp
        <p class="text-lg font-extrabold {{ $avgMargin >= 0 ? 'text-violet-400' : 'text-red-400' }}">
            {{ number_format($avgMargin, 1) }}%
        </p>
    </div>
</div>

{{-- Products Table --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    {{-- Desktop Table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Produk</th>
                    <th class="text-right p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Terjual (Kg)</th>
                    <th class="text-right p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Total Omset</th>
                    <th class="text-right p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Total Modal</th>
                    <th class="text-right p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Profit</th>
                    <th class="text-right p-4 text-white/50 font-semibold text-xs uppercase tracking-wider">Margin %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($products as $product)
                @php
                    $profit = $product->total_revenue - $product->total_cost;
                    $margin = $product->total_revenue > 0 ? ($profit / $product->total_revenue) * 100 : 0;
                @endphp
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            @if($product->foto)
                            <img src="{{ asset('storage/'.$product->foto) }}" alt="" 
                                 class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                            @else
                            <div class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-fish text-white/20 text-sm"></i>
                            </div>
                            @endif
                            <div>
                                <p class="font-semibold text-white">{{ $product->nama }}</p>
                                <p class="text-xs text-white/40">{{ $product->kategori }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-right text-white/80">
                        {{ number_format($product->total_qty_sold, 1) }}
                    </td>
                    <td class="p-4 text-right font-semibold text-white">
                        Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                    </td>
                    <td class="p-4 text-right text-amber-400/80">
                        Rp {{ number_format($product->total_cost, 0, ',', '.') }}
                    </td>
                    <td class="p-4 text-right font-bold {{ $profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        Rp {{ number_format($profit, 0, ',', '.') }}
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <div class="w-16 h-1.5 rounded-full overflow-hidden hidden lg:block" style="background: rgba(255,255,255,0.1);">
                                <div class="h-1.5 rounded-full"
                                     style="width: {{ min(max($margin, 0), 100) }}%;
                                            background: {{ $margin >= 20 ? 'linear-gradient(90deg,#10b981,#059669)' : ($margin >= 0 ? 'linear-gradient(90deg,#f59e0b,#d97706)' : 'linear-gradient(90deg,#ef4444,#dc2626)') }};"></div>
                            </div>
                            <span class="font-bold text-sm {{ $margin >= 20 ? 'text-emerald-400' : ($margin >= 0 ? 'text-amber-400' : 'text-red-400') }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-white/40">
                        <i class="fas fa-chart-pie text-3xl mb-3 block"></i>
                        Belum ada data penjualan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="sm:hidden divide-y divide-white/5">
        @forelse($products as $product)
        @php
            $profit = $product->total_revenue - $product->total_cost;
            $margin = $product->total_revenue > 0 ? ($profit / $product->total_revenue) * 100 : 0;
        @endphp
        <div class="p-4">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2 min-w-0">
                    @if($product->foto)
                    <img src="{{ asset('storage/'.$product->foto) }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                    @endif
                    <div class="min-w-0">
                        <p class="font-semibold text-white truncate">{{ $product->nama }}</p>
                        <p class="text-xs text-white/40">{{ number_format($product->total_qty_sold, 1) }} Kg terjual</p>
                    </div>
                </div>
                <span class="font-bold text-sm flex-shrink-0 {{ $margin >= 20 ? 'text-emerald-400' : ($margin >= 0 ? 'text-amber-400' : 'text-red-400') }}">
                    {{ number_format($margin, 1) }}%
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <div class="text-center p-2 rounded-lg" style="background: rgba(255,255,255,0.04);">
                    <p class="text-white/40">Omset</p>
                    <p class="font-bold text-white mt-0.5">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-2 rounded-lg" style="background: rgba(255,255,255,0.04);">
                    <p class="text-white/40">Modal</p>
                    <p class="font-bold text-amber-400 mt-0.5">Rp {{ number_format($product->total_cost, 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-2 rounded-lg" style="background: rgba(255,255,255,0.04);">
                    <p class="text-white/40">Profit</p>
                    <p class="font-bold mt-0.5 {{ $profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        Rp {{ number_format($profit, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-white/40">Belum ada data penjualan</div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="p-4 border-t border-white/5">{{ $products->links() }}</div>
    @endif
</div>
@endsection
