<?php

namespace App\Http\Controllers;

use App\Models\CourierDriver;
use App\Models\Order;
use Illuminate\Http\Request;

class CourierDriverController extends Controller
{
    public function index()
    {
        $drivers = CourierDriver::withCount([
            'orders as total_deliveries',
            'orders as active_deliveries' => fn($q) => $q->where('status', 'out_for_delivery'),
        ])->latest()->paginate(20);

        $driverStats = [
            'active'      => CourierDriver::where('status', 'active')->count(),
            'on_delivery' => CourierDriver::where('status', 'on_delivery')->count(),
            'inactive'    => CourierDriver::where('status', 'inactive')->count(),
        ];

        return view('admin.courier-drivers.index', compact('drivers', 'driverStats'));
    }

    public function create()
    {
        return view('admin.courier-drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'no_hp'     => 'required|string|max:20',
            'kendaraan' => 'nullable|string|max:255',
            'zona'      => 'nullable|string|max:255',
            'status'    => 'required|in:active,inactive,on_delivery',
            'catatan'   => 'nullable|string|max:1000',
        ]);

        CourierDriver::create($validated);

        return redirect()->route('admin.courier-drivers.index')
            ->with('success', 'Kurir berhasil ditambahkan!');
    }

    public function edit(CourierDriver $courierDriver)
    {
        return view('admin.courier-drivers.edit', compact('courierDriver'));
    }

    public function update(Request $request, CourierDriver $courierDriver)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'no_hp'     => 'required|string|max:20',
            'kendaraan' => 'nullable|string|max:255',
            'zona'      => 'nullable|string|max:255',
            'status'    => 'required|in:active,inactive,on_delivery',
            'catatan'   => 'nullable|string|max:1000',
        ]);

        $courierDriver->update($validated);

        return redirect()->route('admin.courier-drivers.index')
            ->with('success', 'Data kurir berhasil diperbarui!');
    }

    public function destroy(CourierDriver $courierDriver)
    {
        // Check if driver has active deliveries
        if ($courierDriver->orders()->where('status', 'out_for_delivery')->exists()) {
            return back()->with('error', 'Kurir tidak dapat dihapus karena masih memiliki pengiriman aktif.');
        }

        $courierDriver->delete();

        return redirect()->route('admin.courier-drivers.index')
            ->with('success', 'Kurir berhasil dihapus!');
    }

    /**
     * Assign a driver to an order
     */
    public function assignToOrder(Request $request, Order $order)
    {
        $request->validate([
            'courier_driver_id' => 'required|exists:courier_drivers,id',
        ]);

        $driver = CourierDriver::findOrFail($request->courier_driver_id);

        $order->update([
            'courier_driver_id' => $driver->id,
            'courier_name'      => $driver->nama,
            'courier_phone'     => $driver->no_hp,
        ]);

        $driver->update(['status' => 'on_delivery']);

        return back()->with('success', "Kurir {$driver->nama} berhasil ditugaskan ke pesanan {$order->order_number}.");
    }
}
