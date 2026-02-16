@extends('layouts.master')
@section('title', 'Buat Tiket Support')
@section('content')
<section class="py-8 sm:py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white text-sm mb-4 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali ke Tiket
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Buat Tiket Support</h1>
            <p class="text-white/60 mt-1">Jelaskan masalah Anda, tim kami akan segera merespons</p>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="store-glass-card rounded-2xl p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Subject --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Subjek <span class="text-red-400">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                       placeholder="Ringkasan masalah Anda">
                @error('subject')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Kategori <span class="text-red-400">*</span></label>
                <select name="category" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                    <option value="" class="bg-gray-800">Pilih kategori</option>
                    <option value="order_issue" class="bg-gray-800" {{ old('category') == 'order_issue' ? 'selected' : '' }}>Masalah Pesanan</option>
                    <option value="payment" class="bg-gray-800" {{ old('category') == 'payment' ? 'selected' : '' }}>Pembayaran</option>
                    <option value="product_quality" class="bg-gray-800" {{ old('category') == 'product_quality' ? 'selected' : '' }}>Kualitas Produk</option>
                    <option value="delivery" class="bg-gray-800" {{ old('category') == 'delivery' ? 'selected' : '' }}>Pengiriman</option>
                    <option value="other" class="bg-gray-800" {{ old('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('category')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Related Order --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Pesanan Terkait <span class="text-white/40 font-normal">(opsional)</span></label>
                <select name="order_id"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                    <option value="" class="bg-gray-800">Tidak terkait pesanan</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" class="bg-gray-800" {{ ($selectedOrderId == $order->id || old('order_id') == $order->id) ? 'selected' : '' }}>
                            Order #{{ $order->id }} — {{ $order->status }} — Rp {{ number_format($order->total, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('order_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Priority --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Prioritas</label>
                <div class="flex gap-3">
                    @foreach(['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi'] as $val => $label)
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="priority" value="{{ $val }}" class="sr-only peer" {{ (old('priority', 'medium') == $val) ? 'checked' : '' }}>
                            <div class="text-center py-2.5 rounded-xl border text-sm font-medium transition-all
                                        peer-checked:bg-cyan-500/20 peer-checked:border-cyan-500/50 peer-checked:text-cyan-300
                                        border-white/20 text-white/60 hover:border-white/30">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Message --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Pesan <span class="text-red-400">*</span></label>
                <textarea name="message" rows="5" required
                          class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition resize-none"
                          placeholder="Jelaskan masalah Anda secara detail...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Attachment --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Lampiran <span class="text-white/40 font-normal">(opsional, max 2MB)</span></label>
                <input type="file" name="attachment" accept="image/*,.pdf"
                       class="w-full text-sm text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-cyan-500/20 file:text-cyan-300 hover:file:bg-cyan-500/30 transition">
                @error('attachment')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit" class="btn-primary w-full py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
