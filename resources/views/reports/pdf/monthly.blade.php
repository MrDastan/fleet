<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan — {{ $month }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e2a3a; }
        h1 { font-size: 18px; color: #e85d00; margin-bottom: 4px; }
        h2 { font-size: 14px; margin: 16px 0 8px; border-bottom: 2px solid #dde3ee; padding-bottom: 4px; }
        .sub { font-size: 10px; color: #636e72; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f4f6fb; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #636e72; border-bottom: 1px solid #dde3ee; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .total { font-weight: 700; background: #fff0e8; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 700; }
    </style>
</head>
<body>
    <h1>MSD Fleet — Laporan Bulanan</h1>
    <div class="sub">{{ $month }} • Dijana: {{ now()->format('d M Y, H:i') }} • Jumlah kenderaan: {{ $vehicles->count() }}</div>

    <h2>Rekod Servis ({{ $services->count() }})</h2>
    @if($services->count())
    <table>
        <tr><th>No. Plat</th><th>Jenis Servis</th><th>Tarikh</th><th>Bengkel</th><th>Kos</th><th>Status</th></tr>
        @foreach($services as $s)
        <tr>
            <td><strong>{{ $s->vehicle->plat }}</strong></td>
            <td>{{ $s->service_type }}</td>
            <td>{{ $s->date->format('d/m/Y') }}</td>
            <td>{{ $s->workshop ?? '—' }}</td>
            <td>{{ $s->cost ? 'RM ' . number_format($s->cost, 2) : '—' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $s->status)) }}</td>
        </tr>
        @endforeach
        <tr class="total"><td colspan="4">Jumlah</td><td colspan="2">RM {{ number_format($services->sum('cost'), 2) }}</td></tr>
    </table>
    @else <p style="color:#636e72">Tiada rekod servis bulan ini.</p> @endif

    <h2>Rekod Bahan Api ({{ $fuel->count() }})</h2>
    @if($fuel->count())
    <table>
        <tr><th>No. Plat</th><th>Tarikh</th><th>Stesen</th><th>Liter</th><th>Jumlah</th></tr>
        @foreach($fuel as $f)
        <tr>
            <td><strong>{{ $f->vehicle->plat }}</strong></td>
            <td>{{ $f->datetime->format('d/m/Y') }}</td>
            <td>{{ $f->station ?? '—' }}</td>
            <td>{{ number_format($f->liters, 1) }}L</td>
            <td>RM {{ number_format($f->total_cost, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total"><td colspan="3">Jumlah</td><td>{{ number_format($fuel->sum('liters'), 0) }}L</td><td>RM {{ number_format($fuel->sum('total_cost'), 2) }}</td></tr>
    </table>
    @else <p style="color:#636e72">Tiada rekod bahan api bulan ini.</p> @endif

    <h2>Rekod Saman ({{ $saman->count() }})</h2>
    @if($saman->count())
    <table>
        <tr><th>No. Saman</th><th>No. Plat</th><th>Kesalahan</th><th>Jumlah</th><th>Status</th></tr>
        @foreach($saman as $sm)
        <tr>
            <td>{{ $sm->saman_no }}</td>
            <td><strong>{{ $sm->vehicle->plat }}</strong></td>
            <td>{{ $sm->offense }}</td>
            <td>RM {{ number_format($sm->amount, 2) }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $sm->status)) }}</td>
        </tr>
        @endforeach
        <tr class="total"><td colspan="3">Jumlah</td><td colspan="2">RM {{ number_format($saman->sum('amount'), 2) }}</td></tr>
    </table>
    @else <p style="color:#636e72">Tiada rekod saman bulan ini.</p> @endif
</body>
</html>
