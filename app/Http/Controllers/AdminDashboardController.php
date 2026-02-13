<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Models\OrderItem;
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

        // Doughnut chart: Lele vs Ikan Mas
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

        return view('admin.dashboard', compact(
            'totalSales', 'totalOrders', 'todayOrders', 'totalProducts', 'lowStockProducts',
            'pendingOrders', 'chartLabels', 'chartSalesData', 'chartProfitData', 'doughnutLabels',
            'doughnutData', 'recentOrders', 'waitingVerification', 'todaySales', 
            'monthSales', 'pendingRevenue', 'expiredOrders',
            'totalProfit', 'todayProfit', 'monthProfit'
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
}
