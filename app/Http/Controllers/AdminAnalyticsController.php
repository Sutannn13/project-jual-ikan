<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        // ==================== CUSTOMER METRICS ====================
        $totalCustomers = User::where('role', 'user')->count();
        $newCustomersThisMonth = User::where('role', 'user')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Repeat buyers (> 1 completed order)
        $repeatBuyers = User::where('role', 'user')
            ->whereHas('orders', function ($q) {
                $q->where('status', 'completed');
            }, '>=', 2)
            ->count();

        $buyersTotal = User::where('role', 'user')
            ->whereHas('orders', function ($q) {
                $q->where('status', 'completed');
            })
            ->count();

        $retentionRate = $buyersTotal > 0 ? round(($repeatBuyers / $buyersTotal) * 100, 1) : 0;

        // Average Order Value
        $avgOrderValue = Order::where('status', 'completed')->avg('total_price') ?? 0;

        // Customer Lifetime Value
        $avgClv = $buyersTotal > 0
            ? Order::where('status', 'completed')->sum('total_price') / $buyersTotal
            : 0;

        // ==================== PRODUCT PERFORMANCE ====================
        // Best sellers (by revenue, last 30 days)
        $bestSellers = Produk::withCount([
            'orderItems as total_sold' => function ($q) {
                $q->select(DB::raw('COALESCE(SUM(qty), 0)'))
                    ->whereHas('order', function ($q2) {
                        $q2->where('status', 'completed')
                            ->where('created_at', '>=', now()->subDays(30));
                    });
            },
            'orderItems as total_revenue' => function ($q) {
                $q->select(DB::raw('COALESCE(SUM(subtotal), 0)'))
                    ->whereHas('order', function ($q2) {
                        $q2->where('status', 'completed')
                            ->where('created_at', '>=', now()->subDays(30));
                    });
            }
        ])->orderByDesc('total_revenue')->take(5)->get();

        // Slow-moving products (< 5 Kg sold in 30 days, still has stock)
        $slowMoving = Produk::withCount([
            'orderItems as sales_30d' => function ($q) {
                $q->select(DB::raw('COALESCE(SUM(qty), 0)'))
                    ->whereHas('order', function ($q2) {
                        $q2->where('status', 'completed')
                            ->where('created_at', '>=', now()->subDays(30));
                    });
            }
        ])->where('stok', '>', 0)->get()->filter(fn($p) => $p->sales_30d < 5)->take(10);

        // ==================== SALES ANALYTICS ====================
        // Sales by day of week (last 90 days)
        $salesByDayOfWeek = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(90))
            ->select(DB::raw('DAYOFWEEK(created_at) as day_of_week'), DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_price) as total_sales'))
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        $dayNames = ['', 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $salesByDay = [];
        foreach ($salesByDayOfWeek as $item) {
            $salesByDay[] = [
                'day' => $dayNames[$item->day_of_week] ?? 'N/A',
                'orders' => $item->order_count,
                'sales' => $item->total_sales,
            ];
        }

        // Sales by hour (last 30 days)
        $salesByHour = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_price) as total_sales'),
                DB::raw('COUNT(DISTINCT user_id) as unique_customers')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ==================== OPERATIONAL METRICS ====================
        // Average processing time (pending → completed)
        $avgProcessingDays = DB::table('orders')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(90))
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
            ->value('avg_hours');
        $avgProcessingDays = $avgProcessingDays ? round($avgProcessingDays / 24, 1) : 0;

        // Cancellation rate
        $totalOrdersLast30 = Order::where('created_at', '>=', now()->subDays(30))->count();
        $cancelledLast30 = Order::where('status', 'cancelled')
            ->where('created_at', '>=', now()->subDays(30))->count();
        $cancellationRate = $totalOrdersLast30 > 0 ? round(($cancelledLast30 / $totalOrdersLast30) * 100, 1) : 0;

        // Payment success rate
        $paymentAttempts = Order::where('created_at', '>=', now()->subDays(30))
            ->whereNotIn('status', ['cancelled'])->count();
        $paymentSuccess = Order::where('created_at', '>=', now()->subDays(30))
            ->whereIn('status', ['paid', 'confirmed', 'out_for_delivery', 'completed'])->count();
        $paymentSuccessRate = $paymentAttempts > 0 ? round(($paymentSuccess / $paymentAttempts) * 100, 1) : 0;

        // ==================== SUPPORT METRICS ====================
        $openTickets = SupportTicket::open()->count();
        $avgResolutionHours = DB::table('support_tickets')
            ->whereNotNull('closed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours'))
            ->value('avg_hours');
        $avgResolutionHours = $avgResolutionHours ? round($avgResolutionHours, 1) : 0;

        // ==================== REORDER SUGGESTIONS ====================
        $reorderSuggestions = Produk::where('stok', '>', 0)->get()->map(function ($produk) {
            $sales90d = OrderItem::where('produk_id', $produk->id)
                ->whereHas('order', fn($q) => $q->where('status', 'completed')->where('created_at', '>=', now()->subDays(90)))
                ->sum('qty');
            
            $avgSalesPerDay = $sales90d / 90;
            $daysRemaining = $avgSalesPerDay > 0 ? ceil($produk->stok / $avgSalesPerDay) : 999;
            $reorderPoint = $avgSalesPerDay * 7;

            return (object) [
                'produk' => $produk,
                'avg_daily_sales' => round($avgSalesPerDay, 2),
                'days_remaining' => $daysRemaining,
                'should_reorder' => $produk->stok <= $reorderPoint && $avgSalesPerDay > 0,
                'recommended_qty' => round($avgSalesPerDay * 30),
                'out_of_stock_date' => now()->addDays($daysRemaining)->format('d M Y'),
            ];
        })->filter(fn($item) => $item->should_reorder)->sortBy('days_remaining')->take(10);

        // ==================== TOP CUSTOMERS ====================
        $topCustomers = User::where('role', 'user')
            ->withCount(['orders as completed_orders' => fn($q) => $q->where('status', 'completed')])
            ->withSum(['orders as lifetime_spent' => fn($q) => $q->where('status', 'completed')], 'total_price')
            ->having('completed_orders', '>', 0)
            ->orderByDesc('lifetime_spent')
            ->take(10)
            ->get();

        return view('admin.analytics.index', compact(
            'totalCustomers', 'newCustomersThisMonth', 'repeatBuyers', 'retentionRate',
            'avgOrderValue', 'avgClv', 'bestSellers', 'slowMoving',
            'salesByDay', 'salesByHour', 'monthlyTrend',
            'avgProcessingDays', 'cancellationRate', 'paymentSuccessRate',
            'openTickets', 'avgResolutionHours',
            'reorderSuggestions', 'topCustomers'
        ));
    }
}
