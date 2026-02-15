<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderByDesc('created_at')->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.form', [
            'banner' => null,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBanner($request, true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner = Banner::create($validated);

        ActivityLog::log('created', "Banner \"{$banner->title}\" ditambahkan", 'Banner', $banner->id);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', [
            'banner' => $banner,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $this->validateBanner($request, false);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);

        ActivityLog::log('updated', "Banner \"{$banner->title}\" diperbarui", 'Banner', $banner->id);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        ActivityLog::log('deleted', "Banner \"{$banner->title}\" dihapus", 'Banner', $banner->id);

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }

    private function validateBanner(Request $request, bool $isCreate): array
    {
        $rules = [
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string|max:1000',
            'image'      => ($isCreate ? 'required|' : 'nullable|') . 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'link_url'   => 'nullable|url|max:500',
            'position'   => 'required|in:hero,catalog,sidebar',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ];

        $validated = $request->validate($rules);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }
}
