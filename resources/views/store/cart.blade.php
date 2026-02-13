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
            <div class="lg:hidden">
                <div class="store-glass-card rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-white/60 text-sm">Total ({{ count($cartItems) }} produk)</p>
                            <p class="text-xl font-extrabold text-cyan-300">Rp {{ number_format($total, 0, ',', '.') }}</p>
                        </div>
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary py-3 px-6 text-sm btn-shiny">
                                <i class="fas fa-credit-card mr-2"></i>Checkout
                            </button>
                        </form>
                    </div>
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
                                <form action="{{ route('cart.update', $item['produk']->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" onclick="decrementQty(this)" 
                                            class="w-9 h-9 rounded-lg flex items-center justify-center text-ocean-600 hover:bg-ocean-100 transition-colors"
                                            style="background: linear-gradient(135deg, rgba(6,182,212,0.1) 0%, rgba(20,184,166,0.05) 100%);">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <input type="number" name="qty" value="{{ $item['qty'] }}" 
                                           min="0.5" max="{{ $item['produk']->stok }}" step="0.5"
                                           class="w-16 text-center input-premium text-sm font-bold py-2 hide-spinner"
                                           onchange="this.form.submit()">
                                    <button type="button" onclick="incrementQty(this, {{ $item['produk']->stok }})"
                                            class="w-9 h-9 rounded-lg flex items-center justify-center text-white transition-all hover:scale-105"
                                            style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);">
                                        <i class="fas fa-plus text-sm"></i>
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

                {{-- Clear Cart --}}
                <div class="flex justify-end">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-500 hover:text-red-500 text-sm transition-colors">
                            <i class="fas fa-trash mr-1"></i> Kosongkan Keranjang
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

                    <form action="{{ route('checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full py-4 text-base btn-shiny">
                            <i class="fas fa-credit-card mr-2"></i>Checkout Sekarang
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
