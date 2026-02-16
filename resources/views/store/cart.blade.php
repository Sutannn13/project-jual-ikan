@extends('layouts.master')

@section('title', 'Keranjang Belanja')

@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center sm:text-left mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">
                <i class="fas fa-shopping-cart text-cyan-400 mr-2"></i>Keranjang Belanja
            </h1>
            <p class="text-white/60 mt-1">Review pesanan Anda sebelum checkout</p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 rounded-xl" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3);">
            <span class="text-mint-300"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 rounded-xl" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);">
            <span class="text-red-300"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
        </div>
        @endif

        @if(count($cartItems) > 0)
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Mobile Checkout Bar (visible only on mobile/tablet, shown ABOVE items) --}}
            <div class="lg:hidden" x-data="{ mobilePayMethod: 'transfer' }">
                <div class="store-glass-card rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-white/60 text-sm">Total ({{ count($cartItems) }} produk)</p>
                            <p class="text-xl font-extrabold text-cyan-300">Rp {{ number_format($total, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="mobile_pm" value="transfer" x-model="mobilePayMethod" class="sr-only peer">
                            <div class="text-center py-2 rounded-lg border text-xs font-medium transition-all peer-checked:bg-cyan-500/20 peer-checked:border-cyan-500/50 peer-checked:text-cyan-300 border-white/10 text-white/50">
                                <i class="fas fa-university block mb-1"></i>Transfer
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="mobile_pm" value="ewallet" x-model="mobilePayMethod" class="sr-only peer">
                            <div class="text-center py-2 rounded-lg border text-xs font-medium transition-all peer-checked:bg-violet-500/20 peer-checked:border-violet-500/50 peer-checked:text-violet-300 border-white/10 text-white/50">
                                <i class="fas fa-wallet block mb-1"></i>E-Wallet
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="mobile_pm" value="cod" x-model="mobilePayMethod" class="sr-only peer">
                            <div class="text-center py-2 rounded-lg border text-xs font-medium transition-all peer-checked:bg-emerald-500/20 peer-checked:border-emerald-500/50 peer-checked:text-emerald-300 border-white/10 text-white/50">
                                <i class="fas fa-hand-holding-usd block mb-1"></i>COD
                            </div>
                        </label>
                    </div>
                    <form action="{{ route('checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" :value="mobilePayMethod">
                        <button type="submit" class="btn-primary w-full py-3 text-sm btn-shiny">
                            <i class="fas fa-credit-card mr-2"></i>
                            <span x-text="mobilePayMethod === 'cod' ? 'Pesan COD' : (mobilePayMethod === 'ewallet' ? 'Bayar E-Wallet' : 'Checkout')">Checkout</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="card-elevated rounded-2xl p-5 sm:p-6">
                    <div class="flex gap-4">
                        {{-- Product Image --}}
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden flex-shrink-0 bg-white/10">
                            @if($item['produk']->foto)
                                <img src="{{ asset('storage/' . $item['produk']->foto) }}" 
                                     alt="{{ $item['produk']->nama }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-fish text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Product Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-white truncate">{{ $item['produk']->nama }}</h3>
                                    <span class="{{ $item['produk']->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }} text-xs mt-1">
                                        {{ $item['produk']->kategori }}
                                    </span>
                                    <p class="text-sm text-white/50 mt-1">
                                        Rp {{ number_format($item['produk']->harga_per_kg, 0, ',', '.') }}/Kg
                                    </p>
                                </div>
                                <p class="font-extrabold text-lg text-white">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Quantity Controls --}}
                            <div class="flex items-center justify-between mt-4">
                                <form action="{{ route('cart.update', $item['produk']->id) }}" method="POST" class="flex items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    {{-- Button Minus (Kurangi) --}}
                                    <button type="button" onclick="decrementQty(this)" 
                                            class="w-10 h-10 rounded-lg flex items-center justify-center text-white transition-all hover:scale-105 shadow-md"
                                            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                        <i class="fas fa-minus text-sm font-bold"></i>
                                    </button>
                                    {{-- Input Kuantitas --}}
                                    <input type="number" name="qty" value="{{ $item['qty'] }}" 
                                           min="0.5" max="{{ $item['produk']->stok }}" step="0.5"
                                           class="w-20 text-center text-lg font-extrabold py-2 rounded-lg hide-spinner"
                                           style="background: rgba(255,255,255,0.15); color: #fff; border: 2px solid rgba(6,182,212,0.4);"
                                           onchange="this.form.submit()">
                                    {{-- Button Plus (Tambah) --}}
                                    <button type="button" onclick="incrementQty(this, {{ $item['produk']->stok }})"
                                            class="w-10 h-10 rounded-lg flex items-center justify-center text-white transition-all hover:scale-105 shadow-md"
                                            style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);">
                                        <i class="fas fa-plus text-sm font-bold"></i>
                                    </button>
                                </form>

                                <form action="{{ route('cart.remove', $item['produk']->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 hover:bg-red-500/10 px-3 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i>
                                        <span class="hidden sm:inline">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Clear Cart Button (Hapus Semua Item Sekaligus) --}}
                <div class="flex justify-end mt-2">
                    <form action="{{ route('cart.clear') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin mengosongkan semua keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white transition-all hover:scale-105 shadow-md"
                                style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                            <i class="fas fa-trash-alt mr-2"></i>Kosongkan Semua
                        </button>
                    </form>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="lg:col-span-1">
                <div class="store-glass-card rounded-2xl p-6 sticky top-24">
                    <h3 class="font-bold text-white text-lg mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-white/60">
                            <span>Jumlah Item</span>
                            <span class="font-semibold text-white/80">{{ count($cartItems) }} produk</span>
                        </div>
                        <div class="flex justify-between text-white/60">
                            <span>Subtotal</span>
                            <span class="font-semibold text-white/80">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <hr class="border-white/10">
                        <div class="flex justify-between text-lg">
                            <span class="font-bold text-white">Total</span>
                            <span class="font-extrabold text-cyan-300">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('checkout') }}" method="POST" x-data="{ payMethod: 'transfer' }">
                        @csrf
                        
                        {{-- Payment Method Selection --}}
                        <div class="mb-5">
                            <p class="text-sm font-semibold text-white mb-3">Metode Pembayaran</p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                       :class="payMethod === 'transfer' ? 'border-cyan-500/50 bg-cyan-500/10' : 'border-white/10 hover:border-white/20'">
                                    <input type="radio" name="payment_method" value="transfer" x-model="payMethod" class="text-cyan-500 focus:ring-cyan-500">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-white">Transfer Bank</span>
                                        <p class="text-xs text-white/40">Upload bukti bayar setelah checkout</p>
                                    </div>
                                    <i class="fas fa-university text-white/30"></i>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                       :class="payMethod === 'ewallet' ? 'border-violet-500/50 bg-violet-500/10' : 'border-white/10 hover:border-white/20'">
                                    <input type="radio" name="payment_method" value="ewallet" x-model="payMethod" class="text-violet-500 focus:ring-violet-500">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-white">E-Wallet (Dana, GoPay, QRIS)</span>
                                        <p class="text-xs text-white/40">⚡ Pembayaran otomatis & instan</p>
                                    </div>
                                    <i class="fas fa-wallet text-white/30"></i>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                       :class="payMethod === 'cod' ? 'border-emerald-500/50 bg-emerald-500/10' : 'border-white/10 hover:border-white/20'">
                                    <input type="radio" name="payment_method" value="cod" x-model="payMethod" class="text-emerald-500 focus:ring-emerald-500">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-white">COD (Bayar di Tempat)</span>
                                        <p class="text-xs text-white/40">Bayar saat barang diterima</p>
                                    </div>
                                    <i class="fas fa-hand-holding-usd text-white/30"></i>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary w-full py-4 text-base btn-shiny">
                            <i class="fas fa-credit-card mr-2"></i>
                            <span x-text="payMethod === 'cod' ? 'Pesan COD' : (payMethod === 'ewallet' ? 'Bayar dengan E-Wallet' : 'Checkout Sekarang')">Checkout Sekarang</span>
                        </button>
                    </form>

                    <a href="{{ route('catalog') }}" class="block text-center mt-4 text-cyan-400 hover:text-cyan-300 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Lanjutkan Belanja
                    </a>
                </div>
            </div>
        </div>

        @else
        {{-- Empty Cart --}}
        <div class="text-center py-20">
            <div class="w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background: rgba(6,182,212,0.12); border: 1px solid rgba(6,182,212,0.2);">
                <i class="fas fa-shopping-cart text-5xl text-cyan-400"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Keranjang Kosong</h3>
            <p class="text-white/50 max-w-md mx-auto mb-6">
                Anda belum menambahkan produk apapun ke keranjang. Yuk mulai belanja ikan segar!
            </p>
            <a href="{{ route('catalog') }}" class="btn-primary">
                <i class="fas fa-fish mr-2"></i> Lihat Katalog
            </a>
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    function incrementQty(button, maxStok) {
        const form = button.closest('form');
        const input = form.querySelector('input[name="qty"]');
        let current = parseFloat(input.value) || 0.5;
        if (current < maxStok) {
            input.value = (current + 0.5).toFixed(1);
            form.submit();
        }
    }

    function decrementQty(button) {
        const form = button.closest('form');
        const input = form.querySelector('input[name="qty"]');
        let current = parseFloat(input.value) || 0.5;
        if (current > 0.5) {
            input.value = (current - 0.5).toFixed(1);
            form.submit();
        }
    }
</script>
@endpush
@endsection
