<x-fleet-layout title="Road Tax & Insuran">
    <div class="page-header">
        <h2>Road Tax & Insuran</h2>
        <p>Pantau tarikh luput road tax dan polisi insuran semua kenderaan</p>
    </div>

    <div class="grid-2">
        <div class="stat-card" style="border-left:4px solid var(--c-danger)">
            <div style="font-size:12px;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Akan Luput ≤ 30 Hari</div>
            <div style="display:flex;gap:24px;margin-top:10px">
                <div>
                    <div style="font-size:28px;font-weight:800;color:var(--c-danger)">{{ $vehicles->filter(fn($v) => $v->roadtax_days <= 30)->count() }}</div>
                    <div style="font-size:12px;color:var(--c-muted)">Road Tax</div>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:800;color:var(--c-danger)">{{ $vehicles->filter(fn($v) => $v->insurance_days <= 30)->count() }}</div>
                    <div style="font-size:12px;color:var(--c-muted)">Insuran</div>
                </div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid var(--c-ok)">
            <div style="font-size:12px;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Status Baik (> 30 Hari)</div>
            <div style="display:flex;gap:24px;margin-top:10px">
                <div>
                    <div style="font-size:28px;font-weight:800;color:var(--c-ok)">{{ $vehicles->filter(fn($v) => $v->roadtax_days > 30)->count() }}</div>
                    <div style="font-size:12px;color:var(--c-muted)">Road Tax</div>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:800;color:var(--c-ok)">{{ $vehicles->filter(fn($v) => $v->insurance_days > 30)->count() }}</div>
                    <div style="font-size:12px;color:var(--c-muted)">Insuran</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">📄 Senarai Road Tax</span>
        </div>
        <table class="fleet-table">
            <thead><tr><th>No. Plat</th><th>Model</th><th>Luput Road Tax</th><th>Baki Hari</th><th>Luput Insuran</th><th>Baki Hari</th></tr></thead>
            <tbody>
                @foreach($vehicles as $v)
                @php
                    $rtColor = $v->roadtax_days <= 7 ? 'var(--c-danger)' : ($v->roadtax_days <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
                    $insColor = $v->insurance_days <= 7 ? 'var(--c-danger)' : ($v->insurance_days <= 30 ? 'var(--c-warn)' : 'var(--c-ok)');
                @endphp
                <tr>
                    <td><strong>{{ $v->plat }}</strong></td>
                    <td>{{ $v->model }}</td>
                    <td>{{ $v->roadtax_expiry?->format('d M Y') ?? '—' }}</td>
                    <td><span style="color:{{ $rtColor }};font-weight:700">{{ $v->roadtax_days }} hari</span></td>
                    <td>{{ $v->insurance_expiry?->format('d M Y') ?? '—' }}</td>
                    <td><span style="color:{{ $insColor }};font-weight:700">{{ $v->insurance_days }} hari</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-fleet-layout>
