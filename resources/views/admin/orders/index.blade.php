@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-white flex items-center gap-3">
            Data Pesanan
            @php
                $totalNeedsAttention = $statusCounts['waiting_payment'] + $statusCounts['pending'];
            @endphp
            @if($totalNeedsAttention > 0)
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg animate-pulse border border-red-300">
                    {{ $totalNeedsAttention }} Perlu Perhatian
                </span>
            @endif
        </h2>
        <p class="text-sm text-white/50">Kelola dan konfirmasi pesanan pelanggan</p>
    </div>
    
    {{-- Priority Alert: Waiting Payment --}}
    @if(isset($statusCounts) && $statusCounts['waiting_payment'] > 0)
    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl shadow-lg animate-pulse" 
         style="background: rgba(249,115,22,0.2); border: 1px solid rgba(249,115,22,0.4); box-shadow: 0 0 20px rgba(249,115,22,0.2);">
        <i class="fas fa-exclamation-triangle text-orange-300 text-lg"></i>
        <div>
            <p class="text-sm font-bold text-orange-200">{{ $statusCounts['waiting_payment'] }} Pesanan Prioritas</p>
            <p class="text-xs text-orange-300/80">Bukti bayar menunggu verifikasi</p>
        </div>
    </div>
    @endif
</div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-6 -mx-1 px-1 overflow-x-auto pb-2">
    <a href="{{ route('admin.orders.index') }}"
       class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all whitespace-nowrap
              {{ !request('status') ? 'text-white' : 'bg-white/5 backdrop-blur text-white/60 border border-white/10 hover:bg-white/10' }}"
       style="{{ !request('status') ? 'background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 12px rgba(6,182,212,0.3);' : '' }}">
        Semua
    </a>
    @php
        $statusLabels = [
            'pending' => ['label' => 'Menunggu Bayar', 'color' => 'amber'],
            'waiting_payment' => ['label' => 'Perlu Verifikasi', 'color' => 'orange'],
            'paid' => ['label' => 'Sudah Bayar', 'color' => 'cyan'],
            'confirmed' => ['label' => 'Dikonfirmasi', 'color' => 'blue'],
            'out_for_delivery' => ['label' => 'Dikirim', 'color' => 'indigo'],
            'completed' => ['label' => 'Selesai', 'color' => 'mint'],
            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'red'],
        ];
    @endphp
    @foreach($statusLabels as $key => $data)
    <a href="{{ route('admin.orders.index', ['status' => $key]) }}"
       class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all whitespace-nowrap flex items-center gap-2
              {{ request('status') === $key ? 'text-white' : 'bg-white/5 backdrop-blur text-white/60 border border-white/10 hover:bg-white/10' }}"
       style="{{ request('status') === $key ? 'background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 12px rgba(6,182,212,0.3);' : '' }}">
        {{ $data['label'] }}
        @if(isset($statusCounts) && $statusCounts[$key] > 0)
        <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ request('status') === $key ? 'bg-white/20 text-white' : 'bg-' . $data['color'] . '-500/20 text-' . $data['color'] . '-400' }}">
            {{ $statusCounts[$key] }}
        </span>
        @endif
    </a>
    @endforeach
</div>

{{-- Table Card --}}
<form id="bulkForm" action="{{ route('admin.orders.bulk-action') }}" method="POST" x-data="{ selectedOrders: [], bulkAction: '' }">
    @csrf

    {{-- Bulk Action Bar --}}
    <div x-show="selectedOrders.length > 0" x-transition
         class="mb-4 p-4 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center gap-3"
         style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
        <span class="text-sm text-cyan-300 font-semibold" x-text="selectedOrders.length + ' pesanan dipilih'"></span>
        <div class="flex flex-wrap gap-2 sm:ml-auto">
            <button type="submit" name="action" value="mark_processing" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 transition border border-blue-500/20">
                <i class="fas fa-cog mr-1"></i> Proses
            </button>
            <button type="submit" name="action" value="mark_shipped" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition border border-indigo-500/20">
                <i class="fas fa-truck mr-1"></i> Kirim
            </button>
            <button type="submit" name="action" value="mark_completed" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 transition border border-emerald-500/20">
                <i class="fas fa-check mr-1"></i> Selesai
            </button>
            <button type="submit" name="action" value="mark_cancelled" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-500/20 text-red-300 hover:bg-red-500/30 transition border border-red-500/20"
                    onclick="return confirm('Yakin batalkan pesanan terpilih?')">
                <i class="fas fa-times mr-1"></i> Batalkan
            </button>
        </div>
    </div>

<div class="dark-glass-card rounded-2xl overflow-hidden">
    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-white/5">
        @forelse($orders as $order)
        <a href="{{ route('admin.orders.show', $order) }}" 
           class="block p-4 transition-colors {{ $order->status === 'waiting_payment' ? 'bg-orange-500/10 hover:bg-orange-500/15 border-l-4 border-orange-500' : 'hover:bg-white/5' }}">
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        @if($order->status === 'waiting_payment')
                            <i class="fas fa-exclamation-circle text-orange-400 text-xs animate-pulse"></i>
                        @endif
                        <p class="font-bold {{ $order->status === 'waiting_payment' ? 'text-orange-400' : 'text-cyan-400' }} text-sm">{{ $order->order_number }}</p>
                    </div>
                    <p class="text-xs text-white/40 truncate">{{ $order->user->name }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase flex-shrink-0
                    {{ match($order->status) {
                        'pending' => 'bg-amber-500/15 text-amber-400 border border-amber-500/20',
                        'waiting_payment' => 'bg-orange-500/15 text-orange-400 border border-orange-500/20',
                        'paid' => 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/20',
                        'confirmed' => 'bg-blue-500/15 text-blue-400 border border-blue-500/20',
                        'out_for_delivery' => 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/20',
                        'completed' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20',
                        'cancelled' => 'bg-red-500/15 text-red-400 border border-red-500/20',
                        default => 'bg-white/10 text-white/60 border border-white/10'
                    } }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-white/40">{{ $order->items->count() }} item • {{ $order->created_at->format('d M Y') }}</span>
                <span class="font-extrabold text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            @if($order->payment_proof && $order->status === 'waiting_payment')
            <div class="mt-2 text-[10px] text-orange-400 flex items-center gap-1">
                <i class="fas fa-receipt"></i> Bukti bayar menunggu verifikasi
            </div>
            @endif
        </a>
        @empty
        <div class="p-10 text-center text-white/30">
            <i class="fas fa-shopping-cart text-4xl mb-3"></i>
            <p>Belum ada pesanan.</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white/5 text-white/40 text-xs uppercase tracking-wider">
                    <th class="px-4 py-4 text-center w-10">
                        <input type="checkbox" @click="if($event.target.checked) { selectedOrders = [...document.querySelectorAll('.order-checkbox')].map(el => el.value) } else { selectedOrders = [] }" class="rounded bg-white/10 border-white/20 text-cyan-500 focus:ring-cyan-500">
                    </th>
                    <th class="px-6 py-4 text-left">Order ID</th>
                    <th class="px-6 py-4 text-left">Pelanggan</th>
                    <th class="px-6 py-4 text-left">Items</th>
                    <th class="px-6 py-4 text-right">Total</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Bukti Bayar</th>
                    <th class="px-6 py-4 text-center">Tanggal</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($orders as $order)
                <tr class="transition {{ $order->status === 'waiting_payment' ? 'bg-orange-500/10 hover:bg-orange-500/15 border-l-4 border-orange-500' : 'hover:bg-white/5' }}">
                    <td class="px-4 py-4 text-center">
                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox rounded bg-white/10 border-white/20 text-cyan-500 focus:ring-cyan-500"
                               x-model="selectedOrders" :value="'{{ $order->id }}'">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @if($order->status === 'waiting_payment')
                                <i class="fas fa-exclamation-circle text-orange-400 animate-pulse"></i>
                            @endif
                            <span class="font-bold {{ $order->status === 'waiting_payment' ? 'text-orange-400' : 'text-cyan-400' }}">{{ $order->order_number }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-white">{{ $order->user->name }}</p>
                        <p class="text-xs text-white/40">{{ $order->user->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-white/60">
                        {{ $order->items->count() }} item
                    </td>
                    <td class="px-6 py-4 text-right font-extrabold text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold uppercase
                            {{ match($order->status) {
                                'pending' => 'bg-amber-500/15 text-amber-400',
                                'waiting_payment' => 'bg-orange-500/15 text-orange-400',
                                'paid' => 'bg-cyan-500/15 text-cyan-400',
                                'confirmed' => 'bg-blue-500/15 text-blue-400',
                                'out_for_delivery' => 'bg-indigo-500/15 text-indigo-400',
                                'completed' => 'bg-emerald-500/15 text-emerald-400',
                                'cancelled' => 'bg-red-500/15 text-red-400',
                                default => 'bg-white/10 text-white/60'
                            } }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($order->payment_proof)
                            @if($order->status === 'waiting_payment')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-orange-500/15 text-orange-400 text-xs font-medium">
                                <i class="fas fa-clock"></i> Perlu cek
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-500/15 text-emerald-400 text-xs font-medium">
                                <i class="fas fa-check"></i> Ada
                            </span>
                            @endif
                        @else
                            <span class="text-white/30 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-xs text-white/40">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.orders.show', $order) }}" 
                           class="w-9 h-9 rounded-lg inline-flex items-center justify-center text-white transition-all hover:scale-105"
                           style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 10px rgba(6,182,212,0.25);">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-16 text-center text-white/30">
                        <i class="fas fa-shopping-cart text-4xl mb-3 block"></i>
                        <p>Belum ada pesanan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-white/5">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>
</form>
@endsection
