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
            background-color: #0c4a6e !important;
        }
        
        /* Store Premium Scrollbar (vertical only) */
        ::-webkit-scrollbar { width: 6px; height: 0px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

        /* Floating orb animation */
        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -20px) scale(1.05); }
            50% { transform: translate(-10px, -40px) scale(0.95); }
            75% { transform: translate(-30px, -10px) scale(1.02); }
        }
        
        /* Navbar sticky blur effect - always blurred */
        .nav-blur {
            transition: all 0.3s ease;
            background: rgba(14, 116, 144, 0.35);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .nav-blur.scrolled {
            background: rgba(14, 116, 144, 0.7) !important;
            backdrop-filter: blur(24px) !important;
            -webkit-backdrop-filter: blur(24px) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-ocean-waves text-white overflow-x-hidden" x-data="{ mobileOpen: false }">

    {{-- ========================================
         PREMIUM STORE BACKGROUND (like Admin)
         ======================================== --}}
    <div id="master-bg-ocean" class="fixed inset-0 bg-ocean-waves -z-10"></div>
    <div id="master-bg-radial" class="fixed inset-0 -z-10" style="background: 
        radial-gradient(circle at 20% 20%, rgba(6, 182, 212, 0.3), transparent 40%), 
        radial-gradient(circle at 80% 30%, rgba(20, 184, 166, 0.25), transparent 45%), 
        radial-gradient(circle at 50% 80%, rgba(14, 116, 144, 0.2), transparent 50%),
        radial-gradient(circle at 10% 90%, rgba(34, 211, 238, 0.15), transparent 35%),
        radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.15), transparent 40%);"></div>
    <div id="master-bg-grid" class="fixed inset-0 store-grid-pattern -z-10"></div>
    {{-- Animated Floating Orbs --}}
    <div id="master-bg-orbs" class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-gradient-to-br from-cyan-400/15 via-teal-400/10 to-transparent rounded-full blur-[100px]" style="animation: floatOrb 20s ease-in-out infinite;"></div>
        <div class="absolute -bottom-40 -right-40 w-[600px] h-[600px] bg-gradient-to-tl from-teal-500/15 via-ocean-400/10 to-transparent rounded-full blur-[120px]" style="animation: floatOrb 25s ease-in-out infinite; animation-delay: -5s;"></div>
        <div class="absolute top-1/3 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-mint-400/10 to-transparent rounded-full blur-[90px]" style="animation: floatOrb 18s ease-in-out infinite; animation-delay: -8s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-[350px] h-[350px] bg-gradient-to-tr from-cyan-300/10 to-transparent rounded-full blur-[80px]" style="animation: floatOrb 22s ease-in-out infinite; animation-delay: -12s;"></div>
    </div>

    {{-- ========================================
         PREMIUM NAVBAR
         ======================================== --}}
    <nav class="nav-blur sticky top-0 z-50 border-b border-white/10" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)" :class="{ 'scrolled': scrolled }">
        <div class="absolute inset-0 bg-white/8 backdrop-blur-xl transition-all duration-300"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-18">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-105"
                         style="background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <i class="fas fa-fish text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-white drop-shadow-md">
                        FishMarket
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('home') ? 'bg-white/20 text-white shadow-lg' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        Beranda
                    </a>
                    <a href="{{ route('catalog') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('catalog') || request()->routeIs('produk.show') ? 'bg-white/20 text-white shadow-lg' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        Katalog
                    </a>
                    @auth
                        <a href="{{ route('my.orders') }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('my.orders') ? 'bg-white/20 text-white shadow-lg' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            Pesanan Saya
                        </a>
                        <a href="{{ route('wishlist.index') }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('wishlist.*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-heart text-xs mr-1"></i> Wishlist
                        </a>
                    @endauth
                </div>

                {{-- Cart Icon (for logged in users) --}}
                @auth
                <div class="hidden md:flex items-center">
                    @php
                        $cartCount = count(session()->get('cart', []));
                    @endphp
                    <a href="{{ route('cart.index') }}" 
                       class="relative w-10 h-10 flex items-center justify-center rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('cart.*') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-[10px] font-bold text-white flex items-center justify-center"
                              style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 2px 6px rgba(249,115,22,0.4);">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </a>
                </div>
                @endauth

                {{-- Right side --}}
                <div class="hidden md:flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white/70 hover:text-white transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm px-5 py-2.5">
                            <i class="fas fa-user-plus text-xs"></i> Daftar
                        </a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 transition-all">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center overflow-hidden"
                                     style="background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);">
                                    @if(Auth::user()->foto_profil)
                                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Foto Profil" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-user text-white text-xs"></i>
                                    @endif
                                </div>
                                <span class="hidden sm:block max-w-[120px] truncate text-white">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-white/50"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-52 rounded-2xl shadow-xl border border-white/15 py-2 z-50 overflow-hidden"
                                 style="background: rgba(15,40,60,0.9); backdrop-filter: blur(20px);">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                                        <i class="fas fa-tachometer-alt w-4 text-cyan-400"></i> Dashboard Admin
                                    </a>
                                @endif
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                                    <i class="fas fa-user-edit w-4 text-cyan-400"></i> Profil Saya
                                </a>
                                <a href="{{ route('my.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                                    <i class="fas fa-box w-4 text-cyan-400"></i> Pesanan Saya
                                </a>
                                <a href="{{ route('user.addresses.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                                    <i class="fas fa-map-marker-alt w-4 text-cyan-400"></i> Alamat Saya
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                                    <i class="fas fa-heart w-4 text-red-400"></i> Wishlist
                                </a>
                                <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                                    <i class="fas fa-comments w-4 text-cyan-400"></i> Chat Admin
                                </a>
                                <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                                    <i class="fas fa-headset w-4 text-rose-400"></i> Support Ticket
                                </a>
                                <hr class="my-2 border-white/10">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 w-full text-left transition-colors">
                                        <i class="fas fa-sign-out-alt w-4"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

                {{-- Mobile Cart + Hamburger --}}
                <div class="md:hidden flex items-center gap-2">
                    @auth
                    <a href="{{ route('cart.index') }}" 
                       class="relative w-10 h-10 flex items-center justify-center rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('cart.*') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        @php
                            $mobileCartCount = count(session()->get('cart', []));
                        @endphp
                        @if($mobileCartCount > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-[10px] font-bold text-white flex items-center justify-center"
                              style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 2px 6px rgba(249,115,22,0.4);">
                            {{ $mobileCartCount }}
                        </span>
                        @endif
                    </a>
                    @endauth
                    <button @click="mobileOpen = !mobileOpen" class="w-10 h-10 flex items-center justify-center text-white/80 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="fas text-lg" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="md:hidden pb-4">
                <div class="space-y-1 pt-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('home') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-home w-5 text-center"></i> Beranda
                    </a>
                    <a href="{{ route('catalog') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('catalog') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-fish w-5 text-center"></i> Katalog
                    </a>
                    @auth
                        <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('cart.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-shopping-cart w-5 text-center"></i> Keranjang
                            @php $menuCartCount = count(session()->get('cart', [])); @endphp
                            @if($menuCartCount > 0)
                                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold text-white" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">{{ $menuCartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('my.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('my.orders') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-box w-5 text-center"></i> Pesanan Saya
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('wishlist.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-heart w-5 text-center"></i> Wishlist
                        </a>
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('profile.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-user-edit w-5 text-center"></i> Profil
                        </a>
                        <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('chat.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-comments w-5 text-center"></i> Chat Admin
                        </a>
                        <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('tickets.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <i class="fas fa-headset w-5 text-center"></i> Support Ticket
                        </a>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:bg-white/10 hover:text-white">
                                <i class="fas fa-tachometer-alt w-5 text-center"></i> Dashboard Admin
                            </a>
                        @endif
                        <div class="pt-3 mt-2 border-t border-white/10">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20">
                                    <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex gap-3 pt-4 mt-3 border-t border-white/10">
                            <a href="{{ route('login') }}" class="flex-1 text-sm text-center py-3 rounded-xl font-semibold text-white border border-white/20 hover:bg-white/10 transition-all">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="btn-primary flex-1 text-sm text-center py-3">
                                Daftar
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- ========================================
         FLASH MESSAGES
         ======================================== --}}
    @if(session('success') || session('error') || session('warning'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-medium"
                 style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); backdrop-filter: blur(12px);"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                <div class="w-8 h-8 rounded-full bg-mint-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-white text-xs"></i>
                </div>
                <span class="text-mint-300">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-medium"
                 style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); backdrop-filter: blur(12px);"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times text-white text-xs"></i>
                </div>
                <span class="text-red-300">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-medium"
                 style="background: rgba(251,146,60,0.15); border: 1px solid rgba(251,146,60,0.3); backdrop-filter: blur(12px);"
                 x-data="{ show: true }" x-show="show" x-transition>
                <div class="w-8 h-8 rounded-full bg-coral-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation text-white text-xs"></i>
                </div>
                <span class="text-orange-300">{{ session('warning') }}</span>
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
         PREMIUM FOOTER
         ======================================== --}}
    <footer class="mt-auto relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900 to-ocean-950"></div>
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-ocean-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-teal-500/20 rounded-full blur-3xl"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 sm:gap-10">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                             style="background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);">
                            <i class="fas fa-fish text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-white">FishMarket</span>
                    </div>
                    <p class="text-ocean-200 text-sm leading-relaxed">
                        Marketplace ikan air tawar terpercaya. Lele & Ikan Mas berkualitas langsung dari kolam petani.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Navigasi</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('home') }}" class="text-ocean-200 text-sm hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('catalog') }}" class="text-ocean-200 text-sm hover:text-white transition-colors">Katalog Produk</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2.5 text-sm text-ocean-200">
                        <li class="flex items-center gap-2.5">
                            <i class="fas fa-phone w-4 text-ocean-400"></i> +62 812-3456-7890
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fas fa-envelope w-4 text-ocean-400"></i> info@fishmarket.id
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fas fa-map-marker-alt w-4 text-ocean-400"></i> Jl. Ikan Segar No. 1
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 mt-10 pt-8 text-center">
                <p class="text-ocean-300 text-sm">
                    &copy; {{ date('Y') }} FishMarket &mdash; Web Programming 2
                </p>
            </div>
        </div>
    </footer>

    {{-- FLOATING CHAT BUTTON --}}
    @auth
    @if(!Auth::user()->isAdmin())
    <div class="fixed bottom-6 right-6 z-50" x-data="{ unread: 0 }" x-init="
        setInterval(() => {
            fetch('{{ route('chat.unread') }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => { unread = d.count; });
        }, 5000);
    ">
        <a href="{{ route('chat.index') }}" 
           class="group relative w-14 h-14 rounded-full flex items-center justify-center text-white shadow-xl transition-all hover:scale-110"
           style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 8px 25px rgba(6,182,212,0.4);">
            <i class="fas fa-comments text-xl group-hover:scale-110 transition-transform"></i>
            <span x-show="unread > 0" x-transition
                  class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-[10px] font-bold text-white flex items-center justify-center"
                  style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);"
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