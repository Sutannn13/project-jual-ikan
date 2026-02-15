@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
<div>
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-white">Pusat Notifikasi</h2>
        <p class="text-sm text-white/50">Semua notifikasi & alert sistem</p>
    </div>
    <div class="flex items-center gap-2">
        @if($typeCounts['unread'] > 0)
            <form action="{{ route('admin.notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-cyan-500/15 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500/25 transition-colors">
                    <i class="fas fa-check-double mr-1.5"></i>Tandai Semua Dibaca
                </button>
            </form>
        @endif
        <form action="{{ route('admin.notifications.clearRead') }}" method="POST"
              onsubmit="event.preventDefault(); adminConfirm(this, 'Bersihkan Notifikasi', 'Hapus semua notifikasi yang sudah dibaca?', 'danger', 'Ya, Hapus');">
            @csrf
            <button type="submit" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-white/5 text-white/50 border border-white/10 hover:bg-white/10 hover:text-red-400 hover:border-red-500/30 transition-all flex items-center group">
                <i class="fas fa-broom mr-1.5 group-hover:rotate-12 transition-transform"></i>Bersihkan
            </button>
        </form>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="dark-glass-card rounded-xl p-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-cyan-500/15 flex items-center justify-center">
                <i class="fas fa-bell text-cyan-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider">Total</p>
                <p class="text-lg font-bold text-white">{{ $typeCounts['all'] }}</p>
            </div>
        </div>
    </div>
    <div class="dark-glass-card rounded-xl p-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-amber-500/15 flex items-center justify-center">
                <i class="fas fa-envelope text-amber-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider">Belum Dibaca</p>
                <p class="text-lg font-bold text-amber-400">{{ $typeCounts['unread'] }}</p>
            </div>
        </div>
    </div>
    <div class="dark-glass-card rounded-xl p-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-red-500/15 flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-red-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider">Verifikasi</p>
                <p class="text-lg font-bold text-red-400">{{ $typeCounts['payment_uploaded'] }}</p>
            </div>
        </div>
    </div>
    <div class="dark-glass-card rounded-xl p-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-orange-500/15 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-orange-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider">Stok Menipis</p>
                <p class="text-lg font-bold text-orange-400">{{ $typeCounts['low_stock'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-4">
    @php
        $filters = [
            ['label' => 'Semua', 'params' => [], 'active' => !request()->filled('type') && !request()->filled('filter')],
            ['label' => 'Belum Dibaca', 'params' => ['filter' => 'unread'], 'active' => request('filter') === 'unread' && !request()->filled('type')],
            ['label' => 'Pesanan Baru', 'params' => ['type' => 'new_order'], 'active' => request('type') === 'new_order'],
            ['label' => 'Pembayaran', 'params' => ['type' => 'payment_uploaded'], 'active' => request('type') === 'payment_uploaded'],
            ['label' => 'Chat', 'params' => ['type' => 'new_chat'], 'active' => request('type') === 'new_chat'],
            ['label' => 'Stok', 'params' => ['type' => 'low_stock'], 'active' => request('type') === 'low_stock'],
        ];
    @endphp
    @foreach($filters as $f)
        <a href="{{ route('admin.notifications.index', $f['params']) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                  {{ $f['active'] ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' : 'bg-white/5 text-white/50 border border-white/10 hover:bg-white/10' }}">
            {{ $f['label'] }}
        </a>
    @endforeach
</div>

{{-- Notifications List --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    <div class="divide-y divide-white/5">
        @forelse($notifications as $notif)
            <div class="flex items-start gap-3 sm:gap-4 p-4 hover:bg-white/5 transition-colors {{ !$notif->is_read ? 'bg-white/[0.03] border-l-2 border-l-cyan-400' : '' }}">
                {{-- Icon --}}
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                    {{ match($notif->resolved_color) {
                        'red' => 'bg-red-500/15',
                        'orange' => 'bg-orange-500/15',
                        'cyan' => 'bg-cyan-500/15',
                        'green' => 'bg-green-500/15',
                        'yellow' => 'bg-yellow-500/15',
                        default => 'bg-white/10',
                    } }}">
                    <i class="{{ $notif->resolved_icon }} text-sm
                        {{ match($notif->resolved_color) {
                            'red' => 'text-red-400',
                            'orange' => 'text-orange-400',
                            'cyan' => 'text-cyan-400',
                            'green' => 'text-green-400',
                            'yellow' => 'text-yellow-400',
                            default => 'text-white/60',
                        } }}"></i>
                </div>
                
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold text-sm text-white {{ !$notif->is_read ? '' : 'text-white/70' }}">{{ $notif->title }}</p>
                        @if($notif->priority === 'urgent')
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse">URGENT</span>
                        @elseif($notif->priority === 'high')
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30">HIGH</span>
                        @endif
                        @if(!$notif->is_read)
                            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        @endif
                    </div>
                    <p class="text-xs text-white/50 mt-1">{{ $notif->message }}</p>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        @if($notif->action_url)
                            <a href="{{ route('admin.notifications.read', $notif->id) }}" 
                               class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold">
                                {{ $notif->action_text }} <i class="fas fa-arrow-right ml-0.5"></i>
                            </a>
                        @endif
                        <span class="text-[10px] text-white/30">
                            <i class="far fa-clock mr-0.5"></i>{{ $notif->created_at->diffForHumans() }}
                        </span>
                        @if($notif->is_read)
                            <span class="text-[10px] text-white/20">
                                <i class="fas fa-check mr-0.5"></i>Dibaca {{ $notif->read_at?->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="flex items-center gap-1 flex-shrink-0">
                    @if(!$notif->is_read)
                        <form action="{{ route('admin.notifications.read', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-white/30 hover:text-cyan-400 hover:bg-white/10 transition-colors" title="Tandai dibaca">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.notifications.destroy', $notif->id) }}" method="POST"
                          onsubmit="event.preventDefault(); adminConfirm(this, 'Hapus Notifikasi', 'Hapus notifikasi ini?', 'danger', 'Ya, Hapus');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-white/30 hover:text-red-400 hover:bg-white/10 transition-colors" title="Hapus">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-white/30">
                <i class="fas fa-bell-slash text-4xl mb-3"></i>
                <p class="text-sm">Tidak ada notifikasi.</p>
            </div>
        @endforelse
    </div>
    
    @if($notifications->hasPages())
        <div class="px-4 py-3 border-t border-white/5">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

</div> {{-- End wrapper --}}
@endsection
