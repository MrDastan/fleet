<x-fleet-layout title="Pengurusan Saman">
    <div class="page-header">
        <h2>Pengurusan Saman</h2>
        <p>Rekod dan pemantauan saman trafik, parkir & kesalahan lain</p>
    </div>

    <div class="stats-grid" style="margin-bottom:20px">
        <div class="stat-card" style="border-top:3px solid var(--c-danger)">
            <div class="stat-icon" style="background:#ffe8e8">🚨</div>
            <div class="stat-val" style="color:var(--c-danger)">{{ $unpaidCount }}</div>
            <div class="stat-label">Belum Dijelaskan</div>
            <div class="stat-sub red">RM {{ number_format($unpaidTotal) }} tertunggak</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--c-warn)">
            <div class="stat-icon" style="background:#fff3e0">⏳</div>
            <div class="stat-val" style="color:var(--c-warn)">{{ $appealCount }}</div>
            <div class="stat-label">Dalam Rayuan</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--c-ok)">
            <div class="stat-icon" style="background:#e8fff6">✅</div>
            <div class="stat-val" style="color:var(--c-ok)">{{ $paidCount }}</div>
            <div class="stat-label">Telah Dijelaskan</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--c-sky)">
            <div class="stat-icon" style="background:#fff0e8">📋</div>
            <div class="stat-val">{{ $records->count() }}</div>
            <div class="stat-label">Jumlah Saman</div>
        </div>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead><tr><th>No. Saman</th><th>No. Plat</th><th>Jenis</th><th>Kesalahan</th><th>Tarikh</th><th>Jumlah</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($records as $r)
                <tr>
                    <td><strong style="font-family:monospace;font-size:12px">{{ $r->saman_no }}</strong></td>
                    <td><strong>{{ $r->vehicle->plat }}</strong></td>
                    <td>{{ $r->saman_type }}</td>
                    <td>{{ $r->offense }}</td>
                    <td>{{ $r->date->format('d M Y') }}</td>
                    <td><strong>RM {{ number_format($r->amount, 2) }}</strong></td>
                    <td>
                        @if($r->status === 'belum_bayar')<span class="badge-pill badge-danger">Belum Bayar</span>
                        @elseif($r->status === 'dalam_rayuan')<span class="badge-pill badge-warn">Dalam Rayuan</span>
                        @else<span class="badge-pill badge-ok">Telah Bayar</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod saman</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-fleet-layout>
