<x-fleet-layout title="Laporan & Analitik">
    <div class="page-header">
        <h2>Laporan & Analitik</h2>
        <p>Laporan pengurusan fleet untuk keputusan pengurusan</p>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="card-body" style="text-align:center;padding:24px">
                <div style="font-size:32px;margin-bottom:8px">📊</div>
                <div style="font-size:14px;font-weight:700;margin-bottom:4px">Laporan Bulanan</div>
                <div style="font-size:12px;color:var(--c-muted)">Ringkasan servis, kos & penggunaan</div>
                <a href="{{ route('reports.monthly') }}" class="btn btn-sm btn-primary" style="margin-top:12px;text-decoration:none">Jana PDF</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="text-align:center;padding:24px">
                <div style="font-size:32px;margin-bottom:8px">💰</div>
                <div style="font-size:14px;font-weight:700;margin-bottom:4px">Laporan Kos</div>
                <div style="font-size:12px;color:var(--c-muted)">Analisis perbelanjaan setiap kenderaan</div>
                <a href="{{ route('reports.cost') }}" class="btn btn-sm btn-primary" style="margin-top:12px;text-decoration:none">Jana PDF</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="text-align:center;padding:24px">
                <div style="font-size:32px;margin-bottom:8px">📅</div>
                <div style="font-size:14px;font-weight:700;margin-bottom:4px">Laporan Compliance</div>
                <div style="font-size:12px;color:var(--c-muted)">Road tax, insuran & Puspakom</div>
                <a href="{{ route('reports.compliance') }}" class="btn btn-sm btn-primary" style="margin-top:12px;text-decoration:none">Jana PDF</a>
            </div>
        </div>
    </div>

    <!-- YTD Cost Summary -->
    <div class="card">
        <div class="card-header"><span class="card-title">📈 Kos Fleet YTD {{ now()->year }}</span></div>
        <div class="card-body">
            <div class="stats-grid" style="margin-bottom:20px">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fff8e0">⛽</div>
                    <div class="stat-val">RM {{ number_format($totalFuel, 0) }}</div>
                    <div class="stat-label">Bahan Api</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e8f0fb">🔧</div>
                    <div class="stat-val">RM {{ number_format($totalService, 0) }}</div>
                    <div class="stat-label">Servis</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ffe8e8">🚨</div>
                    <div class="stat-val">RM {{ number_format($totalSaman, 0) }}</div>
                    <div class="stat-label">Saman</div>
                </div>
                <div class="stat-card" style="border-top:3px solid var(--c-sky)">
                    <div class="stat-icon" style="background:#fff0e8">💰</div>
                    <div class="stat-val" style="color:var(--c-sky)">RM {{ number_format($totalFuel + $totalService + $totalSaman, 0) }}</div>
                    <div class="stat-label">Jumlah YTD</div>
                </div>
            </div>

            @if($fuelByMonth->count())
            <table class="fleet-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        @foreach($fuelByMonth as $fm)
                            <th>{{ \Carbon\Carbon::parse($fm->month . '-01')->translatedFormat('M') }}</th>
                        @endforeach
                        <th>Jumlah YTD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Bahan Api</strong></td>
                        @foreach($fuelByMonth as $fm)
                            <td>RM {{ number_format($fm->cost, 0) }}</td>
                        @endforeach
                        <td><strong>RM {{ number_format($totalFuel, 0) }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Servis</strong></td>
                        @foreach($fuelByMonth as $fm)
                            @php $svc = $serviceByMonth->firstWhere('month', $fm->month); @endphp
                            <td>{{ $svc ? 'RM ' . number_format($svc->cost, 0) : '—' }}</td>
                        @endforeach
                        <td><strong>RM {{ number_format($totalService, 0) }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Saman</strong></td>
                        @foreach($fuelByMonth as $fm)
                            @php $sm = $samanByMonth->firstWhere('month', $fm->month); @endphp
                            <td>{{ $sm ? 'RM ' . number_format($sm->amount, 0) : '—' }}</td>
                        @endforeach
                        <td><strong>RM {{ number_format($totalSaman, 0) }}</strong></td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>
    </div>
</x-fleet-layout>
