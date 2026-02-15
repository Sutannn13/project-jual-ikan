@extends('layouts.master')

@section('title', 'Tulis Review')

@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-6 sm:mb-8">
            <ol class="flex items-center gap-2 text-sm flex-wrap">
                <li><a href="{{ route('home') }}" class="text-white/50 hover:text-cyan-300 transition-colors">Beranda</a></li>
                <li class="text-white/30"><i class="fas fa-chevron-right text-[10px]"></i></li>
                <li><a href="{{ route('my.orders') }}" class="text-white/50 hover:text-cyan-300 transition-colors">Pesanan Saya</a></li>
                <li class="text-white/30"><i class="fas fa-chevron-right text-[10px]"></i></li>
                <li class="text-cyan-300 font-medium">Tulis Review</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
                 style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 8px 20px rgba(6,182,212,0.4);">
                <i class="fas fa-star text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Tulis Review</h1>
            <p class="text-white/60">Bagikan pengalaman Anda dengan produk ini</p>
        </div>

        {{-- Order Info Card --}}
        <div class="store-glass-card rounded-2xl p-5 sm:p-6 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-receipt text-cyan-400"></i>
                <h3 class="font-bold text-white text-sm">Informasi Pesanan</h3>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-bold text-cyan-300">{{ $order->order_number }}</p>
                    <p class="text-xs text-white/40 mt-0.5">{{ $order->created_at->format('d M Y') }}</p>
                </div>
                <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-mint-500/20 text-mint-300 border border-mint-500/30">
                    SELESAI
                </span>
            </div>
        </div>

        {{-- Product Card --}}
        <div class="store-glass-card rounded-2xl p-5 sm:p-6 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-box text-cyan-400"></i>
                <h3 class="font-bold text-white text-sm">Produk yang Dibeli</h3>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-white/10 border border-white/10">
                    @if($produk->foto)
                        <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-fish text-2xl text-white/20"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-white truncate">{{ $produk->nama }}</h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="{{ $produk->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }} text-[10px] px-2 py-0.5">
                            {{ $produk->kategori }}
                        </span>
                        <span class="text-xs text-white/50">{{ number_format($orderItem->qty, 1) }} Kg</span>
                    </div>
                    <p class="font-bold text-cyan-300 mt-1">
                        Rp {{ number_format($orderItem->subtotal, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Review Form --}}
        <div class="store-glass-card rounded-2xl p-6 sm:p-8" x-data="{ rating: 0, hoverRating: 0 }">
            <div class="flex items-center gap-2 mb-6">
                <i class="fas fa-pen text-cyan-400"></i>
                <h3 class="font-bold text-white">Review Anda</h3>
            </div>

            <form action="{{ route('review.store', $produk) }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                
                {{-- Star Rating --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-white mb-3">
                        Rating <span class="text-red-400">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" @click="rating = {{ $i }}" 
                                @mouseenter="hoverRating = {{ $i }}" @mouseleave="hoverRating = 0"
                                class="text-4xl transition-all duration-200 focus:outline-none transform hover:scale-110">
                            <i class="fas fa-star" :class="(hoverRating || rating) >= {{ $i }} ? 'text-amber-400' : 'text-white/20'"></i>
                        </button>
                        @endfor
                    </div>
                    <div class="mt-3 min-h-[24px]">
                        <p class="text-sm font-medium" 
                           :class="{
                               'text-red-400': rating === 1,
                               'text-orange-400': rating === 2,
                               'text-amber-400': rating === 3,
                               'text-cyan-400': rating === 4,
                               'text-mint-400': rating === 5,
                               'text-white/50': rating === 0
                           }"
                           x-show="rating > 0" 
                           x-text="['', '😞 Buruk Sekali', '😕 Kurang Memuaskan', '🙂 Cukup Baik', '😊 Bagus', '🤩 Sangat Bagus'][rating]">
                        </p>
                        <p class="text-xs text-white/40" x-show="rating === 0">Pilih rating untuk produk ini</p>
                    </div>
                    <input type="hidden" name="rating" x-bind:value="rating" required>
                    @error('rating')
                    <p class="text-red-400 text-xs mt-2">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Comment --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-white mb-2">
                        Komentar <span class="text-white/40 font-normal text-xs">(Opsional)</span>
                    </label>
                    <textarea name="comment" rows="5" 
                              placeholder="Ceritakan pengalaman Anda dengan produk ini... misalnya tentang kualitas, kesegaran, atau layanan pengiriman."
                              class="w-full px-4 py-3.5 rounded-xl text-sm text-white bg-white/10 border border-white/15 focus:bg-white/15 focus:border-cyan-400/50 focus:outline-none focus:ring-2 focus:ring-cyan-400/20 placeholder-white/40 resize-none transition-all">{{ old('comment') }}</textarea>
                    <p class="text-xs text-white/40 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Maksimal 1000 karakter
                    </p>
                    @error('comment')
                    <p class="text-red-400 text-xs mt-2">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-white/10">
                    <a href="{{ route('my.orders') }}" 
                       class="flex-1 py-3.5 px-6 rounded-xl font-semibold text-center transition-all duration-300
                              bg-white/5 text-white/70 hover:bg-white/10 hover:text-white border border-white/10">
                        <i class="fas fa-arrow-left mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="flex-1 py-3.5 px-6 rounded-xl font-semibold text-center transition-all duration-300 btn-primary btn-shiny"
                            :disabled="rating === 0" 
                            :class="{ 'opacity-50 cursor-not-allowed': rating === 0 }">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Review
                    </button>
                </div>
            </form>
        </div>

        {{-- Tips Card --}}
        <div class="mt-6 p-5 rounded-2xl border border-cyan-500/20"
             style="background: rgba(6,182,212,0.08);">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: rgba(6,182,212,0.2); border: 1px solid rgba(6,182,212,0.3);">
                    <i class="fas fa-lightbulb text-cyan-300 text-sm"></i>
                </div>
                <div>
                    <h4 class="font-bold text-cyan-300 text-sm mb-2">Tips Review yang Baik</h4>
                    <ul class="text-xs text-white/60 space-y-1">
                        <li>• Berikan penilaian yang jujur berdasarkan pengalaman Anda</li>
                        <li>• Jelaskan kualitas produk (kesegaran, ukuran, dll)</li>
                        <li>• Ceritakan tentang proses pengiriman jika relevan</li>
                        <li>• Hindari kata-kata kasar atau menyinggung</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on rating when page loads
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const rating = document.querySelector('input[name="rating"]').value;
            if (!rating || rating === '0') {
                e.preventDefault();
                alert('Silakan pilih rating terlebih dahulu');
            }
        });
    }
});
</script>
@endpush
