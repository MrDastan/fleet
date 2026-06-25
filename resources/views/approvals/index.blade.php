<x-fleet-layout title="Permohonan Kenderaan">
    <div class="page-header">
        <h2>Permohonan Kenderaan</h2>
        <p>Sistem kelulusan 3 peringkat: Penjaga → Fleet → Rekod selesai</p>
    </div>

    <div class="card">
        <table class="fleet-table">
            <thead><tr><th>No.</th><th>Pemohon</th><th>Kenderaan</th><th>Tarikh</th><th>Tujuan</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($requests as $r)
                <tr>
                    <td><strong style="font-family:monospace;font-size:11px">{{ $r->request_no }}</strong></td>
                    <td><strong>{{ $r->requester->name }}</strong></td>
                    <td>{{ $r->vehicle->plat }} — {{ $r->vehicle->model }}</td>
                    <td>{{ $r->use_date->format('d M Y') }}</td>
                    <td>{{ $r->purpose }}</td>
                    <td>
                        @if($r->status === 'pending_guard')<span class="badge-pill badge-warn">⏳ Menunggu Penjaga</span>
                        @elseif($r->status === 'pending_fleet')<span class="badge-pill badge-info">📝 Menunggu Fleet</span>
                        @elseif($r->status === 'approved')<span class="badge-pill badge-ok">✅ Diluluskan</span>
                        @elseif($r->status === 'rejected')<span class="badge-pill badge-danger">❌ Ditolak</span>
                        @else<span class="badge-pill badge-neutral">🏁 Selesai</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--c-muted);padding:24px">Tiada permohonan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-fleet-layout>
