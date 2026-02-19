@extends('layouts.admin')
@section('title', 'Analytics Dashboard')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-chart-line text-cyan-400"></i> Analytics Dashboard
        </h1>
        <p class="text-sm text-white/50">Insight mendalam untuk bisnis Anda</p>
    </div>

    {{-- Customer Metrics --}}
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-white/40 mb-3">
            <i class="fas fa-users mr-1"></i> Customer Metrics
        </h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Total Customer</p>
                <p class="text-2xl font-bold text-white">{{ number_format($totalCustomers) }}</p>
                <p class="text-xs text-cyan-400 mt-1">+{{ $newCustomersThisMonth }} bulan ini</p>
            </div>
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Retention Rate</p>
                <p class="text-2xl font-bold text-white">{{ $retentionRate }}%</p>
                <p class="text-xs text-white/30 mt-1">{{ $repeatBuyers }} repeat buyers</p>
            </div>
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Avg. Order Value</p>
                <p class="text-2xl font-bold text-cyan-300">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</p>
            </div>
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Avg. Customer LTV</p>
                <p class="text-2xl font-bold text-emerald-300">Rp {{ number_format($avgClv, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Operational Metrics --}}
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-white/40 mb-3">
            <i class="fas fa-cogs mr-1"></i> Operational
        </h2>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Avg. Processing</p>
                <p class="text-2xl font-bold text-white">{{ $avgProcessingDays }}</p>
                <p class="text-xs text-white/30">hari</p>
            </div>
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Cancel Rate</p>
                <p class="text-2xl font-bold {{ $cancellationRate > 15 ? 'text-red-400' : 'text-white' }}">{{ $cancellationRate }}%</p>
                <p class="text-xs text-white/30">30 hari terakhir</p>
            </div>
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Payment Success</p>
                <p class="text-2xl font-bold text-emerald-300">{{ $paymentSuccessRate }}%</p>
            </div>
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Open Tickets</p>
                <p class="text-2xl font-bold {{ $openTickets > 5 ? 'text-orange-400' : 'text-white' }}">{{ $openTickets }}</p>
            </div>
            <div class="dark-glass-card rounded-2xl p-5">
                <p class="text-xs text-white/40 mb-1">Avg. Resolution</p>
                <p class="text-2xl font-bold text-white">{{ $avgResolutionHours }}</p>
                <p class="text-xs text-white/30">jam</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Best Sellers --}}
        <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-trophy text-amber-400"></i> Best Sellers <span class="text-white/30 font-normal">(30 hari)</span>
            </h3>
            <div class="space-y-3">
                @forelse($bestSellers as $i => $product)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i === 0 ? 'bg-amber-500/20 text-amber-400' : ($i === 1 ? 'bg-gray-400/20 text-gray-300' : ($i === 2 ? 'bg-orange-700/20 text-orange-400' : 'bg-white/10 text-white/40')) }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white truncate">{{ $product->nama }}</p>
                            <p class="text-xs text-white/40">{{ number_format($product->total_sold, 1) }} Kg terjual</p>
                        </div>
                        <span class="text-xs font-semibold text-cyan-300">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-4">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>

        {{-- Top Customers --}}
        <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-crown text-amber-400"></i> Top Customers
            </h3>
            <div class="space-y-3">
                @forelse($topCustomers as $i => $customer)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i === 0 ? 'bg-amber-500/20 text-amber-400' : 'bg-white/10 text-white/40' }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white truncate">{{ $customer->name }}</p>
                            <p class="text-xs text-white/40">{{ $customer->completed_orders }} pesanan</p>
                        </div>
                        <span class="text-xs font-semibold text-emerald-300">Rp {{ number_format($customer->lifetime_spent ?? 0, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sales by Day & Hour --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Sales by Day of Week --}}
        <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-week text-blue-400"></i> Penjualan per Hari <span class="text-white/30 font-normal">(90 hari)</span>
            </h3>
            @if(count($salesByDay) > 0)
                @php $maxSales = max(array_column($salesByDay, 'sales')); @endphp
                <div class="space-y-2">
                    @foreach($salesByDay as $day)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-white/50 w-14">{{ $day['day'] }}</span>
                            <div class="flex-1 h-6 bg-white/5 rounded-lg overflow-hidden relative">
                                <div class="h-full rounded-lg transition-all" 
                                     style="width: {{ $maxSales > 0 ? ($day['sales'] / $maxSales * 100) : 0 }}%; background: linear-gradient(90deg, #0ea5e9, #06b6d4);"></div>
                            </div>
                            <span class="text-xs text-white/50 w-20 text-right">{{ $day['orders'] }} order</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-white/30 text-center py-4">Belum ada data</p>
            @endif
        </div>

        {{-- Sales by Hour --}}
        <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-purple-400"></i> Peak Hours <span class="text-white/30 font-normal">(30 hari)</span>
            </h3>
            @if($salesByHour->count())
                @php $maxHourOrders = $salesByHour->max('order_count'); @endphp
                <div class="flex items-end gap-1 h-32">
                    @for($h = 0; $h < 24; $h++)
                        @php $hourData = $salesByHour->firstWhere('hour', $h); $count = $hourData ? $hourData->order_count : 0; @endphp
                        <div class="flex-1 flex flex-col items-center gap-1" title="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00 — {{ $count }} order">
                            <div class="w-full rounded-t transition-all"
                                 style="height: {{ $maxHourOrders > 0 ? max(4, ($count / $maxHourOrders * 100)) : 4 }}%; background: {{ $count > 0 ? 'linear-gradient(180deg, #a78bfa, #7c3aed)' : 'rgba(255,255,255,0.05)' }};"></div>
                        </div>
                    @endfor
                </div>
                <div class="flex items-center justify-between text-[10px] text-white/30 mt-1">
                    <span>00</span><span>06</span><span>12</span><span>18</span><span>23</span>
                </div>
            @else
                <p class="text-sm text-white/30 text-center py-4">Belum ada data</p>
            @endif
        </div>
    </div>

    {{-- Monthly Trend --}}
    <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-area text-teal-400"></i> Tren Bulanan <span class="text-white/30 font-normal">(6 bulan)</span>
        </h3>
        @if($monthlyTrend->count())
            @php $maxMonthSales = $monthlyTrend->max('total_sales'); @endphp
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-white/40 uppercase tracking-wider">
                            <th class="text-left py-2 pr-4">Bulan</th>
                            <th class="text-right py-2 px-4">Orders</th>
                            <th class="text-right py-2 px-4">Revenue</th>
                            <th class="text-right py-2 px-4">Unique Cust.</th>
                            <th class="text-left py-2 pl-4 w-40">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($monthlyTrend as $month)
                            <tr>
                                <td class="py-3 pr-4 text-white font-semibold">
                                    {{ \Carbon\Carbon::parse($month->month . '-01')->translatedFormat('M Y') }}
                                </td>
                                <td class="py-3 px-4 text-right text-white/70">{{ $month->order_count }}</td>
                                <td class="py-3 px-4 text-right text-cyan-300 font-semibold">Rp {{ number_format($month->total_sales, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right text-white/60">{{ $month->unique_customers }}</td>
                                <td class="py-3 pl-4">
                                    <div class="h-3 bg-white/5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full" 
                                             style="width: {{ $maxMonthSales > 0 ? ($month->total_sales / $maxMonthSales * 100) : 0 }}%; background: linear-gradient(90deg, #14b8a6, #06b6d4);"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-white/30 text-center py-4">Belum ada data</p>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Slow Moving Products --}}
        <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-400"></i> Slow Moving Products
            </h3>
            <div class="space-y-3">
                @forelse($slowMoving as $product)
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-white truncate">{{ $product->nama }}</p>
                            <p class="text-xs text-white/40">Stok: {{ number_format($product->stok, 1) }} Kg · Terjual: {{ number_format($product->sales_30d, 1) }} Kg/30hr</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-4">Tidak ada slow-moving product</p>
                @endforelse
            </div>
        </div>

        {{-- Reorder Suggestions --}}
        <div class="dark-glass-card rounded-2xl p-5 sm:p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-sync-alt text-rose-400"></i> Saran Reorder
            </h3>
            @if($reorderSuggestions->count())
                <div class="space-y-3">
                    @foreach($reorderSuggestions as $item)
                        <div class="p-3 rounded-xl bg-rose-500/5 border border-rose-500/10">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-semibold text-white truncate">{{ $item->nama_produk ?? $item->produk?->nama ?? '-' }}</p>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $item->days_remaining <= 3 ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400' }}">
                                    {{ $item->days_remaining }} hari lagi
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-white/40">
                                <span>Stok: {{ number_format($item->produk->stok, 1) }} Kg</span>
                                <span>Avg: {{ $item->avg_daily_sales }} Kg/hari</span>
                                <span class="text-cyan-300">Reorder: ~{{ $item->recommended_qty }} Kg</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-check-circle text-2xl text-emerald-400 mb-2"></i>
                    <p class="text-sm text-white/40">Semua stok aman untuk saat ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
