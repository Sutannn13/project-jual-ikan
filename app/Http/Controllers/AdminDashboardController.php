<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Models\OrderItem;
use App\Models\SalesTarget;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalSales = Order::where('status', 'completed')->sum('total_price');
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $totalProducts = Produk::count();
        $lowStockProducts = Produk::where('stok', '<=', 10)->orderBy('stok', 'asc')->get();
        $pendingOrders = Order::where('status', 'pending')->count();
        
        $waitingVerification = Order::where('status', 'waiting_payment')->count();
        
        $todaySales = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_price');
        
        $monthSales = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        
        $pendingRevenue = Order::whereIn('status', ['paid', 'confirmed', 'out_for_delivery'])
            ->sum('total_price');
        
        $expiredOrders = Order::expiredPending()->count();

        // ==================== PROFIT CALCULATION ====================
        // Calculate total profit from completed orders
        $completedOrders = Order::where('status', 'completed')->with('items')->get();
        $totalProfit = $completedOrders->sum(fn($order) => $order->gross_profit);
        
        // Today's profit
        $todayProfit = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->with('items')
            ->get()
            ->sum(fn($order) => $order->gross_profit);
        
        // Month's profit
        $monthProfit = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->with('items')
            ->get()
            ->sum(fn($order) => $order->gross_profit);

        // Chart data: Sales vs Profit over last 7 days
        $salesChart = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->with('items')
            ->get();

        $chartLabels = [];
        $chartSalesData = [];
        $chartProfitData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            
            $dayOrders = $salesChart->filter(fn($o) => $o->created_at->format('Y-m-d') === $date);
            $chartSalesData[] = $dayOrders->sum('total_price');
            $chartProfitData[] = $dayOrders->sum(fn($o) => $o->gross_profit);
        }

        // Doughnut chart: Ikan Nila vs Ikan Mas
        $categoryDistribution = OrderItem::join('produks', 'order_items.produk_id', '=', 'produks.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->selectRaw('produks.kategori, SUM(order_items.subtotal) as total')
            ->groupBy('produks.kategori')
            ->get();

        $doughnutLabels = $categoryDistribution->pluck('kategori')->toArray();
        $doughnutData = $categoryDistribution->pluck('total')->map(fn($v) => (float) $v)->toArray();

        // Recent orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Sales targets progress
        $dailyTarget  = SalesTarget::todayTarget();
        $monthTarget  = SalesTarget::thisMonthTarget();

        // Stock expiry alerts (within 3 days)
        $expiringStockCount = StockIn::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(3))
            ->count();

        return view('admin.dashboard', compact(
            'totalSales', 'totalOrders', 'todayOrders', 'totalProducts', 'lowStockProducts',
            'pendingOrders', 'chartLabels', 'chartSalesData', 'chartProfitData', 'doughnutLabels',
            'doughnutData', 'recentOrders', 'waitingVerification', 'todaySales', 
            'monthSales', 'pendingRevenue', 'expiredOrders',
            'totalProfit', 'todayProfit', 'monthProfit',
            'dailyTarget', 'monthTarget', 'expiringStockCount'
        ));
    }

    public function chartData()
    {
        $salesChart = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            $found = $salesChart->firstWhere('date', $date);
            $chartData[] = $found ? (float) $found->total : 0;
        }

        $categoryDistribution = OrderItem::join('produks', 'order_items.produk_id', '=', 'produks.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->selectRaw('produks.kategori, SUM(order_items.subtotal) as total')
            ->groupBy('produks.kategori')
            ->get();

        return response()->json([
            'chartLabels'    => $chartLabels,
            'chartData'      => $chartData,
            'doughnutLabels' => $categoryDistribution->pluck('kategori')->toArray(),
            'doughnutData'   => $categoryDistribution->pluck('total')->map(fn($v) => (float) $v)->toArray(),
        ]);
    }

    /**
     * Live stats API — polling every 30s from dashboard JS
     */
    public function liveStats()
    {
        $pendingOrders      = Order::where('status', 'pending')->count();
        $waitingVerification = Order::where('status', 'waiting_payment')->count();
        $needsAttention     = $pendingOrders + $waitingVerification;
        $todayOrders        = Order::whereDate('created_at', today())->count();
        $todaySales         = Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_price');
        $pendingRevenue     = Order::whereIn('status', ['paid', 'confirmed', 'out_for_delivery'])->sum('total_price');
        $unreadNotifs       = \App\Models\AdminNotification::unreadCount();

        // Last 5 orders for recent orders table
        $recent = Order::with('user')->latest()->take(5)->get()->map(fn($o) => [
            'id'           => $o->id,
            'order_number' => $o->order_number,
            'user_name'    => $o->user->name ?? 'Guest',
            'status'       => $o->status,
            'status_label' => $o->status_label,
            'created_at'   => $o->created_at->diffForHumans(),
            'url'          => route('admin.orders.show', $o),
        ]);

        return response()->json([
            'pendingOrders'       => $pendingOrders,
            'waitingVerification' => $waitingVerification,
            'needsAttention'      => $needsAttention,
            'todayOrders'         => $todayOrders,
            'todaySales'          => $todaySales,
            'pendingRevenue'      => $pendingRevenue,
            'unreadNotifs'        => $unreadNotifs,
            'recentOrders'        => $recent,
            'timestamp'           => now()->toISOString(),
        ]);
    }
}
