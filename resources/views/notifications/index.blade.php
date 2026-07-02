<x-fleet-layout title="Notifikasi">
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
        <div>
            <h1>Notifikasi</h1>
            <p>{{ $unreadCount }} belum dibaca</p>
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf @method('PUT')
            <button type="submit" class="btn btn-sm btn-secondary"><x-icon name="check" :size="14" /> Tandakan Semua Dibaca</button>
        </form>
        @endif
    </div>

    <div class="mb20">
        @forelse($notifications as $n)
            @php $data = $n->data; @endphp
            <div class="card" style="margin-bottom:8px;{{ $n->read_at ? 'opacity:0.6' : 'border-left:3px solid var(--accent)' }}">
                <div class="card-body" style="padding:12px 16px;display:flex;align-items:center;gap:12px">
                    <div class="{{ $n->read_at ? '' : 'soft-accent' }}" style="width:36px;height:36px;border-radius:10px;{{ $n->read_at ? 'background:var(--bg);color:var(--muted)' : '' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <x-icon :name="($data['type'] ?? '') === 'roadtax' ? 'file-text' : (($data['type'] ?? '') === 'insuran' ? 'shield' : 'clipboard-check')" :size="17" />
                    </div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:600">{{ $data['title'] ?? 'Notifikasi' }}</div>
                        <div style="font-size:12px;color:var(--muted)">{{ $data['message'] ?? '' }}</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                    @if(!$n->read_at)
                    <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-sm btn-secondary" style="font-size:11px">Dibaca</button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:40px;color:var(--muted)">
                <div style="margin-bottom:12px;opacity:0.3;display:flex;justify-content:center"><x-icon name="bell" :size="48" :stroke="1.3" /></div>
                <p>Tiada notifikasi</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div style="display:flex;justify-content:center;gap:8px">
        @if($notifications->previousPageUrl())
            <a href="{{ $notifications->previousPageUrl() }}" class="btn btn-sm btn-secondary">← Sebelum</a>
        @endif
        <span style="padding:5px 10px;font-size:12px;color:var(--c-muted)">Halaman {{ $notifications->currentPage() }} / {{ $notifications->lastPage() }}</span>
        @if($notifications->nextPageUrl())
            <a href="{{ $notifications->nextPageUrl() }}" class="btn btn-sm btn-secondary">Seterusnya →</a>
        @endif
    </div>
    @endif
</x-fleet-layout>
