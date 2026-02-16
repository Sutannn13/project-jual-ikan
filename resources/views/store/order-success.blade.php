@extends('layouts.master')

@section('title', 'Pesanan Berhasil')

@push('head')
{{-- Midtrans Snap --}}
<script src="{{ config('midtrans.snap_js_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@section('content')
<section class="py-12 sm:py-20 relative overflow-hidden">
    {{-- Decorative Background Elements --}}
    <div class="absolute top-20 left-10 w-64 h-64 rounded-full blur-3xl -z-10 animate-float" style="background: rgba(6,182,212,0.08);"></div>
    <div class="absolute bottom-10 right-10 w-80 h-80 rounded-full blur-3xl -z-10 animate-float" style="animation-delay: 2s; background: rgba(20,184,166,0.08);"></div>

    <div class="max-w-2xl mx-auto px-4 text-center">
        {{-- Success/Status Icon --}}
        @if($order->status === 'pending')
        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 relative group">
            <div class="absolute inset-0 bg-gradient-to-tr from-amber-400 to-orange-400 rounded-full opacity-20 group-hover:opacity-30 transition-all duration-500 animate-pulse"></div>
            <div class="w-20 h-20 bg-gradient-to-tr from-amber-500 to-orange-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-500/30 transform group-hover:scale-110 transition-all duration-300">
                <i class="fas fa-credit-card text-4xl text-white"></i>
            </div>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 tracking-tight">
            Pesanan Berhasil Dibuat!
        </h1>
        <p class="text-lg text-white/50 mb-8 max-w-lg mx-auto">
            Silakan lakukan pembayaran dan upload bukti transfer untuk memproses pesanan Anda.
        </p>
        @elseif($order->status === 'waiting_payment')
        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 relative group">
            <div class="absolute inset-0 bg-gradient-to-tr from-orange-400 to-amber-400 rounded-full opacity-20 group-hover:opacity-30 transition-all duration-500 animate-pulse"></div>
            <div class="w-20 h-20 bg-gradient-to-tr from-orange-500 to-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-orange-500/30 transform group-hover:scale-110 transition-all duration-300">
                <i class="fas fa-hourglass-half text-4xl text-white"></i>
            </div>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 tracking-tight">
            Menunggu Verifikasi
        </h1>
        <p class="text-lg text-white/50 mb-8 max-w-lg mx-auto">
            Bukti pembayaran sudah diupload. Admin kami akan segera memverifikasi.
        </p>
        @else
        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 relative group">
            <div class="absolute inset-0 bg-gradient-to-tr from-mint-400 to-teal-400 rounded-full opacity-20 group-hover:opacity-30 transition-all duration-500 animate-pulse"></div>
            <div class="w-20 h-20 bg-gradient-to-tr from-mint-500 to-teal-500 rounded-full flex items-center justify-center shadow-lg shadow-mint-500/30 transform group-hover:scale-110 transition-all duration-300">
                <i class="fas fa-check text-4xl text-white"></i>
            </div>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">
            Pembayaran Dikonfirmasi!
        </h1>
        <p class="text-lg text-white/50 mb-8 max-w-lg mx-auto">
            Pesanan Anda sedang diproses. Pantau status melalui halaman lacak pesanan.
        </p>
        @endif

        {{-- Order Number Card --}}
        <div class="store-glass-card rounded-3xl p-8 mb-10 transform hover:-translate-y-1 transition-all duration-300">
            <p class="text-xs uppercase tracking-widest text-white/40 font-bold mb-2">Nomor Pesanan</p>
            <p class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-cyan-300 to-teal-300 bg-clip-text text-transparent mb-6">
                {{ $order->order_number }}
            </p>
            
            {{-- Countdown Timer (only for pending status) --}}
            @if($order->status === 'pending' && $order->payment_deadline)
            <div class="mb-6">
                @if($order->isPaymentExpired())
                <div class="rounded-2xl p-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);">
                    <div class="flex items-center justify-center gap-2 text-red-400">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                        <span class="font-bold">Waktu Pembayaran Habis!</span>
                    </div>
                    <p class="text-sm text-red-300/70 mt-2">Pesanan akan otomatis dibatalkan. Silakan buat pesanan baru.</p>
                </div>
                @else
                <div class="rounded-2xl p-4" style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2);">
                    <p class="text-xs uppercase tracking-wider text-amber-400 font-bold mb-2">
                        <i class="fas fa-clock mr-1"></i> Selesaikan Pembayaran Dalam
                    </p>
                    <div id="countdown" class="flex items-center justify-center gap-2">
                        <div class="rounded-xl px-4 py-2" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                            <span id="hours" class="text-2xl font-black text-amber-400">00</span>
                            <p class="text-[10px] text-white/40 uppercase">Jam</p>
                        </div>
                        <span class="text-2xl font-bold text-amber-500/50">:</span>
                        <div class="rounded-xl px-4 py-2" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                            <span id="minutes" class="text-2xl font-black text-amber-400">00</span>
                            <p class="text-[10px] text-white/40 uppercase">Menit</p>
                        </div>
                        <span class="text-2xl font-bold text-amber-500/50">:</span>
                        <div class="rounded-xl px-4 py-2" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                            <span id="seconds" class="text-2xl font-black text-amber-400">00</span>
                            <p class="text-[10px] text-white/40 uppercase">Detik</p>
                        </div>
                    </div>
                    <p class="text-xs text-amber-400/60 mt-3">
                        Batas: {{ $order->payment_deadline->format('d M Y, H:i') }} WIB
                    </p>
                </div>
                @endif
            </div>
            @endif
            
            <div class="w-full h-px my-6" style="background: linear-gradient(to right, transparent, rgba(255,255,255,0.15), transparent);"></div>

            {{-- Order Summary --}}
            <div class="text-left space-y-4">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <i class="fas fa-receipt text-cyan-400"></i> Ringkasan Pesanan
                </h3>
                <div class="rounded-2xl p-4 space-y-3" style="background: rgba(255,255,255,0.05);">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                                @if($item->produk->foto)
                                    <img src="{{ asset('storage/'.$item->produk->foto) }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                    <i class="fas fa-fish text-white/30 text-xs"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-white text-sm">{{ $item->produk->nama }}</p>
                                <p class="text-xs text-white/40">{{ $item->qty }} Kg</p>
                            </div>
                        </div>
                        <span class="font-bold text-white/70 text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-2">
                    <span class="text-white/50 font-medium">Total Pembayaran</span>
                    <span class="text-2xl font-black text-cyan-300">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Rejection Reason Alert (if payment was rejected) --}}
        @if($order->status === 'pending' && $order->rejection_reason)
        <div class="rounded-2xl p-5 mb-8 text-left" style="background: rgba(239,68,68,0.1); border: 2px solid rgba(239,68,68,0.2);">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(239,68,68,0.15);">
                    <i class="fas fa-times-circle text-red-400 text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-300 mb-1">Bukti Pembayaran Ditolak</h4>
                    <p class="text-sm text-red-300/70 leading-relaxed">{{ $order->rejection_reason }}</p>
                    <p class="text-xs text-red-400/50 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Silakan upload ulang bukti pembayaran yang benar.
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Payment Section (Show if pending OR if status is waiting_payment but payment was rejected) --}}
        @if($order->status === 'pending' || ($order->status === 'waiting_payment' && $order->wasRejected()))
        
        {{-- E-Wallet Auto-Trigger Info (if ewallet was selected) --}}
        @if($order->payment_method === 'ewallet_pending')
        <div class="store-glass-card rounded-3xl p-8 mb-10 text-center">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background: rgba(139,92,246,0.15); border: 1px solid rgba(139,92,246,0.3);">
                <i class="fas fa-wallet text-4xl text-violet-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Pembayaran E-Wallet</h3>
            <p class="text-white/60 mb-6">Popup pembayaran akan muncul otomatis...</p>
            
            <button onclick="payWithMidtrans()"
                    id="pay-button"
                    class="mx-auto flex items-center justify-center gap-3 px-8 py-4 rounded-xl font-bold text-white transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]"
                    style="background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%); box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);">
                <i class="fas fa-bolt text-xl"></i>
                <span class="text-lg">Bayar Sekarang</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-xs px-2 py-1 rounded-lg bg-white/20">Dana</span>
                    <span class="text-xs px-2 py-1 rounded-lg bg-white/20">GoPay</span>
                    <span class="text-xs px-2 py-1 rounded-lg bg-white/20">QRIS</span>
                </div>
            </button>
            
            <div class="rounded-xl p-4 mt-6 text-left" style="background: rgba(139,92,246,0.08); border: 1px solid rgba(139,92,246,0.15);">
                <p class="text-sm text-violet-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Pembayaran otomatis & instan!</strong> Setelah Anda membayar, pesanan akan langsung dikonfirmasi tanpa perlu menunggu verifikasi admin.
                </p>
            </div>

            {{-- OR switch to manual --}}
            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px" style="background: linear-gradient(to right, transparent, rgba(255,255,255,0.15), transparent);"></div>
                <button onclick="document.getElementById('manual-transfer-section').classList.toggle('hidden')" 
                        class="text-xs font-semibold text-white/40 hover:text-white/60 uppercase tracking-wider">
                    Atau transfer manual
                </button>
                <div class="flex-1 h-px" style="background: linear-gradient(to right, rgba(255,255,255,0.15), transparent);"></div>
            </div>
        </div>

        {{-- Manual Transfer Section (Hidden by default if ewallet) --}}
        <div id="manual-transfer-section" class="hidden">
        @endif

        @if($order->payment_method !== 'ewallet_pending')
        <div class="store-glass-card rounded-3xl p-8 mb-10 text-left">
        @else
        <div class="store-glass-card rounded-3xl p-8 mb-10 text-left">
        @endif
            {{-- Bank Account Info --}}
            <h3 class="font-bold text-white flex items-center gap-2 mb-4">
                <i class="fas fa-university text-cyan-400"></i> Transfer ke Rekening
            </h3>
            <div class="rounded-2xl p-5 mb-6" style="background: rgba(6,182,212,0.08); border: 1px solid rgba(6,182,212,0.15);">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                        <i class="fas fa-building text-2xl text-cyan-400"></i>
                    </div>
                    <div>
                        <p class="text-sm text-white/50">Bank BCA</p>
                        <p class="text-2xl font-black text-white tracking-wide">1234567890</p>
                        <p class="text-sm font-semibold text-white/70">a.n. FishMarket Indonesia</p>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl p-4" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                    <div>
                        <p class="text-xs text-white/40">Jumlah Transfer</p>
                        <p class="text-xl font-black text-cyan-300">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                    <button onclick="copyToClipboard('{{ $order->total_price }}')" class="text-cyan-400 hover:text-cyan-300 text-sm font-semibold">
                        <i class="fas fa-copy mr-1"></i> Salin
                    </button>
                </div>
            </div>

            {{-- Upload Form --}}
            <h3 class="font-bold text-white flex items-center gap-2 mb-4">
                <i class="fas fa-upload text-cyan-400"></i> Upload Bukti Transfer
            </h3>
            <form action="{{ route('order.payment', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-white/15 rounded-2xl p-6 text-center hover:border-cyan-400/30 transition-colors mb-4">
                    <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required
                           class="hidden" onchange="previewImage(event)">
                    <label for="payment_proof" class="cursor-pointer">
                        <div id="upload-placeholder">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: rgba(6,182,212,0.12); border: 1px solid rgba(6,182,212,0.2);">
                                <i class="fas fa-cloud-upload-alt text-2xl text-cyan-400"></i>
                            </div>
                            <p class="font-semibold text-white/70 mb-1">Klik untuk upload bukti transfer</p>
                            <p class="text-sm text-white/40">Format: JPG, PNG (Maks. 5MB)</p>
                        </div>
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" src="" alt="Preview" class="max-h-48 mx-auto rounded-xl shadow-lg">
                            <p class="text-sm text-white/40 mt-3">Klik untuk ganti gambar</p>
                        </div>
                    </label>
                </div>

                {{-- Client-side file size error --}}
                <div id="file-size-error" class="hidden rounded-xl p-3 text-red-400 text-sm font-medium mb-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);">
                </div>

                @error('payment_proof')
                <div class="rounded-xl p-3 mb-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);">
                    <p class="text-red-400 text-sm font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                </div>
                @enderror

                <button type="submit" id="submit-payment-btn" class="btn-primary w-full py-4 text-base btn-shiny" {{ $order->isPaymentExpired() ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Bukti Pembayaran
                </button>
            </form>
        </div>

        @if($order->payment_method === 'ewallet_pending')
        </div> {{-- Close manual-transfer-section --}}
        @endif

        {{-- Cancel Order Option --}}
        <div class="flex items-center justify-center gap-2 text-white/40 mb-8">
            <span class="text-sm">Ingin membatalkan?</span>
            <form action="{{ route('order.cancel', $order) }}" method="POST" 
                  onsubmit="event.preventDefault(); userConfirm(this, 'Batalkan Pesanan', 'Yakin ingin membatalkan pesanan ini? Pesanan yang dibatalkan tidak bisa dikembalikan.', 'danger', 'Ya, Batalkan');">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-semibold hover:underline">
                    Batalkan Pesanan
                </button>
            </form>
        </div>

        @elseif($order->status === 'waiting_payment')
        {{-- Waiting Verification Info --}}
        <div class="flex items-start gap-3 rounded-2xl p-4 text-left mb-8" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" style="background: rgba(245,158,11,0.15);">
                <i class="fas fa-hourglass-half text-amber-400"></i>
            </div>
            <div>
                <p class="font-bold text-white text-sm">Menunggu Verifikasi Admin</p>
                <p class="text-sm text-white/60 leading-relaxed mt-1">
                    Bukti pembayaran sudah kami terima pada {{ $order->payment_uploaded_at->format('d M Y, H:i') }}. 
                    Tim kami akan memverifikasi dalam waktu 1x24 jam.
                </p>
            </div>
        </div>

        {{-- Show uploaded proof --}}
        @if($order->payment_proof)
        <div class="store-glass-card rounded-3xl p-6 mb-10">
            <h3 class="font-bold text-white flex items-center gap-2 mb-4">
                <i class="fas fa-image text-cyan-400"></i> Bukti Pembayaran Anda
            </h3>
            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran" 
                 class="max-h-64 mx-auto rounded-xl shadow-lg">
        </div>
        @endif
        @else
        {{-- Already Paid Info --}}
        <div class="flex items-start gap-3 rounded-2xl p-4 text-left mb-8" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" style="background: rgba(16,185,129,0.15);">
                <i class="fas fa-check text-emerald-400"></i>
            </div>
            <div>
                <p class="font-bold text-white text-sm">Pembayaran Terverifikasi</p>
                <p class="text-sm text-white/60 leading-relaxed mt-1">
                    Pesanan Anda sedang diproses. Pantau status melalui halaman lacak pesanan.
                </p>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('order.track', $order) }}" 
               class="btn-primary px-8 py-3.5 rounded-xl shadow-lg shadow-ocean-500/30 hover:shadow-ocean-500/50 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fas fa-map-marker-alt"></i> Lacak Pesanan
            </a>
            <a href="{{ route('catalog') }}" 
               class="px-8 py-3.5 rounded-xl border-2 border-white/15 text-white/80 font-bold hover:bg-white/10 hover:border-white/25 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-shopping-bag"></i> Belanja Lagi
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    // Midtrans Payment Function
    function payWithMidtrans() {
        const payButton = document.getElementById('pay-button');
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

        // Request snap token from server
        fetch("{{ route('payment.snap', $order) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                userToast('Gagal', data.error, 'error');
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-bolt mr-2"></i>Bayar Sekarang';
                return;
            }

            // Open Midtrans Snap Popup
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    userToast('Pembayaran Berhasil!', 'Pesanan Anda sedang diproses.', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    userToast('Menunggu Pembayaran', 'Silakan selesaikan pembayaran Anda.', 'info');
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-bolt mr-2"></i>Bayar Sekarang';
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    userToast('Pembayaran Gagal', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-bolt mr-2"></i>Bayar Sekarang';
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-bolt mr-2"></i>Bayar Sekarang';
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            userToast('Error', 'Terjadi kesalahan koneksi.', 'error');
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-bolt mr-2"></i>Bayar Sekarang';
        });
    }

    function previewImage(event) {
        const input = event.target;
        const placeholder = document.getElementById('upload-placeholder');
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const fileSizeError = document.getElementById('file-size-error');
        const submitBtn = document.getElementById('submit-payment-btn');

        // Reset error state
        if (fileSizeError) fileSizeError.classList.add('hidden');
        if (submitBtn) submitBtn.disabled = false;

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);

            // Client-side file size validation
            if (file.size > MAX_FILE_SIZE) {
                if (fileSizeError) {
                    fileSizeError.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Ukuran file ' + fileSizeMB + 'MB terlalu besar! Maksimal 5MB. Coba kompres gambar terlebih dahulu.';
                    fileSizeError.classList.remove('hidden');
                }
                if (submitBtn) submitBtn.disabled = true;
                input.value = '';
                placeholder.classList.remove('hidden');
                preview.classList.add('hidden');
                return;
            }

            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                if (fileSizeError) {
                    fileSizeError.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Format file tidak didukung! Gunakan format JPG atau PNG.';
                    fileSizeError.classList.remove('hidden');
                }
                if (submitBtn) submitBtn.disabled = true;
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            userToast('Berhasil Disalin!', 'Jumlah berhasil disalin ke clipboard.', 'success');
        });
    }

    // Countdown Timer
    @if($order->status === 'pending' && $order->payment_deadline && !$order->isPaymentExpired())
    (function() {
        const deadline = new Date("{{ $order->payment_deadline->toISOString() }}").getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = deadline - now;
            
            if (distance < 0) {
                document.getElementById('countdown').innerHTML = '<span class="text-red-600 font-bold">Waktu Habis!</span>';
                clearInterval(timer);
                // Reload page setelah 3 detik
                setTimeout(() => location.reload(), 3000);
                return;
            }
            
            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }
        
        updateCountdown();
        const timer = setInterval(updateCountdown, 1000);
    })();
    @endif

    // Auto-trigger E-Wallet payment popup if ewallet was selected
    @if($order->payment_method === 'ewallet_pending' && $order->status === 'pending' && !$order->isPaymentExpired())
    // Wait for page load
    window.addEventListener('load', function() {
        // Wait 1 second for smooth UX
        setTimeout(function() {
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                // Auto trigger payment
                payWithMidtrans();
            }
        }, 1000);
    });
    @endif
</script>
@endpush
@endsection
