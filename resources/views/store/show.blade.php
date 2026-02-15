@extends('layouts.master')

@section('title', $produk->nama)

@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-6 sm:mb-8">
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('home') }}" class="text-white/50 hover:text-cyan-300 transition-colors">Beranda</a></li>
                <li class="text-white/30"><i class="fas fa-chevron-right text-[10px]"></i></li>
                <li><a href="{{ route('catalog') }}" class="text-white/50 hover:text-cyan-300 transition-colors">Katalog</a></li>
                <li class="text-white/30"><i class="fas fa-chevron-right text-[10px]"></i></li>
                <li class="text-cyan-300 font-medium truncate max-w-[150px]">{{ $produk->nama }}</li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
            {{-- Product Image --}}
            <div class="relative">
                <div class="aspect-square rounded-3xl overflow-hidden store-glass-card">
                    @if($produk->foto)
                        <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-white/5">
                            <i class="fas fa-fish text-8xl text-white/20"></i>
                        </div>
                    @endif
                </div>
                
                {{-- Stock Badge --}}
                @if($produk->stok <= 0)
                <div class="absolute top-4 right-4 px-4 py-2 rounded-xl font-bold text-white text-sm"
                     style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); box-shadow: 0 4px 15px rgba(239,68,68,0.4);">
                    <i class="fas fa-times mr-1"></i> Stok Habis
                </div>
                @elseif($produk->stok <= 5)
                <div class="absolute top-4 right-4 px-4 py-2 rounded-xl font-bold text-white text-sm"
                     style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); box-shadow: 0 4px 15px rgba(249,115,22,0.4);">
                    <i class="fas fa-exclamation mr-1"></i> Stok Terbatas
                </div>
                @endif

                {{-- Wishlist Button --}}
                @auth
                <div class="absolute top-4 left-4" 
                     x-data="{ 
                        wishlisted: {{ $isWishlisted ? 'true' : 'false' }}, 
                        loading: false,
                        toggle() {
                            if (this.loading) return;
                            this.loading = true;
                            fetch('{{ route('wishlist.toggle') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ produk_id: {{ $produk->id }} }),
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.wishlisted = data.status === 'added';
                                this.loading = false;
                            })
                            .catch(err => {
                                console.error(err);
                                this.loading = false;
                            });
                        }
                     }">
                    <button @click="toggle()" :disabled="loading"
                            class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 shadow-lg"
                            :class="wishlisted ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-white/90 backdrop-blur text-gray-400 hover:text-red-500 hover:bg-white'"
                            :title="wishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist'">
                        <i class="fas fa-heart text-lg" :class="loading ? 'fa-spinner fa-spin' : 'fa-heart'"></i>
                    </button>
                </div>
                @endauth
            </div>

            {{-- Product Detail --}}
            <div class="flex flex-col">
                {{-- Category Badge --}}
                <span class="{{ $produk->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }} w-fit mb-3">
                    {{ $produk->kategori }}
                </span>
                
                {{-- Product Name --}}
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2">{{ $produk->nama }}</h1>

                {{-- Rating Summary --}}
                @if($produk->review_count > 0)
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($produk->average_rating) ? 'text-amber-400' : 'text-white/20' }}"></i>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-white">{{ $produk->average_rating }}</span>
                    <span class="text-sm text-white/40">({{ $produk->review_count }} review)</span>
                </div>
                @endif
                
                {{-- Price --}}
                <div class="mb-6">
                    <p class="text-3xl sm:text-4xl font-extrabold text-cyan-300">
                        Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}
                        <span class="text-lg text-white/40 font-normal">/Kg</span>
                    </p>
                </div>
                
                {{-- Stock Info --}}
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-xl"
                         style="background: {{ $produk->stok > 5 ? 'rgba(16,185,129,0.15)' : ($produk->stok > 0 ? 'rgba(251,146,60,0.15)' : 'rgba(239,68,68,0.15)') }}; border: 1px solid {{ $produk->stok > 5 ? 'rgba(16,185,129,0.25)' : ($produk->stok > 0 ? 'rgba(251,146,60,0.25)' : 'rgba(239,68,68,0.25)') }};">
                        <i class="fas fa-box {{ $produk->stok > 5 ? 'text-mint-400' : ($produk->stok > 0 ? 'text-amber-400' : 'text-red-400') }}"></i>
                        <span class="font-semibold {{ $produk->stok > 5 ? 'text-mint-300' : ($produk->stok > 0 ? 'text-amber-300' : 'text-red-300') }}">
                            Stok: {{ number_format($produk->stok, 1) }} Kg
                        </span>
                    </div>
                </div>

                {{-- Description --}}
                @if($produk->deskripsi)
                <div class="mb-8">
                    <h3 class="font-bold text-white mb-2">Deskripsi</h3>
                    <p class="text-sm sm:text-base text-white/60 leading-relaxed">{{ $produk->deskripsi }}</p>
                </div>
                @endif

                {{-- Order Section --}}
                <div class="mt-auto">
                    @auth
                        @if($produk->stok > 0)
                        <div class="store-glass-card rounded-2xl p-6">
                            <h3 class="font-bold text-white mb-4">Tambah ke Keranjang</h3>
                            <form action="{{ route('cart.add') }}" method="POST" id="orderForm">
                                @csrf
                                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-white/70 mb-2">Jumlah (Kg)</label>
                                    <div class="flex items-center gap-3">
                                        <button type="button" onclick="decrementQty()" 
                                                class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg text-cyan-300 transition-colors hover:bg-white/10"
                                                style="background: rgba(6,182,212,0.15); border: 1px solid rgba(6,182,212,0.2);">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" name="qty" id="qty" value="0.5" min="0.5" max="{{ $produk->stok }}" step="0.5"
                                               class="w-24 text-center text-lg font-bold text-white bg-white/10 border border-white/15 rounded-xl py-3 focus:outline-none focus:border-white/30 hide-spinner" onchange="updateSubtotal()">
                                        <button type="button" onclick="incrementQty()"
                                                class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg text-white transition-all hover:scale-105"
                                                style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 12px rgba(6,182,212,0.3);">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mb-5 p-4 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-white/60 font-medium">Subtotal:</span>
                                    <span id="subtotal" class="text-xl sm:text-2xl font-extrabold text-cyan-300">
                                        Rp {{ number_format($produk->harga_per_kg * 0.5, 0, ',', '.') }}
                                    </span>
                                </div>

                                <button type="submit" class="btn-primary w-full py-4 text-base btn-shiny">
                                    <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                </button>
                            </form>
                            
                            <a href="{{ route('cart.index') }}" class="block text-center mt-4 text-cyan-400 hover:text-cyan-300 font-medium">
                                <i class="fas fa-shopping-cart mr-1"></i> Lihat Keranjang
                            </a>
                        </div>
                        @else
                        <div class="store-glass-card rounded-2xl p-6 text-center">
                            <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
                                 style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.2);">
                                <i class="fas fa-times text-2xl text-red-400"></i>
                            </div>
                            <h3 class="font-bold text-white mb-2">Stok Habis</h3>
                            <p class="text-white/50 text-sm">Produk ini sedang tidak tersedia. Silakan cek kembali nanti.</p>
                        </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-primary w-full py-4 text-base">
                            <i class="fas fa-sign-in-alt"></i> Login untuk Memesan
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- REVIEWS SECTION --}}
        <div class="mt-12 sm:mt-16" id="reviews">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-white">Review Pelanggan</h2>
                    @if($produk->review_count > 0)
                    <div class="flex items-center gap-3 mt-2">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-lg {{ $i <= round($produk->average_rating) ? 'text-amber-400' : 'text-white/20' }}"></i>
                            @endfor
                        </div>
                        <span class="text-lg font-bold text-white">{{ $produk->average_rating }}</span>
                        <span class="text-white/40">dari {{ $produk->review_count }} review</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Write Review (only for customers who completed an order with this product) --}}
            @auth
            @if($completedOrders->count() > 0)
            <div class="store-glass-card rounded-2xl p-6 mb-8" x-data="{ rating: 0, hoverRating: 0 }">
                <h3 class="font-bold text-white mb-4"><i class="fas fa-pen mr-2 text-cyan-400"></i>Tulis Review</h3>
                <form action="{{ route('review.store', $produk) }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $completedOrders->first()->id }}">
                    
                    {{-- Star Rating --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-white/70 mb-2">Rating</label>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}" 
                                    @mouseenter="hoverRating = {{ $i }}" @mouseleave="hoverRating = 0"
                                    class="text-2xl transition-colors focus:outline-none">
                                <i class="fas fa-star" :class="(hoverRating || rating) >= {{ $i }} ? 'text-amber-400' : 'text-white/20'"></i>
                            </button>
                            @endfor
                            <span class="ml-2 text-sm text-white/50" x-show="rating > 0" x-text="['', 'Buruk', 'Kurang', 'Cukup', 'Bagus', 'Sangat Bagus'][rating]"></span>
                        </div>
                        <input type="hidden" name="rating" x-bind:value="rating">
                        @error('rating')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Comment --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-white/70 mb-2">Komentar (opsional)</label>
                        <textarea name="comment" rows="3" placeholder="Ceritakan pengalaman Anda dengan produk ini..."
                                  class="w-full px-4 py-3.5 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none placeholder-white/40 resize-none">{{ old('comment') }}</textarea>
                    </div>

                    {{-- Order Selection (if multiple orders) --}}
                    @if($completedOrders->count() > 1)
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-white/70 mb-2">Pesanan</label>
                        <select name="order_id" class="w-full px-4 py-3.5 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none">
                            @foreach($completedOrders as $co)
                            <option value="{{ $co->id }}">{{ $co->order_number }} ({{ $co->created_at->format('d M Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <button type="submit" class="btn-primary" :disabled="rating === 0" 
                            :class="{ 'opacity-50 cursor-not-allowed': rating === 0 }">
                        <i class="fas fa-paper-plane"></i> Kirim Review
                    </button>
                </form>
            </div>
            @endif
            @endauth

            {{-- Review List --}}
            @if($produk->reviews->count() > 0)
            <div class="space-y-4">
                @foreach($produk->reviews->sortByDesc('created_at') as $review)
                <div class="card-elevated rounded-2xl p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                 style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                                <i class="fas fa-user text-white/60 text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">{{ $review->user->name }}</h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-amber-400' : 'text-white/20' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-white/40">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        @if(Auth::id() === $review->user_id)
                        <form action="{{ route('review.destroy', $review) }}" method="POST" onsubmit="event.preventDefault(); userConfirm(this, 'Hapus Review', 'Yakin ingin menghapus review kamu?', 'danger', 'Ya, Hapus');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-white/30 hover:text-red-400 transition-colors">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    @if($review->comment)
                    <p class="text-white/60 text-sm mt-3 leading-relaxed">{{ $review->comment }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 card-elevated rounded-2xl">
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
                     style="background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.2);">
                    <i class="fas fa-star text-2xl text-amber-400"></i>
                </div>
                <p class="text-white/60 font-medium">Belum ada review</p>
                <p class="text-white/40 text-sm mt-1">Jadilah yang pertama memberikan review!</p>
            </div>
            @endif
        </div>

        {{-- RELATED PRODUCTS --}}
        @if($relatedProducts->count() > 0)
        <div class="mt-12 sm:mt-16">
            <h2 class="text-2xl font-bold text-white mb-6">Produk Terkait</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($relatedProducts as $related)
                <div class="product-card flex flex-col">
                    <div class="aspect-[4/3] overflow-hidden bg-white/5">
                        @if($related->foto)
                            <img src="{{ asset('storage/' . $related->foto) }}" alt="{{ $related->nama }}"
                                 class="w-full h-full object-cover hover:scale-110 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white/20">
                                <i class="fas fa-fish text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <span class="{{ $related->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }} w-fit text-xs mb-2">{{ $related->kategori }}</span>
                        <h3 class="font-bold text-white text-sm line-clamp-1">{{ $related->nama }}</h3>
                        <p class="text-cyan-300 font-extrabold text-lg mt-auto">
                            Rp {{ number_format($related->harga_per_kg, 0, ',', '.') }}
                            <span class="text-xs text-white/40 font-normal">/Kg</span>
                        </p>
                        <a href="{{ route('produk.show', $related) }}" class="btn-primary mt-3 text-xs py-2">
                            <i class="fas fa-shopping-cart"></i> Beli
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    const hargaPerKg = {{ $produk->harga_per_kg }};
    const maxStok = {{ $produk->stok }};

    function incrementQty() {
        const input = document.getElementById('qty');
        let current = parseFloat(input.value) || 0.5;
        if (current < maxStok) {
            input.value = (current + 0.5).toFixed(1);
            updateSubtotal();
        }
    }

    function decrementQty() {
        const input = document.getElementById('qty');
        let current = parseFloat(input.value) || 0.5;
        if (current > 0.5) {
            input.value = (current - 0.5).toFixed(1);
            updateSubtotal();
        }
    }

    function updateSubtotal() {
        const qty = parseFloat(document.getElementById('qty').value) || 0.5;
        const subtotal = qty * hargaPerKg;
        document.getElementById('subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    }
</script>
@endpush
@endsection
