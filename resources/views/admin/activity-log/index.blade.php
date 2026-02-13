@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-white">Activity Log</h2>
        <p class="text-sm text-white/50">Riwayat semua aktivitas di sistem</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="dark-glass-card rounded-xl p-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-cyan-500/15 flex items-center justify-center">
                <i class="fas fa-clock text-cyan-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider">Hari Ini</p>
                <p class="text-lg font-bold text-white">{{ $stats['today'] }}</p>
            </div>
        </div>
    </div>
    <div class="dark-glass-card rounded-xl p-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-purple-500/15 flex items-center justify-center">
                <i class="fas fa-calendar-week text-purple-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider">Minggu Ini</p>
                <p class="text-lg font-bold text-white">{{ $stats['this_week'] }}</p>
            </div>
        </div>
    </div>
    <div class="dark-glass-card rounded-xl p-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-teal-500/15 flex items-center justify-center">
                <i class="fas fa-database text-teal-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider">Total</p>
                <p class="text-lg font-bold text-white">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="dark-glass-card rounded-xl p-4 mb-4">
    <form action="{{ route('admin.activity-log.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[120px]">
            <label class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1 block">Aksi</label>
            <select name="action" class="w-full px-3 py-2 rounded-lg text-xs bg-white/5 border border-white/10 text-white focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 appearance-none">
                <option value="">Semua Aksi</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[120px]">
            <label class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1 block">Model</label>
            <select name="model" class="w-full px-3 py-2 rounded-lg text-xs bg-white/5 border border-white/10 text-white focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 appearance-none">
                <option value="">Semua Model</option>
                @foreach($models as $model)
                    <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>{{ $model }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[120px]">
            <label class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1 block">User</label>
            <select name="user_id" class="w-full px-3 py-2 rounded-lg text-xs bg-white/5 border border-white/10 text-white focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 appearance-none">
                <option value="">Semua User</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }} ({{ $u->role }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[110px]">
            <label class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1 block">Dari</label>
            <input type="date" name="from" value="{{ request('from') }}" 
                   class="w-full px-3 py-2 rounded-lg text-xs bg-white/5 border border-white/10 text-white focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
        </div>
        <div class="min-w-[110px]">
            <label class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1 block">Sampai</label>
            <input type="date" name="to" value="{{ request('to') }}" 
                   class="w-full px-3 py-2 rounded-lg text-xs bg-white/5 border border-white/10 text-white focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold bg-cyan-500/15 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500/25 transition-colors">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            <a href="{{ route('admin.activity-log.index') }}" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 text-white/50 border border-white/10 hover:bg-white/10 transition-colors">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </div>
    </form>
</div>

{{-- Activity Log Timeline --}}
<div class="dark-glass-card rounded-2xl overflow-hidden">
    <div class="divide-y divide-white/5">
        @forelse($logs as $log)
            <div class="flex items-start gap-3 sm:gap-4 p-4 hover:bg-white/5 transition-colors">
                {{-- Action Icon --}}
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-white/5">
                    <i class="{{ $log->action_icon }} text-sm"></i>
                </div>
                
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        {{-- Action Badge --}}
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                            {{ match($log->action_color) {
                                'green' => 'bg-green-500/15 text-green-400',
                                'blue' => 'bg-blue-500/15 text-blue-400',
                                'red' => 'bg-red-500/15 text-red-400',
                                'emerald' => 'bg-emerald-500/15 text-emerald-400',
                                'orange' => 'bg-orange-500/15 text-orange-400',
                                'cyan' => 'bg-cyan-500/15 text-cyan-400',
                                'purple' => 'bg-purple-500/15 text-purple-400',
                                'teal' => 'bg-teal-500/15 text-teal-400',
                                default => 'bg-white/10 text-white/60',
                            } }}">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                        
                        @if($log->model)
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-white/5 text-white/40 border border-white/10">
                                {{ $log->model }}{{ $log->model_id ? " #{$log->model_id}" : '' }}
                            </span>
                        @endif
                    </div>
                    
                    <p class="text-sm text-white/80">{{ $log->description }}</p>
                    
                    {{-- Changes Detail (if any) --}}
                    @if($log->changes)
                        <details class="mt-2">
                            <summary class="text-[11px] text-cyan-400/80 cursor-pointer hover:text-cyan-400">
                                <i class="fas fa-code mr-1"></i>Lihat detail perubahan
                            </summary>
                            <div class="mt-2 p-3 rounded-lg bg-black/20 border border-white/5">
                                @if(isset($log->changes['old']) && isset($log->changes['new']))
                                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                                        <div>
                                            <p class="text-red-400/60 font-semibold mb-1">Sebelum:</p>
                                            @foreach($log->changes['old'] as $key => $val)
                                                <p class="text-white/40"><span class="text-white/60">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}</p>
                                            @endforeach
                                        </div>
                                        <div>
                                            <p class="text-green-400/60 font-semibold mb-1">Sesudah:</p>
                                            @foreach($log->changes['new'] as $key => $val)
                                                <p class="text-white/40"><span class="text-white/60">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <pre class="text-[11px] text-white/40 whitespace-pre-wrap">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </div>
                        </details>
                    @endif
                    
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        {{-- User --}}
                        <span class="text-[11px] text-white/40">
                            <i class="fas fa-user mr-1"></i>
                            {{ $log->user_name }}
                            @if($log->user_role)
                                <span class="px-1 py-0.5 rounded text-[9px] font-bold uppercase ml-1
                                    {{ $log->user_role === 'admin' ? 'bg-cyan-500/15 text-cyan-400' : 'bg-white/10 text-white/50' }}">
                                    {{ $log->user_role }}
                                </span>
                            @endif
                        </span>
                        {{-- Time --}}
                        <span class="text-[10px] text-white/30">
                            <i class="far fa-clock mr-0.5"></i>{{ $log->created_at->format('d M Y, H:i') }} ({{ $log->created_at->diffForHumans() }})
                        </span>
                        {{-- IP --}}
                        @if($log->ip_address)
                            <span class="text-[10px] text-white/20">
                                <i class="fas fa-globe mr-0.5"></i>{{ $log->ip_address }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-white/30">
                <i class="fas fa-history text-4xl mb-3"></i>
                <p class="text-sm">Belum ada aktivitas tercatat.</p>
                <p class="text-xs text-white/20 mt-1">Aktivitas akan mulai tercatat saat admin & customer melakukan aksi.</p>
            </div>
        @endforelse
    </div>
    
    @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-white/5">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
