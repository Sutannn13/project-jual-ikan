<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Admin FishMarket</title>
    
    {{-- Premium Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Hide scrollbar for sidebar navigation */
        .sidebar-nav {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        .sidebar-nav::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        /* Custom Scrollbar for Main Content - Premium Purple Theme */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: linear-gradient(180deg, rgba(147,51,234,0.1) 0%, rgba(168,85,247,0.1) 100%);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #9333ea 0%, #a855f7 100%);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(147,51,234,0.5);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #a855f7 0%, #c084fc 100%);
        }

        /* Dark Glassmorphism Card */
        .dark-glass-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }
        .dark-glass-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.12);
        }

        /* Animated Shine Effect */
        @keyframes shine {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        .shine-effect {
            position: relative;
            overflow: hidden;
        }

        .shine-effect::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(255, 255, 255, 0.1) 45%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0.1) 55%,
                transparent 100%
            );
            background-size: 200% 100%;
            animation: shine 8s ease-in-out infinite;
            pointer-events: none;
        }

        /* Grid pattern overlay */
        .admin-grid-pattern {
            background-image: 
                linear-gradient(rgba(147,51,234,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(147,51,234,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
        }
        
        /* Particle Animation */
        @keyframes floatParticle {
            0%, 100% { 
                transform: translate(0, 0) scale(1); 
                opacity: 0;
            }
            10% { opacity: 0.8; }
            90% { opacity: 0.8; }
            100% { 
                transform: translate(var(--tx), var(--ty)) scale(0.5);
                opacity: 0;
            }
        }
        
        /* Geometric shapes animation */
        @keyframes rotateShape {
            0% { transform: rotate(0deg) translateY(0px); }
            50% { transform: rotate(180deg) translateY(-20px); }
            100% { transform: rotate(360deg) translateY(0px); }
        }
        
        @keyframes floatShape {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        /* Light ray animation */
        @keyframes lightRay {
            0% { transform: translateX(-100%) rotate(-45deg); }
            100% { transform: translateX(200%) rotate(-45deg); }
        }
        
        /* Pulse glow */
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }

        /* === Admin Form Fields === */
        .input-field {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.75rem;
            color: #ffffff;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            outline: none;
        }
        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }
        .input-field:focus {
            border-color: rgba(6, 182, 212, 0.5);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.12);
            background: rgba(255, 255, 255, 0.1);
        }
        select.input-field {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.5rem;
            cursor: pointer;
        }
        select.input-field option {
            background: #1a1025;
            color: #ffffff;
            padding: 0.5rem;
        }
        .label-field {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 0.375rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        textarea.input-field {
            resize: vertical;
            min-height: 4rem;
        }
    </style>
</head>
<body class="antialiased text-white" 
      x-data="{ 
          sidebarOpen: false,
          particles: [],
          shapes: [],
          init() {
              // Generate floating particles
              for (let i = 0; i < 30; i++) {
                  this.particles.push({
                      id: i,
                      x: Math.random() * 100,
                      y: Math.random() * 100,
                      size: 2 + Math.random() * 4,
                      duration: 15 + Math.random() * 20,
                      delay: Math.random() * 10,
                      tx: (Math.random() - 0.5) * 200,
                      ty: -100 - Math.random() * 100
                  });
              }
              
              // Generate geometric shapes
              const shapeTypes = ['square', 'triangle', 'hexagon', 'circle'];
              for (let i = 0; i < 12; i++) {
                  this.shapes.push({
                      id: i,
                      type: shapeTypes[Math.floor(Math.random() * shapeTypes.length)],
                      x: Math.random() * 100,
                      y: Math.random() * 100,
                      size: 40 + Math.random() * 80,
                      duration: 20 + Math.random() * 15,
                      delay: Math.random() * 8,
                      opacity: 0.03 + Math.random() * 0.07
                  });
              }
          }
      }">
    
    {{-- ========================================
         🌌 PREMIUM ADMIN BACKGROUND - GALAXY PARTICLES
         ======================================== --}}
    {{-- Base Purple Gradient --}}
    <div class="fixed inset-0 -z-10" style="background: 
        linear-gradient(135deg, 
            #1a0c2e 0%, 
            #2d1b4e 15%, 
            #4a2c6d 30%, 
            #6b21a8 45%, 
            #4a2c6d 60%, 
            #2d1b4e 80%, 
            #1a0c2e 100%
        );"></div>
    
    {{-- Animated Radial Glows --}}
    <div class="fixed inset-0 -z-10" style="background: 
        radial-gradient(circle at 20% 30%, rgba(239, 68, 68, 0.2), transparent 35%), 
        radial-gradient(circle at 80% 20%, rgba(251, 146, 60, 0.18), transparent 40%), 
        radial-gradient(circle at 50% 80%, rgba(217, 70, 239, 0.15), transparent 45%),
        radial-gradient(circle at 10% 90%, rgba(147, 51, 234, 0.2), transparent 40%),
        radial-gradient(circle at 90% 70%, rgba(168, 85, 247, 0.15), transparent 35%);"></div>
    
    {{-- Pulsing Glow Orbs --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-10 left-20 w-[600px] h-[600px] rounded-full blur-[120px]" 
             style="background: radial-gradient(circle, rgba(147, 51, 234, 0.25) 0%, transparent 70%); animation: pulseGlow 8s ease-in-out infinite;"></div>
        <div class="absolute bottom-20 right-32 w-[500px] h-[500px] rounded-full blur-[100px]" 
             style="background: radial-gradient(circle, rgba(239, 68, 68, 0.2) 0%, transparent 70%); animation: pulseGlow 10s ease-in-out infinite; animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full blur-[140px]" 
             style="background: radial-gradient(circle, rgba(251, 146, 60, 0.15) 0%, transparent 70%); animation: pulseGlow 12s ease-in-out infinite; animation-delay: -6s;"></div>
    </div>
    
    {{-- Grid Pattern with mask --}}
    <div class="fixed inset-0 admin-grid-pattern -z-10"></div>
    
    {{-- Floating Particles dengan Alpine.js --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <template x-for="particle in particles" :key="particle.id">
            <div class="absolute rounded-full"
                 :style="`
                    left: ${particle.x}%; 
                    top: ${particle.y}%; 
                    width: ${particle.size}px; 
                    height: ${particle.size}px;
                    background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(147,51,234,0.4) 50%, transparent 100%);
                    box-shadow: 0 0 ${particle.size * 2}px rgba(147,51,234,0.6);
                    animation: floatParticle ${particle.duration}s ease-in-out infinite;
                    animation-delay: ${particle.delay}s;
                    --tx: ${particle.tx}px;
                    --ty: ${particle.ty}px;
                 `">
            </div>
        </template>
    </div>
    
    {{-- Geometric Shapes dengan Alpine.js --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <template x-for="shape in shapes" :key="shape.id">
            <div class="absolute"
                 :style="`
                    left: ${shape.x}%; 
                    top: ${shape.y}%; 
                    width: ${shape.size}px; 
                    height: ${shape.size}px;
                    opacity: ${shape.opacity};
                    animation: ${shape.id % 2 === 0 ? 'rotateShape' : 'floatShape'} ${shape.duration}s ease-in-out infinite;
                    animation-delay: ${shape.delay}s;
                 `">
                <div x-show="shape.type === 'square'" 
                     class="w-full h-full border-2 rounded-lg"
                     style="border-color: rgba(168, 85, 247, 0.4); background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(168, 85, 247, 0.05));"></div>
                <div x-show="shape.type === 'circle'" 
                     class="w-full h-full rounded-full border-2"
                     style="border-color: rgba(239, 68, 68, 0.4); background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(251, 146, 60, 0.05));"></div>
                <div x-show="shape.type === 'triangle'" 
                     class="w-full h-full"
                     style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%); background: linear-gradient(135deg, rgba(251, 146, 60, 0.15), rgba(239, 68, 68, 0.08)); border: 2px solid rgba(251, 146, 60, 0.3);"></div>
                <div x-show="shape.type === 'hexagon'" 
                     class="w-full h-full"
                     style="clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); background: linear-gradient(135deg, rgba(217, 70, 239, 0.12), rgba(168, 85, 247, 0.06)); border: 2px solid rgba(217, 70, 239, 0.35);"></div>
            </div>
        </template>
    </div>
    
    {{-- Light Rays Effect --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none opacity-20">
        <div class="absolute top-0 left-1/4 w-2 h-full bg-gradient-to-b from-transparent via-purple-400 to-transparent transform -skew-x-12 blur-sm" 
             style="animation: lightRay 20s linear infinite;"></div>
        <div class="absolute top-0 right-1/3 w-1 h-full bg-gradient-to-b from-transparent via-pink-400 to-transparent transform skew-x-12 blur-sm" 
             style="animation: lightRay 25s linear infinite; animation-delay: -8s;"></div>
    </div>

    <style>
        /* Mobile-first Layout */
        .admin-layout {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }
        
        /* Mobile: Main content scrolls naturally */
        .admin-main {
            display: flex;
            flex-direction: column;
            flex: 1;
            position: relative;
        }
        
        /* Desktop Grid Layout */
        @media (min-width: 1024px) {
            .admin-layout {
                height: 100vh;
                display: grid;
                grid-template-columns: 256px 1fr;
                overflow: hidden;
            }
            .admin-sidebar {
                grid-column: 1;
                grid-row: 1;
                height: 100vh;
                overflow-y: auto;
            }
            .admin-main {
                grid-column: 2;
                grid-row: 1;
                height: 100vh;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }
            .admin-main main {
                flex: 1;
                overflow-y: auto;
            }
        }
    </style>
    
    <div class="admin-layout">
        {{-- ========================================
             PREMIUM SIDEBAR
             ======================================== --}}
        <aside class="admin-sidebar fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 lg:translate-x-0 lg:relative overflow-hidden sidebar-glossy"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            {{-- Glossy Sidebar Background --}}
            <div class="absolute inset-0 bg-sidebar-glossy"></div>
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-red-500/20 via-orange-500/15 to-transparent"></div>
                <div class="absolute bottom-0 left-0 w-full h-64 bg-gradient-to-t from-purple-600/15 via-pink-500/10 to-transparent"></div>
                <div class="absolute inset-0 sidebar-shine"></div>
            </div>
            
            <div class="relative h-full flex flex-col text-white">
                {{-- Logo --}}
                <div class="h-16 flex items-center gap-3.5 px-6 border-b border-white/10 shine-effect flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center glossy-logo"
                         style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.9) 0%, rgba(251, 146, 60, 0.8) 100%); 
                                box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4), 
                                inset 0 1px 0 rgba(255, 255, 255, 0.3),
                                inset 0 -1px 0 rgba(0, 0, 0, 0.2);">
                        <i class="fas fa-fish text-white text-sm drop-shadow-lg"></i>
                    </div>
                    <div>
                        <span class="text-base font-bold block drop-shadow-md tracking-wide">FishMarket</span>
                        <span class="text-[10px] text-orange-200 uppercase tracking-wider">Admin Panel</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto sidebar-nav">
                    <p class="text-[10px] uppercase tracking-widest text-orange-300/80 font-bold mb-3 px-3">Menu Utama</p>

                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}"
                       style="{{ request()->routeIs('admin.dashboard') ? 'box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);' : '' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-br from-red-500 to-orange-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-tachometer-alt text-sm"></i>
                        </div>
                        <span class="tracking-wide">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.produk.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.produk.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.produk.*') ? 'bg-gradient-to-br from-teal-500 to-cyan-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-fish text-sm"></i>
                        </div>
                        <span class="tracking-wide">Produk</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.orders.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-br from-orange-500 to-amber-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-shopping-cart text-sm"></i>
                        </div>
                        <span class="tracking-wide">Pesanan</span>
                        @php 
                            $needsAttentionCount = \App\Models\Order::whereIn('status', ['waiting_payment', 'pending'])->count();
                            $waitingVerification = \App\Models\Order::where('status', 'waiting_payment')->count();
                        @endphp
                        @if($needsAttentionCount > 0)
                            <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg border border-red-400 animate-pulse" 
                                  title="{{ $waitingVerification > 0 ? $waitingVerification . ' menunggu verifikasi pembayaran' : $needsAttentionCount . ' pesanan perlu perhatian' }}">
                                {{ $needsAttentionCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.shipping-zones.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.shipping-zones.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.shipping-zones.*') ? 'bg-gradient-to-br from-sky-500 to-blue-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-truck text-sm"></i>
                        </div>
                        <span class="tracking-wide">Zona Pengiriman</span>
                    </a>

                    <p class="text-[10px] uppercase tracking-widest text-orange-300/80 font-bold mt-6 mb-3 px-3">Inventaris</p>

                    <a href="{{ route('admin.stock-in.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.stock-in.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.stock-in.*') ? 'bg-gradient-to-br from-emerald-500 to-green-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-boxes text-sm"></i>
                        </div>
                        <span class="tracking-wide">Stok Masuk</span>
                    </a>

                    <a href="{{ route('admin.banners.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.banners.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.banners.*') ? 'bg-gradient-to-br from-pink-500 to-rose-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-images text-sm"></i>
                        </div>
                        <span class="tracking-wide">Banner & Promo</span>
                    </a>

                    <a href="{{ route('admin.refunds.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.refunds.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.refunds.*') ? 'bg-gradient-to-br from-yellow-500 to-amber-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-undo text-sm"></i>
                        </div>
                        <span class="tracking-wide">Refund</span>
                        @php
                            $pendingRefundCount = \App\Models\Order::where('refund_status', 'requested')->count();
                        @endphp
                        @if($pendingRefundCount > 0)
                            <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-gradient-to-r from-yellow-500 to-amber-600 text-white shadow-sm border border-yellow-400 animate-pulse">
                                {{ $pendingRefundCount }}
                            </span>
                        @endif
                    </a>

                    <p class="text-[10px] uppercase tracking-widest text-orange-300/80 font-bold mt-6 mb-3 px-3">Komunikasi</p>

                    <a href="{{ route('admin.chat.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.chat.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.chat.*') ? 'bg-gradient-to-br from-teal-500 to-green-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-comments text-sm"></i>
                        </div>
                        <span class="tracking-wide">Chat</span>
                        @php
                            $unreadChatCount = \App\Models\ChatMessage::where('is_read', false)
                                ->whereHas('receiver', fn($q) => $q->where('role', 'admin'))
                                ->count();
                        @endphp
                        @if($unreadChatCount > 0)
                            <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-gradient-to-r from-teal-500 to-green-600 text-white shadow-sm border border-teal-400">
                                {{ $unreadChatCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.notifications.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.notifications.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.notifications.*') ? 'bg-gradient-to-br from-amber-500 to-yellow-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-bell text-sm"></i>
                        </div>
                        <span class="tracking-wide">Notifikasi</span>
                        @php $unreadNotifCount = \App\Models\AdminNotification::unreadCount(); @endphp
                        @if($unreadNotifCount > 0)
                            <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-gradient-to-r from-amber-500 to-yellow-600 text-white shadow-sm border border-amber-400">
                                {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.tickets.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.tickets.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.tickets.*') ? 'bg-gradient-to-br from-rose-500 to-pink-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-headset text-sm"></i>
                        </div>
                        <span class="tracking-wide">Support Ticket</span>
                        @php
                            $openTicketCount = \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count();
                        @endphp
                        @if($openTicketCount > 0)
                            <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-gradient-to-r from-rose-500 to-pink-600 text-white shadow-sm border border-rose-400">
                                {{ $openTicketCount }}
                            </span>
                        @endif
                    </a>

                    <p class="text-[10px] uppercase tracking-widest text-orange-300/80 font-bold mt-6 mb-3 px-3">Analitik</p>

                    <a href="{{ route('admin.analytics.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.analytics.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.analytics.*') ? 'bg-gradient-to-br from-cyan-500 to-blue-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-chart-line text-sm"></i>
                        </div>
                        <span class="tracking-wide">Analytics</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.reports.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.reports.*') ? 'bg-gradient-to-br from-purple-500 to-pink-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-chart-bar text-sm"></i>
                        </div>
                        <span class="tracking-wide">Laporan</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.users.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-br from-blue-500 to-indigo-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                        <span class="tracking-wide">Users</span>
                    </a>

                    <a href="{{ route('admin.activity-log.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('admin.activity-log.*') ? 'bg-white/15 text-white shadow-lg shine-effect border border-white/10' : 'text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ request()->routeIs('admin.activity-log.*') ? 'bg-gradient-to-br from-violet-500 to-purple-500 shadow-lg' : 'bg-white/5' }}">
                            <i class="fas fa-history text-sm"></i>
                        </div>
                        <span class="tracking-wide">Activity Log</span>
                    </a>

                    <div class="my-4 px-3">
                        <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                    </div>

                    <a href="{{ route('home') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white hover:pl-5 transition-all duration-300 group">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-white/5 group-hover:bg-white/10">
                            <i class="fas fa-store text-sm"></i>
                        </div>
                        <span class="tracking-wide">Lihat Toko</span>
                    </a>
                </nav>
                
                {{-- Sidebar Footer --}}
                <div class="p-4 border-t border-white/5 bg-black/10">
                    <div class="rounded-xl p-3.5 shine-effect relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300" 
                         style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(251, 146, 60, 0.1) 100%); border: 1px solid rgba(255,255,255,0.05);">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-orange-400 to-red-500 flex items-center justify-center text-white shadow-md">
                                <span class="font-bold text-xs">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-orange-200/80 mb-0.5 uppercase tracking-wider font-semibold">Admin Panel</p>
                                <p class="text-sm font-bold text-white truncate drop-shadow-sm">{{ Auth::user()->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- OVERLAY for mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-md" x-transition.opacity></div>

        {{-- ========================================
             MAIN CONTENT WRAPPER (SCROLLABLE INDEPENDENTLY)
             ======================================== --}}
        <div class="admin-main flex flex-col flex-1 relative">
            {{-- Header --}}
            <header class="h-16 flex items-center justify-between px-4 sm:px-6 relative z-30 flex-shrink-0 border-b border-white/5 bg-white/5 backdrop-blur-2xl">
                {{-- Header Content --}}
                <div class="relative flex items-center gap-4 z-10 w-full justify-between">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center text-white/70 rounded-xl hover:bg-white/10 lg:hidden transition-colors border border-white/10">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div>
                            <h1 class="text-xl font-bold text-white tracking-tight">@yield('title', 'Dashboard')</h1>
                            <p class="text-xs text-white/40 hidden sm:block font-medium mt-0.5">Welcome back, {{ Auth::user()->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Notification Bell --}}
                        <div class="relative" x-data="{ 
                            showNotifs: false,
                            notifs: [],
                            unreadCount: 0,
                            urgentCount: 0,
                            loading: false,
                            
                            async loadNotifs() {
                                this.loading = true;
                                try {
                                    const r = await fetch('{{ route('admin.notifications.unread') }}');
                                    const data = await r.json();
                                    this.notifs = data.notifications;
                                    this.unreadCount = data.count;
                                    this.urgentCount = data.urgent_count;
                                } catch (e) { console.error(e); }
                                this.loading = false;
                            },
                            async markRead(id) {
                                await fetch(`/admin/notifications/${id}/read`, {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                });
                                this.loadNotifs();
                            },
                            async markAllRead() {
                                await fetch('{{ route('admin.notifications.readAll') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                });
                                this.notifs = [];
                                this.unreadCount = 0;
                                this.urgentCount = 0;
                            }
                        }" x-init="loadNotifs(); setInterval(() => loadNotifs(), 30000)">
                            <button @click="showNotifs = !showNotifs; if(showNotifs) loadNotifs()" 
                                    class="relative w-10 h-10 flex items-center justify-center text-white/70 rounded-xl hover:bg-white/10 transition-colors border border-transparent hover:border-white/10">
                                <i class="fas fa-bell text-base"></i>
                                <span x-show="unreadCount > 0" x-cloak
                                      x-text="unreadCount > 99 ? '99+' : unreadCount"
                                      :class="urgentCount > 0 ? 'animate-pulse bg-gradient-to-r from-red-500 to-red-600 border-red-400' : 'bg-gradient-to-r from-amber-500 to-orange-500 border-amber-400'"
                                      class="absolute -top-1 -right-1 min-w-[18px] px-1 py-0.5 rounded-full text-[10px] font-bold text-white text-center border shadow-lg">
                                </span>
                            </button>
                            
                            {{-- Notification Dropdown --}}
                            <div x-show="showNotifs" @click.away="showNotifs = false" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-[-4.5rem] sm:right-0 mt-2 w-[88vw] sm:w-96 max-w-[360px] max-h-[480px] rounded-2xl shadow-2xl border border-white/10 overflow-hidden z-50"
                                 style="background: rgba(20,15,40,0.97); backdrop-filter: blur(24px);">
                                
                                <div class="p-3.5 border-b border-white/10 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-white text-sm">Notifikasi</h3>
                                        <span x-show="unreadCount > 0" x-text="unreadCount" class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-400"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="markAllRead()" class="text-[11px] text-cyan-400 hover:text-cyan-300 font-medium">
                                            <i class="fas fa-check-double mr-1"></i>Baca Semua
                                        </button>
                                        <a href="{{ route('admin.notifications.index') }}" class="text-[11px] text-white/40 hover:text-white/60 font-medium">
                                            Semua →
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="overflow-y-auto max-h-80 custom-scrollbar">
                                    <template x-if="notifs.length === 0">
                                        <div class="p-8 text-center text-white/30">
                                            <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                            <p class="text-xs">Tidak ada notifikasi baru</p>
                                        </div>
                                    </template>
                                    
                                    <template x-for="n in notifs" :key="n.id">
                                        <a :href="n.action_url" @click="markRead(n.id)"
                                           class="flex gap-3 p-3.5 border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                                 :class="{
                                                     'bg-red-500/15': n.color === 'red',
                                                     'bg-orange-500/15': n.color === 'orange',
                                                     'bg-cyan-500/15': n.color === 'cyan',
                                                     'bg-green-500/15': n.color === 'green',
                                                     'bg-yellow-500/15': n.color === 'yellow',
                                                     'bg-gray-500/15': n.color === 'gray'
                                                 }">
                                                <i :class="n.icon + ' text-sm ' + {
                                                     'red': 'text-red-400',
                                                     'orange': 'text-orange-400',
                                                     'cyan': 'text-cyan-400',
                                                     'green': 'text-green-400',
                                                     'yellow': 'text-yellow-400',
                                                     'gray': 'text-gray-400'
                                                 }[n.color]"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="font-semibold text-xs text-white truncate" x-text="n.title"></p>
                                                    <span x-show="n.priority === 'urgent'" 
                                                          class="flex-shrink-0 px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse">
                                                        URGENT
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-white/50 mt-0.5 line-clamp-2" x-text="n.message"></p>
                                                <p class="text-[10px] text-white/30 mt-1" x-text="n.created_at"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                                
                                <div class="p-2.5 border-t border-white/10 text-center">
                                    <a href="{{ route('admin.notifications.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold">
                                        Lihat Semua Notifikasi <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 pr-1 pl-1 py-1 rounded-full hover:bg-white/10 transition-all border border-transparent hover:border-white/10">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white shadow-md"
                                     style="background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);">
                                    <i class="fas fa-user-shield text-xs"></i>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] text-white/40 mr-2"></i>
                            </button>
                            
                            {{-- Dropdown Profile --}}
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-0 mt-2 w-48 rounded-2xl shadow-xl border border-white/10 py-1 z-50 overflow-hidden"
                                 style="background: rgba(30,20,50,0.95); backdrop-filter: blur(20px);">
                                <div class="px-4 py-3 border-b border-white/5">
                                    <p class="text-xs text-white/40 font-medium">Signed in as</p>
                                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-white/5 w-full text-left transition-colors font-medium">
                                        <i class="fas fa-sign-out-alt w-4"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-2 sm:px-4 lg:px-6 py-3 lg:py-4 scroll-smooth lg:overflow-y-auto custom-scrollbar">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium mb-6 shadow-sm"
                         style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.2); backdrop-filter: blur(10px);"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                        <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-500/20">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <span class="text-emerald-300">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium mb-6 shadow-sm"
                         style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.2); backdrop-filter: blur(10px);"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                        <div class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-500/20">
                            <i class="fas fa-times text-white text-xs"></i>
                        </div>
                        <span class="text-rose-300">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- ========================================
         ADMIN CUSTOM NOTIFICATION SYSTEM
         Dark Glassmorphism Theme
         ======================================== --}}
    <div x-data="adminNotify()" x-cloak>
        {{-- CONFIRM MODAL --}}
        <template x-if="confirm.show">
            <div class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/70 backdrop-blur-md" @click="cancelConfirm()"></div>
                {{-- Modal --}}
                <div class="relative w-full max-w-md rounded-3xl p-7 shadow-2xl transform"
                     style="background: linear-gradient(145deg, rgba(30,27,46,0.97) 0%, rgba(20,17,36,0.98) 100%); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(30px);"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-6"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-90">
                    {{-- Icon --}}
                    <div class="flex items-center justify-center w-16 h-16 rounded-2xl mx-auto mb-5"
                         :class="{
                            'bg-red-500/10 border border-red-500/20': confirm.type === 'danger',
                            'bg-amber-500/10 border border-amber-500/20': confirm.type === 'warning',
                            'bg-cyan-500/10 border border-cyan-500/20': confirm.type === 'info',
                            'bg-emerald-500/10 border border-emerald-500/20': confirm.type === 'success'
                         }">
                        <i class="text-2xl"
                           :class="{
                              'fas fa-trash-alt text-red-400': confirm.type === 'danger',
                              'fas fa-exclamation-triangle text-amber-400': confirm.type === 'warning',
                              'fas fa-question-circle text-cyan-400': confirm.type === 'info',
                              'fas fa-check-circle text-emerald-400': confirm.type === 'success'
                           }"></i>
                    </div>
                    {{-- Title --}}
                    <h3 class="text-xl font-bold text-white text-center mb-2" x-text="confirm.title"></h3>
                    {{-- Message --}}
                    <p class="text-white/50 text-center text-sm leading-relaxed mb-7" x-text="confirm.message"></p>
                    {{-- Actions --}}
                    <div class="flex items-center gap-3">
                        <button @click="cancelConfirm()" 
                                class="flex-1 px-5 py-3 rounded-xl text-sm font-semibold text-white/60 hover:text-white transition-all border border-white/10 hover:border-white/20 hover:bg-white/5">
                            Batal
                        </button>
                        <button @click="proceedConfirm()"
                                class="flex-1 px-5 py-3 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98]"
                                :class="{
                                    'bg-gradient-to-r from-red-500 to-rose-600 shadow-red-500/25': confirm.type === 'danger',
                                    'bg-gradient-to-r from-amber-500 to-orange-600 shadow-amber-500/25': confirm.type === 'warning',
                                    'bg-gradient-to-r from-cyan-500 to-blue-600 shadow-cyan-500/25': confirm.type === 'info',
                                    'bg-gradient-to-r from-emerald-500 to-green-600 shadow-emerald-500/25': confirm.type === 'success'
                                }"
                                x-text="confirm.confirmText">
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- TOAST NOTIFICATION --}}
        <template x-if="toast.show">
            <div class="fixed top-6 right-6 z-[9998] max-w-sm w-full"
                 x-transition:enter="transition ease-out duration-400"
                 x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8 scale-95">
                <div class="rounded-2xl p-4 shadow-2xl flex items-start gap-3"
                     style="background: linear-gradient(145deg, rgba(30,27,46,0.97) 0%, rgba(20,17,36,0.98) 100%); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(30px);">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         :class="{
                            'bg-emerald-500/15 border border-emerald-500/20': toast.type === 'success',
                            'bg-red-500/15 border border-red-500/20': toast.type === 'error',
                            'bg-amber-500/15 border border-amber-500/20': toast.type === 'warning',
                            'bg-cyan-500/15 border border-cyan-500/20': toast.type === 'info'
                         }">
                        <i class="text-sm"
                           :class="{
                              'fas fa-check text-emerald-400': toast.type === 'success',
                              'fas fa-times text-red-400': toast.type === 'error',
                              'fas fa-exclamation text-amber-400': toast.type === 'warning',
                              'fas fa-info text-cyan-400': toast.type === 'info'
                           }"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white" x-text="toast.title"></p>
                        <p class="text-xs text-white/50 mt-0.5" x-text="toast.message"></p>
                    </div>
                    <button @click="toast.show = false" class="text-white/30 hover:text-white/60 transition-colors mt-0.5">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <script>
    function adminNotify() {
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

    // Global helper for admin confirm dialogs
    function adminConfirm(formEl, title, message, type = 'danger', confirmText = 'Ya, Lanjutkan') {
        document.querySelectorAll('[x-data]').forEach(el => {
            if (el._x_dataStack && el._x_dataStack[0] && el._x_dataStack[0].showConfirm) {
                el._x_dataStack[0].showConfirm(formEl, title, message, type, confirmText);
            }
        });
        return false;
    }

    // Global helper for admin toast
    function adminToast(title, message, type = 'success', duration = 3000) {
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
