<x-fleet-layout title="Pusat Peringatan">
    <div class="page-header">
        <h2>Pusat Peringatan</h2>
        <p>Semua notifikasi dan peringatan automatik sistem</p>
    </div>

    <!-- Summary stats -->
    <div class="stats-grid" style="margin-bottom:20px">
        <div class="stat-card" style="border-top:3px solid var(--c-danger)">
            <div class="stat-icon" style="background:#ffe8e8">🔴</div>
            <div class="stat-val" style="color:var(--c-danger)">{{ $critical->count() }}</div>
            <div class="stat-label">Kritikal (≤ 7 hari)</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--c-warn)">
            <div class="stat-icon" style="background:#fff3e0">🟠</div>
            <div class="stat-val" style="color:var(--c-warn)">{{ $attention->count() }}</div>
            <div class="stat-label">Perlu Perhatian (8–30 hari)</div>
        </div>
        <div class="stat-card" style="border-top:3px solid #1a4fa0">
            <div class="stat-icon" style="background:#e8f0fb">🔵</div>
            <div class="stat-val" style="color:#1a4fa0">{{ $upcoming->count() }}</div>
            <div class="stat-label">Akan Datang (31–90 hari)</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--c-ok)">
            <div class="stat-icon" style="background:#e8fff6">✅</div>
            <div class="stat-val" style="color:var(--c-ok)">{{ $critical->count() + $attention->count() + $upcoming->count() }}</div>
            <div class="stat-label">Jumlah Peringatan</div>
        </div>
    </div>

    <div class="grid-2">
        <!-- Critical -->
        <div class="card mb20">
            <div class="card-header">
                <span class="card-title" style="color:var(--c-danger)">🔴 Kritikal (≤ 7 hari)</span>
                <span class="badge-pill badge-danger">{{ $critical->count() }}</span>
            </div>
            <div class="card-body">
                @forelse($critical as $r)
                <div class="reminder-item">
                    <div class="reminder-icon" style="background:#ffe8e8">{{ $r['icon'] }}</div>
                    <div class="reminder-text">
                        <div class="title">{{ $r['label'] }} — {{ $r['vehicle']->plat }}</div>
                        <div class="sub">{{ $r['vehicle']->model }} • Luput: {{ $r['date']->format('d M Y') }}</div>
                    </div>
                    <div class="reminder-days" style="color:var(--c-danger)">
                        <div class="days">{{ max(0, $r['days']) }}</div>
                        <div class="lbl">{{ $r['days'] < 0 ? 'LUPUT' : 'hari' }}</div>
                    </div>
                </div>
                @empty
                <p style="color:var(--c-muted);text-align:center;padding:16px">Tiada peringatan kritikal 🎉</p>
                @endforelse
            </div>
        </div>

        <!-- Attention -->
        <div class="card mb20">
            <div class="card-header">
                <span class="card-title" style="color:var(--c-warn)">🟠 Perlu Perhatian (8–30 hari)</span>
                <span class="badge-pill badge-warn">{{ $attention->count() }}</span>
            </div>
            <div class="card-body">
                @forelse($attention as $r)
                <div class="reminder-item">
                    <div class="reminder-icon" style="background:#fff3e0">{{ $r['icon'] }}</div>
                    <div class="reminder-text">
                        <div class="title">{{ $r['label'] }} — {{ $r['vehicle']->plat }}</div>
                        <div class="sub">{{ $r['vehicle']->model }} • Luput: {{ $r['date']->format('d M Y') }}</div>
                    </div>
                    <div class="reminder-days" style="color:var(--c-warn)">
                        <div class="days">{{ $r['days'] }}</div>
                        <div class="lbl">hari</div>
                    </div>
                </div>
                @empty
                <p style="color:var(--c-muted);text-align:center;padding:16px">Tiada peringatan</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Upcoming -->
    @if($upcoming->count())
    <div class="card mb20">
        <div class="card-header">
            <span class="card-title" style="color:#1a4fa0">🔵 Akan Datang (31–90 hari)</span>
            <span class="badge-pill badge-info">{{ $upcoming->count() }}</span>
        </div>
        <div class="card-body">
            @foreach($upcoming as $r)
            <div class="reminder-item">
                <div class="reminder-icon" style="background:#e8f0fb">{{ $r['icon'] }}</div>
                <div class="reminder-text">
                    <div class="title">{{ $r['label'] }} — {{ $r['vehicle']->plat }}</div>
                    <div class="sub">{{ $r['vehicle']->model }} • Luput: {{ $r['date']->format('d M Y') }}</div>
                </div>
                <div class="reminder-days" style="color:#1a4fa0">
                    <div class="days">{{ $r['days'] }}</div>
                    <div class="lbl">hari</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Overdue Services -->
    @if($overdueServices->count())
    <div class="card mb20">
        <div class="card-header">
            <span class="card-title" style="color:var(--c-danger)">⚠️ Servis Tertangguh</span>
        </div>
        <div class="card-body">
            @foreach($overdueServices as $s)
            <div class="reminder-item">
                <div class="reminder-icon" style="background:#ffe8e8">🔧</div>
                <div class="reminder-text">
                    <div class="title">{{ $s->service_type }} — {{ $s->vehicle->plat }}</div>
                    <div class="sub">{{ $s->vehicle->model }} • Dijadual: {{ $s->date->format('d M Y') }} • {{ $s->workshop ?? 'Bengkel belum ditentukan' }}</div>
                </div>
                <div class="reminder-days" style="color:var(--c-danger)">
                    <div class="days">{{ (int) $s->date->diffInDays(now()) }}</div>
                    <div class="lbl">hari lewat</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Reminder Settings -->
    <div class="card">
        <div class="card-header"><span class="card-title">⚙️ Tetapan Peringatan Automatik</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                @foreach([
                    ['title' => 'ROAD TAX', 'items' => ['30 hari sebelum luput', '14 hari sebelum luput', '7 hari sebelum luput'], 'notify' => 'Admin + Fleet + Staff'],
                    ['title' => 'INSURAN', 'items' => ['60 hari sebelum luput', '30 hari sebelum luput', '14 hari sebelum luput'], 'notify' => 'Admin + Fleet'],
                    ['title' => 'SERVIS BERKALA', 'items' => ['5,000 km sebelum jadual', '14 hari sebelum jadual'], 'notify' => 'Fleet + Penjaga'],
                    ['title' => 'PUSPAKOM', 'items' => ['45 hari sebelum luput', '14 hari sebelum luput'], 'notify' => 'Admin + Fleet'],
                ] as $cfg)
                <div>
                    <div style="font-size:12px;color:var(--c-muted);font-weight:600;margin-bottom:8px">{{ $cfg['title'] }}</div>
                    @foreach($cfg['items'] as $i => $item)
                    <div class="detail-row"><div class="detail-label">Peringatan {{ $i + 1 }}</div><div class="detail-val">{{ $item }}</div></div>
                    @endforeach
                    <div class="detail-row"><div class="detail-label">Notifikasi</div><div class="detail-val">{{ $cfg['notify'] }}</div></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-fleet-layout>
