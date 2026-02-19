@extends('layouts.master')

@section('title', 'Pesanan Saya')

@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center sm:text-left mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Pesanan Saya</h1>
            <p class="text-white/60 mt-1">Kelola dan pantau status pesanan Anda</p>
        </div>

        {{-- Tab Navigation --}}
        <div class="mb-6">
            <div class="flex gap-2 p-1.5 rounded-2xl" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(12px);">
                <a href="{{ route('my.orders', ['tab' => 'active']) }}" 
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-300
                   {{ $tab === 'active' 
                       ? 'text-white shadow-lg' 
                       : 'text-white/60 hover:text-white hover:bg-white/10' }}"
                   style="{{ $tab === 'active' 
                       ? 'background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 15px rgba(6,182,212,0.4);' 
                       : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Pesanan Aktif</span>
                    @if($activeCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $tab === 'active' ? 'bg-white/25 text-white' : 'bg-cyan-500/30 text-cyan-300' }}">
                        {{ $activeCount }}
                    </span>
                    @endif
                </a>
                <a href="{{ route('my.orders', ['tab' => 'history']) }}" 
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-300
                   {{ $tab === 'history' 
                       ? 'text-white shadow-lg' 
                       : 'text-white/60 hover:text-white hover:bg-white/10' }}"
                   style="{{ $tab === 'history' 
                       ? 'background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 15px rgba(6,182,212,0.4);' 
                       : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Riwayat</span>
                    @if($historyCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $tab === 'history' ? 'bg-white/25 text-white' : 'bg-white/15 text-white/70' }}">
                        {{ $historyCount }}
                    </span>
                    @endif
                </a>
            </div>
        </div>

        @if($orders->count() > 0)
        <div class="space-y-4 sm:space-y-5">
            @foreach($orders as $order)
            <div class="store-glass-card rounded-2xl overflow-hidden">
                {{-- Order Header --}}
                <div class="p-5 sm:p-6 border-b border-white/10">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="font-bold text-cyan-300 text-lg">{{ $order->order_number }}</p>
                            <p class="text-xs text-white/40 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase
                                {{ match($order->status) {
                                    'pending' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                                    'waiting_payment' => 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
                                    'paid' => 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30',
                                    'confirmed' => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
                                    'out_for_delivery' => 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30',
                                    'completed' => 'bg-mint-500/20 text-mint-300 border border-mint-500/30',
                                    'cancelled' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                                    default => 'bg-white/10 text-white/70 border border-white/20'
                                } }}">
                                {{ $order->status_label }}
                            </span>
                            <p class="font-extrabold text-white">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="p-5 sm:p-6 bg-white/5">
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl overflow-hidden flex-shrink-0 bg-white/10 border border-white/10">
                                @if($item->produk->foto)
                                    <img src="{{ asset('storage/'.$item->produk->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <i class="fas fa-fish"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-white text-sm truncate">{{ $item->nama_produk ?? $item->produk?->nama ?? 'Produk Dihapus' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="{{ $item->produk->kategori === 'Ikan Nila' ? 'badge-nila' : 'badge-mas' }} text-[10px] px-2 py-0.5">
                                        {{ $item->produk->kategori }}
                                    </span>
                                    <span class="text-xs text-white/50">{{ number_format($item->qty, 1) }} Kg</span>
                                </div>
                            </div>
                            <p class="font-bold text-white text-sm">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Review Section for Completed Orders --}}
                    @if($order->status === 'completed')
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-star text-amber-400 text-xs"></i>
                            <h4 class="font-bold text-white text-xs">Review Produk</h4>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($order->items as $item)
                                @php
                                    $hasReview = $item->hasReviewByUser(Auth::id());
                                @endphp
                                <div class="flex items-center justify-between gap-3 p-3 rounded-xl {{ $hasReview ? 'bg-mint-500/10 border border-mint-500/20' : 'bg-white/5 border border-white/10' }}">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0 bg-white/10">
                                            @if($item->produk->foto)
                                                <img src="{{ asset('storage/'.$item->produk->foto) }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-fish text-white/40 text-xs"></i>
                                            @endif
                                        </div>
                                        <p class="text-xs font-medium text-white truncate">{{ $item->nama_produk ?? $item->produk?->nama ?? 'Produk Dihapus' }}</p>
                                    </div>
                                    @if($hasReview)
                                        <div class="flex items-center gap-1 text-xs">
                                            <i class="fas fa-check-circle text-mint-400"></i>
                                            <span class="text-mint-300 font-semibold">Sudah Direview</span>
                                        </div>
                                    @else
                                        <a href="{{ route('review.create', ['order' => $order->id, 'produk' => $item->produk->id]) }}" 
                                           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:scale-105"
                                           style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); box-shadow: 0 2px 8px rgba(245,158,11,0.3);">
                                            <i class="fas fa-star"></i>
                                            <span>Beri Review</span>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Payment Deadline Alert --}}
                @if($order->status === 'pending' && $order->payment_deadline)
                <div class="px-5 sm:px-6 pb-0 bg-white/5">
                    <div class="{{ $order->isPaymentExpired() ? 'bg-red-500/15 border-red-500/30' : 'bg-amber-500/15 border-amber-500/30' }} border rounded-xl p-3 flex items-center gap-2">
                        <i class="fas {{ $order->isPaymentExpired() ? 'fa-exclamation-circle text-red-400' : 'fa-clock text-amber-400' }}"></i>
                        <span class="text-xs font-medium {{ $order->isPaymentExpired() ? 'text-red-300' : 'text-amber-300' }}">
                            @if($order->isPaymentExpired())
                                Batas waktu habis! Pesanan akan otomatis dibatalkan.
                            @else
                                Bayar sebelum: {{ $order->payment_deadline->format('d M Y, H:i') }} WIB
                            @endif
                        </span>
                    </div>
                </div>
                @endif

                {{-- Rejection Reason Alert --}}
                @if($order->status === 'pending' && $order->rejection_reason)
                <div class="px-5 sm:px-6 pb-0 bg-white/5">
                    <div class="bg-red-500/15 border border-red-500/30 rounded-xl p-3">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-times-circle text-red-400 mt-0.5"></i>
                            <div>
                                <p class="text-xs font-bold text-red-300">Bukti pembayaran ditolak:</p>
                                <p class="text-xs text-red-400 mt-0.5">{{ $order->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Refund Status Alert --}}
                @if($order->refund_status !== 'none')
                <div class="px-5 sm:px-6 pb-0 bg-white/5">
                    @if($order->refund_status === 'requested')
                    <div class="bg-amber-500/15 border border-amber-500/30 rounded-xl p-3">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-clock text-amber-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-amber-300">Permintaan Refund Sedang Diproses</p>
                                <p class="text-xs text-amber-400/80 mt-1"><strong>Alasan:</strong> {{ $order->refund_reason }}</p>
                                <p class="text-[10px] text-white/40 mt-1.5">Diajukan: {{ $order->refund_requested_at?->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @elseif($order->refund_status === 'approved')
                    <div class="bg-green-500/15 border border-green-500/30 rounded-xl p-3">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-green-300">Refund Disetujui</p>
                                <p class="text-xs text-green-400/80 mt-1">{{ $order->refund_admin_note }}</p>
                                <p class="text-[10px] text-white/40 mt-1.5">Diproses: {{ $order->refund_processed_at?->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @elseif($order->refund_status === 'rejected')
                    <div class="bg-red-500/15 border border-red-500/30 rounded-xl p-3">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-times-circle text-red-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-red-300">Refund Ditolak</p>
                                <p class="text-xs text-red-400 mt-1"><strong>Alasan penolakan:</strong> {{ $order->refund_admin_note }}</p>
                                <p class="text-[10px] text-white/40 mt-1.5">Diproses: {{ $order->refund_processed_at?->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Delivery & Courier Info (show when status is confirmed or out_for_delivery or completed) --}}
                @if(in_array($order->status, ['confirmed', 'out_for_delivery', 'completed']) && $order->delivery_note)
                <div class="px-5 sm:px-6 pb-0 bg-white/5">
                    <div class="bg-cyan-500/10 border border-cyan-500/20 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-ocean-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shipping-fast text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-cyan-300 mb-1.5">
                                    <i class="fas fa-box mr-1"></i> Info Pengiriman
                                </p>
                                <p class="text-xs text-white/70 leading-relaxed mb-2">{{ $order->delivery_note }}</p>
                                @if($order->delivery_time)
                                <p class="text-[10px] text-cyan-400 font-semibold flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($order->delivery_time)->format('d M Y, H:i') }} WIB
                                </p>
                                @endif
                                
                                {{-- Courier Info (only if courier_name exists) --}}
                                @if($order->courier_name)
                                <div class="mt-3 pt-3 border-t border-white/10">
                                    <p class="text-xs font-bold text-cyan-300 mb-2">
                                        <i class="fas fa-motorcycle mr-1"></i> Info Kurir
                                    </p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-white/50">Nama</span>
                                            <span class="text-xs font-semibold text-white">{{ $order->courier_name }}</span>
                                        </div>
                                        @if($order->courier_phone)
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-white/50">HP</span>
                                            <a href="tel:{{ $order->courier_phone }}" class="text-xs font-semibold text-cyan-300 underline hover:text-cyan-200">
                                                {{ $order->courier_phone }}
                                            </a>
                                        </div>
                                        @endif
                                        @if($order->tracking_number)
                                        <div class="flex flex-col col-span-2">
                                            <span class="text-[10px] text-white/50">Resi/Plat</span>
                                            <span class="text-xs font-semibold text-white">{{ $order->tracking_number }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                
                                {{-- Link to detail --}}
                                <div class="mt-3 pt-3 border-t border-white/10">
                                    <a href="{{ route('order.track', $order) }}" class="text-xs font-semibold text-cyan-300 hover:text-cyan-200 flex items-center gap-1">
                                        <i class="fas fa-search"></i> Lihat Detail Tracking
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Progress Bar --}}
                @if($order->status !== 'cancelled')
                <div class="px-5 sm:px-6 pb-3 sm:pb-4 bg-white/5">
                    <div class="pt-4 border-t border-white/10">
                        @php
                            $steps = [
                                'pending' => 0, 
                                'waiting_payment' => 1, 
                                'paid' => 2, 
                                'confirmed' => 3, 
                                'out_for_delivery' => 4, 
                                'completed' => 5
                            ];
                            $currentStep = $steps[$order->status] ?? 0;
                            $stepLabels = ['Bayar', 'Verifikasi', 'Dikonfirmasi', 'Diproses', 'Dikirim', 'Selesai'];
                        @endphp
                        <div class="flex items-center justify-between relative">
                            {{-- Progress Line --}}
                            <div class="absolute top-4 left-4 right-4 h-1 rounded-full bg-white/10 -z-10"></div>
                            <div class="absolute top-4 left-4 h-1 rounded-full -z-10 transition-all duration-500"
                                 style="width: {{ $currentStep * 20 }}%; background: linear-gradient(90deg, #0891b2 0%, #14b8a6 100%);"></div>
                            
                            @foreach($stepLabels as $index => $label)
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold transition-all
                                    {{ $index <= $currentStep ? 'text-white' : 'bg-white/10 text-white/40' }}"
                                    style="{{ $index <= $currentStep ? 'background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 10px rgba(6,182,212,0.3);' : '' }}">
                                    @if($index < $currentStep)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span class="text-[8px] sm:text-[10px] mt-1.5 {{ $index <= $currentStep ? 'text-cyan-300 font-semibold' : 'text-white/30' }} text-center leading-tight">
                                    {{ $label }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="px-5 sm:px-6 pb-3 sm:pb-4 bg-white/5">
                    <div class="pt-4 border-t border-white/10 flex items-center justify-center gap-2 text-red-400">
                        <i class="fas fa-times-circle"></i>
                        <span class="text-sm font-medium">Pesanan dibatalkan</span>
                    </div>
                </div>
                @endif

                {{-- ========================================== --}}
                {{-- ACTION BUTTONS (mobile-first, touch-friendly) --}}
                {{-- ========================================== --}}
                <div class="px-5 sm:px-6 pb-5 sm:pb-6 bg-white/5">
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">

                        {{-- PENDING: Bayar Sekarang button --}}
                        @if($order->status === 'pending' && !$order->isPaymentExpired())
                        <a href="{{ route('order.success', $order) }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-white transition-all duration-300 active:scale-[0.97] touch-manipulation"
                           style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); box-shadow: 0 4px 15px rgba(245,158,11,0.4);">
                            <i class="fas fa-credit-card"></i>
                            <span>Bayar Sekarang</span>
                        </a>
                        @endif

                        {{-- PENDING EXPIRED: Batas Waktu Habis --}}
                        @if($order->status === 'pending' && $order->isPaymentExpired())
                        <div class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-red-300 bg-red-500/15 border border-red-500/30 cursor-not-allowed">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Batas Waktu Habis</span>
                        </div>
                        @endif

                        {{-- WAITING PAYMENT: Menunggu Verifikasi --}}
                        @if($order->status === 'waiting_payment')
                        <a href="{{ route('order.success', $order) }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-orange-300 transition-all duration-300 active:scale-[0.97] touch-manipulation"
                           style="background: rgba(249,115,22,0.15); border: 1px solid rgba(249,115,22,0.3);">
                            <i class="fas fa-hourglass-half"></i>
                            <span>Lihat Status Pembayaran</span>
                        </a>
                        @endif

                        {{-- PAID: Pembayaran Dikonfirmasi --}}
                        @if($order->status === 'paid')
                        <a href="{{ route('order.track', $order) }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-cyan-300 transition-all duration-300 active:scale-[0.97] touch-manipulation"
                           style="background: rgba(6,182,212,0.15); border: 1px solid rgba(6,182,212,0.3);">
                            <i class="fas fa-search"></i>
                            <span>Lacak Pesanan</span>
                        </a>
                        @endif

                        {{-- CONFIRMED / OUT FOR DELIVERY: Lacak Pesanan --}}
                        @if(in_array($order->status, ['confirmed', 'out_for_delivery']))
                        <a href="{{ route('order.track', $order) }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-white transition-all duration-300 active:scale-[0.97] touch-manipulation"
                           style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 15px rgba(6,182,212,0.4);">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Lacak Pesanan</span>
                        </a>
                        @endif

                        {{-- REFUND BUTTON for paid/confirmed (if refund not yet requested) --}}
                        @if(in_array($order->status, ['paid', 'confirmed']) && $order->refund_status === 'none')
                        <button type="button" onclick="openRefundModal({{ $order->id }}, '{{ $order->order_number }}')" 
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-amber-400 transition-all duration-300 active:scale-[0.97] touch-manipulation"
                                style="background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.25);">
                            <i class="fas fa-undo"></i>
                            <span>Ajukan Refund</span>
                        </button>
                        @endif

                        {{-- COMPLETED: Lihat Detail --}}
                        @if($order->status === 'completed')
                        <a href="{{ route('order.track', $order) }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-white transition-all duration-300 active:scale-[0.97] touch-manipulation"
                           style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3);">
                            <i class="fas fa-receipt"></i>
                            <span>Lihat Detail</span>
                        </a>
                        @endif

                        {{-- CANCEL BUTTON for pending/waiting_payment --}}
                        @if(in_array($order->status, ['pending', 'waiting_payment']))
                        <form action="{{ route('order.cancel', $order) }}" method="POST" class="flex-shrink-0"
                              onsubmit="event.preventDefault(); userConfirm(this, 'Batalkan Pesanan', 'Yakin ingin membatalkan pesanan {{ $order->order_number }}? Pesanan yang dibatalkan tidak bisa dikembalikan.', 'danger', 'Ya, Batalkan');">
                            @csrf
                            <button type="submit" 
                                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-red-400 transition-all duration-300 active:scale-[0.97] touch-manipulation"
                                    style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);">
                                <i class="fas fa-times"></i>
                                <span>Batalkan</span>
                            </button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $orders->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-20">
            @if($tab === 'active')
            {{-- Empty State: Pesanan Aktif --}}
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.2);">
                <i class="fas fa-check-circle text-4xl text-mint-400"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Pesanan Aktif</h3>
            <p class="text-white/50 max-w-md mx-auto mb-6">Semua pesanan Anda sudah selesai diproses. Yuk belanja ikan segar lagi!</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('catalog') }}" class="btn-primary">
                    <i class="fas fa-fish"></i> Belanja Lagi
                </a>
                @if($historyCount > 0)
                <a href="{{ route('my.orders', ['tab' => 'history']) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 font-semibold rounded-xl border border-white/20 text-white bg-white/10 backdrop-blur hover:bg-white/15 transition-all">
                    <i class="fas fa-history"></i> Lihat Riwayat
                </a>
                @endif
            </div>
            @else
            {{-- Empty State: Riwayat --}}
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                <i class="fas fa-history text-4xl text-white/40"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Belum Ada Riwayat</h3>
            <p class="text-white/50 max-w-md mx-auto mb-6">Pesanan yang sudah selesai atau dibatalkan akan muncul di sini.</p>
            <a href="{{ route('my.orders', ['tab' => 'active']) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 font-semibold rounded-xl border border-white/20 text-white bg-white/10 backdrop-blur hover:bg-white/15 transition-all">
                <i class="fas fa-shopping-bag"></i> Lihat Pesanan Aktif
            </a>
            @endif
        </div>
        @endif
    </div>
</section>

{{-- Modal Refund --}}
<div id="refundModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);">
    <div class="store-glass-card rounded-2xl max-w-md w-full p-6 sm:p-8 relative animate-scale-in">
        <button type="button" onclick="closeRefundModal()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-all">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="mb-6">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                 style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);">
                <i class="fas fa-undo text-white text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-1">Ajukan Refund</h2>
            <p class="text-sm text-white/60">Pesanan: <span id="refundOrderNumber" class="text-cyan-300 font-semibold"></span></p>
        </div>

        <form id="refundForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="refund_reason" class="block text-sm font-semibold text-white mb-2">
                    Alasan Refund <span class="text-red-400">*</span>
                </label>
                <textarea id="refund_reason" name="refund_reason" rows="4" required
                          class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/40 outline-none resize-none"
                          style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                          placeholder="Jelaskan alasan Anda ingin refund (misal: produk tidak sesuai, berubah pikiran, dll.)"></textarea>
                <p class="text-xs text-white/40 mt-1.5">
                    <i class="fas fa-info-circle"></i> Permintaan refund hanya bisa diajukan sebelum pesanan dikirim
                </p>
            </div>

            <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-3">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5 text-sm"></i>
                    <div>
                        <p class="text-xs font-semibold text-amber-300 mb-0.5">Perhatian:</p>
                        <p class="text-xs text-amber-400/80 leading-relaxed">Admin akan meninjau permintaan Anda. Jika disetujui, dana akan dikembalikan dan stok produk akan direstore.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeRefundModal()" 
                        class="flex-1 px-5 py-3 rounded-xl font-bold text-sm text-white/70 transition-all"
                        style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-5 py-3 rounded-xl font-bold text-sm text-white transition-all active:scale-[0.97]"
                        style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); box-shadow: 0 4px 15px rgba(245,158,11,0.4);">
                    Kirim Permintaan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
.animate-scale-in {
    animation: scale-in 0.2s ease-out;
}
</style>

<script>
function openRefundModal(orderId, orderNumber) {
    const modal = document.getElementById('refundModal');
    const form = document.getElementById('refundForm');
    const orderNumberSpan = document.getElementById('refundOrderNumber');
    
    form.action = `/order/${orderId}/refund`;
    orderNumberSpan.textContent = orderNumber;
    document.getElementById('refund_reason').value = '';
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRefundModal() {
    const modal = document.getElementById('refundModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRefundModal();
    }
});

// Close on backdrop click
document.getElementById('refundModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});
</script>
@endsection
