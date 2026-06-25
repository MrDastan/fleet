<x-fleet-layout title="Pusat Peringatan">
    <div class="page-header">
        <h2>Pusat Peringatan</h2>
        <p>Semua notifikasi dan peringatan automatik sistem</p>
    </div>

    <div class="grid-2">
        <div class="card mb20">
            <div class="card-header">
                <span class="card-title" style="color:var(--c-danger)">🔴 Kritikal (≤ 7 hari)</span>
            </div>
            <div class="card-body">
                @forelse($critical as $v)
                <div class="reminder-item">
                    <div class="reminder-icon" style="background:#ffe8e8">{{ $v->roadtax_days <= 7 ? '📄' : '🛡️' }}</div>
                    <div class="reminder-text">
                        <div class="title">{{ $v->roadtax_days <= 7 ? 'Road Tax' : 'Insuran' }} — {{ $v->plat }}</div>
                        <div class="sub">{{ $v->model }}</div>
                    </div>
                    <div class="reminder-days" style="color:var(--c-danger)">
                        <div class="days">{{ min($v->roadtax_days, $v->insurance_days) }}</div>
                        <div class="lbl">hari</div>
                    </div>
                </div>
                @empty
                <p style="color:var(--c-muted);text-align:center;padding:16px">Tiada peringatan kritikal</p>
                @endforelse
            </div>
        </div>

        <div class="card mb20">
            <div class="card-header">
                <span class="card-title" style="color:var(--c-warn)">🟠 Perlu Perhatian (8–30 hari)</span>
            </div>
            <div class="card-body">
                @forelse($attention as $v)
                <div class="reminder-item">
                    <div class="reminder-icon" style="background:#fff3e0">{{ $v->roadtax_days <= 30 ? '📄' : '🛡️' }}</div>
                    <div class="reminder-text">
                        <div class="title">{{ $v->roadtax_days <= 30 && $v->roadtax_days > 7 ? 'Road Tax' : 'Insuran' }} — {{ $v->plat }}</div>
                        <div class="sub">{{ $v->model }}</div>
                    </div>
                    <div class="reminder-days" style="color:var(--c-warn)">
                        <div class="days">{{ min($v->roadtax_days, $v->insurance_days) }}</div>
                        <div class="lbl">hari</div>
                    </div>
                </div>
                @empty
                <p style="color:var(--c-muted);text-align:center;padding:16px">Tiada peringatan</p>
                @endforelse
            </div>
        </div>
    </div>
</x-fleet-layout>
