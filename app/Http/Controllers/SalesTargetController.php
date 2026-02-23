<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesTargetController extends Controller
{
    public function index()
    {
        $targets = SalesTarget::with('creator')
            ->latest()
            ->paginate(20);

        // Today & this month stats for quick view
        $todayTarget   = SalesTarget::todayTarget();
        $monthTarget   = SalesTarget::thisMonthTarget();

        $todaySales    = Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_price');
        $monthSales    = Order::where('status', 'completed')
                              ->whereYear('created_at', now()->year)
                              ->whereMonth('created_at', now()->month)
                              ->sum('total_price');

        return view('admin.sales-targets.index', compact('targets', 'todayTarget', 'monthTarget', 'todaySales', 'monthSales'));
    }

    public function create()
    {
        return view('admin.sales-targets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|in:daily,monthly',
            'target_amount' => 'required|numeric|min:1',
            'target_date'   => 'required|date',
            'notes'         => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = Auth::id();

        SalesTarget::create($validated);

        return redirect()->route('admin.sales-targets.index')
            ->with('success', 'Target penjualan berhasil ditambahkan!');
    }

    public function edit(SalesTarget $salesTarget)
    {
        return view('admin.sales-targets.edit', compact('salesTarget'));
    }

    public function update(Request $request, SalesTarget $salesTarget)
    {
        $validated = $request->validate([
            'type'          => 'required|in:daily,monthly',
            'target_amount' => 'required|numeric|min:1',
            'target_date'   => 'required|date',
            'notes'         => 'nullable|string|max:500',
        ]);

        $salesTarget->update($validated);

        return redirect()->route('admin.sales-targets.index')
            ->with('success', 'Target penjualan berhasil diperbarui!');
    }

    public function destroy(SalesTarget $salesTarget)
    {
        $salesTarget->delete();
        return redirect()->route('admin.sales-targets.index')
            ->with('success', 'Target penjualan berhasil dihapus!');
    }
}
