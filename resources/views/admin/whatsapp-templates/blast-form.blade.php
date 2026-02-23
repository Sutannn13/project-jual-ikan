@extends('layouts.admin')

@section('title', 'Blast WhatsApp')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.whatsapp-templates.index') }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-all">
            <i class="fas fa-arrow-left text-white/70"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Blast WhatsApp</h1>
            <p class="text-white/50 text-sm mt-0.5">Template: {{ $template->nama }}</p>
        </div>
    </div>

    {{-- Template Preview --}}
    <div class="dark-glass-card rounded-xl p-4 mb-5 border border-emerald-500/20">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);">
                <i class="fab fa-whatsapp text-white text-sm"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs font-semibold text-emerald-400 mb-2">Preview Pesan:</p>
                <p class="text-sm text-white/80 whitespace-pre-line leading-relaxed">{{ Str::limit($template->pesan, 300) }}</p>
            </div>
        </div>
    </div>

    <div class="dark-glass-card rounded-2xl p-6">
        <form action="{{ route('admin.whatsapp-templates.blast', $template) }}" method="POST" class="space-y-5">
            @csrf

            {{-- Target --}}
            <div>
                <label class="label-field">Target Penerima</label>
                <div class="space-y-2 mt-2">
                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all hover:bg-white/5
                                  has-[:checked]:border-cyan-500/50"
                           style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <input type="radio" name="target" value="all" {{ old('target', 'all') === 'all' ? 'checked' : '' }}
                               class="text-cyan-400">
                        <div>
                            <p class="text-sm font-semibold text-white">Semua Pelanggan</p>
                            <p class="text-xs text-white/40">Kirim ke semua user yang punya nomor HP</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all hover:bg-white/5"
                           style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <input type="radio" name="target" value="with_orders" {{ old('target') === 'with_orders' ? 'checked' : '' }}
                               class="text-cyan-400">
                        <div>
                            <p class="text-sm font-semibold text-white">Pelanggan yang Pernah Pesan</p>
                            <p class="text-xs text-white/40">Hanya user yang punya minimal 1 pesanan</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all hover:bg-white/5"
                           style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <input type="radio" name="target" value="custom" {{ old('target') === 'custom' ? 'checked' : '' }}
                               class="text-cyan-400" onclick="document.getElementById('customPhones').classList.remove('hidden')">
                        <div>
                            <p class="text-sm font-semibold text-white">Nomor Custom</p>
                            <p class="text-xs text-white/40">Masukkan nomor HP secara manual</p>
                        </div>
                    </label>
                </div>
                @error('target') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Custom Phones --}}
            <div id="customPhones" class="{{ old('target') === 'custom' ? '' : 'hidden' }}">
                <label class="label-field">Nomor HP (pisahkan dengan koma atau baris baru)</label>
                <textarea name="custom_phones" rows="3" class="input-field text-sm"
                          placeholder="08123456789, 08987654321&#10;atau satu per baris">{{ old('custom_phones') }}</textarea>
                @error('custom_phones') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Variable Substitution --}}
            <div>
                <h3 class="text-sm font-bold text-white mb-3">Nilai Variabel (opsional)</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-white/50 font-semibold">{produk}</label>
                        <input type="text" name="var_produk" value="{{ old('var_produk') }}" 
                               class="input-field mt-1" placeholder="Ikan Nila Segar">
                    </div>
                    <div>
                        <label class="text-xs text-white/50 font-semibold">{harga}</label>
                        <input type="text" name="var_harga" value="{{ old('var_harga') }}" 
                               class="input-field mt-1" placeholder="Rp 35.000/Kg">
                    </div>
                    <div>
                        <label class="text-xs text-white/50 font-semibold">{stok}</label>
                        <input type="text" name="var_stok" value="{{ old('var_stok') }}" 
                               class="input-field mt-1" placeholder="50 Kg">
                    </div>
                </div>
                <p class="text-xs text-white/30 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    {nama} otomatis diisi nama pelanggan, {toko} dan {tanggal} otomatis diisi sistem.
                </p>
            </div>

            <div class="p-4 rounded-xl" style="background: rgba(251,191,36,0.06); border: 1px solid rgba(251,191,36,0.15);">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5 text-sm"></i>
                    <p class="text-xs text-amber-300/80">
                        Blast akan menghasilkan daftar link wa.me yang perlu Anda buka satu per satu. 
                        Ini bukan otomatis mengirim — Anda tetap perlu klik "Kirim" di WhatsApp.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                <button type="submit" 
                        class="flex items-center gap-2 px-8 py-3 rounded-xl text-sm font-bold text-white transition-all"
                        style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); box-shadow: 0 4px 12px rgba(37,211,102,0.3);">
                    <i class="fab fa-whatsapp text-base"></i> Generate Link Blast
                </button>
                <a href="{{ route('admin.whatsapp-templates.index') }}" class="px-6 py-3 rounded-xl text-sm text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="target"]').forEach(el => {
    el.addEventListener('change', () => {
        const customDiv = document.getElementById('customPhones');
        customDiv.classList.toggle('hidden', el.value !== 'custom');
    });
});
</script>
@endpush
@endsection
