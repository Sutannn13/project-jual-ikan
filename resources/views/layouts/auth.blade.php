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
        }
        body {
            background: url('{{ asset('images/hero-background.jpg') }}') no-repeat center center fixed !important;
            background-size: cover !important;
        }
        
        @keyframes floatUp {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            animation: floatUp linear infinite;
        }
     
        .grid-pattern {
            background-image: 
                linear-gradient(rgba(6, 182, 212, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(6, 182, 212, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased text-gray-800 overflow-x-hidden">

    {{-- ========================================
         HERO BACKGROUND OVERLAY
         ======================================== --}}
    <div class="fixed inset-0 w-full h-full" style="background: linear-gradient(135deg, rgba(7,21,37,0.55) 0%, rgba(8,80,120,0.45) 50%, rgba(14,116,144,0.55) 100%); z-index: 0; pointer-events: none;"></div>
    <div class="fixed inset-0 grid-pattern" style="z-index: 0; pointer-events: none;"></div>

    {{-- Floating particles --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none" style="z-index: 0;">
        <div class="particle w-1 h-1 bg-ocean-400/30" style="left: 10%; animation-duration: 15s; animation-delay: 0s;"></div>
        <div class="particle w-1.5 h-1.5 bg-teal-400/20" style="left: 25%; animation-duration: 20s; animation-delay: 3s;"></div>
        <div class="particle w-1 h-1 bg-ocean-300/25" style="left: 45%; animation-duration: 18s; animation-delay: 7s;"></div>
        <div class="particle w-2 h-2 bg-teal-300/15" style="left: 65%; animation-duration: 22s; animation-delay: 2s;"></div>
        <div class="particle w-1 h-1 bg-ocean-400/20" style="left: 80%; animation-duration: 16s; animation-delay: 5s;"></div>
        <div class="particle w-1.5 h-1.5 bg-teal-400/25" style="left: 92%; animation-duration: 19s; animation-delay: 8s;"></div>
    </div>

    {{-- ========================================
         COMPACT TOP BAR
         ======================================== --}}
    <nav class="sticky top-0 z-10 border-b border-white/10" style="background: rgba(7, 21, 37, 0.5); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);">
        <div class="absolute inset-0 bg-white/5"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);">
                        <i class="fas fa-fish text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-white">
                        FishMarket
                    </span>
                </div>

                {{-- Empty space for balance --}}
                <div></div>
            </div>
        </div>
    </nav>

    {{-- ========================================
         FLASH MESSAGES
         ======================================== --}}
    @if(session('success') || session('error') || session('warning'))
    <div class="max-w-md mx-auto px-4 pt-4 relative z-10">
        @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-medium"
                 style="background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, rgba(5,150,105,0.1) 100%); border: 1px solid rgba(16,185,129,0.3); backdrop-filter: blur(12px);"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                <div class="w-8 h-8 rounded-full bg-mint-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-white text-xs"></i>
                </div>
                <span class="text-mint-300">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-medium"
                 style="background: linear-gradient(135deg, rgba(239,68,68,0.15) 0%, rgba(220,38,38,0.1) 100%); border: 1px solid rgba(239,68,68,0.3); backdrop-filter: blur(12px);"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times text-white text-xs"></i>
                </div>
                <span class="text-red-300">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-medium"
                 style="background: linear-gradient(135deg, rgba(251,146,60,0.15) 0%, rgba(249,115,22,0.1) 100%); border: 1px solid rgba(251,146,60,0.3); backdrop-filter: blur(12px);"
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
    <main class="flex-1 relative z-10">
        @yield('content')
    </main>

    {{-- ========================================
         MINIMAL FOOTER
         ======================================== --}}
    <footer class="relative z-10 border-t border-white/10 py-6 mt-auto">
        <div class="text-center">
            <p class="text-white/30 text-xs">
                &copy; {{ date('Y') }} FishMarket &mdash; Platform Ikan Segar Terpercaya
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
