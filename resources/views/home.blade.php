@extends('layouts.master')

@section('title', 'Beranda')

@section('content')
@guest
{{-- ========================================
     PREMIUM LANDING PAGE FOR GUESTS
     ======================================== --}}

{{-- HERO SECTION with Animated Gradient --}}
<section class="relative overflow-hidden -mt-[1px]">
    {{-- Base Gradient Background --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#0e7490] via-[#0891b2] to-[#155e75]"></div>
    
    {{-- ENHANCED Lightning Glow Effects (More Dramatic!) --}}
    <div class="absolute inset-0" style="background: 
        radial-gradient(circle at 15% 15%, rgba(6, 182, 212, 0.6) 0%, rgba(6, 182, 212, 0.3) 25%, transparent 50%),
        radial-gradient(circle at 85% 25%, rgba(20, 184, 166, 0.5) 0%, rgba(20, 184, 166, 0.25) 30%, transparent 55%),
        radial-gradient(circle at 50% 90%, rgba(14, 116, 144, 0.45) 0%, rgba(14, 116, 144, 0.2) 35%, transparent 60%),
        radial-gradient(circle at 95% 80%, rgba(22, 211, 238, 0.4) 0%, rgba(22, 211, 238, 0.15) 25%, transparent 45%),
        radial-gradient(circle at 40% 50%, rgba(34, 211, 238, 0.3) 0%, transparent 40%);">
    </div>
    
    {{-- Animated Floating Light Orbs (BIGGER & BRIGHTER) --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-gradient-to-br from-cyan-400/40 via-teal-400/30 to-transparent rounded-full blur-[100px] animate-float"></div>
        <div class="absolute -bottom-32 -right-32 w-[600px] h-[600px] bg-gradient-to-tl from-ocean-300/35 via-cyan-300/25 to-transparent rounded-full blur-[120px] animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-white/15 rounded-full blur-[80px] animate-float" style="animation-delay: -5s;"></div>
        <div class="absolute top-20 right-1/4 w-[350px] h-[350px] bg-gradient-to-br from-teal-300/30 via-transparent to-transparent rounded-full blur-[90px] animate-float" style="animation-delay: -7s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-[450px] h-[450px] bg-gradient-to-tr from-cyan-400/25 to-transparent rounded-full blur-[110px] animate-float" style="animation-delay: -4s;"></div>
    </div>
    
    {{-- Shimmer Effect Overlay --}}
    <div class="absolute inset-0 opacity-30" style="background: 
        linear-gradient(110deg, transparent 30%, rgba(255,255,255,0.2) 50%, transparent 70%);
        background-size: 200% 100%;
        animation: shimmer 8s infinite linear;">
    </div>
    
    {{-- Grid Pattern Overlay (Thinner) --}}
    <div class="absolute inset-0 opacity-20" style="background-image: 
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
        background-size: 50px 50px;">
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 md:py-20 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            {{-- Hero Text (Left side on desktop, centered on mobile) --}}
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-6"
                     style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                    <div class="w-2 h-2 rounded-full bg-teal-300 animate-pulse"></div>
                    <span class="text-white/90 text-xs sm:text-sm font-medium">Platform Jual Beli Ikan #1 di Indonesia</span>
                </div>
                
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] mb-6">
                    Ikan Segar
                    <span class="block mt-2 bg-gradient-to-r from-teal-200 via-teal-300 to-ocean-200 bg-clip-text text-transparent">
                        Langsung dari Kolam
                    </span>
                </h1>
                
                <p class="text-base sm:text-lg md:text-xl text-ocean-100 mb-8 max-w-2xl lg:max-w-none mx-auto lg:mx-0 leading-relaxed">
                    Dapatkan <strong class="text-white">Lele & Ikan Mas</strong> segar berkualitas premium dengan harga terbaik. Tanpa perantara!
                </p>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-10">
                    <a href="{{ route('register') }}" class="btn-shiny group inline-flex items-center justify-center gap-3 px-6 sm:px-8 py-3 sm:py-4 font-bold text-ocean-900 rounded-2xl transition-all duration-300 hover:scale-105 text-sm sm:text-base"
                       style="background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); box-shadow: 0 8px 30px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,1);">
                        <i class="fas fa-rocket text-ocean-600"></i>
                        <span>Mulai Sekarang - Gratis!</span>
                        <i class="fas fa-arrow-right text-ocean-500 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-4 font-semibold text-white rounded-2xl transition-all duration-300 hover:bg-white/20 text-sm sm:text-base"
                       style="background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.3); backdrop-filter: blur(10px);">
                        <i class="fas fa-fish"></i>
                        <span>Lihat Katalog</span>
                    </a>
                </div>

                {{-- Trust Badges --}}
                <div class="flex flex-wrap items-center gap-4 sm:gap-6 justify-center lg:justify-start">
                    <div class="flex items-center gap-2 text-white/80 text-xs sm:text-sm">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-400/30 flex items-center justify-center">
                            <i class="fas fa-truck text-teal-200 text-xs"></i>
                        </div>
                        <span class="hidden sm:inline">Same-Day Delivery</span>
                        <span class="sm:hidden">Same-Day</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/80 text-xs sm:text-sm">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-400/30 flex items-center justify-center">
                            <i class="fas fa-shield-alt text-teal-200 text-xs"></i>
                        </div>
                        <span class="hidden sm:inline">100% Garansi Segar</span>
                        <span class="sm:hidden">100% Segar</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/80 text-xs sm:text-sm">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-400/30 flex items-center justify-center">
                            <i class="fas fa-tags text-teal-200 text-xs"></i>
                        </div>
                        <span>Harga Transparan</span>
                    </div>
                </div>
            </div>

            {{-- Stats Card (Right side on desktop) --}}
            <div class="w-full">
                <div class="relative">
                    {{-- Glow Effect --}}
                    <div class="absolute -inset-2 sm:-inset-4 bg-gradient-to-r from-teal-400/30 to-ocean-400/30 rounded-3xl blur-2xl"></div>
                    
                    <div class="relative rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 overflow-hidden"
                         style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(20px);">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div class="text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.1);">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-1">{{ $produks->count() }}+</div>
                                <p class="text-ocean-200 text-xs sm:text-sm">Produk Tersedia</p>
                            </div>
                            <div class="text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.1);">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-teal-300 mb-1">100%</div>
                                <p class="text-ocean-200 text-xs sm:text-sm">Segar Terjamin</p>
                            </div>
                            <div class="text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.1);">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-1">24h</div>
                                <p class="text-ocean-200 text-xs sm:text-sm">Pengiriman Cepat</p>
                            </div>
                            <div class="text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.1);">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-teal-300 mb-1">5.0</div>
                                <p class="text-ocean-200 text-xs sm:text-sm">Rating Pelanggan</p>
                            </div>
                        </div>
                        
                        {{-- Decorative fish (hidden on small screens) --}}
                        <div class="absolute -bottom-4 -right-4 text-white/5 text-7xl sm:text-9xl hidden sm:block">
                            <i class="fas fa-fish"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Wave Divider --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 75C480 70 600 80 720 85C840 90 960 90 1080 85C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="rgba(12,74,110,0.01)"/>
        </svg>
    </div>
</section>

{{-- ABOUT SECTION --}}
<section class="py-12 sm:py-16 md:py-20 lg:py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-14">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold mb-4" style="background: rgba(6,182,212,0.12); color: #67e8f9; border: 1px solid rgba(6,182,212,0.2);">
                <i class="fas fa-info-circle"></i> Tentang Kami
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">Apa itu FishMarket?</h2>
            <p class="text-white/60 text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed px-4">
                Platform e-commerce yang menghubungkan konsumen langsung dengan petani ikan lokal melalui sistem pemesanan online yang mudah dan transparan.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            {{-- Mission Card --}}
            <div class="relative rounded-2xl sm:rounded-3xl p-6 sm:p-8 overflow-hidden group"
                 style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 50%, #155e75 100%);">
                <div class="absolute top-0 right-0 w-48 h-48 sm:w-64 sm:h-64 bg-teal-400/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl flex items-center justify-center mb-4 sm:mb-5"
                         style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-bullseye text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-3 sm:mb-4">Misi Kami</h3>
                    <p class="text-ocean-100 text-sm sm:text-base leading-relaxed">
                        Memberikan akses mudah kepada masyarakat untuk mendapatkan ikan segar berkualitas tinggi dengan harga adil, sekaligus memberdayakan petani ikan lokal melalui platform digital.
                    </p>
                </div>
            </div>
            
            {{-- Feature Cards --}}
            <div class="space-y-3 sm:space-y-4">
                <div class="card-glass rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start gap-3 sm:gap-4 group hover:scale-[1.02] transition-transform duration-300">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);">
                        <i class="fas fa-handshake text-white text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white mb-1 text-sm sm:text-base">Langsung dari Petani</h4>
                        <p class="text-white/50 text-xs sm:text-sm">Tanpa perantara, harga lebih murah dan petani mendapat keuntungan lebih baik.</p>
                    </div>
                </div>
                <div class="card-glass rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start gap-3 sm:gap-4 group hover:scale-[1.02] transition-transform duration-300">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-shield-alt text-white text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white mb-1 text-sm sm:text-base">Jaminan Kualitas</h4>
                        <p class="text-white/50 text-xs sm:text-sm">Setiap ikan dipilih dengan standar tinggi dan dijamin kesegarannya.</p>
                    </div>
                </div>
                <div class="card-glass rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start gap-3 sm:gap-4 group hover:scale-[1.02] transition-transform duration-300">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
                        <i class="fas fa-bolt text-white text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white mb-1 text-sm sm:text-base">Proses Cepat & Mudah</h4>
                        <p class="text-white/50 text-xs sm:text-sm">Pesan online, bayar, dan ikan segar langsung dikirim ke rumah Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- WHY CHOOSE US --}}
<section class="py-12 sm:py-16 md:py-20 relative overflow-hidden">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-14">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold mb-4" style="background: rgba(255,255,255,0.08); color: #67e8f9; border: 1px solid rgba(255,255,255,0.15);">
                <i class="fas fa-star"></i> Keunggulan
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">Kenapa Memilih FishMarket?</h2>
            <p class="text-white/60 text-sm sm:text-base md:text-lg max-w-xl mx-auto px-4">Pengalaman belanja ikan yang berbeda dan lebih baik</p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach([
                ['icon' => 'fa-truck-fast', 'title' => 'Pengiriman Same-Day', 'desc' => 'Pesan pagi, sore sudah sampai. Langsung dari kolam ke dapur dalam hitungan jam.', 'gradient' => 'from-ocean-500 to-ocean-600', 'glow' => 'ocean'],
                ['icon' => 'fa-certificate', 'title' => 'Garansi Segar 100%', 'desc' => 'Tidak segar? Uang kembali! Kami jamin setiap ikan dalam kondisi prima.', 'gradient' => 'from-teal-500 to-mint-500', 'glow' => 'teal'],
                ['icon' => 'fa-tags', 'title' => 'Harga Transparan', 'desc' => 'Harga langsung dari petani, tanpa markup berlebihan. Hemat hingga 30%.', 'gradient' => 'from-coral-500 to-coral-400', 'glow' => 'coral'],
            ] as $feature)
            <div class="card-elevated rounded-2xl sm:rounded-3xl p-6 sm:p-8 text-center group hover:scale-[1.02] transition-all duration-300">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-5 bg-gradient-to-br {{ $feature['gradient'] }} shadow-lg"
                     style="box-shadow: 0 8px 20px rgba(6, 182, 212, 0.25);">
                    <i class="fas {{ $feature['icon'] }} text-white text-xl sm:text-2xl"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-white mb-2 sm:mb-3">{{ $feature['title'] }}</h3>
                <p class="text-white/50 text-sm sm:text-base leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="py-12 sm:py-16 md:py-20 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-14">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold mb-4" style="background: rgba(6,182,212,0.12); color: #67e8f9; border: 1px solid rgba(6,182,212,0.2);">
                <i class="fas fa-magic"></i> Cara Kerja
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">Mudah & Cepat, Hanya 3 Langkah!</h2>
        </div>

        <div class="grid sm:grid-cols-3 gap-6 sm:gap-8 relative">
            {{-- Connection Line (hidden on mobile) --}}
            <div class="hidden sm:block absolute top-1/3 left-1/4 right-1/4 h-0.5" style="background: linear-gradient(to right, rgba(6,182,212,0.3), rgba(20,184,166,0.3), rgba(251,113,133,0.3));"></div>
            
            @foreach([
                ['num' => '1', 'title' => 'Daftar & Pilih Ikan', 'desc' => 'Buat akun gratis, lalu pilih ikan segar dari katalog kami.', 'gradient' => 'from-ocean-500 to-ocean-600'],
                ['num' => '2', 'title' => 'Pesan & Bayar', 'desc' => 'Masukkan jumlah (dalam Kg), checkout, dan tunggu konfirmasi.', 'gradient' => 'from-teal-500 to-mint-500'],
                ['num' => '3', 'title' => 'Terima Ikan Segar', 'desc' => 'Ikan langsung dikirim dari kolam ke alamat Anda. Segar!', 'gradient' => 'from-coral-500 to-coral-400'],
            ] as $step)
            <div class="text-center relative">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-5 shadow-xl bg-gradient-to-br {{ $step['gradient'] }}"
                     style="box-shadow: 0 10px 30px rgba(6, 182, 212, 0.3);">
                    <span class="text-2xl sm:text-3xl font-extrabold text-white">{{ $step['num'] }}</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-white mb-2">{{ $step['title'] }}</h3>
                <p class="text-white/50 text-sm sm:text-base px-2">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="py-12 sm:py-16 md:py-20 relative overflow-hidden">
    {{-- Base Gradient --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#0e7490] via-[#0891b2] to-[#155e75]"></div>
    
    {{-- ENHANCED Lightning Effects --}}
    <div class="absolute inset-0" style="background: 
        radial-gradient(circle at 25% 25%, rgba(6, 182, 212, 0.55) 0%, rgba(6, 182, 212, 0.25) 30%, transparent 50%),
        radial-gradient(circle at 85% 40%, rgba(20, 184, 166, 0.5) 0%, rgba(20, 184, 166, 0.2) 35%, transparent 55%),
        radial-gradient(circle at 40% 85%, rgba(14, 116, 144, 0.45) 0%, rgba(14, 116, 144, 0.15) 40%, transparent 60%),
        radial-gradient(circle at 70% 70%, rgba(34, 211, 238, 0.35) 0%, transparent 45%);">
    </div>
    
    {{-- Animated Light Orbs --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-[300px] h-[300px] sm:w-[450px] sm:h-[450px] bg-gradient-to-br from-teal-400/35 to-transparent rounded-full blur-[80px] sm:blur-[100px] animate-float"></div>
        <div class="absolute bottom-0 right-1/3 w-[350px] h-[350px] sm:w-[500px] sm:h-[500px] bg-gradient-to-tl from-ocean-300/30 via-cyan-300/20 to-transparent rounded-full blur-[90px] sm:blur-[110px] animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute top-1/3 right-1/4 w-[280px] h-[280px] sm:w-[400px] sm:h-[400px] bg-gradient-to-tr from-cyan-400/25 to-transparent rounded-full blur-[70px] sm:blur-[90px] animate-float" style="animation-delay: -4s;"></div>
    </div>
    
    {{-- Shimmer Effect --}}
    <div class="absolute inset-0 opacity-25" style="background: 
        linear-gradient(110deg, transparent 30%, rgba(255,255,255,0.15) 50%, transparent 70%);
        background-size: 200% 100%;
        animation: shimmer 8s infinite linear;">
    </div>
    
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl flex items-center justify-center mx-auto mb-5 sm:mb-6"
             style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
            <i class="fas fa-fish text-3xl sm:text-4xl text-white"></i>
        </div>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">Siap Belanja Ikan Segar?</h2>
        <p class="text-ocean-100 text-sm sm:text-base md:text-lg mb-6 sm:mb-8 max-w-lg mx-auto px-4">
            Bergabunglah dengan ribuan pelanggan yang sudah mempercayai FishMarket untuk kebutuhan ikan segar mereka.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="btn-shiny inline-flex items-center justify-center gap-3 px-6 sm:px-8 py-3 sm:py-4 font-bold text-ocean-900 rounded-2xl transition-all duration-300 hover:scale-105 text-sm sm:text-base"
               style="background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); box-shadow: 0 8px 30px rgba(0,0,0,0.2);">
                <i class="fas fa-rocket"></i> Daftar Sekarang - Gratis!
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-4 font-semibold text-white rounded-2xl transition-all text-sm sm:text-base"
               style="background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.3);">
                <i class="fas fa-sign-in-alt"></i> Sudah Punya Akun? Login
            </a>
        </div>
    </div>
</section>

@else
{{-- ========================================
     HOMEPAGE FOR LOGGED-IN USERS
     ======================================== --}}

{{-- WELCOME BANNER --}}
<section class="relative overflow-hidden -mt-[1px]">
    {{-- Base Gradient --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#0e7490] via-[#0891b2] to-[#155e75]"></div>
    
    {{-- Lightning Glow Effects --}}
    <div class="absolute inset-0" style="background: 
        radial-gradient(circle at 75% 20%, rgba(6, 182, 212, 0.5) 0%, rgba(6, 182, 212, 0.2) 30%, transparent 50%),
        radial-gradient(circle at 15% 60%, rgba(20, 184, 166, 0.45) 0%, rgba(20, 184, 166, 0.15) 35%, transparent 55%);">
    </div>
    
    {{-- Animated Orbs --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-10 -right-10 w-[350px] h-[350px] bg-gradient-to-br from-teal-400/35 to-transparent rounded-full blur-[80px] animate-float"></div>
        <div class="absolute -bottom-10 -left-10 w-[400px] h-[400px] bg-gradient-to-tl from-ocean-300/30 to-transparent rounded-full blur-[90px] animate-float" style="animation-delay: -3s;"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 md:py-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">
                    Selamat Datang, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-ocean-100 text-sm sm:text-base mt-1">Mau beli ikan segar apa hari ini?</p>
            </div>
            <a href="{{ route('my.orders') }}" class="inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl font-semibold text-white transition-all hover:bg-white/20 text-sm sm:text-base"
               style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-box"></i> Pesanan Saya
            </a>
        </div>
    </div>
</section>

{{-- PRODUCT CATALOG --}}
<section class="py-8 sm:py-10 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- PROMO BANNERS --}}
        @if(isset($banners) && $banners->count() > 0)
        <div class="mb-8 sm:mb-10">
            <div class="grid grid-cols-1 {{ $banners->count() > 1 ? 'md:grid-cols-2' : '' }} gap-4">
                @foreach($banners->take(4) as $banner)
                <a href="{{ $banner->link_url ?? route('catalog') }}" 
                   class="block rounded-2xl overflow-hidden group relative {{ $banners->count() === 1 ? 'md:col-span-1 max-w-3xl mx-auto w-full' : '' }}">
                    <div class="aspect-[16/7] overflow-hidden">
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5">
                        <h3 class="text-white font-bold text-sm sm:text-lg drop-shadow-lg">{{ $banner->title }}</h3>
                        @if($banner->description)
                            <p class="text-white/80 text-xs sm:text-sm mt-1 drop-shadow-md">{{ $banner->description }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 sm:mb-8">
            <div>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Produk Segar Hari Ini</h2>
                <p class="text-white/50 text-sm sm:text-base mt-1">Langsung dari kolam, segar untuk meja makan Anda</p>
            </div>
            <a href="{{ route('catalog') }}" class="hidden sm:inline-flex items-center gap-2 text-cyan-300 hover:text-cyan-200 font-semibold text-sm">
                Lihat Semua <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        @if($produks->count() > 0)
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
            @foreach($produks->take(8) as $produk)
            <div class="product-card flex flex-col group">
                <div class="aspect-[4/3] overflow-hidden bg-white/5 relative">
                    @if($produk->foto)
                        <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/20">
                            <i class="fas fa-fish text-3xl sm:text-4xl md:text-5xl"></i>
                        </div>
                    @endif

                    {{-- Wishlist Button --}}
                    <div class="absolute top-2 right-2 z-10"
                         x-data="{ 
                            wishlisted: {{ in_array($produk->id, $wishlistedIds ?? []) ? 'true' : 'false' }}, 
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
                        <button @click.prevent="toggle()" :disabled="loading"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center transition-all duration-300 shadow-lg"
                                :class="wishlisted ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-white/80 backdrop-blur text-gray-400 hover:text-red-500 hover:bg-white'"
                                :title="wishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist'">
                            <i class="text-xs sm:text-sm" :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-heart'"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3 sm:p-4 md:p-5 flex flex-col flex-1">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="{{ $produk->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }} text-xs">
                            {{ $produk->kategori }}
                        </span>
                        <span class="text-xs text-white/40">{{ number_format($produk->stok, 1) }} Kg</span>
                    </div>
                    <h3 class="font-bold text-white mb-1 line-clamp-1 text-sm sm:text-base">{{ $produk->nama }}</h3>
                    <p class="text-cyan-300 font-extrabold text-base sm:text-lg md:text-xl mt-auto">
                        Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}
                        <span class="text-xs text-white/40 font-normal">/Kg</span>
                    </p>
                    <a href="{{ route('produk.show', $produk) }}" class="btn-primary mt-2 sm:mt-3 text-xs sm:text-sm py-2 sm:py-2.5">
                        <i class="fas fa-shopping-cart"></i> Beli
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 sm:py-16 text-white/30">
            <i class="fas fa-box-open text-4xl sm:text-5xl mb-4"></i>
            <p class="text-base sm:text-lg">Belum ada produk tersedia.</p>
        </div>
        @endif

        <div class="text-center mt-6 sm:mt-8 sm:hidden">
            <a href="{{ route('catalog') }}" class="btn-primary text-sm">
                <i class="fas fa-th"></i> Lihat Semua Produk
            </a>
        </div>
    </div>
</section>

{{-- QUICK BENEFITS --}}
<section class="py-6 sm:py-8 md:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            @foreach([
                ['icon' => 'fa-truck', 'title' => 'Same-Day', 'desc' => 'Pesan pagi, sore sampai', 'gradient' => 'from-ocean-500 to-ocean-600'],
                ['icon' => 'fa-certificate', 'title' => 'Garansi Segar', 'desc' => '100% uang kembali', 'gradient' => 'from-teal-500 to-mint-500'],
                ['icon' => 'fa-hand-holding-usd', 'title' => 'Harga Petani', 'desc' => 'Langsung dari kolam', 'gradient' => 'from-coral-500 to-coral-400'],
            ] as $benefit)
            <div class="card-glass rounded-xl sm:rounded-2xl p-3 sm:p-4 md:p-6 text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-2 sm:mb-3 bg-gradient-to-br {{ $benefit['gradient'] }}">
                    <i class="fas {{ $benefit['icon'] }} text-white text-sm sm:text-base"></i>
                </div>
                <h3 class="font-bold text-white text-xs sm:text-sm md:text-base">{{ $benefit['title'] }}</h3>
                <p class="text-white/50 text-xs mt-1 hidden sm:block">{{ $benefit['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endguest
@endsection