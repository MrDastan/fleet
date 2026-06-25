<x-fleet-layout title="Log Pergerakan">
    <div class="page-header">
        <h2>Log Pergerakan & Check-in/out</h2>
        <p>Log keluar masuk kenderaan di stor / parkir syarikat</p>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead><tr><th>No. Plat</th><th>Pemandu</th><th>Jabatan</th><th>Tujuan</th><th>Masa Keluar</th><th>Masa Masuk</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($logs as $l)
                <tr>
                    <td><strong>{{ $l->vehicle->plat }}</strong></td>
                    <td>{{ $l->driver?->name ?? '—' }}</td>
                    <td>{{ $l->department ?? '—' }}</td>
                    <td>{{ $l->purpose }}</td>
                    <td>{{ $l->checkout_time?->format('H:i') ?? '—' }}</td>
                    <td>{{ $l->checkin_time?->format('H:i') ?? '—' }}</td>
                    <td>
                        @if($l->status === 'di_luar')<span class="badge-pill badge-ok">Di Luar</span>
                        @else<span class="badge-pill badge-neutral">Kembali</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--c-muted);padding:24px">Tiada rekod pergerakan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-fleet-layout>
