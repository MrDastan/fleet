<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Compliance</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e2a3a; }
        h1 { font-size: 18px; color: #e85d00; margin-bottom: 4px; }
        .sub { font-size: 10px; color: #636e72; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f4f6fb; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #636e72; border-bottom: 1px solid #dde3ee; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        .danger { color: #d63031; font-weight: 700; }
        .warn { color: #e17055; font-weight: 700; }
        .ok { color: #00b894; font-weight: 700; }
    </style>
</head>
<body>
    <h1>MSD Fleet — Laporan Compliance</h1>
    <div class="sub">Road Tax, Insuran & Puspakom • Dijana: {{ now()->format('d M Y, H:i') }}</div>

    <table>
        <tr><th>No. Plat</th><th>Model</th><th>Road Tax Luput</th><th>Baki</th><th>Insuran Luput</th><th>Baki</th><th>Puspakom</th><th>Status</th></tr>
        @foreach($vehicles as $v)
        @php
            $rtD = $v->roadtax_days; $insD = $v->insurance_days;
            $pskD = $v->puspakom_expiry ? (int) now()->diffInDays($v->puspakom_expiry, false) : null;
            $worst = min($rtD, $insD, $pskD ?? 999);
        @endphp
        <tr>
            <td><strong>{{ $v->plat }}</strong></td>
            <td>{{ $v->model }}</td>
            <td>{{ $v->roadtax_expiry?->format('d/m/Y') ?? '—' }}</td>
            <td class="{{ $rtD <= 7 ? 'danger' : ($rtD <= 30 ? 'warn' : 'ok') }}">{{ $rtD }} hari</td>
            <td>{{ $v->insurance_expiry?->format('d/m/Y') ?? '—' }}</td>
            <td class="{{ $insD <= 7 ? 'danger' : ($insD <= 30 ? 'warn' : 'ok') }}">{{ $insD }} hari</td>
            <td>{{ $v->puspakom_expiry?->format('d/m/Y') ?? '—' }}</td>
            <td>
                @if($worst <= 7) <span class="danger">URGENT</span>
                @elseif($worst <= 30) <span class="warn">SEGERA</span>
                @else <span class="ok">OK</span> @endif
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>
