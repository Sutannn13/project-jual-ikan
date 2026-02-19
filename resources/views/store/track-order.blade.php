@extends('layouts.master')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="py-8 sm:py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <a href="{{ route('my.orders') }}" class="inline-flex items-center gap-2 text-sm font-medium text-white/50 hover:text-cyan-300 mb-8 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white/10 border border-white/15 flex items-center justify-center group-hover:border-cyan-400/40 group-hover:bg-cyan-400/10 transition-all">
                <i class="fas fa-arrow-left text-xs"></i>
            </div>
            Kembali ke Pesanan Saya
        </a>
        </a>

        {{-- Main Card --}}
        <div class="store-glass-card rounded-3xl overflow-hidden mb-8">
            {{-- Header with Status --}}
            <div class="p-6 sm:p-8 border-b border-white/10 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 rounded-bl-full -mr-16 -mt-16 pointer-events-none" style="background: rgba(6,182,212,0.06);"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
                    <div>
                        <p class="text-xs font-bold text-white/40 uppercase tracking-wider mb-1">Nomor Pesanan</p>
                        <h1 class="text-2xl sm:text-3xl font-black text-cyan-300 tracking-tight">{{ $order->order_number }}</h1>
                        <p class="text-sm text-white/50 mt-1 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-cyan-400"></i>
                            Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                    
                    <div class="flex flex-col items-end">
                        <span class="px-4 py-2 rounded-xl text-sm font-bold uppercase tracking-wide
                            {{ match($order->status) {
                                'pending' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                                'confirmed' => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
                                'out_for_delivery' => 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30',
                                'completed' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
                                'cancelled' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                                default => 'bg-white/10 text-white/60 border border-white/15'
                            } }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="p-6 sm:p-8" style="background: rgba(255,255,255,0.03);">
                @if($order->status === 'cancelled')
                    <div class="rounded-2xl p-6 text-center" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: rgba(239,68,68,0.15);">
                            <i class="fas fa-times text-2xl text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">Pesanan Dibatalkan</h3>
                        <p class="text-white/50 mt-1">Pesanan ini telah dibatalkan dan tidak akan diproses.</p>
                    </div>
                @else
                    <div class="relative">
                        {{-- Timeline Line --}}
                        <div class="absolute top-0 left-6 sm:left-8 bottom-0 w-1 rounded-full" style="background: rgba(255,255,255,0.1);"></div>
                        
                        {{-- Steps --}}
                        @php
                            $steps = [
                                'pending' => ['title' => 'Pesanan Dibuat', 'desc' => 'Kami telah menerima pesanan Anda.', 'icon' => 'fa-receipt'],
                                'confirmed' => ['title' => 'Dikonfirmasi', 'desc' => 'Admin telah memverifikasi pesanan.', 'icon' => 'fa-clipboard-check'],
                                'out_for_delivery' => ['title' => 'Dalam Pengiriman', 'desc' => 'Kurir sedang menuju lokasi Anda.', 'icon' => 'fa-truck-fast'],
                                'completed' => ['title' => 'Selesai', 'desc' => 'Pesanan telah diterima.', 'icon' => 'fa-check-double'],
                            ];
                            
                            $currentStatusIndex = array_search($order->status, array_keys($steps));
                            if ($currentStatusIndex === false) $currentStatusIndex = -1;
                        @endphp

                        <div class="space-y-8">
                            @foreach($steps as $key => $step)
                                @php
                                    $stepIndex = array_search($key, array_keys($steps));
                                    $isCompleted = $stepIndex <= $currentStatusIndex;
                                    $isCurrent = $stepIndex === $currentStatusIndex;
                                @endphp
                                <div class="relative flex gap-6 sm:gap-8 hover:bg-white/5 p-2 rounded-xl transition-colors">
                                    {{-- Dot/Icon --}}
                                    <div class="relative z-10 w-12 h-12 sm:w-16 sm:h-16 rounded-full border-4 flex items-center justify-center flex-shrink-0 transition-all duration-500"
                                         style="background: rgba(15,30,50,0.8);"
                                         :class="[
                                            {{ $isCompleted ? "'border-cyan-400 text-cyan-300 shadow-lg'" : "'border-white/15 text-white/30'" }},
                                            {{ $isCurrent ? "'scale-110 ring-4 ring-cyan-400/20'" : "''" }}
                                         ]">
                                        <i class="fas {{ $step['icon'] }} {{ $isCompleted ? 'text-lg sm:text-xl' : 'text-base sm:text-lg' }}"></i>
                                        
                                        @if($isCompleted && !$isCurrent)
                                            <div class="absolute -right-1 -bottom-1 w-5 h-5 bg-emerald-500 rounded-full border-2 flex items-center justify-center" style="border-color: rgba(15,30,50,0.8);">
                                                <i class="fas fa-check text-[10px] text-white"></i>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="pt-1 sm:pt-2">
                                        <h3 class="text-base sm:text-lg font-bold {{ $isCompleted ? 'text-white' : 'text-white/30' }}">
                                            {{ $step['title'] }}
                                        </h3>
                                        <p class="text-sm {{ $isCompleted ? 'text-white/60' : 'text-white/30' }}">{{ $step['desc'] }}</p>
                                        
                                        @if($isCurrent && $key === 'out_for_delivery' && $order->delivery_note)
                                            <div class="mt-3 rounded-xl p-4 text-sm" style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
                                                <p class="font-semibold text-cyan-300 mb-1"><i class="fas fa-info-circle mr-1"></i> Info Pengiriman:</p>
                                                <p class="text-cyan-200/70">{{ $order->delivery_note }}</p>
                                                @if($order->delivery_time)
                                                    <p class="text-cyan-300/60 mt-1 text-xs font-semibold">Estimasi: {{ \Carbon\Carbon::parse($order->delivery_time)->format('d M Y, H:i') }}</p>
                                                @endif
                                                
                                                {{-- Courier Info --}}
                                                @if($order->courier_name)
                                                <div class="mt-3 pt-3 border-t border-cyan-400/20">
                                                    <p class="font-semibold text-cyan-300 mb-2"><i class="fas fa-motorcycle mr-1"></i> Info Kurir:</p>
                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                        <div>
                                                            <span class="text-cyan-300/60">Nama:</span>
                                                            <span class="font-semibold text-white">{{ $order->courier_name }}</span>
                                                        </div>
                                                        @if($order->courier_phone)
                                                        <div>
                                                            <span class="text-cyan-300/60">HP:</span>
                                                            <a href="tel:{{ $order->courier_phone }}" class="font-semibold text-cyan-300 underline">{{ $order->courier_phone }}</a>
                                                        </div>
                                                        @endif
                                                        @if($order->tracking_number)
                                                        <div class="col-span-2">
                                                            <span class="text-cyan-300/60">Resi/Plat:</span>
                                                            <span class="font-semibold text-white">{{ $order->tracking_number }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Detailed Status History --}}
        @if($order->statusHistories && $order->statusHistories->count())
        <div class="store-glass-card rounded-3xl overflow-hidden p-4 sm:p-6 lg:p-8 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-bold text-white mb-4 sm:mb-5 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-cyan-400"></i> Riwayat Status
            </h3>
            <div class="space-y-2 sm:space-y-3">
                @foreach($order->statusHistories as $history)
                    <div class="flex items-start gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-white/5 transition-colors">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" style="background: rgba(6,182,212,0.1);">
                            <i class="{{ $history->status_icon }} text-[10px] sm:text-xs" style="color: {{ $history->status_color }};"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-white">{{ $history->status_label }}</p>
                            @if($history->notes)
                                <p class="text-[10px] sm:text-xs text-white/50 mt-0.5">{{ $history->notes }}</p>
                            @endif
                            <p class="text-[10px] sm:text-xs text-white/30 mt-1">{{ $history->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Order Items --}}
        <div class="store-glass-card rounded-3xl overflow-hidden p-4 sm:p-6 lg:p-8">
            <h3 class="text-base sm:text-lg font-bold text-white mb-4 sm:mb-6 flex items-center gap-2">
                <i class="fas fa-shopping-basket text-cyan-400"></i> Detail Item
            </h3>
            
            <div class="space-y-3 sm:space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center gap-2.5 sm:gap-3 lg:gap-4 py-2 sm:py-3 border-b border-white/5 last:border-0 hover:bg-white/5 p-2 rounded-xl transition-colors">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg sm:rounded-xl bg-white/5 flex-shrink-0 overflow-hidden border border-white/10">
                        @if($item->produk->foto)
                            <img src="{{ asset('storage/'.$item->produk->foto) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white/20">
                                <i class="fas fa-fish text-sm"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 sm:gap-2 mb-0.5 sm:mb-1">
                            <h4 class="font-bold text-white text-xs sm:text-sm lg:text-base truncate">{{ $item->nama_produk ?? $item->produk?->nama ?? 'Produk Dihapus' }}</h4>
                            <span class="{{ $item->produk->kategori === 'Ikan Nila' ? 'badge-nila' : 'badge-mas' }} text-[9px] sm:text-[10px] px-1.5 sm:px-2 py-0.5">{{ $item->produk->kategori }}</span>
                        </div>
                        <p class="text-[10px] sm:text-xs text-white/40">{{ number_format($item->qty, 1) }} Kg × Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}</p>
                    </div>
                    <p class="font-bold text-white text-xs sm:text-sm lg:text-base whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            
            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-white/10 flex justify-between items-center">
                <span class="text-white/50 font-medium text-sm sm:text-base">Total Pembayaran</span>
                <span class="text-xl sm:text-2xl lg:text-3xl font-black text-cyan-300">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
