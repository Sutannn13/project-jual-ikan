<?php

namespace App\Http\Controllers;

use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::latest()->get();
        return view('admin.shipping-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.shipping-zones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_name' => 'required|string|max:255',
            'areas' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Convert comma-separated areas to array
        $areasArray = array_map('trim', explode(',', $validated['areas']));

        ShippingZone::create([
            'zone_name' => $validated['zone_name'],
            'areas' => $areasArray,
            'cost' => $validated['cost'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.shipping-zones.index')
            ->with('success', 'Zona pengiriman berhasil ditambahkan!');
    }

    public function edit(ShippingZone $shippingZone)
    {
        return view('admin.shipping-zones.edit', compact('shippingZone'));
    }

    public function update(Request $request, ShippingZone $shippingZone)
    {
        $validated = $request->validate([
            'zone_name' => 'required|string|max:255',
            'areas' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Convert comma-separated areas to array
        $areasArray = array_map('trim', explode(',', $validated['areas']));

        $shippingZone->update([
            'zone_name' => $validated['zone_name'],
            'areas' => $areasArray,
            'cost' => $validated['cost'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.shipping-zones.index')
            ->with('success', 'Zona pengiriman berhasil diupdate!');
    }

    public function destroy(ShippingZone $shippingZone)
    {
        // Prevent deletion if orders reference this shipping zone
        $activeOrderCount = \App\Models\Order::where('shipping_zone_id', $shippingZone->id)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->count();

        if ($activeOrderCount > 0) {
            return redirect()->route('admin.shipping-zones.index')
                ->with('error', "Zona pengiriman tidak bisa dihapus karena masih digunakan oleh {$activeOrderCount} pesanan aktif. Nonaktifkan saja.");
        }

        $shippingZone->delete();
        
        return redirect()->route('admin.shipping-zones.index')
            ->with('success', 'Zona pengiriman berhasil dihapus!');
    }
}
