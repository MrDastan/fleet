<x-fleet-layout title="Dashboard">
    <div class="page-header">
        <h2>Dashboard</h2>
        <p>Gambaran keseluruhan fleet kenderaan syarikat</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fb">🚗</div>
            <div class="stat-val">{{ $totalVehicles }}</div>
            <div class="stat-label">Jumlah Kenderaan</div>
            <div class="stat-sub green">● {{ $activeVehicles }} Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0">⚠️</div>
            <div class="stat-val">{{ $needsAttention }}</div>
            <div class="stat-label">Perlu Perhatian</div>
            <div class="stat-sub red">● {{ $urgentCount }} Urgent</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8fff6">🔧</div>
            <div class="stat-val">{{ $inService }}</div>
            <div class="stat-label">Dalam Servis</div>
            <div class="stat-sub orange">● Dijangka siap 3 hari</div>
        </div>
        <a href="{{ route('saman.index') }}" class="stat-card" style="cursor:pointer">
            <div class="stat-icon" style="background:#ffe8e8">🚨</div>
            <div class="stat-val" style="color:var(--c-danger)">{{ $unpaidSaman }}</div>
            <div class="stat-label">Saman Belum Bayar</div>
            <div class="stat-sub red">● RM {{ number_format($unpaidSamanTotal) }} tertunggak</div>
        </a>
    </div>

    <!-- Urgent Alerts -->
    @if($urgentReminders->count())
        <div class="alert alert-danger">
            <span>🔴</span>
            <div>
                <strong>Urgent:</strong>
                @foreach($urgentReminders->take(2) as $v)
                    @if($v->roadtax_days <= 7) Road tax {{ $v->plat }}@endif
                    @if($v->insurance_days <= 7) @if($v->roadtax_days <= 7) dan @endif Insuran {{ $v->plat }}@endif
                @endforeach
                akan luput dalam <strong>7 hari</strong>. Sila ambil tindakan segera.
            </div>
        </div>
    @endif

    @if($unpaidSaman > 0)
        <a href="{{ route('saman.index') }}" class="alert alert-warn" style="cursor:pointer;text-decoration:none">
            <span>🚨</span>
            <div><strong>{{ $unpaidSaman }} saman belum dijelaskan.</strong> Jumlah tertunggak: <strong>RM {{ number_format($unpaidSamanTotal) }}</strong>. <span style="text-decoration:underline">Lihat semua saman →</span></div>
        </a>
    @endif

    <div class="grid-2">
        <!-- Peringatan Segera -->
        <div class="card mb20">
            <div class="card-header">
                <span class="card-title">🔔 Peringatan Segera</span>
                <a href="{{ route('reminders.index') }}" class="btn btn-sm btn-secondary">Semua</a>
            </div>
            <div class="card-body">
                @foreach($vehicles->sortBy(fn($v) => min($v->roadtax_days, $v->insurance_days))->take(4) as $v)
                    @php
                        $minDays = min($v->roadtax_days, $v->insurance_days);
                        $type = $v->roadtax_days < $v->insurance_days ? 'Road Tax' : 'Insuran';
                        $typeIcon = $v->roadtax_days < $v->insurance_days ? '📄' : '🛡️';
                        $color = $minDays <= 7 ? 'var(--c-danger)' : ($minDays <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
                        $bg = $minDays <= 7 ? '#ffe8e8' : ($minDays <= 30 ? '#fff3e0' : '#e8fff6');
                    @endphp
                    <div class="reminder-item">
                        <div class="reminder-icon" style="background:{{ $bg }}">{{ $typeIcon }}</div>
                        <div class="reminder-text">
                            <div class="title">{{ $type }} — {{ $v->plat }}</div>
                            <div class="sub">{{ $v->model }} • {{ $v->department }}</div>
                        </div>
                        <div class="reminder-days" style="color:{{ $color }}">
                            <div class="days">{{ $minDays }}</div>
                            <div class="lbl">hari lagi</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Status Kenderaan Hari Ini -->
        <div class="card mb20">
            <div class="card-header">
                <span class="card-title">📍 Status Kenderaan Hari Ini</span>
                <span class="badge-pill badge-info">{{ now()->format('d M Y') }}</span>
            </div>
            <div class="card-body">
                @foreach($vehicles->where('status', '!=', 'tidak_aktif')->take(4) as $v)
                    <div class="location-row">
                        <div class="loc-icon" style="background:#e8f0fb">{{ $v->emoji }}</div>
                        <div class="loc-info">
                            <div class="loc-main">{{ $v->plat }} — {{ $v->model }}</div>
                            <div class="sub">{{ $v->department }}</div>
                        </div>
                        <div class="loc-status">
                            @if($v->status === 'servis')
                                <span class="badge-pill badge-warn">Dalam Servis</span>
                            @elseif($v->status === 'aktif')
                                <span class="badge-pill badge-ok">Aktif</span>
                            @else
                                <span class="badge-pill badge-neutral">{{ ucfirst($v->status) }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Fuel Summary -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">⛽ Rekod Bahan Api — {{ now()->format('M Y') }}</span>
        </div>
        <div class="card-body">
            <div style="display:flex;gap:40px;margin-bottom:16px">
                <div><span style="font-size:22px;font-weight:700">RM {{ number_format($fuelTotal) }}</span><span style="color:var(--c-muted);font-size:12px;margin-left:6px">Jumlah {{ now()->format('M') }}</span></div>
                <div><span style="font-size:22px;font-weight:700">{{ number_format($fuelLiters) }}L</span><span style="color:var(--c-muted);font-size:12px;margin-left:6px">Jumlah liter</span></div>
            </div>
        </div>
    </div>
</x-fleet-layout>
