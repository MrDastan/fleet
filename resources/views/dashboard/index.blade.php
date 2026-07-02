@php
    $dc = fn($days) => $days <= 7 ? 'var(--danger-text)' : ($days <= 30 ? 'var(--warn-text)' : 'var(--ok)');
    $hour = now()->hour;
    $greetWord = $hour < 12 ? 'Selamat pagi' : ($hour < 19 ? 'Selamat petang' : 'Selamat malam');
    $firstName = explode(' ', auth()->user()->name)[0];
@endphp
<x-fleet-layout title="Dashboard">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:22px">
        <div>
            <div style="font-size:12.5px;color:var(--muted);font-weight:500;margin-bottom:5px">{{ now()->translatedFormat('l, j F Y') }}</div>
            <h1 style="margin:0">{{ $greetWord }}, {{ $firstName }}</h1>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary"><x-icon name="file-text" :size="16" /> Laporan</a>
            <a href="{{ route('vehicles.index') }}" class="btn btn-primary"><x-icon name="plus" :size="16" /> Kenderaan</a>
        </div>
    </div>

    <!-- KPI ROW -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-soft-icon soft-accent"><x-icon name="car" :size="20" /></div>
                <x-sparkline :points="$vehicleTrend" color="var(--ok)" />
            </div>
            <div class="kpi-value">{{ $totalVehicles }}</div>
            <div class="kpi-label">Jumlah Kenderaan</div>
            <div class="kpi-sub" style="color:var(--ok)">{{ $activeVehicles }} aktif</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-soft-icon soft-warn"><x-icon name="triangle-alert" :size="20" /></div>
                <x-sparkline :points="[$needsAttention, $needsAttention, $urgentCount, $needsAttention, $needsAttention, $needsAttention]" color="var(--danger-text)" />
            </div>
            <div class="kpi-value">{{ $needsAttention }}</div>
            <div class="kpi-label">Perlu Perhatian</div>
            <div class="kpi-sub" style="color:var(--danger-text)">{{ $urgentCount }} kritikal</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-soft-icon soft-info"><x-icon name="wrench" :size="20" /></div>
                <x-sparkline :points="[$inService, $inService, $inService, $inService, $inService, $inService]" color="var(--info)" />
            </div>
            <div class="kpi-value">{{ $inService }}</div>
            <div class="kpi-label">Dalam Servis</div>
            @if($servicesInProgress->count())
                <div class="kpi-sub" style="color:var(--warn-text)">{{ $servicesInProgress->first()->vehicle->plat }}{{ $servicesInProgress->count() > 1 ? ' & ' . ($servicesInProgress->count() - 1) . ' lagi' : '' }}</div>
            @endif
        </div>
        <a href="{{ route('saman.index') }}" class="kpi-card clickable">
            <div class="kpi-top">
                <div class="kpi-soft-icon soft-danger"><x-icon name="receipt" :size="20" /></div>
                <x-sparkline :points="[1, max($unpaidSaman-2,1), max($unpaidSaman-1,1), $unpaidSaman, $unpaidSaman, $unpaidSaman]" color="var(--danger-text)" />
            </div>
            <div class="kpi-value">RM {{ number_format($unpaidSamanTotal) }}</div>
            <div class="kpi-label">Saman Belum Bayar</div>
            <div class="kpi-sub" style="color:var(--danger-text)">{{ $unpaidSaman }} saman tertunggak</div>
        </a>
    </div>

    <!-- URGENT BANNER -->
    @if($urgentVehicles->count())
        <div class="urgent-banner">
            <div class="icon-box"><x-icon name="triangle-alert" :size="18" /></div>
            <div class="txt">
                <strong>Urgent:</strong>
                @foreach($urgentVehicles as $v)
                    @if($v->roadtax_days <= 7)Road tax <strong>{{ $v->plat }}</strong>@endif
                    @if($v->insurance_days <= 7){{ $v->roadtax_days <= 7 ? ' dan ' : '' }}Insuran <strong>{{ $v->plat }}</strong>@endif
                    @if(!$loop->last), @endif
                @endforeach
                akan luput dalam <strong>7 hari</strong>. Sila ambil tindakan segera.
            </div>
            <a href="{{ route('reminders.index') }}" class="link">Lihat <x-icon name="chevron-right" :size="15" /></a>
        </div>
    @endif

    @if($unpaidSaman > 0)
        <a href="{{ route('saman.index') }}" class="alert alert-danger" style="cursor:pointer">
            <x-icon name="triangle-alert" :size="16" />
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
        <a href="{{ route('approvals.index') }}" class="alert alert-info" style="cursor:pointer">
            <x-icon name="clipboard-check" :size="16" />
            <div><strong>{{ $pendingApprovals }} permohonan kenderaan</strong> menunggu kelulusan. <span style="text-decoration:underline">Lihat permohonan →</span></div>
        </a>
    @endif

    <!-- MAIN SPLIT -->
    <div class="dash-split">
        <div class="dash-col-left">
            <!-- Reminders -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><span class="icon-accent"><x-icon name="bell" :size="17" /></span>Peringatan Segera</span>
                    <a href="{{ route('reminders.index') }}" style="font-size:12.5px;color:var(--ink-2);font-weight:600">Semua</a>
                </div>
                <div class="card-body" style="padding:6px 20px 10px">
                    @foreach($vehicles->sortBy(fn($v) => min($v->roadtax_days, $v->insurance_days))->take(4) as $v)
                        @php
                            $isRt = $v->roadtax_days <= $v->insurance_days;
                            $minDays = min($v->roadtax_days, $v->insurance_days);
                            $type = $isRt ? 'Road Tax' : 'Insuran';
                            $kind = $minDays <= 7 ? 'danger' : ($minDays <= 30 ? 'warn' : 'ok');
                        @endphp
                        <div class="reminder-item">
                            <div class="reminder-icon soft-{{ $kind }}"><x-icon :name="$isRt ? 'file-text' : 'shield'" :size="18" /></div>
                            <div class="reminder-text">
                                <div class="title">{{ $type }} — {{ $v->plat }}</div>
                                <div class="sub">{{ $v->model }} · {{ $v->department }}</div>
                            </div>
                            <x-countdown-ring :days="$minDays" :color="$dc($minDays)" />
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Fuel chart -->
            <div class="card" style="padding:18px 22px 20px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px">
                    <span class="card-title"><span class="icon-accent"><x-icon name="fuel" :size="17" /></span>Rekod Bahan Api</span>
                    <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono)">6 bulan terakhir</span>
                </div>
                <div class="fuel-summary">
                    <div><span class="val">RM {{ number_format($fuelTotal, 0) }}</span><div class="sub">Jumlah {{ now()->translatedFormat('F') }}</div></div>
                    <div><span class="val">{{ number_format($fuelLiters, 0) }}<span style="font-size:14px;color:var(--muted)">L</span></span><div class="sub">Jumlah liter</div></div>
                    <div><span class="val">{{ $fuelAvg ? number_format($fuelAvg, 1) : '—' }}</span><div class="sub">L/100km purata</div></div>
                </div>
                @php $fuelMax = max(1, $fuelMonthly->max('total')); @endphp
                <div class="bar-chart">
                    @foreach($fuelMonthly as $m)
                        <div class="bar-col {{ $m['current'] ? 'current' : '' }}">
                            <div class="bar-val">{{ number_format($m['total'] / 1000, 1) }}k</div>
                            <div class="bar" style="height:{{ max(6, round(($m['total'] / $fuelMax) * 88)) }}px"></div>
                            <div class="bar-lbl">{{ $m['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="dash-col-right">
            <!-- AI teaser -->
            <a href="{{ route('anomalies.index') }}" class="ai-card">
                <div class="ai-card-head">
                    <span class="ai-card-icon"><x-icon name="sparkles" :size="16" /></span>
                    <span class="ai-card-title">Pengesanan Anomali AI</span>
                    <span class="ai-card-live"><span class="dot"></span>Live</span>
                </div>
                <div class="ai-card-value">{{ $anomalyWeekCount }}</div>
                <div class="ai-card-caption">anomali dikesan minggu ini</div>
                @if($topAnomaly)
                    @php
                        $conf = ['critical' => 92, 'warning' => 76, 'info' => 58][$topAnomaly->severity] ?? 60;
                    @endphp
                    <div class="ai-card-inner">
                        <div class="t">{{ $topAnomaly->title }}</div>
                        <div class="s">{{ $topAnomaly->vehicle?->plat }}{{ $topAnomaly->vehicle ? ' · ' : '' }}{{ \Illuminate\Support\Str::limit($topAnomaly->description, 48) }}</div>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:9px">
                            <div class="ai-card-bar"><div class="ai-card-bar-fill" style="width:{{ $conf }}%"></div></div>
                            <span style="font-family:var(--font-mono);font-size:11px;color:var(--accent-light);font-weight:500">{{ $conf }}%</span>
                        </div>
                    </div>
                @else
                    <div class="ai-card-empty">Tiada anomali aktif buat masa ini.</div>
                @endif
                <div class="ai-card-cta">Siasat semua anomali <x-icon name="arrow-up-right" :size="14" /></div>
            </a>

            <!-- Status today -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><span class="icon-accent"><x-icon name="map-pin" :size="17" /></span>Status Hari Ini</span>
                    <span style="font-size:10.5px;color:var(--muted);font-family:var(--font-mono)">{{ now()->format('d M') }}</span>
                </div>
                <div class="card-body" style="padding:4px 20px 12px">
                    @foreach($vehicles->take(5) as $v)
                        @php
                            $latestMove = $v->movementLogs()->latest('checkout_time')->first();
                            $isOut = $latestMove && $latestMove->status === 'di_luar';
                            $kind = $v->status === 'servis' ? 'warn' : ($isOut ? 'ok' : 'neutral');
                        @endphp
                        <div class="location-row">
                            <div class="loc-icon soft-{{ $kind }}"><x-icon :name="$v->status === 'servis' ? 'wrench' : ($isOut ? 'route' : 'car')" :size="16" /></div>
                            <div class="loc-info">
                                <div class="loc-main">{{ $v->plat }}</div>
                                <div class="sub">{{ $latestMove ? 'Pemandu: ' . ($latestMove->driver?->name ?? '—') : $v->model . ' · ' . $v->department }}</div>
                            </div>
                            @if($v->status === 'servis')
                                <span class="badge-pill badge-warn">Dalam Servis</span>
                            @elseif($isOut)
                                <span class="badge-pill badge-ok">Dalam Perjalanan</span>
                            @else
                                <span class="badge-pill badge-neutral">Di Pejabat</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-fleet-layout>
