@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-white">Laporan Penjualan</h2>
        <p class="text-sm text-white/50">Laporan pesanan dengan status "Selesai"</p>
    </div>
    <a href="{{ route('admin.reports.pdf', request()->only(['from', 'to'])) }}" class="btn-danger inline-flex items-center">
        <i class="fas fa-file-pdf mr-2"></i> Download PDF
    </a>
</div>

{{-- Quick Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.reports.index', ['period' => 'today']) }}" 
       class="dark-glass-card rounded-xl {{ request('period') === 'today' ? 'border-cyan-500 ring-2 ring-cyan-500/20' : '' }} p-4 hover:bg-white/10 transition-all">
        <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Hari Ini</p>
        <p class="text-xl font-bold text-white">Rp {{ number_format($stats['today'] ?? 0, 0, ',', '.') }}</p>
    </a>
    <a href="{{ route('admin.reports.index', ['period' => 'week']) }}" 
       class="dark-glass-card rounded-xl {{ request('period') === 'week' ? 'border-cyan-500 ring-2 ring-cyan-500/20' : '' }} p-4 hover:bg-white/10 transition-all">
        <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Minggu Ini</p>
        <p class="text-xl font-bold text-white">Rp {{ number_format($stats['week'] ?? 0, 0, ',', '.') }}</p>
    </a>
    <a href="{{ route('admin.reports.index', ['period' => 'month']) }}" 
       class="dark-glass-card rounded-xl {{ request('period') === 'month' ? 'border-cyan-500 ring-2 ring-cyan-500/20' : '' }} p-4 hover:bg-white/10 transition-all">
        <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Bulan Ini</p>
        <p class="text-xl font-bold text-white">Rp {{ number_format($stats['month'] ?? 0, 0, ',', '.') }}</p>
    </a>
    <div class="rounded-xl p-4" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
        <p class="text-xs text-amber-400 uppercase tracking-wider mb-1">Dalam Proses</p>
        <p class="text-xl font-bold text-amber-300">Rp {{ number_format($stats['pending'] ?? 0, 0, ',', '.') }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="dark-glass-card rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row gap-4 sm:items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-white/60 mb-2 uppercase tracking-wide">Dari Tanggal</label>
            <input type="date" name="from" value="{{ request('from') }}" 
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-white/20 focus:outline-none focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/50 transition-all"
                   style="color-scheme: dark;">
        </div>
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-white/60 mb-2 uppercase tracking-wide">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ request('to') }}" 
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-white/20 focus:outline-none focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/50 transition-all"
                   style="color-scheme: dark;">
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button type="submit" class="btn-primary flex-1 sm:flex-none justify-center px-6 py-2.5 inline-flex items-center">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request('from') || request('to') || request('period'))
            <a href="{{ route('admin.reports.index') }}" class="btn-secondary flex-1 sm:flex-none justify-center px-4 py-2.5 inline-flex items-center">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Grand Total Card --}}
<div class="bg-gradient-to-r from-ocean-600 to-ocean-700 rounded-2xl p-6 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-ocean-200 text-sm">
                Grand Total Penjualan 
                @if(request('period'))
                    ({{ ucfirst(request('period') === 'today' ? 'Hari Ini' : (request('period') === 'week' ? 'Minggu Ini' : 'Bulan Ini')) }})
                @elseif(request('from') || request('to'))
                    (Filter Aktif)
                @else
                    (Semua Waktu)
                @endif
            </p>
            <p class="text-3xl font-extrabold mt-1">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            <p class="text-ocean-200 text-sm mt-2">
                <i class="fas fa-shopping-cart mr-1"></i> {{ $stats['total_orders'] ?? 0 }} transaksi
            </p>
        </div>
        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-chart-line text-2xl"></i>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-white/5">
        @forelse($orders as $order)
        <div class="p-4 hover:bg-white/5 transition-colors">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div>
                    <span class="font-bold text-cyan-400 block">{{ $order->order_number }}</span>
                    <span class="text-xs text-white/40">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <span class="font-bold text-white text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="mb-2">
                <p class="text-sm font-semibold text-white/80">{{ $order->user->name }}</p>
                <div class="text-xs text-white/50 mt-1 line-clamp-2">
                    @foreach($order->items as $item)
                        {{ $item->nama_produk ?? $item->produk?->nama ?? '-' }} ({{ $item->qty }}Kg){{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-white/30">
            <i class="fas fa-chart-bar text-4xl mb-3"></i>
            <p>Tidak ada data untuk periode ini.</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white/5 text-white/40 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 text-left">Order ID</th>
                    <th class="px-6 py-4 text-left">Pelanggan</th>
                    <th class="px-6 py-4 text-left">Items</th>
                    <th class="px-6 py-4 text-right">Total</th>
                    <th class="px-6 py-4 text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($orders as $order)
                <tr class="hover:bg-white/5">
                    <td class="px-6 py-4 font-bold text-cyan-400">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 font-medium text-white">{{ $order->user->name }}</td>
                    <td class="px-6 py-4 text-white/60">
                        @foreach($order->items as $item)
                            <span class="text-xs">{{ $item->nama_produk ?? $item->produk?->nama ?? '-' }} ({{ $item->qty }}Kg)</span>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center text-xs text-white/40">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-white/30">
                        <i class="fas fa-chart-bar text-4xl mb-3"></i>
                        <p>Tidak ada data untuk periode ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-white/5">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
