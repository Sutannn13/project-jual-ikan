@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
{{-- SUMMARY CARDS --}}
<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2 sm:gap-2.5 mb-4">
    {{-- Total Sales --}}
    <div class="dark-glass-card rounded-xl p-2.5 sm:p-3 relative overflow-hidden group transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-teal-500/10 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); box-shadow: 0 4px 15px rgba(20,184,166,0.4);">
                    <i class="fas fa-wallet text-white text-sm"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] text-teal-400 font-semibold"><i class="fas fa-arrow-up"></i> All time</span>
            </div>
            <p class="text-[10px] sm:text-xs text-white/40 font-semibold uppercase tracking-wider">Total Penjualan</p>
            <p class="text-sm sm:text-lg md:text-xl font-extrabold text-white mt-1 break-all leading-tight">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Today Sales --}}
    <div class="dark-glass-card rounded-xl p-2.5 sm:p-3 relative overflow-hidden group transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); box-shadow: 0 4px 15px rgba(6,182,212,0.4);">
                    <i class="fas fa-chart-bar text-white text-sm"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] text-cyan-400 font-semibold"><i class="fas fa-calendar-alt"></i> Hari ini</span>
            </div>
            <p class="text-[10px] sm:text-xs text-white/40 font-semibold uppercase tracking-wider">Penjualan Hari Ini</p>
            <p class="text-sm sm:text-lg md:text-xl font-extrabold text-white mt-1 break-all leading-tight">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</p>
            <p class="text-[10px] sm:text-xs text-cyan-400/80 mt-1 sm:mt-1.5 font-medium truncate">Bulan ini: Rp {{ number_format($monthSales ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Waiting Verification --}}
    <a href="{{ route('admin.orders.index', ['status' => 'waiting_payment']) }}" 
       class="dark-glass-card rounded-xl p-2.5 sm:p-3 relative overflow-hidden group transition-all duration-300 hover:scale-[1.02]">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0 relative"
                     style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); box-shadow: 0 4px 15px rgba(249,115,22,0.4);">
                    <i class="fas fa-file-invoice-dollar text-white text-sm"></i>
                    @if(($waitingVerification ?? 0) > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                        <span class="text-white text-[8px] font-bold">!</span>
                    </span>
                    @endif
                </div>
                <span class="text-[9px] sm:text-[10px] text-orange-400 font-semibold"><i class="fas fa-clock"></i> Cek</span>
            </div>
            <p class="text-[10px] sm:text-xs text-white/40 font-semibold uppercase tracking-wider">Perlu Verifikasi</p>
            <p class="text-sm sm:text-lg md:text-xl font-extrabold text-white mt-1">{{ $waitingVerification ?? 0 }}</p>
            @if(($expiredOrders ?? 0) > 0)<p class="text-[10px] text-red-400 font-bold mt-1">{{ $expiredOrders }} expired!</p>@endif
        </div>
    </a>

    {{-- Pending Revenue --}}
    <div class="dark-glass-card rounded-xl p-2.5 sm:p-3 relative overflow-hidden group transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-green-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 15px rgba(16,185,129,0.4);">
                    <i class="fas fa-hourglass-half text-white text-sm"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] text-emerald-400 font-semibold"><i class="fas fa-shipping-fast"></i> Aktif</span>
            </div>
            <p class="text-[10px] sm:text-xs text-white/40 font-semibold uppercase tracking-wider">Dalam Proses</p>
            <p class="text-sm sm:text-lg md:text-xl font-extrabold text-white mt-1 break-all leading-tight">Rp {{ number_format($pendingRevenue ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Total Profit (NEW) --}}
    <div class="dark-glass-card rounded-xl p-2.5 sm:p-3 relative overflow-hidden group transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 4px 15px rgba(139,92,246,0.4);">
                    <i class="fas fa-chart-pie text-white text-sm"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] text-violet-400 font-semibold"><i class="fas fa-trending-up"></i> Profit</span>
            </div>
            <p class="text-[10px] sm:text-xs text-white/40 font-semibold uppercase tracking-wider">Keuntungan Bersih</p>
            <p class="text-sm sm:text-lg md:text-xl font-extrabold text-white mt-1 break-all leading-tight">Rp {{ number_format($totalProfit ?? 0, 0, ',', '.') }}</p>
            <p class="text-[10px] sm:text-xs text-violet-400/80 mt-1 sm:mt-1.5 font-medium truncate">Hari ini: Rp {{ number_format($todayProfit ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Quick Stats Bar --}}
<div class="dark-glass-card rounded-xl p-2.5 sm:p-3 mb-4">
    <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-[10px] sm:text-sm">
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm shadow-amber-400/50"></div>
                <span class="text-white/60">Menunggu Bayar: <strong class="text-white/90">{{ $pendingOrders }}</strong></span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 shadow-sm shadow-cyan-400/50"></div>
                <span class="text-white/60">Pesanan Hari Ini: <strong class="text-white/90">{{ $todayOrders }}</strong></span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-teal-400 shadow-sm shadow-teal-400/50"></div>
                <span class="text-white/60">Total Produk: <strong class="text-white/90">{{ $totalProducts }}</strong></span>
            </div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs sm:text-sm text-cyan-400 hover:text-cyan-300 font-semibold whitespace-nowrap transition-colors">
            Lihat Semua Pesanan <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

{{-- CHARTS SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5 mb-4">
    {{-- Sales Trend Chart --}}
    <div class="dark-glass-card rounded-xl p-4 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-white">Tren Penjualan & Profit</h3>
                <p class="text-[10px] text-white/40">7 hari terakhir (Omset vs Keuntungan)</p>
            </div>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                 style="background: linear-gradient(135deg, rgba(6,182,212,0.2) 0%, rgba(20,184,166,0.15) 100%); border: 1px solid rgba(6,182,212,0.15);">
                <i class="fas fa-chart-line text-cyan-400 text-xs"></i>
            </div>
        </div>
        <div class="h-56 sm:h-64" id="salesChartContainer">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Category Distribution Chart --}}
    <div class="dark-glass-card rounded-xl p-4 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-white">Distribusi Penjualan</h3>
                <p class="text-[10px] text-white/40">Berdasarkan kategori</p>
            </div>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                 style="background: linear-gradient(135deg, rgba(249,115,22,0.2) 0%, rgba(251,146,60,0.15) 100%); border: 1px solid rgba(249,115,22,0.15);">
                <i class="fas fa-pie-chart text-orange-400 text-xs"></i>
            </div>
        </div>
        <div class="h-56 sm:h-64 flex items-center justify-center" id="categoryChartContainer">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

{{-- BOTTOM SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
    {{-- Low Stock Alert --}}
    <div class="dark-glass-card rounded-xl overflow-hidden">
        <div class="p-3 border-b border-white/5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(239,68,68,0.2) 0%, rgba(220,38,38,0.15) 100%); border: 1px solid rgba(239,68,68,0.15);">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Peringatan Stok Rendah</h3>
                    <p class="text-[10px] text-white/40">Produk dengan stok ≤ 10 Kg</p>
                </div>
            </div>
        </div>
        <div class="p-3">
            @if($lowStockProducts->count() > 0)
                <div class="space-y-2">
                    @foreach($lowStockProducts as $product)
                    <div class="flex items-center justify-between p-3 rounded-lg transition-colors"
                         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.1);">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex items-center justify-center flex-shrink-0" style="background: rgba(255,255,255,0.06);">
                                @if($product->foto)
                                    <img src="{{ asset('storage/'.$product->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-fish text-white/30"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-white text-sm truncate">{{ $product->nama }}</p>
                                <span class="text-xs {{ $product->kategori === 'Ikan Nila' ? 'text-amber-400' : 'text-cyan-400' }}">{{ $product->kategori }}</span>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $product->stok <= 0 ? 'bg-red-500/80 text-white' : 'bg-red-500/15 text-red-400 border border-red-500/20' }}">
                            {{ number_format($product->stok, 1) }} Kg
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
                         style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.15);">
                        <i class="fas fa-check text-2xl text-emerald-400"></i>
                    </div>
                    <p class="text-white/40 text-sm">Semua stok aman</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="dark-glass-card rounded-xl overflow-hidden">
        <div class="p-3 border-b border-white/5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         style="background: linear-gradient(135deg, rgba(6,182,212,0.2) 0%, rgba(14,116,144,0.15) 100%); border: 1px solid rgba(6,182,212,0.15);">
                        <i class="fas fa-history text-cyan-400 text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Pesanan Terbaru</h3>
                        <p class="text-[10px] text-white/40">5 pesanan terakhir</p>
                    </div>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-[10px] text-cyan-400 hover:text-cyan-300 font-semibold transition-colors">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="p-3">
            @if($recentOrders->count() > 0)
                <div class="space-y-2">
                    @foreach($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" 
                       class="block p-3 rounded-lg hover:bg-white/5 transition-colors border border-white/5">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="font-bold text-cyan-400 text-sm">{{ $order->order_number }}</p>
                                <p class="text-white/40 text-xs truncate">{{ $order->user->name }} — {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase
                                {{ match($order->status) {
                                    'pending' => 'bg-amber-500/15 text-amber-400 border border-amber-500/20',
                                    'confirmed' => 'bg-blue-500/15 text-blue-400 border border-blue-500/20',
                                    'out_for_delivery' => 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/20',
                                    'completed' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20',
                                    'cancelled' => 'bg-red-500/15 text-red-400 border border-red-500/20',
                                    default => 'bg-white/10 text-white/60 border border-white/10'
                                } }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-shopping-cart text-2xl text-white/20"></i>
                    </div>
                    <p class="text-white/40 text-sm">Belum ada pesanan</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dark Theme Chart Colors
    const oceanGradient = (ctx, chartArea) => {
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, 'rgba(6, 182, 212, 0.02)');
        gradient.addColorStop(1, 'rgba(6, 182, 212, 0.25)');
        return gradient;
    };

    // Sales Trend Chart - Dark Theme
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Omset (Rp)',
                        data: @json($chartSalesData),
                        borderColor: '#22d3ee',
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return 'rgba(6, 182, 212, 0.1)';
                            return oceanGradient(ctx, chartArea);
                        },
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#22d3ee',
                        pointBorderColor: 'rgba(30,20,50,0.8)',
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#22d3ee',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2
                    },
                    {
                        label: 'Profit (Rp)',
                        data: @json($chartProfitData),
                        borderColor: '#a78bfa',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#a78bfa',
                        pointBorderColor: 'rgba(30,20,50,0.8)',
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#a78bfa',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 11 },
                            color: 'rgba(255,255,255,0.6)',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleColor: '#ffffff',
                        bodyColor: '#22d3ee',
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255,255,255,0.35)', font: { size: 11 } },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                        ticks: { 
                            color: 'rgba(255,255,255,0.35)', 
                            font: { size: 11 },
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Category Distribution Chart - Dark Theme
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($doughnutLabels),
                datasets: [{
                    data: @json($doughnutData),
                    backgroundColor: [
                        'rgba(34, 211, 238, 0.85)',
                        'rgba(251, 146, 60, 0.85)'
                    ],
                    borderColor: ['rgba(34, 211, 238, 0.3)', 'rgba(251, 146, 60, 0.3)'],
                    borderWidth: 2,
                    hoverOffset: 10,
                    hoverBorderColor: ['#22d3ee', '#fb923c']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12 },
                            color: 'rgba(255,255,255,0.6)'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 10,
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1
                    }
                }
            }
        });
    }
});
</script>
@endpush
