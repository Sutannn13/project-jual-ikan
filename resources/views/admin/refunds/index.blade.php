@extends('layouts.admin')

@section('title', 'Manajemen Refund')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Permintaan Refund</h1>
            <p class="text-white/50 text-sm mt-1">Kelola permintaan refund dari customer</p>
        </div>
    </div>

    @if($refunds->isEmpty())
        <div class="dark-glass-card rounded-2xl p-12 text-center">
            <i class="fas fa-undo text-5xl text-white/15 mb-4"></i>
            <h3 class="text-lg font-semibold text-white/60">Belum Ada Permintaan Refund</h3>
            <p class="text-white/40 text-sm mt-2">Semua pesanan berjalan lancar!</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($refunds as $order)
            <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <span class="font-bold text-white">{{ $order->order_number }}</span>
                            @php
                                $refundColor = match($order->refund_status) {
                                    'requested' => 'amber',
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    default => 'gray',
                                };
                                $refundLabel = match($order->refund_status) {
                                    'requested' => 'Menunggu',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => '-',
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-{{ $refundColor }}-500/20 text-{{ $refundColor }}-400 border border-{{ $refundColor }}-500/30">
                                {{ $refundLabel }}
                            </span>
                        </div>
                        <p class="text-white/60 text-sm">
                            <i class="fas fa-user text-white/30 mr-1"></i> {{ $order->user->name }}
                            <span class="text-white/30 mx-2">|</span>
                            <i class="fas fa-money-bill text-white/30 mr-1"></i> Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            <span class="text-white/30 mx-2">|</span>
                            <i class="fas fa-clock text-white/30 mr-1"></i> {{ $order->refund_requested_at?->format('d M Y H:i') }}
                        </p>
                        <div class="mt-3 p-3 rounded-xl" style="background: rgba(255,255,255,0.05);">
                            <p class="text-xs text-white/40 uppercase font-semibold mb-1">Alasan Refund:</p>
                            <p class="text-white/70 text-sm">{{ $order->refund_reason }}</p>
                        </div>
                        @if($order->refund_admin_note)
                        <div class="mt-2 p-3 rounded-xl" style="background: rgba(255,255,255,0.03);">
                            <p class="text-xs text-white/40 uppercase font-semibold mb-1">Catatan Admin:</p>
                            <p class="text-white/50 text-sm">{{ $order->refund_admin_note }}</p>
                        </div>
                        @endif
                    </div>

                    @if($order->refund_status === 'requested')
                    <div class="flex sm:flex-col gap-2">
                        <form action="{{ route('admin.refunds.approve', $order) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="admin_note" value="Refund disetujui oleh admin.">
                            <button type="submit" onclick="event.preventDefault(); adminConfirm(this.closest('form'), 'Setujui Refund', 'Setujui refund ini? Stok produk akan dikembalikan secara otomatis.', 'success', 'Ya, Setujui');"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30 transition-all">
                                <i class="fas fa-check mr-1"></i> Setujui
                            </button>
                        </form>
                        <button onclick="document.getElementById('reject-{{ $order->id }}').classList.toggle('hidden')"
                                class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all">
                            <i class="fas fa-times mr-1"></i> Tolak
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Reject form (hidden) --}}
                @if($order->refund_status === 'requested')
                <div id="reject-{{ $order->id }}" class="hidden mt-4 pt-4 border-t border-white/10">
                    <form action="{{ route('admin.refunds.reject', $order) }}" method="POST" class="flex gap-3">
                        @csrf
                        <input type="text" name="admin_note" placeholder="Alasan penolakan refund..." required
                               class="flex-1 px-4 py-2.5 rounded-xl text-sm text-white placeholder-white/30 outline-none"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all">
                            Kirim Penolakan
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $refunds->links() }}
        </div>
    @endif
</div>
@endsection
