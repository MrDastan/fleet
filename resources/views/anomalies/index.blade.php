<x-fleet-layout title="Pengesanan Anomali">
    <div class="page-header">
        <h2>Pengesanan Anomali</h2>
        <p>Sistem pengesanan automatik tingkah laku luar biasa dalam penggunaan kenderaan</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="margin-bottom:20px">
        <a href="{{ route('anomalies.index', ['severity' => 'critical']) }}" class="stat-card" style="border-top:3px solid var(--c-danger);text-decoration:none">
            <div class="stat-icon" style="background:#ffe8e8">🚨</div>
            <div class="stat-val" style="color:var(--c-danger)">{{ $counts['critical'] }}</div>
            <div class="stat-label">Kritikal</div>
            @if($counts['critical'] > 0)<div class="stat-sub red">Perlu tindakan segera</div>@endif
        </a>
        <a href="{{ route('anomalies.index', ['severity' => 'warning']) }}" class="stat-card" style="border-top:3px solid var(--c-warn);text-decoration:none">
            <div class="stat-icon" style="background:#fff3e0">⚠️</div>
            <div class="stat-val" style="color:var(--c-warn)">{{ $counts['warning'] }}</div>
            <div class="stat-label">Amaran</div>
            @if($counts['warning'] > 0)<div class="stat-sub orange">Perlu disemak</div>@endif
        </a>
        <a href="{{ route('anomalies.index', ['severity' => 'info']) }}" class="stat-card" style="border-top:3px solid #1a4fa0;text-decoration:none">
            <div class="stat-icon" style="background:#e8f0fb">🔍</div>
            <div class="stat-val" style="color:#1a4fa0">{{ $counts['info'] }}</div>
            <div class="stat-label">Diperhatikan</div>
        </a>
        <a href="{{ route('anomalies.index', ['severity' => 'resolved']) }}" class="stat-card" style="border-top:3px solid var(--c-ok);text-decoration:none">
            <div class="stat-icon" style="background:#e8fff6">✅</div>
            <div class="stat-val" style="color:var(--c-ok)">{{ $counts['resolved'] }}</div>
            <div class="stat-label">Diselesaikan</div>
        </a>
    </div>

    <!-- Engine status -->
    <div style="display:flex;align-items:center;justify-content:space-between;background:#e8fff6;border:1px solid #a8dfc8;border-radius:10px;padding:12px 18px;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:10px;height:10px;border-radius:50%;background:var(--c-ok);box-shadow:0 0 0 3px #d4faf0"></div>
            <div>
                <div style="font-size:13px;font-weight:600;color:#007a5e">Enjin Anomali Aktif</div>
                <div style="font-size:11px;color:var(--c-muted)">{{ count($rules) }} peraturan aktif</div>
            </div>
        </div>
        <form method="POST" action="{{ route('anomalies.scan') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-secondary">🔄 Imbas Sekarang</button>
        </form>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="{{ route('anomalies.index') }}" class="tab {{ !request('severity') ? 'active' : '' }}">Semua</a>
        <a href="{{ route('anomalies.index', ['severity' => 'critical']) }}" class="tab {{ request('severity') === 'critical' ? 'active' : '' }}">🚨 Kritikal</a>
        <a href="{{ route('anomalies.index', ['severity' => 'warning']) }}" class="tab {{ request('severity') === 'warning' ? 'active' : '' }}">⚠️ Amaran</a>
        <a href="{{ route('anomalies.index', ['severity' => 'info']) }}" class="tab {{ request('severity') === 'info' ? 'active' : '' }}">🔍 Diperhatikan</a>
        <a href="{{ route('anomalies.index', ['severity' => 'resolved']) }}" class="tab {{ request('severity') === 'resolved' ? 'active' : '' }}">✅ Selesai</a>
    </div>

    <!-- Anomaly List -->
    <div class="mb20">
        @forelse($records as $a)
            @php
                $colors = ['critical' => ['bg' => '#ffe8e8', 'border' => 'var(--c-danger)', 'icon' => '🚨'],
                           'warning' => ['bg' => '#fff3e0', 'border' => 'var(--c-warn)', 'icon' => '⚠️'],
                           'info' => ['bg' => '#e8f0fb', 'border' => '#1a4fa0', 'icon' => '🔍']];
                $c = $colors[$a->severity] ?? $colors['info'];
                $isResolved = $a->status === 'resolved';
            @endphp
            <div class="card" style="margin-bottom:12px;border-left:4px solid {{ $isResolved ? 'var(--c-ok)' : $c['border'] }};{{ $isResolved ? 'opacity:0.65' : '' }}">
                <div class="card-body" style="padding:14px 18px">
                    <div style="display:flex;align-items:flex-start;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:{{ $isResolved ? '#e8fff6' : $c['bg'] }};display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
                            {{ $isResolved ? '✅' : $c['icon'] }}
                        </div>
                        <div style="flex:1">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                                <strong style="font-size:14px">{{ $a->title }}</strong>
                                @if($a->severity === 'critical' && !$isResolved)<span class="badge-pill badge-danger">Kritikal</span>
                                @elseif($a->severity === 'warning' && !$isResolved)<span class="badge-pill badge-warn">Amaran</span>
                                @elseif(!$isResolved)<span class="badge-pill badge-info">Info</span>
                                @else<span class="badge-pill badge-ok">Selesai</span>@endif
                                <span style="font-size:11px;color:var(--c-muted)">{{ $a->created_at->diffForHumans() }}</span>
                            </div>
                            <p style="font-size:13px;color:var(--c-text2);margin:0">{{ $a->description }}</p>
                            @if($a->vehicle)
                            <div style="font-size:11px;color:var(--c-muted);margin-top:4px">🚗 {{ $a->vehicle->plat }} — {{ $a->vehicle->model }}</div>
                            @endif
                            @if($a->resolution_notes)
                            <div style="font-size:12px;color:var(--c-ok);margin-top:6px;padding:6px 10px;background:#e8fff6;border-radius:6px">
                                ✅ {{ $a->resolution_notes }} — {{ $a->resolved_at?->format('d M Y, H:i') }}
                            </div>
                            @endif
                        </div>
                        @if(!$isResolved)
                        <div style="display:flex;gap:6px;flex-shrink:0">
                            @if($a->status !== 'investigating')
                            <form method="POST" action="{{ route('anomalies.investigate', $a) }}">@csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-secondary">🔍 Siasat</button>
                            </form>
                            @endif
                            <button class="btn btn-sm btn-primary" onclick="openResolve({{ $a->id }}, '{{ addslashes($a->title) }}')">✅ Selesai</button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:40px;color:var(--c-muted)">
                <div style="font-size:48px;margin-bottom:12px;opacity:0.3">🧠</div>
                <p>Tiada anomali dikesan {{ request('severity') ? 'untuk kategori ini' : '' }}</p>
                <form method="POST" action="{{ route('anomalies.scan') }}" style="margin-top:12px">@csrf
                    <button type="submit" class="btn btn-primary">🔄 Imbas Sekarang</button>
                </form>
            </div>
        @endforelse
    </div>

    <!-- Rules Config -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚙️ Peraturan Pengesanan Anomali</span>
            <span class="badge-pill badge-info">{{ count($rules) }} peraturan aktif</span>
        </div>
        <div class="card-body">
            <table class="fleet-table">
                <thead><tr><th></th><th>Peraturan</th><th>Penerangan</th><th>Tahap</th><th>Ambang</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($rules as $r)
                    <tr>
                        <td style="font-size:20px">{{ $r['icon'] }}</td>
                        <td><strong>{{ $r['name'] }}</strong><div style="font-size:11px;color:var(--c-muted);font-family:monospace">{{ $r['code'] }}</div></td>
                        <td style="font-size:12px;color:var(--c-text2)">{{ $r['description'] }}</td>
                        <td>
                            @if($r['severity'] === 'critical')<span class="badge-pill badge-danger">Kritikal</span>
                            @elseif($r['severity'] === 'warning')<span class="badge-pill badge-warn">Amaran</span>
                            @else<span class="badge-pill badge-info">Info</span>@endif
                        </td>
                        <td style="font-size:12px;font-weight:600">{{ $r['threshold'] }}</td>
                        <td><span class="badge-pill badge-ok">Aktif</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resolve Modal -->
    <div class="modal-overlay" id="resolveModal">
        <div class="modal" style="max-width:420px">
            <div class="modal-header">
                <div class="modal-title" id="resolveTitle">✅ Selesaikan Anomali</div>
                <div class="modal-close" onclick="closeModal('resolveModal')">✕</div>
            </div>
            <form method="POST" id="resolveForm">
                @csrf @method('PUT')
                <div class="form-group"><label class="form-label">Catatan Penyelesaian</label>
                    <textarea name="resolution_notes" class="form-control" rows="3" placeholder="Terangkan tindakan yang diambil..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('resolveModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">✅ Tandakan Selesai</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openResolve(id, title) {
        document.getElementById('resolveTitle').textContent = '✅ ' + title;
        document.getElementById('resolveForm').action = '/anomalies/' + id + '/resolve';
        document.getElementById('resolveModal').classList.add('open');
    }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
