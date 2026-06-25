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
            @if($servicesInProgress->count())
                <div class="stat-sub orange">● {{ $servicesInProgress->first()->vehicle->plat }}</div>
            @endif
        </div>
        <a href="{{ route('saman.index') }}" class="stat-card" style="cursor:pointer;text-decoration:none">
            <div class="stat-icon" style="background:#ffe8e8">🚨</div>
            <div class="stat-val" style="color:var(--c-danger)">{{ $unpaidSaman }}</div>
            <div class="stat-label">Saman Belum Bayar</div>
            <div class="stat-sub red">● RM {{ number_format($unpaidSamanTotal) }} tertunggak</div>
        </a>
    </div>

    <!-- Urgent Alerts -->
    @if($urgentVehicles->count())
        <div class="alert alert-danger">
            <span>🔴</span>
            <div>
                <strong>Urgent:</strong>
                @foreach($urgentVehicles as $v)
                    @if($v->roadtax_days <= 7)Road tax {{ $v->plat }}@endif
                    @if($v->insurance_days <= 7){{ $v->roadtax_days <= 7 ? ' dan ' : '' }}Insuran {{ $v->plat }}@endif
                    @if(!$loop->last), @endif
                @endforeach
                akan luput dalam <strong>7 hari</strong>. Sila ambil tindakan segera.
            </div>
        </div>
    @endif

    @if($unpaidSaman > 0)
        <a href="{{ route('saman.index') }}" class="alert alert-warn" style="cursor:pointer;text-decoration:none;display:flex">
            <span>🚨</span>
            <div>
                <strong>{{ $unpaidSaman }} saman belum dijelaskan</strong> —
                @foreach($samanList->take(3) as $s)
                    {{ $s->vehicle->plat }} (RM {{ number_format($s->amount) }}){{ !$loop->last ? ', ' : '' }}
                @endforeach
                . Jumlah tertunggak: <strong>RM {{ number_format($unpaidSamanTotal) }}</strong>.
                <span style="text-decoration:underline">Lihat semua saman →</span>
            </div>
        </a>
    @endif

    @if($pendingApprovals > 0)
        <a href="{{ route('approvals.index') }}" class="alert alert-info" style="cursor:pointer;text-decoration:none;display:flex">
            <span>📋</span>
            <div><strong>{{ $pendingApprovals }} permohonan kenderaan</strong> menunggu kelulusan. <span style="text-decoration:underline">Lihat permohonan →</span></div>
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
                        $isRt = $v->roadtax_days <= $v->insurance_days;
                        $minDays = min($v->roadtax_days, $v->insurance_days);
                        $type = $isRt ? 'Road Tax' : 'Insuran';
                        $typeIcon = $isRt ? '📄' : '🛡️';
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
                @foreach($vehicles->take(5) as $v)
                    @php
                        $latestMove = $v->movementLogs()->latest('checkout_time')->first();
                        $isOut = $latestMove && $latestMove->status === 'di_luar';
                    @endphp
                    <div class="location-row">
                        <div class="loc-icon" style="background:{{ $v->status === 'servis' ? '#ffe8e8' : '#e8f0fb' }}">{{ $v->emoji }}</div>
                        <div class="loc-info">
                            <div class="loc-main">{{ $v->plat }} — {{ $v->model }}</div>
                            <div class="sub">{{ $latestMove ? 'Pemandu: ' . ($latestMove->driver?->name ?? '—') : $v->department }}</div>
                        </div>
                        <div class="loc-status">
                            @if($v->status === 'servis')
                                <span class="badge-pill badge-warn">Dalam Servis</span>
                            @elseif($isOut)
                                <span class="badge-pill badge-ok">Dalam Perjalanan</span>
                                <div class="km">{{ $latestMove->destination }}</div>
                            @else
                                <span class="badge-pill badge-neutral">Di Pejabat</span>
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
            <span class="card-title">⛽ Rekod Bahan Api — {{ now()->translatedFormat('M Y') }}</span>
        </div>
        <div class="card-body">
            <div style="display:flex;gap:40px;margin-bottom:16px;flex-wrap:wrap">
                <div><span style="font-size:22px;font-weight:700">RM {{ number_format($fuelTotal, 0) }}</span><span style="color:var(--c-muted);font-size:12px;margin-left:6px">Jumlah {{ now()->translatedFormat('M') }}</span></div>
                <div><span style="font-size:22px;font-weight:700">{{ number_format($fuelLiters, 0) }}L</span><span style="color:var(--c-muted);font-size:12px;margin-left:6px">Jumlah liter</span></div>
                <div><span style="font-size:22px;font-weight:700">{{ $fuelAvg ? number_format($fuelAvg, 1) : '—' }}</span><span style="color:var(--c-muted);font-size:12px;margin-left:6px">L/100km avg</span></div>
            </div>
        </div>
    </div>
</x-fleet-layout>
