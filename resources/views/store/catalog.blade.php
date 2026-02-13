@extends('layouts.master')

@section('title', 'Katalog Produk')

@section('content')
{{-- PAGE HEADER --}}
<section class="py-8 sm:py-10 relative overflow-hidden">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center sm:text-left">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Katalog Produk</h1>
            <p class="text-white/60 mt-1">Pilih ikan segar berkualitas untuk keluarga Anda</p>
        </div>
    </div>
</section>

{{-- FILTERS & SEARCH --}}
<section class="pb-6 sm:pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('catalog') }}" method="GET" x-data="{ showAdvanced: {{ request('min_price') || request('max_price') || request('sort') ? 'true' : 'false' }} }">
            <div class="store-glass-card rounded-2xl p-4 sm:p-5">
                {{-- Main Search Row --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-white/40"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau deskripsi produk..."
                               class="w-full px-4 py-3.5 pl-11 rounded-xl text-sm transition-all duration-300 placeholder-white/40 text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none focus:ring-0">
                    </div>
                    <div class="flex gap-3">
                        <select name="kategori" class="px-4 py-3.5 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none sm:w-44">
                            <option value="" class="bg-gray-800">Semua Kategori</option>
                            <option value="Lele" class="bg-gray-800" {{ request('kategori') === 'Lele' ? 'selected' : '' }}>Lele</option>
                            <option value="Mas" class="bg-gray-800" {{ request('kategori') === 'Mas' ? 'selected' : '' }}>Ikan Mas</option>
                        </select>
                        <button type="button" @click="showAdvanced = !showAdvanced" 
                                class="px-4 py-2.5 rounded-xl text-sm font-medium border transition-all"
                                :class="showAdvanced ? 'bg-white/20 text-white border-white/30' : 'bg-white/5 text-white/70 border-white/15 hover:bg-white/10'">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                        <button type="submit" class="btn-primary px-6">
                            <i class="fas fa-search"></i>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                </div>

                {{-- Advanced Filters --}}
                <div x-show="showAdvanced" x-collapse class="mt-4 pt-4 border-t border-white/10">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Price Range --}}
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-1.5">Harga Minimum</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40 text-sm">Rp</span>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       placeholder="{{ isset($priceRange) ? number_format($priceRange->min_price, 0) : '0' }}"
                                       class="w-full px-4 py-3.5 pl-10 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none placeholder-white/30" min="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-1.5">Harga Maksimum</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40 text-sm">Rp</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       placeholder="{{ isset($priceRange) ? number_format($priceRange->max_price, 0) : '0' }}"
                                       class="w-full px-4 py-3.5 pl-10 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none placeholder-white/30" min="0">
                            </div>
                        </div>
                        
                        {{-- Sort --}}
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-1.5">Urutkan</label>
                            <select name="sort" class="w-full px-4 py-3.5 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-white/30 focus:outline-none">
                                <option value="" class="bg-gray-800">Terbaru</option>
                                <option value="price_low" class="bg-gray-800" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Harga: Rendah → Tinggi</option>
                                <option value="price_high" class="bg-gray-800" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Harga: Tinggi → Rendah</option>
                                <option value="name_az" class="bg-gray-800" {{ request('sort') === 'name_az' ? 'selected' : '' }}>Nama: A → Z</option>
                                <option value="popular" class="bg-gray-800" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="rating" class="bg-gray-800" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Active Filter Tags --}}
                    @if(request('min_price') || request('max_price') || request('sort'))
                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                        <span class="text-xs text-white/50">Filter aktif:</span>
                        @if(request('min_price'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-500/20 text-cyan-300">
                            Min: Rp {{ number_format(request('min_price'), 0, ',', '.') }}
                        </span>
                        @endif
                        @if(request('max_price'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-500/20 text-cyan-300">
                            Max: Rp {{ number_format(request('max_price'), 0, ',', '.') }}
                        </span>
                        @endif
                        @if(request('sort'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-500/20 text-cyan-300">
                            {{ ['price_low' => 'Harga Rendah', 'price_high' => 'Harga Tinggi', 'name_az' => 'Nama A-Z', 'popular' => 'Terpopuler', 'rating' => 'Rating'][request('sort')] ?? request('sort') }}
                        </span>
                        @endif
                        <a href="{{ route('catalog') }}" class="text-xs text-red-400 hover:text-red-300 font-medium ml-1">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

{{-- PRODUCT GRID --}}
<section class="pb-12 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($produks->count() > 0)
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($produks as $produk)
            <div class="product-card flex flex-col group">
                {{-- Image --}}
                <div class="aspect-[4/3] overflow-hidden relative bg-white/5">
                    @if($produk->foto)
                        <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/20">
                            <i class="fas fa-fish text-4xl sm:text-5xl"></i>
                        </div>
                    @endif
                    
                    {{-- Stock Badge Overlay --}}
                    @if($produk->stok <= 0)
                    <div class="absolute inset-0 bg-gray-900/60 flex items-center justify-center">
                        <span class="px-4 py-2 bg-red-500 text-white font-bold rounded-lg text-sm">Habis</span>
                    </div>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="p-4 sm:p-5 flex flex-col flex-1">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="{{ $produk->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }}">
                            {{ $produk->kategori }}
                        </span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $produk->stok > 5 ? 'bg-mint-500/20 text-mint-300' : ($produk->stok > 0 ? 'bg-amber-500/20 text-amber-300' : 'bg-red-500/20 text-red-300') }}">
                            {{ number_format($produk->stok, 1) }} Kg
                        </span>
                    </div>
                    
                    <h3 class="font-bold text-white mb-1 line-clamp-1 text-sm sm:text-base">{{ $produk->nama }}</h3>
                    
                    {{-- Rating --}}
                    @if($produk->review_count > 0)
                    <div class="flex items-center gap-1 mt-0.5">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-[10px] {{ $i <= round($produk->average_rating) ? 'text-amber-400' : 'text-white/20' }}"></i>
                            @endfor
                        </div>
                        <span class="text-[10px] text-white/40">({{ $produk->review_count }})</span>
                    </div>
                    @endif

                    <p class="text-cyan-300 font-extrabold text-lg sm:text-xl mt-auto">
                        Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}
                        <span class="text-xs text-white/40 font-normal">/Kg</span>
                    </p>
                    
                    @if($produk->stok > 0)
                    <a href="{{ route('produk.show', $produk) }}" 
                       class="btn-primary mt-4 text-sm py-2.5 btn-shiny">
                        <i class="fas fa-shopping-cart"></i> Beli Sekarang
                    </a>
                    @else
                    <button disabled class="inline-flex items-center justify-center gap-2 px-4 py-2.5 mt-4 rounded-xl text-sm font-medium bg-white/10 text-white/30 cursor-not-allowed">
                        <i class="fas fa-times"></i> Stok Habis
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($produks->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $produks->withQueryString()->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-20">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                <i class="fas fa-search text-4xl text-white/30"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Produk Ditemukan</h3>
            <p class="text-white/50 max-w-md mx-auto">Coba ubah filter pencarian atau cari dengan kata kunci yang berbeda.</p>
            <a href="{{ route('catalog') }}" class="btn-primary mt-6">
                <i class="fas fa-refresh"></i> Reset Filter
            </a>
        </div>
        @endif
    </div>
</section>
@endsection
