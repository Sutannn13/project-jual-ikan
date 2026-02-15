@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-5xl mx-auto">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm text-white/50 hover:text-cyan-400 mb-6 transition-colors">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Header --}}
            <div class="dark-glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-2xl font-bold text-cyan-400">{{ $order->order_number }}</p>
                        <p class="text-sm text-white/40">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold
                        {{ match($order->status) {
                            'pending' => 'bg-yellow-500/15 text-yellow-400 border border-yellow-500/20',
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

                {{-- Payment Deadline Warning --}}
                @if($order->status === 'pending' && $order->payment_deadline)
                <div class="mb-4 p-3 rounded-xl {{ $order->isPaymentExpired() ? 'bg-red-500/10 border border-red-500/20' : 'bg-amber-500/10 border border-amber-500/20' }}">
                    <div class="flex items-center gap-2">
                        <i class="fas {{ $order->isPaymentExpired() ? 'fa-times-circle text-red-400' : 'fa-clock text-amber-400' }}"></i>
                        <span class="text-sm font-medium {{ $order->isPaymentExpired() ? 'text-red-300' : 'text-amber-300' }}">
                            @if($order->isPaymentExpired())
                                Batas waktu pembayaran sudah lewat!
                            @else
                                Batas bayar: {{ $order->payment_deadline->format('d M Y, H:i') }} WIB ({{ $order->remaining_time }})
                            @endif
                        </span>
                    </div>
                </div>
                @endif

                {{-- Customer Info --}}
                <div class="rounded-xl p-4 mb-4" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);">
                    <h4 class="text-xs uppercase tracking-wider text-white/40 font-semibold mb-2">Pelanggan</h4>
                    <p class="font-medium text-white">{{ $order->user->name }}</p>
                    <p class="text-sm text-white/50">{{ $order->user->email }}</p>
                    @if($order->user->no_hp)
                    <p class="text-sm text-white/50"><i class="fas fa-phone text-xs mr-1"></i> {{ $order->user->no_hp }}</p>
                    @endif
                    @if($order->user->alamat)
                    <p class="text-sm text-white/50 mt-1"><i class="fas fa-map-marker-alt text-xs mr-1"></i> {{ $order->user->alamat }}</p>
                    @endif
                </div>

                {{-- Items --}}
                <h4 class="text-xs uppercase tracking-wider text-white/40 font-semibold mb-3">Item Pesanan</h4>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between py-3 border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <span class="{{ $item->produk->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }}">{{ $item->produk->kategori }}</span>
                            <div>
                                <p class="font-medium text-white">{{ $item->produk->nama }}</p>
                                <p class="text-xs text-white/40">{{ $item->qty }} Kg × Rp {{ number_format($item->produk->harga_per_kg, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-4 mt-2 border-t-2 border-cyan-500/20">
                    <span class="font-bold text-white text-lg">Total</span>
                    <span class="text-2xl font-extrabold text-cyan-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Payment Proof Section --}}
            <div class="dark-glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-white flex items-center gap-2">
                        <i class="fas fa-receipt text-cyan-400"></i> Bukti Pembayaran
                    </h3>
                    @if($order->payment_method)
                    <span class="text-xs px-2 py-1 rounded-lg {{ $order->payment_method === 'manual_transfer' ? 'bg-blue-500/15 text-blue-400' : 'bg-purple-500/15 text-purple-400' }}">
                        <i class="fas fa-{{ $order->payment_method === 'manual_transfer' ? 'money-bill-wave' : 'credit-card' }} mr-1"></i>
                        {{ $order->payment_method === 'manual_transfer' ? 'Transfer Manual' : ucfirst($order->payment_method) }}
                    </span>
                    @endif
                </div>
                
                @if($order->payment_proof)
                <div class="space-y-4">
                    {{-- Payment Info --}}
                    <div class="flex items-center gap-3 p-3 rounded-xl" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(16,185,129,0.15);">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-emerald-300 text-sm">Bukti pembayaran sudah diupload</p>
                            @if($order->payment_uploaded_at)
                            <p class="text-xs text-emerald-400/70">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $order->payment_uploaded_at->format('d M Y, H:i') }} WIB
                                ({{ $order->payment_uploaded_at->diffForHumans() }})
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Payment Image Preview --}}
                    <div class="rounded-xl overflow-hidden" style="border: 1px solid rgba(255,255,255,0.08);">
                        <div class="px-4 py-2 flex items-center justify-between" style="background: rgba(255,255,255,0.04); border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <span class="text-sm font-medium text-white/60">
                                <i class="fas fa-image mr-1"></i> Preview Bukti Transfer
                            </span>
                            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" 
                               class="text-cyan-400 hover:text-cyan-300 text-sm font-medium transition-colors">
                                <i class="fas fa-external-link-alt mr-1"></i> Buka Full Size
                            </a>
                        </div>
                        <div class="p-4 flex items-center justify-center min-h-[200px]" style="background: rgba(0,0,0,0.2);">
                            <img src="{{ asset('storage/' . $order->payment_proof) }}" 
                                 alt="Bukti Pembayaran {{ $order->order_number }}"
                                 class="max-h-96 rounded-lg shadow-lg cursor-pointer hover:scale-105 transition-transform"
                                 onclick="openImageModal(this.src)">
                        </div>
                    </div>

                    {{-- Verify/Reject Buttons (only show if waiting_payment) --}}
                    @if($order->status === 'waiting_payment')
                    <div class="flex gap-3 pt-2">
                        <form action="{{ route('admin.orders.verify', $order) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full py-3 rounded-xl font-bold text-white transition-all hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16,185,129,0.3);"
                                    onclick="event.preventDefault(); adminConfirm(this.closest('form'), 'Verifikasi Pembayaran', 'Verifikasi pembayaran ini? Status order akan berubah menjadi PAID.', 'success', 'Ya, Verifikasi');">
                                <i class="fas fa-check-circle mr-2"></i> Terima & Verifikasi
                            </button>
                        </form>
                        <button type="button" 
                                onclick="openRejectModal()"
                                class="flex-1 py-3 rounded-xl font-bold text-white bg-red-500 hover:bg-red-600 transition-all hover:scale-[1.02]"
                                style="box-shadow: 0 4px 12px rgba(239,68,68,0.3);">
                            <i class="fas fa-times-circle mr-2"></i> Tolak Bukti
                        </button>
                    </div>
                    @elseif($order->status === 'paid')
                    <div class="p-3 rounded-xl" style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
                        <p class="text-sm text-cyan-300 font-medium">
                            <i class="fas fa-info-circle mr-1"></i> Pembayaran sudah diverifikasi. Silakan konfirmasi pesanan untuk proses selanjutnya.
                        </p>
                    </div>
                    @elseif(in_array($order->status, ['confirmed', 'out_for_delivery', 'completed']))
                    <div class="p-3 rounded-xl" style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2);">
                        <p class="text-sm text-blue-300 font-medium">
                            <i class="fas fa-check mr-1"></i> Pembayaran terverifikasi - Pesanan sedang diproses/selesai.
                        </p>
                    </div>
                    @endif
                </div>
                @else
                {{-- No Payment Proof Yet --}}
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.15);">
                        <i class="fas fa-hourglass-half text-2xl text-amber-400"></i>
                    </div>
                    <p class="font-semibold text-white/70 mb-1">Belum Ada Bukti Pembayaran</p>
                    
                    @if($order->midtrans_snap_token && !$order->payment_method)
                    <p class="text-sm text-white/40">Customer sedang melakukan pembayaran via <span class="text-purple-400">Midtrans</span>.</p>
                    <p class="text-xs text-white/30 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Status akan otomatis update saat Midtrans mengirim notifikasi
                    </p>
                    @else
                    <p class="text-sm text-white/40">Customer belum mengupload bukti transfer.</p>
                    @if($order->status === 'pending')
                    <div class="mt-4 p-3 rounded-xl inline-block" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.15);">
                        <p class="text-sm text-amber-300">
                            <i class="fas fa-info-circle mr-1"></i> Status: Menunggu customer upload bukti bayar
                        </p>
                    </div>
                    @endif
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            {{-- Confirm Order (only show if status is PAID) --}}
            @if($order->status === 'paid')
            <div class="dark-glass-card rounded-2xl p-6" style="border: 2px solid rgba(6,182,212,0.3);">
                <h3 class="font-semibold text-white mb-4">
                    <i class="fas fa-check-circle text-cyan-400 mr-2"></i>Konfirmasi Pesanan
                </h3>
                <div class="mb-4 p-3 rounded-xl" style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.15);">
                    <p class="text-sm text-cyan-300">
                        <i class="fas fa-info-circle mr-1"></i> Pembayaran sudah diverifikasi. Lanjutkan dengan mengisi info pengiriman.
                    </p>
                </div>
                <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label-field">Catatan Pengiriman *</label>
                        <textarea name="delivery_note" class="input-field @error('delivery_note') !border-red-400 @enderror" rows="3"
                                  placeholder="Contoh: Dikirim jam 09:00 WIB via Grab" required>{{ old('delivery_note') }}</textarea>
                        @error('delivery_note') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label-field">Estimasi Waktu Kirim</label>
                        <input type="datetime-local" name="delivery_time" class="input-field" value="{{ old('delivery_time') }}">
                    </div>
                    
                    {{-- Courier Info --}}
                    <div class="pt-3 border-t border-white/10">
                        <p class="text-xs uppercase tracking-wider text-white/40 font-semibold mb-3">
                            <i class="fas fa-motorcycle mr-1"></i> Info Kurir (Opsional)
                        </p>
                        <div class="space-y-3">
                            <div>
                                <label class="label-field">Nama Kurir</label>
                                <input type="text" name="courier_name" class="input-field" placeholder="Contoh: Pak Budi" value="{{ old('courier_name') }}">
                            </div>
                            <div>
                                <label class="label-field">No. HP Kurir</label>
                                <input type="text" name="courier_phone" class="input-field" placeholder="081234567890" value="{{ old('courier_phone') }}">
                            </div>
                            <div>
                                <label class="label-field">No. Resi/Plat</label>
                                <input type="text" name="tracking_number" class="input-field" placeholder="Contoh: B 1234 ABC" value="{{ old('tracking_number') }}">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-mint w-full">
                        <i class="fas fa-check mr-2"></i> Konfirmasi & Proses Pesanan
                    </button>
                </form>
            </div>
            @endif

            {{-- Update Status --}}
            <div class="dark-glass-card rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-4"><i class="fas fa-sync-alt text-cyan-400 mr-2"></i>Ubah Status</h3>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                    @csrf @method('PATCH')
                    <select name="status" class="input-field">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="waiting_payment" {{ $order->status === 'waiting_payment' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Pembayaran Dikonfirmasi</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Pesanan Dikonfirmasi</option>
                        <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Dalam Pengiriman</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    <button type="submit" class="btn-ocean w-full" onclick="event.preventDefault(); adminConfirm(this.closest('form'), 'Ubah Status Pesanan', 'Yakin ubah status pesanan ini? Pelanggan akan menerima notifikasi perubahan status.', 'warning', 'Ya, Update');">
                        <i class="fas fa-save mr-2"></i> Update Status
                    </button>
                </form>
            </div>

            {{-- Delivery & Courier Info --}}
            @if($order->delivery_note || $order->courier_name)
            <div class="rounded-2xl p-5 space-y-4" style="background: rgba(6,182,212,0.08); border: 1px solid rgba(6,182,212,0.15);">
                @if($order->delivery_note)
                <div>
                    <h4 class="font-semibold text-cyan-300 text-sm mb-2"><i class="fas fa-truck mr-1"></i> Info Pengiriman</h4>
                    <p class="text-cyan-200/70 text-sm">{{ $order->delivery_note }}</p>
                    @if($order->delivery_time)
                    <p class="text-cyan-200/60 text-sm mt-2"><i class="fas fa-clock mr-1"></i> {{ $order->delivery_time->format('d M Y, H:i') }} WIB</p>
                    @endif
                </div>
                @endif
                
                @if($order->courier_name)
                <div class="pt-3 border-t border-cyan-500/15">
                    <h4 class="font-semibold text-cyan-300 text-sm mb-2"><i class="fas fa-motorcycle mr-1"></i> Info Kurir</h4>
                    <div class="space-y-1 text-sm text-cyan-200/70">
                        <p><strong>Nama:</strong> {{ $order->courier_name }}</p>
                        @if($order->courier_phone)
                        <p><strong>HP:</strong> <a href="tel:{{ $order->courier_phone }}" class="underline">{{ $order->courier_phone }}</a></p>
                        @endif
                        @if($order->tracking_number)
                        <p><strong>Resi/Plat:</strong> {{ $order->tracking_number }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Order Timeline --}}
            <div class="dark-glass-card rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-4"><i class="fas fa-history text-cyan-400 mr-2"></i>Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(6,182,212,0.15);">
                            <i class="fas fa-cart-plus text-cyan-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Pesanan Dibuat</p>
                            <p class="text-xs text-white/40">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($order->payment_uploaded_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(245,158,11,0.15);">
                            <i class="fas fa-receipt text-amber-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Bukti Bayar Diupload</p>
                            <p class="text-xs text-white/40">{{ $order->payment_uploaded_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    @if(in_array($order->status, ['paid', 'confirmed', 'out_for_delivery', 'completed']))
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(6,182,212,0.15);">
                            <i class="fas fa-check text-cyan-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Pembayaran Diverifikasi</p>
                            <p class="text-xs text-white/40">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-[90vh]">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-2xl">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Bukti Pembayaran" class="max-h-[85vh] rounded-xl shadow-2xl">
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="dark-glass-card rounded-2xl shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(239,68,68,0.15);">
                <i class="fas fa-times-circle text-red-400"></i>
            </div>
            <h3 class="text-lg font-bold text-white">Tolak Bukti Pembayaran</h3>
        </div>
        
        <p class="text-sm text-white/60 mb-4">
            Berikan alasan penolakan agar customer tahu mengapa bukti bayar mereka ditolak dan bisa upload ulang dengan benar.
        </p>
        
        <form action="{{ route('admin.orders.reject', $order) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="label-field">Alasan Penolakan *</label>
                <textarea name="rejection_reason" 
                          class="input-field" 
                          rows="3" 
                          required
                          placeholder="Contoh: Foto tidak jelas, nominal tidak sesuai, bukan transfer ke rekening yang benar..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" 
                        class="flex-1 py-2.5 rounded-xl font-semibold text-white/60 bg-white/10 hover:bg-white/15 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 py-2.5 rounded-xl font-semibold text-white bg-red-500 hover:bg-red-600 transition">
                    <i class="fas fa-times-circle mr-1"></i> Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
            closeRejectModal();
        }
    });
    
    // Close reject modal on outside click
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>
@endpush
@endsection
