<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('store.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('store.addresses.form', [
            'address' => null,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAddress($request);
        $validated['user_id'] = Auth::id();

        $address = UserAddress::create($validated);

        // Jika ini alamat pertama atau dipilih sebagai default
        if ($request->boolean('is_default') || Auth::user()->addresses()->count() === 1) {
            $address->setAsDefault();
        }

        return redirect()->route('user.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan!');
    }

    public function edit(UserAddress $address)
    {
        $this->authorizeAddress($address);

        return view('store.addresses.form', [
            'address' => $address,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, UserAddress $address)
    {
        $this->authorizeAddress($address);

        $validated = $this->validateAddress($request);
        $address->update($validated);

        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }

        return redirect()->route('user.addresses.index')
            ->with('success', 'Alamat berhasil diperbarui!');
    }

    public function destroy(UserAddress $address)
    {
        $this->authorizeAddress($address);

        if ($address->is_default && Auth::user()->addresses()->count() > 1) {
            // Set another address as default
            $newDefault = Auth::user()->addresses()->where('id', '!=', $address->id)->first();
            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }

        $address->delete();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Set alamat sebagai default
     */
    public function setDefault(UserAddress $address)
    {
        $this->authorizeAddress($address);
        $address->setAsDefault();

        return back()->with('success', "Alamat \"{$address->label}\" dijadikan alamat utama.");
    }

    private function authorizeAddress(UserAddress $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label'          => 'required|string|max:50',
            'penerima'       => 'required|string|max:255',
            'telepon'        => 'required|string|max:20',
            'alamat_lengkap' => 'required|string|max:1000',
            'kecamatan'      => 'nullable|string|max:255',
            'kota'           => 'nullable|string|max:255',
            'provinsi'       => 'nullable|string|max:255',
            'kode_pos'       => 'nullable|string|max:10',
            'catatan'        => 'nullable|string|max:500',
        ], [
            'penerima.required'       => 'Nama penerima wajib diisi.',
            'telepon.required'        => 'Nomor telepon wajib diisi.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
        ]);
    }
}
