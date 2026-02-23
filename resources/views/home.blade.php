@extends('layouts.master')

@section('title', 'Beranda')

@push('styles')
@guest
<style>
    /* ============================================
       LANDING PAGE – HERO BACKGROUND
    ============================================ */
    html.scroll-smooth,
    html.scroll-smooth body,
    html.scroll-smooth body.bg-ocean-waves {
        background: url('{{ asset('images/hero-background.jpg') }}') no-repeat center center fixed !important;
        background-size: cover !important;
        background-color: #0c4a6e !important;
    }
    #master-bg-ocean, #master-bg-radial, #master-bg-grid, #master-bg-orbs {
        display: none !important; opacity: 0 !important; visibility: hidden !important;
    }

    /* ============================================
       SCROLL-REVEAL – BIDIRECTIONAL
       ENTER: slide in from direction
       EXIT-UP (scrolled past): slide out upward / shrink
       EXIT-DOWN (not yet reached): revert to initial
    ============================================ */
    .reveal-up, .reveal-left, .reveal-right, .reveal-scale {
        will-change: opacity, transform;
    }

    /* ── ENTER (initial / below viewport) ── */
    .reveal-up    { opacity: 0; transform: translateY(52px);  transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
    .reveal-left  { opacity: 0; transform: translateX(-60px); transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
    .reveal-right { opacity: 0; transform: translateX(60px);  transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
    .reveal-scale { opacity: 0; transform: scale(.86);        transition: opacity .65s cubic-bezier(.22,1,.36,1), transform .65s cubic-bezier(.22,1,.36,1); }

    /* ── VISIBLE ── */
    .reveal-up.visible, .reveal-left.visible, .reveal-right.visible, .reveal-scale.visible {
        opacity: 1; transform: none;
    }

    /* ── EXIT-UP (scrolled past above viewport) ── */
    .reveal-up.exit-up    { opacity: 0; transform: translateY(-38px); transition: opacity .5s cubic-bezier(.4,0,.6,1), transform .5s cubic-bezier(.4,0,.6,1) !important; }
    .reveal-left.exit-up  { opacity: 0; transform: translateX(44px);  transition: opacity .5s cubic-bezier(.4,0,.6,1), transform .5s cubic-bezier(.4,0,.6,1) !important; }
    .reveal-right.exit-up { opacity: 0; transform: translateX(-44px); transition: opacity .5s cubic-bezier(.4,0,.6,1), transform .5s cubic-bezier(.4,0,.6,1) !important; }
    .reveal-scale.exit-up { opacity: 0; transform: scale(1.08);       transition: opacity .5s cubic-bezier(.4,0,.6,1), transform .5s cubic-bezier(.4,0,.6,1) !important; }

    /* ── Cancel stagger on exit (instant re-hide) ── */
    .reveal-up.exit-up, .reveal-left.exit-up, .reveal-right.exit-up, .reveal-scale.exit-up {
        transition-delay: 0s !important;
    }

    /* ── STAGGER ENTER delays ── */
    .reveal-d1 { transition-delay: .1s; }
    .reveal-d2 { transition-delay: .2s; }
    .reveal-d3 { transition-delay: .3s; }
    .reveal-d4 { transition-delay: .4s; }
    .reveal-d5 { transition-delay: .5s; }
    .reveal-d6 { transition-delay: .6s; }

    /* ============================================
       HERO HEADLINE TYPEWRITER / SLIDE
    ============================================ */
    @keyframes heroSlideIn {
        from { opacity: 0; transform: translateY(32px) scale(.97); }
        to   { opacity: 1; transform: none; }
    }
    @keyframes heroBadgeIn {
        from { opacity: 0; transform: translateY(-16px) scale(.9); }
        to   { opacity: 1; transform: none; }
    }
    .hero-badge-anim  { animation: heroBadgeIn  .7s cubic-bezier(.22,1,.36,1) .2s both; }
    .hero-title-anim  { animation: heroSlideIn  .8s cubic-bezier(.22,1,.36,1) .35s both; }
    .hero-sub-anim    { animation: heroSlideIn  .8s cubic-bezier(.22,1,.36,1) .5s both; }
    .hero-btn-anim    { animation: heroSlideIn  .8s cubic-bezier(.22,1,.36,1) .65s both; }
    .hero-trust-anim  { animation: heroSlideIn  .8s cubic-bezier(.22,1,.36,1) .8s both; }
    .hero-card-anim   { animation: heroSlideIn  .9s cubic-bezier(.22,1,.36,1) .6s both; }

    /* ============================================
       STATS CARD GLOW POP
    ============================================ */
    @keyframes statsPop {
        0%   { transform: scale(.85); opacity: 0; }
        60%  { transform: scale(1.06); opacity: 1; }
        100% { transform: scale(1); }
    }
    .stats-card { animation: statsPop .65s cubic-bezier(.22,1,.36,1) both; }
    .stats-card:nth-child(1) { animation-delay: .7s; }
    .stats-card:nth-child(2) { animation-delay: .85s; }
    .stats-card:nth-child(3) { animation-delay: 1.0s; }
    .stats-card:nth-child(4) { animation-delay: 1.15s; }

    /* Subtle continuous glow pulse on stat numbers */
    @keyframes numGlow {
        0%, 100% { text-shadow: 0 0 12px currentColor; }
        50%       { text-shadow: 0 0 28px currentColor, 0 0 50px currentColor; }
    }
    .stat-number { animation: numGlow 3s ease-in-out infinite; }

    /* ============================================
       BUTTON SHINE SWEEP
    ============================================ */
    @keyframes btnShine {
        0%   { left: -75%; opacity: .6; }
        100% { left: 150%;  opacity: 0; }
    }
    .btn-shine-wrap { position: relative; overflow: hidden; }
    .btn-shine-wrap::after {
        content: '';
        position: absolute; top: 0; left: -75%;
        width: 40%; height: 100%;
        background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.55) 50%, transparent 100%);
        transform: skewX(-20deg);
        animation: btnShine 2.8s ease-in-out infinite;
    }

    /* ============================================
       FLOATING FISH PARALLAX (hero deco)
    ============================================ */
    @keyframes fishSwim {
        0%   { transform: translateX(0) translateY(0) rotate(-5deg) scaleX(1); }
        25%  { transform: translateX(12px) translateY(-8px) rotate(0deg) scaleX(1); }
        50%  { transform: translateX(22px) translateY(-4px) rotate(5deg) scaleX(1); }
        75%  { transform: translateX(12px) translateY(6px) rotate(0deg) scaleX(1); }
        100% { transform: translateX(0) translateY(0) rotate(-5deg) scaleX(1); }
    }
    @keyframes fishSwim2 {
        0%   { transform: translateX(0) translateY(0) rotate(8deg) scaleX(-1); }
        50%  { transform: translateX(-18px) translateY(-10px) rotate(-4deg) scaleX(-1); }
        100% { transform: translateX(0) translateY(0) rotate(8deg) scaleX(-1); }
    }
    .fish-deco-1 { animation: fishSwim  6s ease-in-out infinite; }
    .fish-deco-2 { animation: fishSwim2 8s ease-in-out infinite; }
    .fish-deco-3 { animation: fishSwim  9s ease-in-out infinite 1.5s; }

    /* ============================================
       WAVE BOTTOM ANIMATED SVG
    ============================================ */
    @keyframes waveMorph {
        0%,100% { d: path("M0 120L60 110C120 100 240 80 360 75C480 70 600 80 720 85C840 90 960 90 1080 85C1200 80 1320 70 1380 65L1440 60V120H0Z"); }
        50%      { d: path("M0 90L60 100C120 110 240 90 360 80C480 70 600 95 720 90C840 85 960 75 1080 80C1200 85 1320 95 1380 85L1440 80V120H0Z"); }
    }

    /* ============================================
       SECTION BADGE WOBBLE
    ============================================ */
    @keyframes badgeWobble {
        0%,100% { transform: rotate(-2deg) scale(1); }
        50%      { transform: rotate(2deg) scale(1.04); }
    }
    .section-badge:hover { animation: badgeWobble .5s ease-in-out; }

    /* ============================================
       CARD HOVER LIFT + BORDER GLOW
    ============================================ */
    .card-hover-magic {
        transition: transform .35s cubic-bezier(.22,1,.36,1),
                    box-shadow .35s cubic-bezier(.22,1,.36,1),
                    border-color .35s;
    }
    .card-hover-magic:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 24px 60px rgba(6,182,212,.35), 0 0 0 1px rgba(6,182,212,.3);
    }

    /* ============================================
       STEP NUMBER BOUNCE
    ============================================ */
    @keyframes stepBounce {
        0%,100% { transform: translateY(0) scale(1); }
        40%      { transform: translateY(-10px) scale(1.07); }
        70%      { transform: translateY(3px) scale(.97); }
    }
    .step-circle:hover { animation: stepBounce .6s cubic-bezier(.22,1,.36,1); }

    /* ============================================
       COUNTER ROLL (set via JS)
    ============================================ */
    .counter-value { display: inline-block; }

    /* ============================================
       RESPONSIVE MOBILE TWEAKS
    ============================================ */
    @media (max-width: 374px) { /* iPhone SE & very small */
        .hero-text-xs { font-size: .75rem; }
        .hero-btn-xs  { padding: .6rem 1rem; font-size: .8rem; }
        .stat-number  { font-size: 1.4rem; }
    }
    @media (max-width: 480px) {
        .stats-grid-mobile { gap: .6rem; }
        .trust-wrap  { gap: .5rem; }
    }

    /* Ensure buttons never overflow on tiny screens */
    .cta-flex-wrap { flex-wrap: wrap; }

    /* ============================================
       PROGRESS BAR (page load)
    ============================================ */
    #lp-progress {
        position: fixed; top: 0; left: 0;
        height: 3px; width: 0;
        background: linear-gradient(90deg, #fbbf24, #06b6d4, #14b8a6);
        z-index: 9999;
        transition: width .4s cubic-bezier(.22,1,.36,1);
        box-shadow: 0 0 12px rgba(251,191,36,.7);
    }
</style>
@endguest
@endpush

@section('content')
@guest
{{-- ========================================
     PREMIUM LANDING PAGE FOR GUESTS
     ======================================== --}}

{{-- Page-load progress bar --}}
@guest <div id="lp-progress"></div> @endguest

{{-- Background overlay gradient only (image now in body background from styles) --}}
<div class="fixed inset-0 w-full h-full bg-gradient-to-br from-ocean-900/70 via-ocean-800/60 to-cyan-900/70" style="z-index: -1;"></div>

{{-- HERO SECTION with Background Image --}}
<section class="relative overflow-x-hidden -mt-[1px] min-h-screen w-full flex items-center">
    
    {{-- Subtle shimmer effect --}}
    <div class="absolute inset-0 w-full h-full opacity-10" style="background: 
        linear-gradient(110deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
        background-size: 200% 100%;
        animation: shimmer 10s infinite linear;">
    </div>

    {{-- Floating decorative fish (desktop only) --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none hidden md:block" aria-hidden="true">
        <i class="fas fa-fish fish-deco-1 absolute text-white/5 text-[7rem] top-[15%] right-[8%]"></i>
        <i class="fas fa-fish fish-deco-2 absolute text-cyan-300/10 text-[5rem] bottom-[25%] left-[5%]"></i>
        <i class="fas fa-fish fish-deco-3 absolute text-amber-300/8 text-[4rem] top-[55%] right-[18%]"></i>
    </div>
    
    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 md:py-20 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">

            {{-- Hero Text (Left side on desktop, centered on mobile) --}}
            <div class="text-center lg:text-left">
                <div class="hero-badge-anim inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full mb-5 sm:mb-6"
                     style="background: rgba(20,20,40,0.7); border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(20px); box-shadow: 0 4px 20px rgba(0,0,0,0.4);">
                    <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shadow-[0_0_10px_rgba(251,191,36,0.7)]"></div>
                    <span class="text-white font-semibold text-xs sm:text-sm drop-shadow-lg hero-text-xs">Platform Jual Beli Ikan #1 di Tapos</span>
                </div>
                
                <h1 class="hero-title-anim text-3xl xs:text-4xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.1] mb-5 sm:mb-6">
                    <span class="text-white drop-shadow-[0_4px_20px_rgba(0,0,0,0.9)]">Ikan Segar</span>
                    <span class="block mt-1 sm:mt-2 bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-100 bg-clip-text text-transparent drop-shadow-[0_4px_25px_rgba(251,191,36,0.6)]">
                        Langsung dari Kolam
                    </span>
                </h1>
                
                <p class="hero-sub-anim text-sm sm:text-base md:text-lg lg:text-xl text-white/95 mb-7 sm:mb-8 max-w-2xl lg:max-w-none mx-auto lg:mx-0 leading-relaxed drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]">
                    Dapatkan <strong class="text-amber-200">Ikan Nila & Ikan Mas</strong> segar berkualitas premium dengan harga terbaik. Tanpa perantara!
                </p>
                
                {{-- CTA Buttons --}}
                <div class="hero-btn-anim flex flex-col xs:flex-row sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start mb-8 sm:mb-10 cta-flex-wrap">
                    <a href="{{ route('register') }}" class="btn-shine-wrap btn-shiny group inline-flex items-center justify-center gap-2 sm:gap-3 px-5 sm:px-8 py-3 sm:py-4 font-bold rounded-2xl transition-all duration-300 hover:scale-105 text-sm sm:text-base hero-btn-xs"
                       style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #1e293b; box-shadow: 0 10px 40px rgba(251,191,36,0.5), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <i class="fas fa-rocket"></i>
                        <span>Mulai Sekarang - Gratis!</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform hidden sm:inline"></i>
                    </a>
                    <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 px-5 sm:px-8 py-3 sm:py-4 font-semibold text-white rounded-2xl transition-all duration-300 hover:bg-white/30 text-sm sm:text-base hero-btn-xs"
                       style="background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.4); backdrop-filter: blur(20px); box-shadow: 0 4px 20px rgba(0,0,0,0.4);">
                        <i class="fas fa-fish"></i>
                        <span>Lihat Katalog</span>
                    </a>
                </div>

                {{-- Trust Badges --}}
                <div class="hero-trust-anim flex flex-wrap items-center gap-3 sm:gap-5 justify-center lg:justify-start trust-wrap">
                    <div class="flex items-center gap-1.5 sm:gap-2 text-white text-xs sm:text-sm">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white/25 backdrop-blur-md flex items-center justify-center border border-white/40 shadow-lg flex-shrink-0">
                            <i class="fas fa-truck text-amber-300 text-xs drop-shadow-md"></i>
                        </div>
                        <span class="drop-shadow-lg font-medium">Same-Day</span>
                    </div>
                    <div class="flex items-center gap-1.5 sm:gap-2 text-white text-xs sm:text-sm">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white/25 backdrop-blur-md flex items-center justify-center border border-white/40 shadow-lg flex-shrink-0">
                            <i class="fas fa-shield-alt text-amber-300 text-xs drop-shadow-md"></i>
                        </div>
                        <span class="drop-shadow-lg font-medium">100% Garansi</span>
                    </div>
                    <div class="flex items-center gap-1.5 sm:gap-2 text-white text-xs sm:text-sm">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white/25 backdrop-blur-md flex items-center justify-center border border-white/40 shadow-lg flex-shrink-0">
                            <i class="fas fa-tags text-amber-300 text-xs drop-shadow-md"></i>
                        </div>
                        <span class="drop-shadow-lg font-medium">Harga Transparan</span>
                    </div>
                </div>
            </div>

            {{-- Stats Card (Right side on desktop) --}}
            <div class="w-full hero-card-anim">
                <div class="relative">
                    {{-- Glow Effect --}}
                    <div class="absolute -inset-2 sm:-inset-4 bg-gradient-to-r from-amber-400/25 to-cyan-400/25 rounded-3xl blur-2xl"></div>
                    
                    <div class="relative rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 overflow-hidden"
                         style="background: rgba(20,20,40,0.6); border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(30px); box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 stats-grid-mobile">
                            <div class="stats-card text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                                <div class="stat-number text-2xl sm:text-3xl md:text-4xl font-extrabold text-amber-300 mb-1 drop-shadow-[0_2px_15px_rgba(251,191,36,0.6)]"
                                     data-counter="3" data-suffix="+">3+</div>
                                <p class="text-white/95 text-xs sm:text-sm drop-shadow-md font-medium">Produk Tersedia</p>
                            </div>
                            <div class="stats-card text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                                <div class="stat-number text-2xl sm:text-3xl md:text-4xl font-extrabold text-cyan-300 mb-1 drop-shadow-[0_2px_15px_rgba(34,211,238,0.6)]">100%</div>
                                <p class="text-white/95 text-xs sm:text-sm drop-shadow-md font-medium">Segar Terjamin</p>
                            </div>
                            <div class="stats-card text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                                <div class="stat-number text-2xl sm:text-3xl md:text-4xl font-extrabold text-amber-300 mb-1 drop-shadow-[0_2px_15px_rgba(251,191,36,0.6)]">24h</div>
                                <p class="text-white/95 text-xs sm:text-sm drop-shadow-md font-medium">Pengiriman Cepat</p>
                            </div>
                            <div class="stats-card text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                                <div class="stat-number text-2xl sm:text-3xl md:text-4xl font-extrabold text-cyan-300 mb-1 drop-shadow-[0_2px_15px_rgba(34,211,238,0.6)]">5.0</div>
                                <p class="text-white/95 text-xs sm:text-sm drop-shadow-md font-medium">Rating Pelanggan</p>
                            </div>
                        </div>
                        
                        {{-- Decorative fish (hidden on small screens) --}}
                        <div class="absolute -bottom-4 -right-4 text-white/5 text-7xl sm:text-9xl hidden sm:block pointer-events-none fish-deco-1">
                            <i class="fas fa-fish"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Animated Wave Divider --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" style="height:50px;display:block;">
            <path d="M0 80L60 73C120 67 240 53 360 50C480 47 600 53 720 57C840 60 960 60 1080 57C1200 53 1320 47 1380 43L1440 40V80H0Z" fill="rgba(12,74,110,0.15)"/>
            <path d="M0 80L80 68C160 56 320 32 480 29C640 26 800 44 960 50C1120 56 1280 50 1360 47L1440 44V80H0Z" fill="rgba(8,145,178,0.12)" style="animation: waveMorph 8s ease-in-out infinite;"/>
        </svg>
    </div>
</section>

{{-- ABOUT SECTION --}}
<section class="py-12 sm:py-16 md:py-20 lg:py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-14 reveal-up">
            <div class="section-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold mb-4" style="background: rgba(6,182,212,0.12); color: #67e8f9; border: 1px solid rgba(6,182,212,0.2);">
                <i class="fas fa-info-circle"></i> Tentang Kami
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">Apa itu FishMarket?</h2>
            <p class="text-white/60 text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed px-4">
                Platform e-commerce yang menghubungkan konsumen langsung dengan petani ikan lokal melalui sistem pemesanan online yang mudah dan transparan.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            {{-- Mission Card --}}
            <div class="reveal-left card-hover-magic relative rounded-2xl sm:rounded-3xl p-6 sm:p-8 overflow-hidden group"
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
                <div class="reveal-right reveal-d1 card-glass card-hover-magic rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start gap-3 sm:gap-4 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);">
                        <i class="fas fa-handshake text-white text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white mb-1 text-sm sm:text-base">Langsung dari Petani</h4>
                        <p class="text-white/50 text-xs sm:text-sm">Tanpa perantara, harga lebih murah dan petani mendapat keuntungan lebih baik.</p>
                    </div>
                </div>
                <div class="reveal-right reveal-d2 card-glass card-hover-magic rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start gap-3 sm:gap-4 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-shield-alt text-white text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white mb-1 text-sm sm:text-base">Jaminan Kualitas</h4>
                        <p class="text-white/50 text-xs sm:text-sm">Setiap ikan dipilih dengan standar tinggi dan dijamin kesegarannya.</p>
                    </div>
                </div>
                <div class="reveal-right reveal-d3 card-glass card-hover-magic rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start gap-3 sm:gap-4 group">
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
        <div class="text-center mb-10 sm:mb-14 reveal-up">
            <div class="section-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold mb-4" style="background: rgba(255,255,255,0.08); color: #67e8f9; border: 1px solid rgba(255,255,255,0.15);">
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
            ] as $i => $feature)
            <div class="reveal-scale reveal-d{{ $i+1 }} card-elevated card-hover-magic rounded-2xl sm:rounded-3xl p-6 sm:p-8 text-center group">
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
        <div class="text-center mb-10 sm:mb-14 reveal-up">
            <div class="section-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold mb-4" style="background: rgba(6,182,212,0.12); color: #67e8f9; border: 1px solid rgba(6,182,212,0.2);">
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
            ] as $i => $step)
            <div class="reveal-up reveal-d{{ $i+1 }} text-center relative">
                <div class="step-circle w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-5 shadow-xl bg-gradient-to-br {{ $step['gradient'] }} cursor-pointer"
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
    
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-up">
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl flex items-center justify-center mx-auto mb-5 sm:mb-6 fish-deco-1"
             style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
            <i class="fas fa-fish text-3xl sm:text-4xl text-white"></i>
        </div>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">Siap Belanja Ikan Segar?</h2>
        <p class="text-ocean-100 text-sm sm:text-base md:text-lg mb-6 sm:mb-8 max-w-lg mx-auto px-4">
            Bergabunglah dengan ribuan pelanggan yang sudah mempercayai FishMarket untuk kebutuhan ikan segar mereka.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="btn-shine-wrap btn-shiny inline-flex items-center justify-center gap-3 px-6 sm:px-8 py-3 sm:py-4 font-bold text-ocean-900 rounded-2xl transition-all duration-300 hover:scale-105 text-sm sm:text-base"
               style="background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); box-shadow: 0 8px 30px rgba(0,0,0,0.2);">
                <i class="fas fa-rocket"></i> Daftar Sekarang - Gratis!
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-4 font-semibold text-white rounded-2xl transition-all text-sm sm:text-base hover:bg-white/25"
               style="background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.3);">
                <i class="fas fa-sign-in-alt"></i> Sudah Punya Akun? Login
            </a>
        </div>
    </div>
</section>

@push('scripts')
@guest
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── 1. PAGE-LOAD PROGRESS BAR ─────────────────────── */
    const bar = document.getElementById('lp-progress');
    if (bar) {
        let w = 0;
        const iv = setInterval(() => {
            w = Math.min(w + Math.random() * 18, 90);
            bar.style.width = w + '%';
        }, 120);
        window.addEventListener('load', () => {
            clearInterval(iv);
            bar.style.width = '100%';
            setTimeout(() => { bar.style.opacity = '0'; bar.style.transition = 'opacity .4s'; }, 400);
        });
    }

    /* ── 2. INTERSECTION OBSERVER – BIDIRECTIONAL SCROLL REVEAL ── */
    const revealEls = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right, .reveal-scale');

    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                const el   = e.target;
                const rect = el.getBoundingClientRect();

                if (e.isIntersecting) {
                    /* ── Element entering viewport → show ── */
                    el.classList.remove('exit-up');
                    /* tiny rAF so removing exit-up doesn't cancel the enter transition */
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => el.classList.add('visible'));
                    });
                } else {
                    /* ── Element leaving viewport ── */
                    if (rect.top < 0) {
                        /* Left from the TOP → apply exit-up (slide/shrink upward) */
                        el.classList.remove('visible');
                        el.classList.add('exit-up');
                    } else {
                        /* Left from the BOTTOM (not yet reached) → revert to initial */
                        el.classList.remove('visible', 'exit-up');
                    }
                }
            });
        }, {
            threshold: [0, 0.15],          /* fire on enter AND fully-left */
            rootMargin: '0px 0px -60px 0px'
        });

        revealEls.forEach(el => obs.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('visible'));
    }

    /* ── 3. COUNTER ANIMATION ──────────────────────────── */
    const counters = document.querySelectorAll('[data-counter]');
    const counterObs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const el     = e.target;
                const target = parseInt(el.dataset.counter, 10);
                const suffix = el.dataset.suffix || '';
                let current  = 0;
                const step   = Math.ceil(target / 30);
                const timer  = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current + suffix;
                    if (current >= target) clearInterval(timer);
                }, 40);
                counterObs.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(el => counterObs.observe(el));

    /* ── 4. NAVBAR PARALLAX TINT ON SCROLL ─────────────── */
    const heroSection = document.querySelector('section.min-h-screen');
    if (heroSection) {
        const overlay = heroSection.querySelector('.fixed, .absolute');
    }

    /* ── 5. SMOOTH HOVER TILT ON STATS CARDS ───────────── */
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width  - 0.5;
            const y = (e.clientY - rect.top)  / rect.height - 0.5;
            card.style.transform = `perspective(400px) rotateY(${x*12}deg) rotateX(${-y*12}deg) scale(1.03)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.22,1,.36,1)';
        });
    });

    /* ── 6. RIPPLE ON BUTTONS ──────────────────────────── */
    document.querySelectorAll('a[href*="register"], a[href*="catalog"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect   = btn.getBoundingClientRect();
            const size   = Math.max(rect.width, rect.height);
            ripple.style.cssText = `
                position:absolute; border-radius:50%;
                width:${size}px; height:${size}px;
                left:${e.clientX - rect.left - size/2}px;
                top:${e.clientY  - rect.top  - size/2}px;
                background:rgba(255,255,255,.25);
                transform:scale(0); animation:rippleAnim .55s linear;
                pointer-events:none;
            `;
            if (getComputedStyle(btn).position === 'static') btn.style.position = 'relative';
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    /* inject ripple keyframe once */
    if (!document.getElementById('ripple-style')) {
        const s = document.createElement('style');
        s.id = 'ripple-style';
        s.textContent = '@keyframes rippleAnim{to{transform:scale(4);opacity:0}}';
        document.head.appendChild(s);
    }
});
</script>
@endguest
@endpush

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

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 sm:gap-4 mb-6 sm:mb-8">
            <div>
                <p class="text-cyan-300/70 text-xs sm:text-sm font-semibold uppercase tracking-widest mb-1">Pilihan Terbaik Hari Ini</p>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white leading-tight">Produk Segar Hari Ini</h2>
                <p class="text-white/40 text-xs sm:text-sm mt-1">Langsung dari kolam, segar untuk meja makan Anda</p>
            </div>
            <a href="{{ route('catalog') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-cyan-300 hover:text-white hover:bg-white/10 border border-cyan-400/20 hover:border-cyan-300/40 transition-all duration-200">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if($produks->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
            @foreach($produks->take(8) as $index => $produk)
            {{-- Determine category color class --}}
            @php
                $catClass = match(true) {
                    str_contains(strtolower($produk->kategori), 'nila') => 'badge-cat-nila',
                    str_contains(strtolower($produk->kategori), 'mas')  => 'badge-cat-mas',
                    str_contains(strtolower($produk->kategori), 'lele') => 'badge-cat-lele',
                    default => 'badge-cat-default',
                };
                $stokLow = $produk->stok < 10;
            @endphp

            <div class="product-card" style="animation: cardFadeUp .5s cubic-bezier(.22,1,.36,1) {{ $index * 0.07 }}s both;">

                {{-- ── IMAGE ──────────────────────────── --}}
                <div class="product-img-wrap">
                    @if($produk->foto)
                        <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama }}" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-cyan-900/40 to-teal-900/40">
                            <i class="fas fa-fish text-white/15 text-5xl sm:text-6xl"></i>
                        </div>
                    @endif

                    {{-- Wishlist button --}}
                    <div class="absolute top-2 right-2 z-10"
                         x-data="{{ json_encode(['wishlisted' => in_array($produk->id, $wishlistedIds ?? []), 'loading' => false]) }}"
                         x-init="wishlisted = {{ in_array($produk->id, $wishlistedIds ?? []) ? 'true' : 'false' }}">
                        <button @click.prevent="
                                if (loading) return;
                                loading = true;
                                fetch('{{ route('wishlist.toggle') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ produk_id: {{ $produk->id }} }),
                                })
                                .then(r => r.json())
                                .then(d => { wishlisted = d.status === 'added'; loading = false; })
                                .catch(() => loading = false);"
                                :disabled="loading"
                                class="w-8 h-8 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 focus:outline-none"
                                :class="wishlisted
                                    ? 'bg-red-500 text-white scale-110 shadow-red-500/40'
                                    : 'bg-black/40 backdrop-blur text-white/60 hover:bg-black/60 hover:text-red-400 hover:scale-110'"
                                :title="wishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist'">
                            <template x-if="loading"><i class="fas fa-spinner fa-spin text-xs"></i></template>
                            <template x-if="!loading"><i class="fas fa-heart text-xs"></i></template>
                        </button>
                    </div>

                    {{-- Stock chip (on image, bottom-left) --}}
                    <div class="stock-chip">
                        <i class="fas fa-box-open text-[.6rem] opacity-70"></i>
                        <span :class="{{ $stokLow ? 'true' : 'false' }} ? 'text-orange-300' : ''" 
                              class="{{ $stokLow ? 'text-orange-300' : '' }}">
                            {{ number_format($produk->stok, 1) }} Kg
                            @if($stokLow) <span class="ml-0.5 text-orange-300">●</span> @endif
                        </span>
                    </div>
                </div>

                {{-- ── BODY ──────────────────────────── --}}
                <div class="product-body">
                    {{-- Category badge --}}
                    <div>
                        <span class="badge-cat {{ $catClass }}">
                            <i class="fas fa-tag text-[.55rem]"></i>
                            {{ $produk->kategori }}
                        </span>
                    </div>

                    {{-- Product name --}}
                    <h3 class="font-bold text-white text-sm sm:text-[.95rem] leading-snug line-clamp-2 min-h-[2.4em]">
                        {{ $produk->nama }}
                    </h3>

                    {{-- Divider --}}
                    <div class="h-px bg-white/8 my-0.5"></div>

                    {{-- Price --}}
                    <div class="flex items-end justify-between gap-1">
                        <div>
                            <p class="product-price">Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}</p>
                            <span class="text-[.68rem] text-white/35 font-normal leading-none">per kilogram</span>
                        </div>
                    </div>

                    {{-- Buy button --}}
                    <a href="{{ route('produk.show', $produk) }}" class="btn-buy mt-1">
                        <i class="fas fa-cart-plus text-xs"></i>
                        <span>Beli Sekarang</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Animation keyframe (once) --}}
        <style>
            @keyframes cardFadeUp {
                from { opacity: 0; transform: translateY(28px) scale(.97); }
                to   { opacity: 1; transform: none; }
            }
        </style>
        @else
        <div class="text-center py-16 text-white/30">
            <div class="w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-4" style="background:rgba(255,255,255,0.05);">
                <i class="fas fa-box-open text-4xl"></i>
            </div>
            <p class="text-lg font-semibold text-white/40">Belum ada produk tersedia.</p>
            <p class="text-sm text-white/25 mt-1">Silakan coba lagi nanti.</p>
        </div>
        @endif

        <div class="text-center mt-7 sm:hidden">
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white border border-white/20 hover:bg-white/10 transition-all">
                <i class="fas fa-th-large text-xs"></i> Lihat Semua Produk
            </a>
        </div>
    </div>
</section>

{{-- QUICK BENEFITS --}}
<section class="py-5 sm:py-6 md:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-2 sm:gap-3 md:gap-4">
            @foreach([
                ['icon' => 'fa-truck', 'title' => 'Same-Day', 'desc' => 'Pesan pagi, sore sampai', 'color' => 'from-cyan-500 to-sky-600'],
                ['icon' => 'fa-shield-halved', 'title' => 'Garansi Segar', 'desc' => '100% uang kembali', 'color' => 'from-emerald-500 to-teal-600'],
                ['icon' => 'fa-hand-holding-dollar', 'title' => 'Harga Petani', 'desc' => 'Langsung dari kolam', 'color' => 'from-amber-500 to-orange-500'],
            ] as $b)
            <div class="group flex flex-col items-center text-center p-3 sm:p-4 md:p-5 rounded-xl sm:rounded-2xl transition-all duration-300 hover:scale-[1.03]"
                 style="background:rgba(255,255,255,0.055); border:1px solid rgba(255,255,255,0.09);">
                <div class="w-9 h-9 sm:w-11 sm:h-11 md:w-13 md:h-13 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-3 bg-gradient-to-br {{ $b['color'] }} shadow-lg">
                    <i class="fas {{ $b['icon'] }} text-white text-sm sm:text-base"></i>
                </div>
                <h3 class="font-bold text-white text-xs sm:text-sm">{{ $b['title'] }}</h3>
                <p class="text-white/40 text-[.68rem] sm:text-xs mt-0.5 leading-tight">{{ $b['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endguest
@endsection