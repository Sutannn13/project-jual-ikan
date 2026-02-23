<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FishMarket') | FishMarket</title>
    
    {{-- Premium Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Hide horizontal scrollbar & prevent white corners */
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw;
            background: linear-gradient(135deg, #0c4a6e 0%, #0e7490 50%, #0891b2 100%) !important;
        }
        
        /* Premium Ocean Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 0px; }
        ::-webkit-scrollbar-track { 
            background: linear-gradient(180deg, rgba(6,182,212,0.1) 0%, rgba(20,184,166,0.1) 100%);
        }
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #06b6d4 0%, #14b8a6 100%);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(6,182,212,0.5);
        }
        ::-webkit-scrollbar-thumb:hover { 
            background: linear-gradient(180deg, #22d3ee 0%, #2dd4bf 100%);
        }

        /* Floating orb animation - lebih dinamis */
        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
            25% { transform: translate(40px, -30px) scale(1.1) rotate(90deg); }
            50% { transform: translate(-20px, -50px) scale(0.9) rotate(180deg); }
            75% { transform: translate(-40px, -15px) scale(1.05) rotate(270deg); }
        }
        
        /* Wave animation untuk background */
        @keyframes wave {
            0%, 100% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(-25%) translateY(-5%); }
        }
        
        @keyframes waveReverse {
            0%, 100% { transform: translateX(-25%) translateY(-5%); }
            50% { transform: translateX(0) translateY(0); }
        }
        
        /* Fish bubble animation */
        @keyframes bubble {
            0% { transform: translateY(0) scale(1); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-100vh) scale(1.5); opacity: 0; }
        }
        
        /* Navbar glassmorphism dengan ocean theme */
        .nav-blur {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.3) 0%, rgba(20, 184, 166, 0.25) 100%);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .nav-blur.scrolled {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.6) 0%, rgba(20, 184, 166, 0.5) 100%) !important;
            backdrop-filter: blur(32px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(32px) saturate(200%) !important;
            box-shadow: 0 8px 32px rgba(6, 182, 212, 0.3), 0 0 80px rgba(20, 184, 166, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
        }
        
        /* Shimmer effect untuk accent elements */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-white overflow-x-hidden" 
      x-data="{ 
          mobileOpen: false,
          bubbles: [],
          init() {
              // Generate random bubbles
              setInterval(() => {
                  if (this.bubbles.length < 15) {
                      this.bubbles.push({
                          id: Date.now() + Math.random(),
                          left: Math.random() * 100,
                          size: 10 + Math.random() * 40,
                          duration: 10 + Math.random() * 15,
                          delay: Math.random() * 5
                      });
                  }
              }, 3000);
              
              // Remove old bubbles
              setInterval(() => {
                  if (this.bubbles.length > 0) {
                      this.bubbles.shift();
                  }
              }, 15000);
          }
      }">

    {{-- ========================================
         🌊 PREMIUM OCEAN BACKGROUND - ULTRA VIBRANT
         ======================================== --}}
    {{-- Base Ocean Gradient dengan multi-layer --}}
    <div id="master-bg-ocean" class="fixed inset-0 -z-10" style="background: 
        linear-gradient(135deg, 
            #0c4a6e 0%, 
            #0e7490 15%, 
            #0891b2 30%, 
            #06b6d4 45%, 
            #14b8a6 60%, 
            #0891b2 75%, 
            #0e7490 90%, 
            #0c4a6e 100%
        );"></div>
    
    {{-- Animated Wave Layers dengan Alpine.js --}}
    <div class="fixed inset-0 -z-10 overflow-hidden opacity-40">
        <div class="absolute inset-0" style="
            background: radial-gradient(ellipse at 50% 120%, rgba(34, 211, 238, 0.4) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 0%, rgba(20, 184, 166, 0.3) 0%, transparent 50%),
                        radial-gradient(ellipse at 0% 50%, rgba(6, 182, 212, 0.3) 0%, transparent 50%);
            animation: wave 20s ease-in-out infinite;
        "></div>
        <div class="absolute inset-0" style="
            background: radial-gradient(ellipse at 20% 0%, rgba(45, 212, 191, 0.3) 0%, transparent 60%),
                        radial-gradient(ellipse at 100% 100%, rgba(34, 211, 238, 0.25) 0%, transparent 50%);
            animation: waveReverse 25s ease-in-out infinite;
        "></div>
    </div>
    
    {{-- Radial Highlights - Lebih vibrant --}}
    <div id="master-bg-radial" class="fixed inset-0 -z-10" style="background: 
        radial-gradient(circle at 20% 20%, rgba(34, 211, 238, 0.35), transparent 40%), 
        radial-gradient(circle at 80% 30%, rgba(20, 184, 166, 0.3), transparent 45%), 
        radial-gradient(circle at 50% 80%, rgba(6, 182, 212, 0.25), transparent 50%),
        radial-gradient(circle at 10% 90%, rgba(45, 212, 191, 0.2), transparent 40%);"></div>
    
    {{-- Grid Pattern dengan glow effect --}}
    <div id="master-bg-grid" class="fixed inset-0 -z-10 opacity-20" style="
        background-image: 
            linear-gradient(rgba(34, 211, 238, 0.15) 1px, transparent 1px),
            linear-gradient(90deg, rgba(34, 211, 238, 0.15) 1px, transparent 1px);
        background-size: 50px 50px;
        mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
    "></div>
    
    {{-- Animated Floating Orbs - Lebih banyak & lebih besar --}}
    <div id="master-bg-orbs" class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-48 -left-48 w-[700px] h-[700px] bg-gradient-to-br from-cyan-400/25 via-teal-400/15 to-transparent rounded-full blur-[120px]" 
             style="animation: floatOrb 20s ease-in-out infinite;"></div>
        <div class="absolute -bottom-60 -right-60 w-[800px] h-[800px] bg-gradient-to-tl from-sky-400/20 via-cyan-500/15 to-transparent rounded-full blur-[130px]" 
             style="animation: floatOrb 25s ease-in-out infinite; animation-delay: -5s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-teal-500/15 via-cyan-400/15 to-sky-500/15 rounded-full blur-[140px]" 
             style="animation: floatOrb 30s ease-in-out infinite; animation-delay: -10s;"></div>
        <div class="absolute top-20 right-1/4 w-[400px] h-[400px] bg-gradient-to-bl from-cyan-300/20 to-transparent rounded-full blur-[100px]" 
             style="animation: floatOrb 22s ease-in-out infinite; animation-delay: -7s;"></div>
    </div>
    
    {{-- Fish Bubbles dengan Alpine.js --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <template x-for="bubble in bubbles" :key="bubble.id">
            <div class="absolute bottom-0 rounded-full bg-gradient-to-t from-white/30 via-cyan-200/40 to-white/20 border border-white/30"
                 :style="`left: ${bubble.left}%; width: ${bubble.size}px; height: ${bubble.size}px; animation: bubble ${bubble.duration}s ease-in-out ${bubble.delay}s infinite;`">
            </div>
        </template>
    </div>

    {{-- ========================================
         🌊 PREMIUM NAVBAR - ULTRA GLASSMORPHISM
         ======================================== --}}
    <nav class="nav-blur sticky top-0 z-50 border-b border-white/20" 
         x-data="{ scrolled: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 20)" 
         :class="{ 'scrolled': scrolled }">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/5 to-white/10 backdrop-blur-2xl transition-all duration-300"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-cyan-400/10 to-transparent pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-18">
                {{-- Logo dengan glow effect --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="relative w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110"
                         style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.3) 0%, rgba(20, 184, 166, 0.25) 100%); 
                                box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4), inset 0 1px 0 rgba(255,255,255,0.3);">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-white/20 to-transparent"></div>
                        <i class="fas fa-fish text-white text-lg relative z-10 drop-shadow-lg"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-white via-cyan-100 to-white bg-clip-text text-transparent drop-shadow-2xl">
                        FishMarket
                    </span>
                </a>

                {{-- Desktop Nav dengan hover effects --}}
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('home') }}" 
                       class="relative px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('home') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white shadow-lg shadow-cyan-500/30 border border-white/30' : 'text-white/80 hover:bg-white/15 hover:text-white hover:shadow-lg hover:shadow-cyan-500/20' }}">
                        <span class="relative z-10">Beranda</span>
                        @if(request()->routeIs('home'))
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-400/20 to-teal-400/20 blur-sm"></div>
                        @endif
                    </a>
                    <a href="{{ route('catalog') }}" 
                       class="relative px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('catalog') || request()->routeIs('produk.show') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white shadow-lg shadow-cyan-500/30 border border-white/30' : 'text-white/80 hover:bg-white/15 hover:text-white hover:shadow-lg hover:shadow-cyan-500/20' }}">
                        <span class="relative z-10">Katalog</span>
                        @if(request()->routeIs('catalog') || request()->routeIs('produk.show'))
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-400/20 to-teal-400/20 blur-sm"></div>
                        @endif
                    </a>
                    @auth
                        <a href="{{ route('my.orders') }}" 
                           class="relative px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('my.orders') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white shadow-lg shadow-cyan-500/30 border border-white/30' : 'text-white/80 hover:bg-white/15 hover:text-white hover:shadow-lg hover:shadow-cyan-500/20' }}">
                            <span class="relative z-10">Pesanan Saya</span>
                            @if(request()->routeIs('my.orders'))
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-400/20 to-teal-400/20 blur-sm"></div>
                            @endif
                        </a>
                        <a href="{{ route('wishlist.index') }}" 
                           class="relative px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('wishlist.*') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white shadow-lg shadow-cyan-500/30 border border-white/30' : 'text-white/80 hover:bg-white/15 hover:text-white hover:shadow-lg hover:shadow-cyan-500/20' }}">
                            <i class="fas fa-heart text-xs mr-1"></i>
                            <span class="relative z-10">Wishlist</span>
                            @if(request()->routeIs('wishlist.*'))
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-400/20 to-teal-400/20 blur-sm"></div>
                            @endif
                        </a>
                    @endauth
                </div>

                {{-- Cart Icon dengan glow effect --}}
                @auth
                <div class="hidden md:flex items-center">
                    @php
                        $cartCount = \App\Http\Controllers\CartController::getCartCount();
                    @endphp
                    <a href="{{ route('cart.index') }}" 
                       class="relative w-12 h-12 flex items-center justify-center rounded-xl text-white/90 hover:bg-gradient-to-br hover:from-cyan-500/30 hover:to-teal-500/30 hover:text-white transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/40 {{ request()->routeIs('cart.*') ? 'bg-gradient-to-br from-cyan-500/40 to-teal-500/40 shadow-lg shadow-cyan-500/30 border border-white/30' : '' }}">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-xs font-bold text-white flex items-center justify-center {{ session('cart_added') ? 'badge-cart-pop' : 'animate-pulse' }}"
                              style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 4px 12px rgba(249,115,22,0.6), 0 0 20px rgba(249,115,22,0.4);">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </a>
                </div>
                @endauth

                {{-- Right side - Login/Register atau User Menu --}}
                <div class="hidden md:flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-semibold text-white/90 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="relative group px-6 py-2.5 text-sm font-bold text-white rounded-xl overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-teal-500/40">
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-teal-500"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-teal-500 to-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="relative flex items-center gap-2">
                                <i class="fas fa-user-plus text-xs"></i> Daftar
                            </span>
                        </a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center gap-3 pl-3 pr-4 py-2 rounded-xl text-sm font-medium text-white/95 hover:bg-white/15 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/30">
                                <div class="relative w-10 h-10 rounded-full flex items-center justify-center overflow-hidden ring-2 ring-white/30 group-hover:ring-cyan-400/60 transition-all"
                                     style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.3), rgba(20, 184, 166, 0.3));">
                                    @if(Auth::user()->foto_profil)
                                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Foto Profil" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-user text-white text-sm"></i>
                                    @endif
                                </div>
                                <span class="hidden sm:block max-w-[120px] truncate text-white font-semibold">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-white/70 transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-0 mt-3 w-64 rounded-2xl shadow-2xl border border-white/20 py-2 z-50 overflow-hidden"
                                 style="background: linear-gradient(165deg, rgba(8,51,68,0.98) 0%, rgba(14,116,144,0.95) 100%); backdrop-filter: blur(30px);">
                                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-transparent to-teal-500/10"></div>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="relative flex items-center gap-3 px-4 py-3 text-sm text-white/90 hover:bg-white/15 hover:text-white transition-all">
                                        <i class="fas fa-tachometer-alt w-5 text-center text-cyan-400"></i> 
                                        <span class="font-medium">Dashboard Admin</span>
                                    </a>
                                    <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent my-2"></div>
                                @endif
                                <a href="{{ route('profile.show') }}" class="relative flex items-center gap-3 px-4 py-3 text-sm text-white/90 hover:bg-white/15 hover:text-white transition-all">
                                    <i class="fas fa-user-edit w-5 text-center text-cyan-400"></i> 
                                    <span class="font-medium">Profil Saya</span>
                                </a>
                                <a href="{{ route('my.orders') }}" class="relative flex items-center gap-3 px-4 py-3 text-sm text-white/90 hover:bg-white/15 hover:text-white transition-all">
                                    <i class="fas fa-box w-5 text-center text-teal-400"></i> 
                                    <span class="font-medium">Pesanan Saya</span>
                                </a>
                                <a href="{{ route('user.addresses.index') }}" class="relative flex items-center gap-3 px-4 py-3 text-sm text-white/90 hover:bg-white/15 hover:text-white transition-all">
                                    <i class="fas fa-map-marker-alt w-5 text-center text-sky-400"></i> 
                                    <span class="font-medium">Alamat Saya</span>
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="relative flex items-center gap-3 px-4 py-3 text-sm text-white/90 hover:bg-white/15 hover:text-white transition-all">
                                    <i class="fas fa-heart w-5 text-center text-rose-400"></i> 
                                    <span class="font-medium">Wishlist</span>
                                </a>
                                <a href="{{ route('chat.index') }}" class="relative flex items-center gap-3 px-4 py-3 text-sm text-white/90 hover:bg-white/15 hover:text-white transition-all">
                                    <i class="fas fa-comments w-5 text-center text-cyan-400"></i> 
                                    <span class="font-medium">Chat Admin</span>
                                </a>
                                <a href="{{ route('tickets.index') }}" class="relative flex items-center gap-3 px-4 py-3 text-sm text-white/90 hover:bg-white/15 hover:text-white transition-all">
                                    <i class="fas fa-headset w-5 text-center text-amber-400"></i> 
                                    <span class="font-medium">Support Ticket</span>
                                </a>
                                <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent my-2"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="relative flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/20 w-full text-left transition-all font-medium">
                                        <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

                {{-- Mobile Cart + Hamburger dengan improved styling --}}
                <div class="md:hidden flex items-center gap-2">
                    @auth
                    <a href="{{ route('cart.index') }}" 
                       class="relative w-11 h-11 flex items-center justify-center rounded-xl text-white/90 hover:bg-gradient-to-br hover:from-cyan-500/30 hover:to-teal-500/30 hover:text-white transition-all {{ request()->routeIs('cart.*') ? 'bg-gradient-to-br from-cyan-500/40 to-teal-500/40 border border-white/30' : '' }}">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        @php
                            $mobileCartCount = \App\Http\Controllers\CartController::getCartCount();
                        @endphp
                        @if($mobileCartCount > 0)
                        <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-xs font-bold text-white flex items-center justify-center {{ session('cart_added') ? 'badge-cart-pop' : 'animate-pulse' }}"
                              style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 3px 10px rgba(249,115,22,0.6);">
                            {{ $mobileCartCount }}
                        </span>
                        @endif
                    </a>
                    @endauth
                    <button @click="mobileOpen = !mobileOpen" 
                            class="w-11 h-11 flex items-center justify-center text-white rounded-xl hover:bg-white/15 transition-all"
                            :class="{ 'bg-white/20': mobileOpen }">
                        <i class="fas text-lg transition-transform" :class="mobileOpen ? 'fa-times rotate-90' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu dengan improved glassmorphism --}}
            <div x-show="mobileOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="md:hidden pb-4 mt-4">
                <div class="space-y-2 p-3 rounded-2xl" style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.15) 0%, rgba(20, 184, 166, 0.15) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.15);">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('home') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-home w-5 text-center"></i> Beranda
                    </a>
                    <a href="{{ route('catalog') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('catalog') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-fish w-5 text-center"></i> Katalog
                    </a>
                    @auth
                        <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('cart.*') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-shopping-cart w-5 text-center"></i> Keranjang
                            @php $menuCartCount = \App\Http\Controllers\CartController::getCartCount(); @endphp
                            @if($menuCartCount > 0)
                                <span class="ml-auto px-2.5 py-1 rounded-full text-xs font-bold text-white {{ session('cart_added') ? 'badge-cart-pop' : '' }}" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">{{ $menuCartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('my.orders') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('my.orders') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-box w-5 text-center"></i> Pesanan Saya
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('wishlist.*') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-heart w-5 text-center"></i> Wishlist
                        </a>
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('profile.*') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-user-edit w-5 text-center"></i> Profil
                        </a>
                        <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('chat.*') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-comments w-5 text-center"></i> Chat Admin
                        </a>
                        <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('tickets.*') ? 'bg-gradient-to-r from-cyan-500/40 to-teal-500/40 text-white border border-white/20 shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-headset w-5 text-center"></i> Support Ticket
                        </a>
                        @if(Auth::user()->isAdmin())
                            <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent my-2"></div>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all">
                                <i class="fas fa-tachometer-alt w-5 text-center text-cyan-400"></i> Dashboard Admin
                            </a>
                        @endif
                        <div class="pt-3 mt-3" style="border-top: 1px solid rgba(255,255,255,0.15);">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-red-500/20 to-rose-500/20 text-red-300 hover:from-red-500/30 hover:to-rose-500/30 hover:text-white transition-all border border-red-400/20">
                                    <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex gap-3 pt-4 mt-3" style="border-top: 1px solid rgba(255,255,255,0.15);">
                            <a href="{{ route('login') }}" class="flex-1 text-sm text-center py-3.5 rounded-xl font-bold text-white/90 border border-white/30 hover:bg-white/15 transition-all">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="flex-1 text-sm text-center py-3.5 rounded-xl font-bold text-white shadow-lg transition-all hover:scale-105" style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%);">
                                Daftar
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- ========================================
         FLASH MESSAGES - VIBRANT CARDS
         ======================================== --}}
    @if(session('success') || session('error') || session('warning'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5">
        @if(session('success'))
            <div class="relative flex items-center gap-4 px-5 py-4 rounded-2xl text-sm font-semibold overflow-hidden shadow-xl"
                 style="background: linear-gradient(135deg, rgba(16,185,129,0.25) 0%, rgba(16,185,129,0.15) 100%); border: 1px solid rgba(16,185,129,0.4); backdrop-filter: blur(16px);"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-transparent"></div>
                <div class="relative w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 15px rgba(16,185,129,0.4);">
                    <i class="fas fa-check text-white"></i>
                </div>
                <span class="relative text-emerald-50">{{ session('success') }}</span>
                <button @click="show = false" class="relative ml-auto text-white/70 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="relative flex items-center gap-4 px-5 py-4 rounded-2xl text-sm font-semibold overflow-hidden shadow-xl"
                 style="background: linear-gradient(135deg, rgba(239,68,68,0.25) 0%, rgba(239,68,68,0.15) 100%); border: 1px solid rgba(239,68,68,0.4); backdrop-filter: blur(16px);"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 to-transparent"></div>
                <div class="relative w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 15px rgba(239,68,68,0.4);">
                    <i class="fas fa-times text-white"></i>
                </div>
                <span class="relative text-red-50">{{ session('error') }}</span>
                <button @click="show = false" class="relative ml-auto text-white/70 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        @if(session('warning'))
            <div class="relative flex items-center gap-4 px-5 py-4 rounded-2xl text-sm font-semibold overflow-hidden shadow-xl"
                 style="background: linear-gradient(135deg, rgba(251,146,60,0.25) 0%, rgba(251,146,60,0.15) 100%); border: 1px solid rgba(251,146,60,0.4); backdrop-filter: blur(16px);"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-500/10 to-transparent"></div>
                <div class="relative w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); box-shadow: 0 4px 15px rgba(251,146,60,0.4);">
                    <i class="fas fa-exclamation text-white"></i>
                </div>
                <span class="relative text-orange-50">{{ session('warning') }}</span>
                <button @click="show = false" class="relative ml-auto text-white/70 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    </div>
    @endif

    {{-- ========================================
         MAIN CONTENT
         ======================================== --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ========================================
         🌊 PREMIUM FOOTER - OCEAN DEPTH THEME
         ======================================== --}}
    <footer class="mt-auto relative overflow-hidden">
        {{-- Deep Ocean Gradient Background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-950 via-sky-900 to-teal-950"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
        
        {{-- Animated Glow Effects --}}
        <div class="absolute inset-0 opacity-40">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-cyan-500/30 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-teal-500/30 rounded-full blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-sky-500/20 rounded-full blur-[120px]"></div>
        </div>
        
        {{-- Wave Pattern Overlay --}}
        <div class="absolute inset-0 opacity-10" style="
            background-image: 
                repeating-linear-gradient(90deg, rgba(34, 211, 238, 0.1) 0px, transparent 50px, transparent 100px, rgba(34, 211, 238, 0.1) 150px),
                repeating-linear-gradient(0deg, rgba(20, 184, 166, 0.1) 0px, transparent 30px, transparent 60px, rgba(20, 184, 166, 0.1) 90px);
        "></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10 sm:gap-12">
                {{-- Brand Section --}}
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="relative w-12 h-12 rounded-xl flex items-center justify-center"
                             style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.3), rgba(20, 184, 166, 0.3)); box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4); border: 1px solid rgba(255,255,255,0.2);">
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-white/20 to-transparent"></div>
                            <i class="fas fa-fish text-white text-xl relative z-10"></i>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-white via-cyan-100 to-white bg-clip-text text-transparent">FishMarket</span>
                    </div>
                    <p class="text-cyan-100/80 text-sm leading-relaxed">
                        Marketplace ikan air tawar terpercaya. Ikan Nila & Ikan Mas berkualitas langsung dari kolam petani.
                    </p>
                    <div class="mt-5 flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition-all duration-300 hover:scale-110" style="background: rgba(6, 182, 212, 0.15);">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition-all duration-300 hover:scale-110" style="background: rgba(6, 182, 212, 0.15);">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition-all duration-300 hover:scale-110" style="background: rgba(6, 182, 212, 0.15);">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                {{-- Navigation Section --}}
                <div>
                    <h4 class="font-bold text-white mb-5 text-lg">Navigasi</h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('home') }}" class="flex items-center gap-2 text-cyan-100/80 text-sm hover:text-white hover:translate-x-1 transition-all duration-300">
                                <i class="fas fa-chevron-right text-xs text-cyan-400"></i> Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('catalog') }}" class="flex items-center gap-2 text-cyan-100/80 text-sm hover:text-white hover:translate-x-1 transition-all duration-300">
                                <i class="fas fa-chevron-right text-xs text-cyan-400"></i> Katalog Produk
                            </a>
                        </li>
                        @auth
                        <li>
                            <a href="{{ route('my.orders') }}" class="flex items-center gap-2 text-cyan-100/80 text-sm hover:text-white hover:translate-x-1 transition-all duration-300">
                                <i class="fas fa-chevron-right text-xs text-cyan-400"></i> Pesanan Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 text-cyan-100/80 text-sm hover:text-white hover:translate-x-1 transition-all duration-300">
                                <i class="fas fa-chevron-right text-xs text-cyan-400"></i> Wishlist
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>
                
                {{-- Contact Section --}}
                <div>
                    <h4 class="font-bold text-white mb-5 text-lg">Hubungi Kami</h4>
                    <ul class="space-y-4 text-sm text-cyan-100/80">
                        <li class="flex items-start gap-3 group hover:translate-x-1 transition-transform duration-300">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-cyan-500/20 transition-colors" style="background: rgba(6, 182, 212, 0.15);">
                                <i class="fas fa-phone text-cyan-400"></i>
                            </div>
                            <span class="pt-1.5">+62 819-1704-3981</span>
                        </li>
                        <li class="flex items-start gap-3 group hover:translate-x-1 transition-transform duration-300">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-cyan-500/20 transition-colors" style="background: rgba(6, 182, 212, 0.15);">
                                <i class="fas fa-envelope text-cyan-400"></i>
                            </div>
                            <span class="pt-1.5">info@fishmarket.id</span>
                        </li>
                        <li class="flex items-start gap-3 group hover:translate-x-1 transition-transform duration-300">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-cyan-500/20 transition-colors" style="background: rgba(6, 182, 212, 0.15);">
                                <i class="fas fa-map-marker-alt text-cyan-400"></i>
                            </div>
                            <span class="pt-1.5">Jl. Kav. Polri 2 No.14B, Cilangkap, Kec. Tapos, Kota Depok, Jawa Barat 16458</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            {{-- Copyright Section dengan decorative line --}}
            <div class="relative mt-12 pt-8">
                <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-cyan-400/30 to-transparent"></div>
                <div class="text-center">
                    <p class="text-cyan-100/60 text-sm font-medium">
                        &copy; {{ date('Y') }} <span class="text-white font-bold">FishMarket</span> &mdash; Sutan Arlie
                    </p>
                    <p class="text-cyan-100/40 text-xs mt-2">
                        Made with <i class="fas fa-heart text-red-400 animate-pulse"></i> for fresh seafood lovers
                    </p>
                </div>
            </div>
        </div>
    </footer>

    {{-- FLOATING CHAT BUTTON dengan pulse effect --}}
    @auth
    @if(!Auth::user()->isAdmin())
    <div class="fixed bottom-6 right-6 z-50" x-data="{ unread: 0 }" x-init="
        setInterval(() => {
            fetch('{{ route('chat.unread') }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => { unread = d.count; });
        }, 5000);
    ">
        <a href="{{ route('chat.index') }}" 
           class="group relative w-14 h-14 rounded-2xl flex items-center justify-center text-white/80 transition-all duration-300 hover:scale-105"
           style="background: rgba(8,145,178,0.25); border: 1px solid rgba(6,182,212,0.3); box-shadow: 0 4px 15px rgba(6,182,212,0.2);">
            <i class="fas fa-comments text-xl relative z-10"></i>
            <span x-show="unread > 0" x-transition
                  class="absolute -top-2 -right-2 min-w-[28px] h-7 px-2 rounded-full text-xs font-bold text-white flex items-center justify-center animate-bounce"
                  style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); box-shadow: 0 4px 15px rgba(239,68,68,0.6);"
                  x-text="unread"></span>
        </a>
    </div>
    @endif
    @endauth

    {{-- ========================================
         USER CUSTOM NOTIFICATION SYSTEM
         Ocean / Teal Glassmorphism Theme
         ======================================== --}}
    <div x-data="userNotify()" x-cloak>
        {{-- CONFIRM MODAL --}}
        <template x-if="confirm.show">
            <div class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                {{-- Backdrop with blur --}}
                <div class="absolute inset-0 bg-navy-950/80 backdrop-blur-lg" @click="cancelConfirm()"></div>
                {{-- Modal --}}
                <div class="relative w-full max-w-sm rounded-3xl overflow-hidden shadow-2xl transform"
                     style="background: linear-gradient(165deg, rgba(8,51,68,0.97) 0%, rgba(6,78,97,0.95) 50%, rgba(13,42,58,0.98) 100%); border: 1px solid rgba(6,182,212,0.15); backdrop-filter: blur(30px);"
                     x-transition:enter="transition ease-out duration-400"
                     x-transition:enter-start="opacity-0 scale-75 translate-y-10"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-90">
                    {{-- Decorative top glow --}}
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-1 rounded-b-full"
                         :class="{
                            'bg-gradient-to-r from-transparent via-red-400 to-transparent': confirm.type === 'danger',
                            'bg-gradient-to-r from-transparent via-amber-400 to-transparent': confirm.type === 'warning',
                            'bg-gradient-to-r from-transparent via-cyan-400 to-transparent': confirm.type === 'info',
                            'bg-gradient-to-r from-transparent via-teal-400 to-transparent': confirm.type === 'success'
                         }"></div>
                    <div class="p-7">
                        {{-- Icon with animated ring --}}
                        <div class="relative flex items-center justify-center w-20 h-20 mx-auto mb-5">
                            <div class="absolute inset-0 rounded-full animate-ping opacity-20"
                                 :class="{
                                    'bg-red-400': confirm.type === 'danger',
                                    'bg-amber-400': confirm.type === 'warning',
                                    'bg-cyan-400': confirm.type === 'info',
                                    'bg-teal-400': confirm.type === 'success'
                                 }"></div>
                            <div class="relative w-16 h-16 rounded-full flex items-center justify-center"
                                 :class="{
                                    'bg-red-500/15 border-2 border-red-400/30': confirm.type === 'danger',
                                    'bg-amber-500/15 border-2 border-amber-400/30': confirm.type === 'warning',
                                    'bg-cyan-500/15 border-2 border-cyan-400/30': confirm.type === 'info',
                                    'bg-teal-500/15 border-2 border-teal-400/30': confirm.type === 'success'
                                 }">
                                <i class="text-2xl"
                                   :class="{
                                      'fas fa-trash-alt text-red-400': confirm.type === 'danger',
                                      'fas fa-exclamation-triangle text-amber-400': confirm.type === 'warning',
                                      'fas fa-question-circle text-cyan-300': confirm.type === 'info',
                                      'fas fa-check-circle text-teal-400': confirm.type === 'success'
                                   }"></i>
                            </div>
                        </div>
                        {{-- Title --}}
                        <h3 class="text-lg font-bold text-white text-center mb-2" x-text="confirm.title"></h3>
                        {{-- Message --}}
                        <p class="text-cyan-200/50 text-center text-sm leading-relaxed mb-7" x-text="confirm.message"></p>
                        {{-- Actions --}}
                        <div class="flex items-center gap-3">
                            <button @click="cancelConfirm()" 
                                    class="flex-1 px-5 py-3 rounded-2xl text-sm font-semibold text-white/50 hover:text-white transition-all border border-white/10 hover:border-cyan-400/30 hover:bg-white/5">
                                Batal
                            </button>
                            <button @click="proceedConfirm()"
                                    class="flex-1 px-5 py-3 rounded-2xl text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.02] active:scale-[0.97]"
                                    :class="{
                                        'bg-gradient-to-r from-red-500 to-rose-500 shadow-red-500/30': confirm.type === 'danger',
                                        'bg-gradient-to-r from-amber-500 to-orange-500 shadow-amber-500/30': confirm.type === 'warning',
                                        'bg-gradient-to-r from-cyan-500 to-teal-500 shadow-cyan-500/30': confirm.type === 'info',
                                        'bg-gradient-to-r from-teal-500 to-emerald-500 shadow-teal-500/30': confirm.type === 'success'
                                    }"
                                    x-text="confirm.confirmText">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- TOAST NOTIFICATION --}}
        <template x-if="toast.show">
            <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9998] max-w-sm w-full px-4"
                 x-transition:enter="transition ease-out duration-400"
                 x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                <div class="rounded-2xl p-4 shadow-2xl flex items-center gap-3"
                     style="background: linear-gradient(145deg, rgba(8,51,68,0.97) 0%, rgba(13,42,58,0.98) 100%); border: 1px solid rgba(6,182,212,0.15); backdrop-filter: blur(30px);">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         :class="{
                            'bg-teal-500/15 border border-teal-400/25': toast.type === 'success',
                            'bg-red-500/15 border border-red-400/25': toast.type === 'error',
                            'bg-amber-500/15 border border-amber-400/25': toast.type === 'warning',
                            'bg-cyan-500/15 border border-cyan-400/25': toast.type === 'info'
                         }">
                        <i class="text-sm"
                           :class="{
                              'fas fa-check text-teal-400': toast.type === 'success',
                              'fas fa-times text-red-400': toast.type === 'error',
                              'fas fa-exclamation text-amber-400': toast.type === 'warning',
                              'fas fa-info text-cyan-400': toast.type === 'info'
                           }"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white" x-text="toast.title"></p>
                        <p class="text-xs text-cyan-200/40 mt-0.5" x-text="toast.message"></p>
                    </div>
                    <button @click="toast.show = false" class="text-white/30 hover:text-white/60 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <script>
    function userNotify() {
        return {
            confirm: { show: false, title: '', message: '', type: 'danger', confirmText: 'Ya, Lanjutkan', formEl: null, callback: null },
            toast: { show: false, title: '', message: '', type: 'success' },

            showConfirm(formEl, title, message, type = 'danger', confirmText = 'Ya, Lanjutkan') {
                this.confirm = { show: true, title, message, type, confirmText, formEl, callback: null };
            },

            showConfirmCallback(callback, title, message, type = 'danger', confirmText = 'Ya, Lanjutkan') {
                this.confirm = { show: true, title, message, type, confirmText, formEl: null, callback };
            },

            proceedConfirm() {
                this.confirm.show = false;
                if (this.confirm.callback) {
                    this.confirm.callback();
                } else if (this.confirm.formEl) {
                    this.confirm.formEl.submit();
                }
            },

            cancelConfirm() {
                this.confirm.show = false;
                this.confirm.formEl = null;
                this.confirm.callback = null;
            },

            showToast(title, message, type = 'success', duration = 3000) {
                this.toast = { show: true, title, message, type };
                setTimeout(() => { this.toast.show = false; }, duration);
            }
        }
    }

    // Global helper for user confirm dialogs
    function userConfirm(formEl, title, message, type = 'danger', confirmText = 'Ya, Lanjutkan') {
        document.querySelectorAll('[x-data]').forEach(el => {
            if (el._x_dataStack && el._x_dataStack[0] && el._x_dataStack[0].showConfirm) {
                el._x_dataStack[0].showConfirm(formEl, title, message, type, confirmText);
            }
        });
        return false;
    }

    // Global helper for user toast
    function userToast(title, message, type = 'success', duration = 3000) {
        document.querySelectorAll('[x-data]').forEach(el => {
            if (el._x_dataStack && el._x_dataStack[0] && el._x_dataStack[0].showToast) {
                el._x_dataStack[0].showToast(title, message, type, duration);
            }
        });
    }
    </script>

    @stack('scripts')
</body>
</html>