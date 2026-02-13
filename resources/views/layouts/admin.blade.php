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

        /* Custom Scrollbar for Main Content - Dark Theme */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.2);
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
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 50px 50px;
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
<body class="antialiased text-white" x-data="{ sidebarOpen: false }">
    {{-- Glossy Premium Background --}}
    <div class="fixed inset-0 bg-admin-glossy -z-10"></div>
    <div class="fixed inset-0 -z-10" style="background: radial-gradient(circle at 30% 20%, rgba(239, 68, 68, 0.15), transparent 40%), radial-gradient(circle at 70% 60%, rgba(251, 146, 60, 0.15), transparent 50%), radial-gradient(circle at 50% 100%, rgba(217, 70, 239, 0.1), transparent 40%);"></div>
    <div class="fixed inset-0 admin-grid-pattern -z-10"></div>

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

                    <p class="text-[10px] uppercase tracking-widest text-orange-300/80 font-bold mt-6 mb-3 px-3">Analitik</p>

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

    @stack('scripts')
</body>
</html>
