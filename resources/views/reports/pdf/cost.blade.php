<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kos — {{ $year }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e2a3a; }
        h1 { font-size: 18px; color: #e85d00; margin-bottom: 4px; }
        .sub { font-size: 10px; color: #636e72; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f4f6fb; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #636e72; border-bottom: 1px solid #dde3ee; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        .total { font-weight: 700; background: #fff0e8; }
    </style>
</head>
<body>
    <h1>MSD Fleet — Laporan Kos Per Kenderaan</h1>
    <div class="sub">Tahun {{ $year }} • Dijana: {{ now()->format('d M Y, H:i') }}</div>

    <table>
        <tr><th>No. Plat</th><th>Model</th><th>Jabatan</th><th>Bahan Api</th><th>Servis</th><th>Saman</th><th>Jumlah</th></tr>
        @php $tFuel = 0; $tSvc = 0; $tSaman = 0; @endphp
        @foreach($data as $d)
        @php $total = $d['fuel'] + $d['service'] + $d['saman']; $tFuel += $d['fuel']; $tSvc += $d['service']; $tSaman += $d['saman']; @endphp
        <tr>
            <td><strong>{{ $d['vehicle']->plat }}</strong></td>
            <td>{{ $d['vehicle']->model }}</td>
            <td>{{ $d['vehicle']->department }}</td>
            <td>RM {{ number_format($d['fuel'], 2) }}</td>
            <td>RM {{ number_format($d['service'], 2) }}</td>
            <td>RM {{ number_format($d['saman'], 2) }}</td>
            <td><strong>RM {{ number_format($total, 2) }}</strong></td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="3">JUMLAH</td>
            <td>RM {{ number_format($tFuel, 2) }}</td>
            <td>RM {{ number_format($tSvc, 2) }}</td>
            <td>RM {{ number_format($tSaman, 2) }}</td>
            <td>RM {{ number_format($tFuel + $tSvc + $tSaman, 2) }}</td>
        </tr>
    </table>
</body>
</html>
