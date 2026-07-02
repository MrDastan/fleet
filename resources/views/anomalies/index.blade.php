@php
    $sevMeta = [
        'critical' => ['accent' => 'var(--danger-text)', 'soft' => 'soft-danger', 'badge' => 'badge-danger', 'label' => 'Keutamaan Tinggi', 'conf' => 92, 'icon' => 'triangle-alert'],
        'warning' => ['accent' => 'var(--warn-text)', 'soft' => 'soft-warn', 'badge' => 'badge-warn', 'label' => 'Sederhana', 'conf' => 76, 'icon' => 'triangle-alert'],
        'info' => ['accent' => 'var(--info)', 'soft' => 'soft-info', 'badge' => 'badge-info', 'label' => 'Info', 'conf' => 58, 'icon' => 'eye'],
    ];
    $ruleIcons = [
        'FUEL_HIGH' => 'fuel', 'MILEAGE_JUMP' => 'route', 'FREQ_REFUEL' => 'fuel',
        'SERVICE_OVERDUE' => 'wrench', 'NO_APPROVAL' => 'clipboard-check',
        'AFTER_HOURS' => 'clock', 'WEEKEND_USE' => 'calendar',
    ];
@endphp
<x-fleet-layout title="Pengesanan Anomali">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px">
        <div style="width:40px;height:40px;border-radius:11px;background:var(--sidebar);color:var(--accent-light);display:flex;align-items:center;justify-content:center"><x-icon name="sparkles" :size="20" /></div>
        <div>
            <h1 style="margin:0">Pengesanan Anomali AI</h1>
            <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Model mempelajari corak biasa setiap kenderaan & memberi amaran anomali</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;margin:20px 0">
        <a href="{{ route('anomalies.index', ['severity' => 'critical']) }}" class="stat-card" style="text-decoration:none">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:9px;color:var(--danger-text)"><x-icon name="triangle-alert" :size="18" :stroke="1.9" /><span style="font-size:11.5px;color:var(--muted);font-weight:500">Kritikal</span></div>
            <div class="stat-val">{{ $counts['critical'] }}</div>
        </a>
        <a href="{{ route('anomalies.index', ['severity' => 'warning']) }}" class="stat-card" style="text-decoration:none">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:9px;color:var(--warn-text)"><x-icon name="triangle-alert" :size="18" :stroke="1.9" /><span style="font-size:11.5px;color:var(--muted);font-weight:500">Amaran</span></div>
            <div class="stat-val">{{ $counts['warning'] }}</div>
        </a>
        <a href="{{ route('anomalies.index', ['severity' => 'info']) }}" class="stat-card" style="text-decoration:none">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:9px;color:var(--accent-dark)"><x-icon name="activity" :size="18" :stroke="1.9" /><span style="font-size:11.5px;color:var(--muted);font-weight:500">Diperhatikan</span></div>
            <div class="stat-val">{{ $counts['info'] }}</div>
        </a>
        <a href="{{ route('anomalies.index', ['severity' => 'resolved']) }}" class="stat-card" style="text-decoration:none">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:9px;color:var(--ok)"><x-icon name="shield" :size="18" :stroke="1.9" /><span style="font-size:11.5px;color:var(--muted);font-weight:500">Diselesaikan</span></div>
            <div class="stat-val">{{ $counts['resolved'] }}</div>
        </a>
    </div>

    <!-- Engine status -->
    <div style="display:flex;align-items:center;justify-content:space-between;background:#E3F2EA;border:1px solid #B9DFC9;border-radius:12px;padding:12px 18px;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:10px;height:10px;border-radius:50%;background:var(--ok);box-shadow:0 0 0 3px rgba(19,112,73,.18)"></div>
            <div>
                <div style="font-size:13px;font-weight:600;color:var(--ok)">Enjin Anomali Aktif</div>
                <div style="font-size:11px;color:var(--muted)">{{ count($rules) }} peraturan aktif</div>
            </div>
        </div>
        <form method="POST" action="{{ route('anomalies.scan') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-secondary">Imbas Sekarang</button>
        </form>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="{{ route('anomalies.index') }}" class="tab {{ !request('severity') ? 'active' : '' }}">Semua</a>
        <a href="{{ route('anomalies.index', ['severity' => 'critical']) }}" class="tab {{ request('severity') === 'critical' ? 'active' : '' }}">Kritikal</a>
        <a href="{{ route('anomalies.index', ['severity' => 'warning']) }}" class="tab {{ request('severity') === 'warning' ? 'active' : '' }}">Amaran</a>
        <a href="{{ route('anomalies.index', ['severity' => 'info']) }}" class="tab {{ request('severity') === 'info' ? 'active' : '' }}">Diperhatikan</a>
        <a href="{{ route('anomalies.index', ['severity' => 'resolved']) }}" class="tab {{ request('severity') === 'resolved' ? 'active' : '' }}">Selesai</a>
    </div>

    <!-- Anomaly List -->
    <div class="mb20">
        @forelse($records as $a)
            @php
                $isResolved = $a->status === 'resolved';
                $m = $sevMeta[$a->severity] ?? $sevMeta['info'];
                $accent = $isResolved ? 'var(--ok)' : $m['accent'];
                $soft = $isResolved ? 'soft-ok' : $m['soft'];
                $icon = $isResolved ? 'shield' : ($ruleIcons[$a->rule_code] ?? $m['icon']);
            @endphp
            <div class="anomaly-card" style="border-left:3px solid {{ $accent }};{{ $isResolved ? 'opacity:0.65' : '' }}">
                <div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap">
                    <div class="icon-box {{ $soft }}"><x-icon :name="$icon" :size="20" :stroke="1.9" /></div>
                    <div style="flex:1;min-width:200px">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                            <span style="font-family:var(--font-display);font-size:15.5px;font-weight:600">{{ $a->title }}</span>
                            @if($isResolved)
                                <span class="badge-pill badge-ok">Selesai</span>
                            @else
                                <span class="badge-pill {{ $m['badge'] }}">{{ $m['label'] }}</span>
                            @endif
                            <span style="font-size:11px;color:var(--muted)">{{ $a->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="anomaly-desc">{{ $a->description }}</p>
                        @if($a->resolution_notes)
                        <div style="font-size:12px;color:var(--ok);margin-top:6px;padding:6px 10px;background:#E3F2EA;border-radius:8px">
                            {{ $a->resolution_notes }} — {{ $a->resolved_at?->format('d M Y, H:i') }}
                        </div>
                        @endif
                        <div class="anomaly-meta">
                            @if($a->vehicle)
                            <span class="anomaly-chip">{{ $a->vehicle->plat }}</span>
                            @endif
                            <span class="anomaly-time"><x-icon name="clock" :size="13" />{{ $a->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div style="width:130px;flex-shrink:0">
                        <div class="anomaly-conf-label">Keyakinan AI</div>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div class="anomaly-conf-bar"><div class="anomaly-conf-fill" style="width:{{ $m['conf'] }}%;background:{{ $accent }}"></div></div>
                            <span class="anomaly-conf-val" style="color:{{ $accent }}">{{ $m['conf'] }}%</span>
                        </div>
                        @if(!$isResolved)
                        <div style="display:flex;gap:8px;margin-top:14px">
                            @if($a->status !== 'investigating')
                            <form method="POST" action="{{ route('anomalies.investigate', $a) }}">@csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-dark">Siasat</button>
                            </form>
                            @endif
                            <button class="btn btn-sm btn-secondary" onclick="openResolve({{ $a->id }}, '{{ addslashes($a->title) }}')">Selesai</button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:40px;color:var(--muted)">
                <div style="margin-bottom:12px;opacity:0.3;display:flex;justify-content:center"><x-icon name="sparkles" :size="48" :stroke="1.3" /></div>
                <p>Tiada anomali dikesan {{ request('severity') ? 'untuk kategori ini' : '' }}</p>
                <form method="POST" action="{{ route('anomalies.scan') }}" style="margin-top:12px">@csrf
                    <button type="submit" class="btn btn-primary">Imbas Sekarang</button>
                </form>
            </div>
        @endforelse
    </div>

    <!-- Rules Config -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><span class="icon-accent"><x-icon name="settings" :size="17" /></span>Peraturan Pengesanan Anomali</span>
            <span class="badge-pill badge-info">{{ count($rules) }} peraturan aktif</span>
        </div>
        <div class="card-body">
            <table class="fleet-table">
                <thead><tr><th></th><th>Peraturan</th><th>Penerangan</th><th>Tahap</th><th>Ambang</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($rules as $r)
                    <tr>
                        <td style="color:var(--ink-2)"><x-icon :name="$ruleIcons[$r['code']] ?? 'activity'" :size="18" /></td>
                        <td><strong>{{ $r['name'] }}</strong><div style="font-size:11px;color:var(--muted);font-family:var(--font-mono)">{{ $r['code'] }}</div></td>
                        <td style="font-size:12px;color:var(--ink-2)">{{ $r['description'] }}</td>
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
                <div class="modal-title" id="resolveTitle">Selesaikan Anomali</div>
                <div class="modal-close" onclick="closeModal('resolveModal')">✕</div>
            </div>
            <form method="POST" id="resolveForm">
                @csrf @method('PUT')
                <div class="form-group"><label class="form-label">Catatan Penyelesaian</label>
                    <textarea name="resolution_notes" class="form-control" rows="3" placeholder="Terangkan tindakan yang diambil..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('resolveModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Tandakan Selesai</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openResolve(id, title) {
        document.getElementById('resolveTitle').textContent = title;
        document.getElementById('resolveForm').action = '/anomalies/' + id + '/resolve';
        document.getElementById('resolveModal').classList.add('open');
    }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>
