@extends('layouts.master')

@section('title', 'Wishlist Saya')

@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center sm:text-left mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">
                <i class="fas fa-heart text-red-400 mr-2"></i>Wishlist Saya
            </h1>
            <p class="text-white/60 mt-1">Produk yang Anda simpan untuk nanti</p>
        </div>

        @if($wishlists->count() > 0)
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($wishlists as $wishlist)
            @if($wishlist->produk)
            <div class="product-card flex flex-col group relative">
                {{-- Remove from Wishlist Button --}}
                <form action="{{ route('wishlist.remove', $wishlist->produk) }}" method="POST" class="absolute top-3 right-3 z-10">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-9 h-9 rounded-full bg-white/90 backdrop-blur shadow-sm flex items-center justify-center text-red-500 hover:bg-red-50 hover:text-red-600 transition-all"
                            title="Hapus dari Wishlist">
                        <i class="fas fa-heart"></i>
                    </button>
                </form>

                {{-- Image --}}
                <div class="aspect-[4/3] overflow-hidden relative bg-white/5">
                    @if($wishlist->produk->foto)
                        <img src="{{ asset('storage/' . $wishlist->produk->foto) }}" alt="{{ $wishlist->produk->nama }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="fas fa-fish text-4xl sm:text-5xl"></i>
                        </div>
                    @endif
                    
                    @if($wishlist->produk->stok <= 0)
                    <div class="absolute inset-0 bg-gray-900/60 flex items-center justify-center">
                        <span class="px-4 py-2 bg-red-500 text-white font-bold rounded-lg text-sm">Habis</span>
                    </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="p-4 sm:p-5 flex flex-col flex-1">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="{{ $wishlist->produk->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }}">
                            {{ $wishlist->produk->kategori }}
                        </span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $wishlist->produk->stok > 5 ? 'bg-mint-500/20 text-mint-300' : ($wishlist->produk->stok > 0 ? 'bg-amber-500/20 text-amber-300' : 'bg-red-500/20 text-red-300') }}">
                            {{ number_format($wishlist->produk->stok, 1) }} Kg
                        </span>
                    </div>

                    <h3 class="font-bold text-white mb-1 line-clamp-1 text-sm sm:text-base">{{ $wishlist->produk->nama }}</h3>

                    {{-- Rating --}}
                    @if($wishlist->produk->review_count > 0)
                    <div class="flex items-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= round($wishlist->produk->average_rating) ? 'text-amber-400' : 'text-white/20' }}"></i>
                        @endfor
                        <span class="text-xs text-white/40 ml-1">({{ $wishlist->produk->review_count }})</span>
                    </div>
                    @endif

                    <p class="text-cyan-300 font-extrabold text-lg sm:text-xl mt-auto">
                        Rp {{ number_format($wishlist->produk->harga_per_kg, 0, ',', '.') }}
                        <span class="text-xs text-white/40 font-normal">/Kg</span>
                    </p>

                    @if($wishlist->produk->stok > 0)
                    <a href="{{ route('produk.show', $wishlist->produk) }}" class="btn-primary mt-4 text-sm py-2.5 btn-shiny">
                        <i class="fas fa-shopping-cart"></i> Beli Sekarang
                    </a>
                    @else
                    <button disabled class="inline-flex items-center justify-center gap-2 px-4 py-2.5 mt-4 rounded-xl text-sm font-medium bg-white/10 text-white/30 cursor-not-allowed">
                        <i class="fas fa-times"></i> Stok Habis
                    </button>
                    @endif
                </div>
            </div>
            @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($wishlists->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $wishlists->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-20">
            <div class="w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.2);">
                <i class="fas fa-heart text-5xl text-red-400"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Wishlist Kosong</h3>
            <p class="text-white/50 max-w-md mx-auto mb-6">
                Anda belum menyimpan produk apapun ke wishlist. Temukan ikan segar favorit Anda!
            </p>
            <a href="{{ route('catalog') }}" class="btn-primary">
                <i class="fas fa-fish mr-2"></i> Lihat Katalog
            </a>
        </div>
        @endif
    </div>
</section>
@endsection
