<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.produk'])->where('status', 'completed');

        // Quick filter presets
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        } else {
            // Manual date filter
            if ($request->filled('from')) {
                $query->whereDate('created_at', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('created_at', '<=', $request->to);
            }
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        $grandTotal = Order::where('status', 'completed')
            ->when($request->filled('period'), function($q) use ($request) {
                switch ($request->period) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                        break;
                }
            })
            ->when($request->filled('from') && !$request->filled('period'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to') && !$request->filled('period'), fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->sum('total_price');

        // Additional stats
        $stats = [
            'today' => Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_price'),
            'week' => Order::where('status', 'completed')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('total_price'),
            'month' => Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price'),
            'pending' => Order::whereIn('status', ['paid', 'confirmed', 'out_for_delivery'])->sum('total_price'),
            'total_orders' => $orders->total(),
        ];

        return view('admin.reports.index', compact('orders', 'grandTotal', 'stats'));
    }

    public function exportPdf(Request $request)
    {
        $query = Order::with(['user', 'items.produk'])->where('status', 'completed');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->latest()->get();
        $grandTotal = $orders->sum('total_price');

        $pdf = Pdf::loadView('admin.reports.pdf', compact('orders', 'grandTotal'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan-Penjualan-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}

